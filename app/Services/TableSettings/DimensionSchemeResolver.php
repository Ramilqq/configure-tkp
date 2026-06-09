<?php

namespace App\Services\TableSettings;

use App\Models\TableSettings\TemplateDimensionScheme;

/**
 * Подбор габаритной схемы по:
 * - опциям продукта (option_key => value) из product.product_option[*].get_name.key
 * - выбранным правилам цены (rules_fields: rule_key => value)
 */
class DimensionSchemeResolver
{
    /** @var array<int, \Illuminate\Support\Collection<int,TemplateDimensionScheme>> */
    private array $cache = [];

    public function resolveForNode(array $node): ?array
    {
        $templateId = (int)($node['template_id'] ?? ($node['product']['template_id'] ?? 0));

        if ($templateId <= 0) return null;

        $options = $this->extractOptionMap($node);
        $rules   = $this->extractRuleMap($node);

        $schemes = $this->loadSchemes($templateId);
        $results = [];
        foreach ($schemes as $scheme) {
            if (!$scheme->enabled) continue;

            $okOptions = $this->matchConditions((array)($scheme->conditions ?? []), (string)($scheme->match_mode ?? 'all'), $options, 'option_key');
            $okRules = $this->matchConditions((array)($scheme->rule_conditions ?? []), (string)($scheme->match_mode ?? 'all'), $rules, 'rule_key');
            
            if (!$okRules || !$okOptions) continue;

            // схема подошла
            $results[] = $scheme;
        }

        return $results;
    }

    private function loadSchemes(int $templateId)
    {
        if (!isset($this->cache[$templateId])) {
            $this->cache[$templateId] = TemplateDimensionScheme::query()
                ->where('template_id', $templateId)
                ->orderBy('sort')
                ->orderBy('id')
                ->with('images')
                ->get();
        }
        return $this->cache[$templateId];
    }

    private function extractOptionMap(array $node): array
    {
        $map = [];
        $product = $node['product'] ?? null;
        if (!is_array($product)) return $map;

        foreach (($product['product_option'] ?? []) as $opt) {
            $key = $opt['get_name']['key'] ?? null;
            if (!$key) continue;
            $map[(string)$key] = $opt['value'] ?? null;
        }

        return $map;
    }

    private function extractRuleMap(array $node): array
    {
        $rules = [];
        
        if ($node['product']['price_rules_applied']) {
            foreach($node['product']['price_rules_applied'] as $rules_key => $rules_value) {
                $rules[$rules_value['rule_id']] = $rules_value['value'] ?? null;
            }
        }
        return is_array($rules) ? $rules : [];
    }

    /**
     * @param array<int,array<string,mixed>> $conditions
     * @param array<string,mixed> $sourceMap
     */
    private function matchConditions(array $conditions, string $matchMode, array $sourceMap, string $keyField): bool
    {
        if (empty($conditions)) return true;

        $matchMode = $matchMode === 'any' ? 'any' : 'all';

        $results = [];

        foreach ($conditions as $cond) {
            $key = $cond[$keyField] ?? null;
            if (!$key) {
                // пустая строка условия — игнор
                continue;
            }

            $op = (string)($cond['op'] ?? 'equals');
            $expected = $cond['value'] ?? null;
            $actual = $sourceMap[(string)$key] ?? null;

            $results[] = $this->passes($op, $actual, $expected);
        }

        // если все условия пустые => true
        if (empty($results)) return true;

        return $matchMode === 'any'
            ? in_array(true, $results, true)
            : !in_array(false, $results, true);
    }

    private function passes(string $op, mixed $left, mixed $right): bool
    {
        $op = $op ?: 'exists';

        $has = $left !== null;
        $filled = $has && trim((string)$left) !== '';

        if ($op === 'exists') return $has;
        if ($op === 'filled') return $filled;

        

        if (!$filled) return false;

        $l = trim((string)$left);

        if ($op === 'equals') {
            return $l === trim((string)($right ?? ''));
        }

        if ($op === 'not_equals') {
            //dd($op, $left, $right, $l, ($l !== trim(   (string)($right ?? '')    ))   );
            return $l !== trim((string)($right ?? ''));
        }
        
        $rightArr = [];
        if (is_array($right)) {
            $rightArr = array_map(fn($v) => trim((string)$v), $right);
        } elseif (is_string($right)) {
            $rightArr = array_filter(array_map('trim', explode(',', $right)), fn($v) => $v !== '');
        } elseif ($right !== null) {
            $rightArr = [trim((string)$right)];
        }

        if ($op === 'in') {
            return in_array($l, $rightArr, true);
        }

        if ($op === 'not_in') {
            return !in_array($l, $rightArr, true);
        }

        if ($op === 'contains') {
            return is_string($right) && $right !== '' && str_contains($l, (string)$right);
        }

        return false;
    }
}

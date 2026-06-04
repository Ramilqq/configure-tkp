<?php

namespace App\Services\TableSettings;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\TemplatePriceRule;
use App\Services\BankRequest;

class TemplatePriceRuleService
{
    /**
     * Применить правила цены к продукту.
     *
     * @param  Product  $product       — продукт с загруженным шаблоном и priceRules
     * @param  array    $option_applied — результат FrOptionsAppliedService::apply():
     *                                   [templateOption->key => ['value' => ..., 'price' => ..., ...]]
     *
     * Условия (все AND):
     *   option_conditions       — сравниваем $option_applied[key]['value'] с condition.value
     *   option_price_conditions — сравниваем $option_applied[key]['price'] с condition.value
     */
    public function apply(Product $product, array $option_applied = []): array
    {
        $price    = $this->toFloat($product->price);
        $delivery = $this->toFloat($product->delivery);
        $banks = new BankRequest();

        // --- загружаем правила шаблона ---
        $rules = $product->relationLoaded('template') && $product->template?->relationLoaded('priceRules')
            ? $product->template->priceRules
            : TemplatePriceRule::query()
                ->where('template_id', (int)$product->template_id)
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

        if ($rules->isEmpty()) {
            return ['price' => $price, 'delivery' => $delivery, 'applied_rules' => []];
        }

        // --- собираем все template_option_id из всех условий ---
        $allOptionIds = [];
        foreach ($rules as $rule) {
            $conditions = $rule->conditions ?? [];
            foreach ($conditions['option_conditions'] ?? [] as $c) {
                $allOptionIds[] = (int)($c['template_option_id'] ?? 0);
            }
            foreach ($conditions['option_price_conditions'] ?? [] as $c) {
                $allOptionIds[] = (int)($c['template_option_id'] ?? 0);
            }
        }

        // --- загружаем маппинг template_option_id => key ---
        // (один запрос на все правила)
        $idToKey = [];
        if (!empty($allOptionIds)) {
            $idToKey = TemplateOption::query()
                ->whereIn('id', array_unique($allOptionIds))
                ->pluck('key', 'id')
                ->all();
        }

        $applied = [];

        foreach ($rules as $rule) {
            if (!$rule->enabled) {
                continue;
            }

            $target = (string)$rule->target_field;
            if (!in_array($target, ['price', 'delivery'], true)) {
                continue;
            }

            $value = $this->toFloat($rule->value);
            if ($value === null) {
                continue;
            }

            $conditions = $rule->conditions ?? [];

            // --- проверяем option_conditions ---
            foreach ($conditions['option_conditions'] ?? [] as $cond) {
                $tid      = (int)($cond['template_option_id'] ?? 0);
                $operator = (string)($cond['operator'] ?? '=');
                $condVal  = (string)($cond['value'] ?? '');

                $key    = $idToKey[$tid] ?? null;
                $actual = $key !== null ? (string)($option_applied[$key]['value'] ?? '') : null;

                if ($actual === null || !$this->compare($actual, $operator, $condVal)) {
                    continue 2;
                }
            }

            // --- проверяем option_price_conditions ---
            foreach ($conditions['option_price_conditions'] ?? [] as $cond) {
                $tid      = (int)($cond['template_option_id'] ?? 0);
                $operator = (string)($cond['operator'] ?? '=');
                $condVal  = $this->toFloat($cond['value'] ?? null);

                $key    = $idToKey[$tid] ?? null;
                $actual = $key !== null ? $this->toFloat($option_applied[$key]['price'] ?? null) : null;

                if ($actual === null || $condVal === null || !$this->compareNumeric($actual, $operator, $condVal)) {
                    continue 2;
                }
            }

            // --- все условия выполнены, применяем ---
            $before = $target === 'price' ? $price : $delivery;
            $after  = $this->applyMode((string)$rule->mode, $before, $value);

            if ($target === 'price') {
                $price = $after;
            } else {
                $delivery = $after;
            }

            $currency_val = 1.0;
            if ($rule->currency == 'RUB') $currency_val = 1.0;
            else $currency_val = $banks->getValue($product['currency']);

            $applied[] = [
                'rule_id'   => (int)$rule->id,
                'rule_name' => (string)$rule->name,
                'target'    => $target,
                'mode'      => (string)$rule->mode,
                'value'     => $value,
                'currency'  => (string)$rule->currency,
                'currency_val' => $currency_val,
                'before'    => $before,
                'after'     => $after,
            ];
        }

        return [
            'price'         => $price,
            'delivery'      => $delivery,
            'applied_rules' => $applied,
        ];
    }

    // -------------------------------------------------------------------------

    /**
     * Сравнение строковых значений (для option_conditions).
     * Если оба значения числовые — сравниваем числово.
     */
    private function compare(string $actual, string $operator, string $expected): bool
    {
        $aNum = $this->toFloat($actual);
        $eNum = $this->toFloat($expected);

        if ($aNum !== null && $eNum !== null) {
            return $this->compareNumeric($aNum, $operator, $eNum);
        }

        return match ($operator) {
            '='  => $actual === $expected,
            '!=' => $actual !== $expected,
            default => false,
        };
    }

    /**
     * Числовое сравнение (для option_price_conditions).
     */
    private function compareNumeric(float $actual, string $operator, float $expected): bool
    {
        return match ($operator) {
            '>'  => $actual > $expected,
            '>=' => $actual >= $expected,
            '='  => $actual == $expected,
            '<'  => $actual < $expected,
            '<=' => $actual <= $expected,
            default => false,
        };
    }

    private function applyMode(string $mode, ?float $base, float $value): float
    {
        $base = $base ?? 0.0;

        return match ($mode) {
            'replace'  => $value,
            'add'      => $base + $value,
            'multiply' => $base * $value,
            default    => $base,
        };
    }

    private function toFloat(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_int($v) || is_float($v)) {
            return (float)$v;
        }

        $s = str_replace(["\xC2\xA0", ' '], '', trim((string)$v));
        $s = str_replace(',', '.', $s);

        return is_numeric($s) ? (float)$s : null;
    }
}

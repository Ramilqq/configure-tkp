<?php

namespace App\Services\TableSettings;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\TemplatePriceRule;

class TemplatePriceRuleService
{
    public function apply(Product $product, array $rulesForm): array
    {
        $price = $this->toFloatOrNull($product->price);
        $delivery = $this->toFloatOrNull($product->delivery);

        // template_option_id => value
        $optValues = [];
        if ($product->relationLoaded('productOption')) {
            foreach ($product->productOption as $po) {
                $optValues[(int)$po->template_option_id] = $po->value;
            }
        } else {
            foreach ($product->productOption()->get(['template_option_id','value']) as $po) {
                $optValues[(int)$po->template_option_id] = $po->value;
            }
        }

        // правила шаблона
        $rules = [];
        if ($product->relationLoaded('template') && $product->template && $product->template->relationLoaded('priceRules')) {
            $rules = $product->template->priceRules;
        } else {
            $rules = TemplatePriceRule::query()
                ->where('template_id', (int)$product->template_id)
                ->orderBy('sort')
                ->orderBy('id')
                ->get();
        }
        
        $applied = [];

        foreach ($rules as $rule) {
            if (!$rule->enabled) continue;

            $target = (string)$rule->target_field;
            if (!in_array($target, ['price','delivery'], true)) continue;

            // 0) правило должно присутствовать в форме
            if (!array_key_exists($rule->key, $rulesForm)) {
                continue;
            }
            
            $formVal = $rulesForm[$rule->key];
            
            // 1) условие по значению из формы (rulesForm)
            if (!$this->passes(
                (string)($rule->condition_operator ?? 'exists'),
                $formVal,
                $rule->condition_value,
                (string)($rule->condition_field ?? 'checkbox')
            )) {
                continue;
            }

            // 2) дополнительное условие по текстовой опции товара (НЕ обязательно)
            if (!empty($rule->text_option_id)) {
                $textVal = $optValues[(int)$rule->text_option_id] ?? null;

                if (!$this->passes(
                    (string)($rule->text_operator ?? 'exists'),
                    $textVal,
                    $rule->text_value,
                    (string)($rule->text_field ?? 'checkbox')
                )) {
                    continue;
                }
            }
            
            // 3) получить значение для применения:
            //    - если есть driver_option_id => mapping по диапазонам/строкам
            //    - иначе => fixed_value (без выбора опций продукта)
            $valueToApply = null;

            if (!empty($rule->driver_option_id)) {
                $driverVal = $optValues[(int)$rule->driver_option_id] ?? null;
                $valueToApply = $this->lookupMappedValue($driverVal, (array)($rule->mapping ?? []), $formVal, $textVal ?? null);
            } else {
                $valueToApply = $this->toFloatOrNull($rule->fixed_value);
            }

            
            
            if ($valueToApply === null) {
                continue;
            }

            // 4) применить к цене
            $before = ($target === 'price') ? $price : $delivery;
            $after  = $this->applyMode((string)$rule->mode, $before, $valueToApply);

            if ($target === 'price') $price = $after;
            else $delivery = $after;

            
            if ($rule->generation_name_status) {
                $generation_name = $this->generateName($rule, $formVal ?? null);
            } else {
                $generation_name = null;
            }

            $applied[] = [
                'rule_id' => (int)$rule->id,
                'rule_key' => (string)$rule->key,
                'rule_name' => (string)$rule->name,
                'target' => $target,
                'mode' => (string)$rule->mode,

                'generation_name' => $generation_name,

                'form_value' => $formVal,

                'text_option_id' => $rule->text_option_id ? (int)$rule->text_option_id : null,
                'driver_option_id' => $rule->driver_option_id ? (int)$rule->driver_option_id : null,

                'mapped_or_fixed_value' => $valueToApply,

                'before' => $before,
                'after' => $after,
            ];
        }

        return [
            'price' => $price,
            'delivery' => $delivery,
            'applied_rules' => $applied,
        ];
    }

    public function generateName(TemplatePriceRule $rule, mixed $formVal = null): ?string
    {
        return $rule->generation_name_text ?? $formVal;
    }

    /**
     * Универсальная проверка условий exists/filled/equals/not_equals.
     * $left — фактическое значение (из формы или из опции товара).
     * $right — значение из правила (condition_value / text_value).
     */
    private function passes(string $op, mixed $left, mixed $right, string $field_type = 'checkbox'): bool
    {
        $op = $op ?: 'exists';

        $has = $left !== null;
        $filled = $has && trim((string)$left) !== '';
        
        if ($op === 'exists') return $has;
        if ($op === 'filled') return $filled;
  
        // equals / not_equals
        // bool-режим (если left явно bool или right похож на bool)
        if (is_bool($left) || $this->looksLikeBool($right)) {
            $l = $this->toBool($left);
            $r = $this->toBool($right);

            return $op === 'equals' ? ($l === $r) : ($l !== $r);
        }

        if ($field_type === 'checkbox') {
            $l = $filled; // для чекбокса сравниваем не само значение, а факт его наличия/отсутствия
            $r = $this->toBool($right);
        } elseif ($field_type === 'select') {
            $right_arr = explode(',', (string)$right);
            $r = null;
            foreach ($right_arr as $k => $v) {
                if (trim($v) === trim((string)$left)) {
                    $r = trim($v);
                    break;
                }
            }
            $l = trim((string)$left);
        } else {
            $l = trim((string)$left);
            $r = trim((string)($right ?? ''));
        }
        

        return $op === 'equals' ? ($l === $r) : ($l !== $r);
    }

    private function looksLikeBool(mixed $v): bool
    {
        $s = mb_strtolower(trim((string)$v));
        return in_array($s, ['1','0','true','false','да','нет','yes','no','on','off'], true);
    }

    private function toBool(mixed $v): bool
    {
        if (is_bool($v)) return $v;

        $s = mb_strtolower(trim((string)$v));
        return in_array($s, ['1','true','да','yes','on'], true);
    }

    private function lookupMappedValue(mixed $driverValRaw, array $mapping, mixed $formConditionVal = null, mixed $textConditionVal = null): ?float
    {
        $driverStr = trim((string)($driverValRaw ?? ''));
        if ($driverStr === '') return null;

        $driverNum = $this->toFloatOrNull($driverStr);

        foreach ($mapping as $row) {
            if (!is_array($row)) continue;

            $fromRaw = $row['from'] ?? null;
            $toRaw   = $row['to'] ?? null;
            $valRaw  = $row['value'] ?? null;

            $val = $this->toFloatOrNull($valRaw);
            if ($val === null) continue;

            // Если в строке mapping заданы дополнительные ограничения по условию или по текстовому значению,
            // то они должны совпадать с текущими значениями из формы / товара.
            $mappingCond = $row['condition'] ?? null;
            if ($mappingCond !== null && trim((string)$mappingCond) !== '') {
                if (!$this->matchesMappingValue($formConditionVal, $mappingCond)) {
                    continue;
                }
            }

            $mappingText = $row['text'] ?? null;
            if ($mappingText !== null && trim((string)$mappingText) !== '') {
                if (!$this->matchesMappingValue($textConditionVal, $mappingText)) {
                    continue;
                }
            }
            
            // числовой сценарий
            if ($driverNum !== null) {
                $from = $this->toFloatOrNull($fromRaw);
                $to   = $this->toFloatOrNull($toRaw);

                $min = ($from === null) ? -INF : $from;
                $max = ($to === null) ? INF : $to;

                if ($driverNum >= $min && $driverNum <= $max) {
                    return $val;
                }
                continue;
            }

            // строковый сценарий
            $fromStr = trim((string)($fromRaw ?? ''));
            if ($fromStr !== '' && $driverStr === $fromStr) {
                return $val;
            }
        }

        return null;
    }

    private function applyMode(string $mode, ?float $base, float $value): ?float
    {
        $base = $base ?? 0.0;

        return match ($mode) {
            'replace'  => $value,
            'add'      => $base + $value,
            'multiply' => $base * $value,
            default    => $base,
        };
    }

    private function toFloatOrNull(mixed $v): ?float
    {
        if ($v === null) return null;
        if (is_int($v) || is_float($v)) return (float)$v;

        $s = trim((string)$v);
        if ($s === '') return null;

        $s = str_replace(["\xC2\xA0", ' '], '', $s);
        $s = str_replace(',', '.', $s);

        return is_numeric($s) ? (float)$s : null;
    }

    private function matchesMappingValue(mixed $actual, mixed $expected): bool
    {
        if ($expected === null) return true;

        // normalize expected to array of strings
        $expArr = [];
        if (is_array($expected)) {
            $expArr = array_map(fn($v) => trim((string)$v), $expected);
        } elseif (is_string($expected) && str_contains($expected, ',')) {
            $expArr = array_map('trim', explode(',', $expected));
        } else {
            $expArr = [trim((string)$expected)];
        }
        
        // normalize actual to string(s)
        if (is_array($actual)) {
            $actArr = array_map(fn($v) => trim((string)$v), $actual);
        } else {
            $actArr = [trim((string)($actual ?? ''))];
        }

        // if expected looks like boolean values, compare as boolean
        $looksBool = $this->looksLikeBool($expArr[0] ?? '');
        
        if ($looksBool) {
            foreach ($actArr as $a) {
                foreach ($expArr as $e) {
                    if ($this->toBool($a) === $this->toBool($e)) return true;
                }
            }
            return false;
        }

        foreach ($actArr as $a) {
            foreach ($expArr as $e) {
                if ($a === $e) return true;
            }
        }

        return false;
    }
}

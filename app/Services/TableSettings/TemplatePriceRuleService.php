<?php

namespace App\Services\TableSettings;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\TemplatePriceRule;

class TemplatePriceRuleService
{
    /**
     * Применяет enabled-правила шаблона к продукту (НЕ сохраняет в БД).
     * Возвращает новые price/delivery + список применённых правил.
     */
    public function apply(Product $product, array $rulesForm): array
    {
        // Берём базовые значения
        $price = $this->toFloatOrNull($product->price);
        $delivery = $this->toFloatOrNull($product->delivery);
        
        // Опции продукта: template_option_id => value
        $optValues = [];
        if ($product->relationLoaded('productOption')) {
            foreach ($product->productOption as $po) {
                $optValues[(int)$po->template_option_id] = $po->value;
            }
        } else {
            // на всякий случай (но лучше eager-load)
            foreach ($product->productOption()->get(['template_option_id','value']) as $po) {
                $optValues[(int)$po->template_option_id] = $po->value;
            }
        }

        // Правила шаблона
        $rules = [];
        if ($product->relationLoaded('template') && $product->template && $product->template->relationLoaded('priceRules')) {
            $rules = $product->template->priceRules;
        } else {
            // fallback
            $rules = TemplatePriceRule::query()
                ->where('template_id', (int)$product->template_id)
                ->orderBy('sort')
                ->orderBy('id')
                ->get();
        }

        $applied = [];

        foreach ($rules as $rule) {
            if (!$rule->enabled) {
                continue;
            }

            $target = (string)$rule->target_field; // price|delivery
            if (!in_array($target, ['price','delivery'], true)) {
                continue;
            }

            $driverId = $rule->driver_option_id ? (int)$rule->driver_option_id : null;
            if (!$driverId) {
                continue;
            }

            $driverValRaw = $optValues[$driverId] ?? null;

            if (!isset($rulesForm[$rule->key])) {
                continue;
            }
            
            // 1) проверяем условие
            if (!$this->conditionPass($rule, $rulesForm[$rule->key])) {
                continue;
            }

            // 2) ищем значение по mapping
            $mapped = $this->lookupMappedValue($driverValRaw, (array)($rule->mapping ?? []));
            if ($mapped === null) {
                continue;
            }

            // 3) применяем
            $before = ($target === 'price') ? $price : $delivery;
            $after  = $this->applyMode((string)$rule->mode, $before, $mapped);

            if ($target === 'price') {
                $price = $after;
            } else {
                $delivery = $after;
            }

            $applied[] = [
                'rule_id' => (int)$rule->id,
                'rule_key' => (string)$rule->key,
                'rule_name' => (string)$rule->name,
                'target' => $target,
                'mode' => (string)$rule->mode,
                'driver_option_id' => $driverId,
                'driver_value' => $driverValRaw,
                'mapped_value' => $mapped,
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

    private function conditionPass(TemplatePriceRule $rule, mixed $driverValRaw): bool
    {
        $op = (string)($rule->condition_operator ?? 'exists');
        
        $hasOption = $driverValRaw !== null;           // есть запись
        $filled    = $hasOption && trim((string)$driverValRaw) !== '';

        if ($op === 'exists') {
            return $hasOption;
        }
        if ($op === 'filled') {
            return $filled;
        }

        // equals / not_equals: сравниваем как строки (после trim)
        $left = $filled ? trim((string)$driverValRaw) : null;
        $right = trim((string)($rule->condition_value ?? ''));

        // equals / not_equals: сравниваем как bool true/false
        if (is_bool($driverValRaw)) {
            $left = (bool)$driverValRaw;
            $right = (bool)$rule->condition_value;
        }

        // если драйвер пустой/нет — не применяем (безопаснее, чем случайно применять)
        if ($left === null) {
            return false;
        }

        if ($op === 'equals') {
            return $left === $right;
        }
        if ($op === 'not_equals') {
            return $left !== $right;
        }

        // неизвестный оператор -> не применяем
        return false;
    }

    /**
     * Возвращает float значение из mapping, если найдено совпадение.
     * Поддержка:
     * - числовой драйвер: ищем диапазон from..to (пустое from/to = открытый край)
     * - строковый драйвер: match по from (to можно не заполнять)
     */
    private function lookupMappedValue(mixed $driverValRaw, array $mapping): ?float
    {
        $driverStr = trim((string)($driverValRaw ?? ''));
        if ($driverStr === '') {
            return null;
        }

        $driverNum = $this->toFloatOrNull($driverStr);

        foreach ($mapping as $row) {
            if (!is_array($row)) continue;

            $fromRaw = $row['from'] ?? null;
            $toRaw   = $row['to'] ?? null;
            $valRaw  = $row['value'] ?? null;

            $val = $this->toFloatOrNull($valRaw);
            if ($val === null) {
                continue;
            }

            // Числовой сценарий
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

            // Строковый сценарий (если драйвер НЕ число)
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
            default    => $base, // неизвестный режим: ничего не меняем
        };
    }

    private function toFloatOrNull(mixed $v): ?float
    {
        if ($v === null) return null;
        if (is_int($v) || is_float($v)) return (float)$v;

        $s = trim((string)$v);
        if ($s === '') return null;

        // "1 234,56" -> "1234.56"
        $s = str_replace(["\xC2\xA0", ' '], '', $s);
        $s = str_replace(',', '.', $s);

        return is_numeric($s) ? (float)$s : null;
    }
}

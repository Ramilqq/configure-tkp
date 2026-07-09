<?php

namespace App\Services\ProductSearch;

/**
 * HTML-сообщение для пользователя из шагов диагностики поиска
 * (см. SearchStrategyInterface::diagnoseSearch).
 */
class SearchDiagnosticsFormatter
{
    public function format(array $steps = []): string
    {
        if (!$steps) return 'Нет данных для диагностики.';

        $html = 'Продукт не найден.<br><br>';
        $html .= 'Диагностика поиска:<br>';

        foreach ($steps as $step) {
            $html .= sprintf(
                '%s (%s %s) — было: %d, осталось: %d%s<br>',
                e($step['label']),
                e($step['operator']),
                e((string)$step['value']),
                (int)$step['before'],
                (int)$step['after'],
                $step['failed'] ? ' ❌' : ''
            );

            if ($step['failed'] && !empty($step['available_values'])) {
                $html .= 'Доступные значения у оставшихся товаров: '
                    . e(implode(', ', $step['available_values']))
                    . '<br>';
            }
        }

        $failedStep = collect($steps)->firstWhere('failed', true);

        if ($failedStep) {
            $html .= '<br><b>Причина:</b> поиск обнулился на опции "'
                . e($failedStep['label'])
                . '".';
        }

        return $html;
    }
}

<?php

namespace App\Services\ProductSearch;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TableSettings\TemplateOption;
use App\Services\ProductSearch\SearchStrategyInterface;

class UppProductSearchStrategy implements SearchStrategyInterface
{
    public $templateOptions = [];
    public $templateId = 0;

    public string $eventMessage = 'editModalUpp.getMessage';
    public string $eventUpdateFilter = 'editModalUpp.updateFilterUPP';
    public string $eventSyncModalData = 'editModalUpp.syncModalData';
    public string $view = 'blocks.form-edit-modal-upp';

    public function __construct($templateId = 0)
    {
        $this->templateId = $templateId;
        $this->templateOptions = TemplateOption::where('template_id', $templateId)->get()->keyBy('key');
    }

    public function getEventMessage(): string {
        return $this->eventMessage;
    }

    public function getEventUpdateFilter(): string {
        return $this->eventUpdateFilter;
    }

    public function getEventSyncModalData(): string {
        return $this->eventSyncModalData;
    }

    public function getView(): string {
        return $this->view;
    }

    public function buildQuery(Builder $query, array $filterData): Builder
    {
        $query->with('productOptionPrice');                 // жадная загрузка для оптимизации количества запросов при фильтрации по ценовым правилам
        $query->where('template_id', $this->templateId);    // фильтруем только по шаблону ЧРП
        
        $checks = $this->getSearchChecks($filterData);    // получаем массив проверок для ЧРП на основе входящих данных

        foreach ($checks as $check) {
            if ($check['value'] == '') continue;
            $query = $this->applySearchCheck($query, $check);
        }

        return $query;
    }

    public function getDefaultFilterFields(array $savedFields): array
    {
        return [
            'v_input' => $savedFields['v_input'] ?? '6000',
            'nominalnyi_tok_ed_a' => $savedFields['nominalnyi_tok_ed_a'] ?? '50',
            'p_output' => $savedFields['p_output'] ?? '420',
            'count_power_thyristors' => $savedFields['count_power_thyristors'] ?? '18',
            'bypass' => $savedFields['bypass'] ?? 'Контактор',

            // Дополнительные опции
            'manufacturer' => $savedFields['manufacturer'] ?? 'ООО "Завод РУ-Драйв"',
            'v_control' => $savedFields['v_control'] ?? '220В AC',
            'ip' => $savedFields['ip'] ?? '40',
            'bypass_breaker' => $savedFields['bypass_breaker'] ?? 'Нет',
            'service_smv' => $savedFields['service_smv'] ?? 'Двухсторонний',
            'interface' => $savedFields['interface'] ?? 'RS-485, Modbus RTU',
            'motor_type' => $savedFields['motor_type'] ?? 'A',
            'motor_reverse' => $savedFields['bypass_breaker'] ?? 'Нет',
            'cascade' => $savedFields['cascade'] ?? 'Нет',
            'line_switch' => $savedFields['line_switch'] ?? 'Нет',
            'smv_series' => $savedFields['smv_series'] ?? 'Стандарт',
            
        ];
    }

    public function getSearchChecks(array $getData = []): array
    {
        return [
            [
                'label' => 'Входное напряжение',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['v_input']->id,
                'operator' => '>=',
                'value' => (int)($getData['v_input'] ?? 10000),
            ],
            [
                'label' => 'Номинальный ток',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['i_output']->id,
                'operator' => '>=',
                //'value' => (int)($getData['i_output'] ?? 0),
                'value' => (int)($getData['nominalnyi_tok_ed_a'] ?? 0),
            ],
            [
                'label' => 'Кол-во силовых тиристоров УПП',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['count_power_thyristors']->id,
                'operator' => '=',
                'value' => (string)($getData['count_power_thyristors'] ?? ''),
            ],
            [
                'label' => 'Тип байпаса',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['bypass']->id,
                'operator' => '=',
                'value' => (string)($getData['bypass'] ?? 'A'),
            ],
            [
                'label' => 'Напряжение оперативного питания',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['v_control']->id,
                'operator' => '=',
                'value' => (string)($getData['v_control'] ?? 0),
            ],
            [
                'label' => 'IP',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['ip']->id,
                'operator' => '=',
                'value' => (string)($getData['ip'] ?? ''),
            ],
            [
                'label' => 'Байпасный выключатель',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['bypass_breaker']->id,
                'operator' => '=',
                'value' => (string)($getData['bypass_breaker'] ?? ''),
            ],
            [
                'label' => 'Способ обслуживания',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['service_smv']->id,
                'operator' => '=',
                'value' => (string)($getData['service_smv'] ?? ''),
            ],
            [
                'label' => 'Интерфейс',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['interface']->id,
                'operator' => '=',
                'value' => (string)($getData['interface'] ?? ''),
            ],
            [
                'label' => 'Тип ЭД',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['motor_type']->id,
                'operator' => '=',
                'value' => (string)($getData['motor_type'] ?? ''),
            ],
            [
                'label' => 'Реверс двигателя (Секция реверса)',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['motor_reverse']->id,
                'operator' => '=',
                'value' => (string)($getData['motor_reverse'] ?? ''),
            ],
            [
                'label' => 'Каскадный пуск',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['cascade']->id,
                'operator' => '=',
                'value' => (string)($getData['cascade'] ?? ''),
            ],
            [
                'label' => 'Линейный выключатель',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['line_switch']->id,
                'operator' => '=',
                'value' => (string)($getData['line_switch'] ?? ''),
            ],
            [
                'label' => 'Серия УПП',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['smv_series']->id,
                'operator' => '=',
                'value' => (string)($getData['smv_series'] ?? ''),
            ]
        ];
    }

    public function applySearchCheck(Builder $query, array $check): Builder
    {
        return $query->whereHas($check['relation'], function ($q) use ($check) {
            $q->where('template_option_id', $check['template_option_id'])
            ->where('value', $check['operator'], $check['value']);
        });
    }

    public function diagnoseSearch(array $filterData = []): array
    {
        $checks = $this->getSearchChecks($filterData);
        
        $productIds = Product::query()
            ->where('template_id', $this->templateId)
            ->pluck('id')
            ->map(fn ($id) => (int)$id)
            ->all();

        $steps = [];

        foreach ($checks as $check) {
            if ($check['value'] == '') continue;
            $beforeCount = count($productIds);

            if ($beforeCount === 0) {
                $steps[] = [
                    ...$check,
                    'before' => 0,
                    'after' => 0,
                    'failed' => true,
                    'available_values' => [],
                ];
                break;
            }

            $afterIds = Product::query()
                ->whereIn('id', $productIds)
                ->whereHas($check['relation'], function ($q) use ($check) {
                    $q->where('template_option_id', $check['template_option_id'])
                    ->where('value', $check['operator'], $check['value']);
                })
                ->pluck('id')
                ->map(fn ($id) => (int)$id)
                ->all();

            $availableValues = [];

            if (count($afterIds) === 0) {
                $availableValues = $this->getAvailableOptionValues(
                    $productIds,
                    $check['relation'],
                    (int)$check['template_option_id']
                );
            }

            $steps[] = [
                ...$check,
                'before' => $beforeCount,
                'after' => count($afterIds),
                'failed' => count($afterIds) === 0,
                'available_values' => $availableValues,
            ];

            $productIds = $afterIds;
        }

        return $steps;
    }

    public function getAvailableOptionValues(array $productIds, string $relation, int $templateOptionId): array
    {
        if (empty($productIds)) {
            return [];
        }

        $query = $relation === 'productOptionPrice'
            ? ProductOptionPrice::query()
            : ProductOption::query();

        return $query
            ->whereIn('product_id', $productIds)
            ->where('template_option_id', $templateOptionId)
            ->whereNotNull('value')
            ->pluck('value')
            ->map(fn ($value) => trim((string)$value))
            ->filter(fn ($value) => $value !== '')
            ->unique()
            ->take(10)
            ->values()
            ->all();
    }
}
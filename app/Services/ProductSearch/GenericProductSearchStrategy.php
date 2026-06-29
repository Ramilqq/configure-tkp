<?php

namespace App\Services\ProductSearch;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TableSettings\TemplateOption;
use App\Services\ProductSearch\SearchStrategyInterface;
use App\Services\ProductSearch\SearchStrategyAbstract;

class GenericProductSearchStrategy extends SearchStrategyAbstract implements SearchStrategyInterface
{
    public $templateOptions = [];
    public $templateId = 0;

    public string $eventMessage = 'editModalOther.getMessage';
    public string $eventUpdateFilter = 'editModalOther.updateFilter';
    public string $eventSyncModalData = 'editModalOther.syncModalData';
    public string $view = 'blocks.form-edit-modal-other';

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
        $query->where('template_id', $this->templateId);    // фильтруем только по шаблону

        foreach ($filterData as $value) {
            $query->whereHas('productOption', fn($q) => $q->where('value', $value));
        }
        return $query;
    }

    public function getDefaultFilterFields(array $savedFields, string $type = ''): array
    {
        $manufacturer   = 'ООО "Завод РУ-Драйв"';
        $suplier        = 'ООО "Завод РУ-Драйв"';
        
         
        if ($type === 'Asinxronnyj_dvigatel' || 
            $type === 'Sinxronnyj_dvigatel_s_postoyannymi_magnitami' || 
            $type === 'Sinxronnyj_dvigatel'
        ) $manufacturer = 'Внешний';
        
        if ($type === 'Asinxronnyj_dvigatel' || 
            $type === 'Sinxronnyj_dvigatel_s_postoyannymi_magnitami' || 
            $type === 'Sinxronnyj_dvigatel'
        ) $suplier = 'Внешний';
         
        if ($type === 'Yachejka_pitaniya' || 
            $type === 'Izmeritelnyj_transformator_napryazheniya'
        ) $manufacturer = 'Заказчик';
        
        if ($type === 'Yachejka_pitaniya' || 
            $type === 'Izmeritelnyj_transformator_napryazheniya'
        ) $suplier = 'Заказчик';
        
        $savedFields['manufacturer'] = $savedFields['manufacturer'] ?? $manufacturer;
        $savedFields['suplier'] = $savedFields['suplier'] ?? $suplier;
        return $savedFields; // дефолты берём из saved_schema как есть
    }

    // формируем массив проверок для ЧРП на основе входящих данных, который будет использоваться для построения запроса в buildQuery
    public function getSearchChecks(array $getData = []): array
    {
        $cheks = [];

        foreach ($this->templateOptions as $key => $templateOptions){
            if (!$getData[$key]) continue;
            $cheks[] = [
                'label' => $templateOptions->name,
                'relation' => 'productOption',
                'template_option_id' => $templateOptions->id,
                'operator' => '>=',
                'value' => (int)($getData[$key]),
            ];
        }
        
        return $cheks;
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
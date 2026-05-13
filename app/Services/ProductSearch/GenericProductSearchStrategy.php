<?php

namespace App\Services\ProductSearch;

use App\Models\TableSettings\TemplateOption;
use Illuminate\Database\Eloquent\Builder;

class GenericProductSearchStrategy
{
    public $templateOptions = [];
    public $templateId = 0;

    public function __construct($templateId = 0)
    {
        $this->templateId = $templateId;
        $this->templateOptions = TemplateOption::where('template_id', $templateId)->get()->keyBy('key');
    }

    public function buildQuery(Builder $query, array $filterData): Builder
    {
        $query->where('template_id', $this->templateId);    // фильтруем только по шаблону

        foreach ($filterData as $value) {
            $query->whereHas('productOption', fn($q) => $q->where('value', $value));
        }
        return $query;
    }

    public function getDefaultFilterFields(array $savedFields): array
    {
        return $savedFields; // дефолты берём из saved_schema как есть
    }

    // формируем массив проверок для ЧРП на основе входящих данных, который будет использоваться для построения запроса в buildQuery
    public function getFrSearchChecks(array $getData = []): array
    {
        $cheks = [];

        foreach ($this->templateOptions as $key => $templateOptions){
            $cheks[] = [
                'label' => $templateOptions->name,
                'relation' => 'productOption',
                'template_option_id' => $templateOptions->id,
                'operator' => '>=',
                'value' => (int)($getData[$key] ?? 10000),
            ];
        }
        
        return $cheks;
    }

    // диагностируем причину отсутствия результатов при поиске ЧРП,
    // поэтапно применяя проверки и фиксируя количество оставшихся товаров,
    // а также формируем массив доступных значений для первой провальной проверки,
    // чтобы показать пользователю альтернативные варианты для корректировки фильтра
    public function diagnoseFrSearch(array $filterData = []): array
    {
        return [];
    }
}
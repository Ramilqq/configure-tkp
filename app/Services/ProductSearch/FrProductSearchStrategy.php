<?php
namespace App\Services\ProductSearch;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TableSettings\TemplateOption;
use App\Services\ProductSearch\SearchStrategyInterface;
use App\Services\ProductSearch\SearchStrategyAbstract;

class FrProductSearchStrategy extends SearchStrategyAbstract implements SearchStrategyInterface
{
    public $templateOptions = [];
    public $templateId = 0;

    public string $eventMessage = 'editModalFr.getMessage';
    public string $eventUpdateFilter = 'editModalFr.updateFilterFR';
    public string $eventSyncModalData = 'editModalFr.syncModalData';
    public string $view = 'blocks.form-edit-modal-fr';

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
            // Характеристики электродвигателя
            'motor_type' => $savedFields['motor_type'] ?? 'A',
            'v_output' => $savedFields['v_output'] ?? '6000',
            'p_output' => $savedFields['p_output'] ?? '0',
            'nominalnyi_tok_ed_a' => $savedFields['nominalnyi_tok_ed_a'] ?? '0',
            'kpd' => $savedFields['kpd'] ?? '0',
            'cos_phi' => $savedFields['cos_phi'] ?? '0',
            'manufacturer' => $savedFields['manufacturer'] ?? 'ООО "Завод РУ-Драйв"',
            'count_power_cell' => $savedFields['count_power_cell'] ?? '5',

            // Дополнительные опции
            'interface' => $savedFields['interface'] ?? 'RS-485, Modbus RTU',
            'plc_syn' => $savedFields['plc_syn'] ?? 'Нет',
            'vfd_series' => $savedFields['vfd_series'] ?? 'Стандарт',
            'material_trans' => $savedFields['material_trans'] ?? 'Медь',
            'power_cell_bypass' => $savedFields['power_cell_bypass'] ?? 'Нет',
            'sync_to_grid' => $savedFields['sync_to_grid'] ?? 'Нет',
            'ip' => $savedFields['ip'] ?? 31,
            'precharge' => $savedFields['precharge'] ?? 'Нет',
            'service_vfd' => $savedFields['service_vfd'] ?? '',
            'bypass_vfd' => $savedFields['bypass_vfd'] ?? 'Нет',
            //'section_in_out' => $savedFields['section_in_out'] ?? 'Нет',
            'plc_pt_100' => $savedFields['plc_pt_100'] ?? 'Нет',
        ];
    }

    public function getSearchChecks(array $getData = []): array
    {
        return [
            [
                'label' => 'Номинальное напряжение',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['v_output']->id,
                'operator' => '>=',
                'value' => (int)($getData['v_output'] ?? 10000),
            ],
            [
                'label' => 'Мощность',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['p_output']->id,
                'operator' => '>=',
                'value' => (int)($getData['p_output'] ?? 0),
            ],
            [
                'label' => 'Номинальный ток',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['i_output']->id,
                'operator' => '>=',
                'value' => (int)($getData['nominalnyi_tok_ed_a'] ?? 0),
            ],
            [
                'label' => 'Количество силовых ячеек на фазу',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['count_power_cell']->id,
                'operator' => '>=',
                'value' => (int)($getData['count_power_cell'] ?? 5),
            ],
            /*[
                'label' => 'Наличие функции предзаряда',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['precharge_function']->id,
                'operator' => '=',
                'value' => (string)($getData['precharge_function'] ?? null),
            ],
            [
                'label' => 'Исполнение функции предзаряда',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['precharge_function_exec']->id,
                'operator' => '=',
                'value' => (string)($getData['precharge_function_exec'] ?? null),
            ],*/
            [
                'label' => 'Наличие сервиса ЧРП',
                'relation' => 'productOption',
                'template_option_id' => $this->templateOptions['service_vfd']->id,
                'operator' => '=',
                'value' => (string)($getData['service_vfd'] ?? ''),
            ],
            [
                'label' => 'Тип двигателя',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['motor_type']->id,
                'operator' => '=',
                'value' => (string)($getData['motor_type'] ?? 'A'),
            ],
            [
                'label' => 'Интерфейс',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['interface']->id,
                'operator' => '=',
                'value' => (string)($getData['interface'] ?? ''),
            ],
            [
                'label' => 'Наличие ПЛК синхронизации',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['plc_syn']->id,
                'operator' => '=',
                'value' => (string)($getData['plc_syn'] ?? ''),
            ],
            [
                'label' => 'Серия ЧРП',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['vfd_series']->id,
                'operator' => '=',
                'value' => (string)($getData['vfd_series'] ?? ''),
            ],
            [
                'label' => 'Материал трансформатора',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['material_trans']->id,
                'operator' => '=',
                'value' => (string)($getData['material_trans'] ?? ''),
            ],
            [
                'label' => 'Наличие байпаса силовых ячеек',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['power_cell_bypass']->id,
                'operator' => '=',
                'value' => (string)($getData['power_cell_bypass'] ?? ''),
            ],
            [
                'label' => 'Наличие функции синхронизации с сетью',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['sync_to_grid']->id,
                'operator' => '=',
                'value' => (string)($getData['sync_to_grid'] ?? ''),
            ],
            [
                'label' => 'Степень защиты',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['ip']->id,
                'operator' => '=',
                'value' => (int)($getData['ip'] ?? 31),
            ],
            [
                'label' => 'Наличие предзаряда',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['precharge']->id,
                'operator' => '=',
                'value' => (string)($getData['precharge'] ?? ''),
            ],
            [
                'label' => 'Исполнение байпаса ЧРП',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['bypass_vfd']->id,
                'operator' => '=',
                'value' => (string)($getData['bypass_vfd'] ?? ''),
            ],
            /*[
                'label' => 'Секция ввода/вывода сверху',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['section_in_out']->id,
                'operator' => '=',
                'value' => (string)($getData['section_in_out'] ?? ''),
            ],*/
            [
                'label' => 'ПЛК и датчики контроля температуры обмоток и подшипников ЭД',
                'relation' => 'productOptionPrice',
                'template_option_id' => $this->templateOptions['plc_pt_100']->id,
                'operator' => '=',
                'value' => (string)($getData['plc_pt_100'] ?? ''),
            ],
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
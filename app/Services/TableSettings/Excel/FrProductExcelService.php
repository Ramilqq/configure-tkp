<?php

namespace App\Services\TableSettings\Excel;

use App\Models\TableSettings\GroupOption;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\ProductOptionPrice;
use App\Models\TableSettings\Template;
use App\Models\TableSettings\TemplateOption;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FrProductExcelService
{
    private const HEADER_ROW = 3;
    private const LABEL_ROW  = 4;
    private const DATA_ROW   = 5;

    /**
     * Бизнес-группы ЧРП.
     * Каждая группа = одна TemplateOption.
     * base = базовое значение опции
     * variants = связанные подполя/подварианты этой же опции
     */
    private const FR_GROUPS = [
        'material_trans' => [
            'template_name' => 'Материал обмоток ТН ВПЧ',
            'template_key'  => 'material_trans',
            'base' => [
                'value' => '[Material_trans]',
            ],
            'variants' => [
                [
                    'value' => '[Material_trans_AlumTrans]',
                    'price' => '[Price_VFD_AlumTrans]',
                ],
            ],
        ],

        'power_cell_bypass' => [
            'template_name' => 'Байпас неисправной силовой ячейки',
            'template_key'  => 'power_cell_bypass',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Механический',
                    'price'     => '[Price_VFD_mechBypass]',
                    'drawing'   => '[Drawing_VFD_mechBypass]',
                    'airflow'   => '[Airflow_rate_VFD_mechBypass]',
                    'dimension' => '[Dimension_VFD_mechBypass]',
                    'weight'    => '[VFD_mechBypass_Weight]',
                    'service'   => '[Service_VFD_mechBypass]',
                ],
            ],
        ],

        'sync_to_grid' => [
            'template_name' => 'Синхронизация на сеть',
            'template_key'  => 'sync_to_grid',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_SynchrOption]',
                    'drawing'   => '[Drawing_Reactor]',
                    'dimension' => '[Dimension_Reactor]',
                    'airflow'   => '[Airflow_rate_Reactor]',
                    'service'   => '[Service_Reactor]',
                    'weight'    => '[Weight_reactor]',
                ],
            ],
        ],

        'ip' => [
            'template_name' => 'Степень защиты IP',
            'template_key'  => 'ip',
            'base' => [
                'value' => '[IP]',
                'rename_title' => '[IP]',
            ],
            'variants' => [
                [
                    'value' => '[IP41]',
                    'price' => '[Price_IP41]',
                    'rename_title' => '[IP41]',
                ],
                [
                    'value' => '[IP42]',
                    'price' => '[Price_IP42]',
                    'rename_title' => '[IP42]',

                ],
            ],
        ],

        'precharge' => [
            'template_name' => 'Предзаряд силовых ячеек',
            'template_key'  => 'precharge',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_PreCharge]',
                    'drawing'   => '[Drawing_PreCharge]',
                    'dimension' => '[Dimension_PreCharge]',
                    'service'   => '[Service_PreCharge]',
                    'weight'    => '[Weight_PreCharge]',
                ],
            ],
        ],

        'vfd_series' => [
            'template_name' => 'Серия ВПЧ',
            'template_key'  => 'vfd_series',
            'base' => [
                'value'      => '[VFD_Series]',
                'rename_title'     => '[VFD_Series_Start]',
                'rename_title_end' => '[VFD_Series_End]',
            ],
            'variants' => [
                [
                    'value'  => '[VFD_Series (Minprom)]',
                    'rename_title'     => '[VFD_Series_Start (Minprom)]',
                    'rename_title_end' => '[VFD_Series_End]',
                ],
            ],
        ],


        'motor_type' => [
            'template_name' => 'Тип ЭД',
            'template_key'  => 'motor_type',
            'base' => [
                'value'  => '[Motor_type]',
                'rename_title' => '[Motor_type]',
                'rename_description' => '[Motor_type_full]',
            ],
            'variants' => [
                [
                    'value'  => '[Motor_type_Syn]',
                    'rename_title' => '[Motor_type_Syn]',
                    'rename_description' => '[Motor_type_full_Syn]',

                ],
            ],
        ],

        'plc_syn' => [
            'template_name' => 'Наличие ПЛК управления системой возбуждения',
            'template_key'  => 'plc_syn',
            'variants' => [
                [
                    'value' => 'Нет',
                ],
                [
                    'value' => 'Да',
                    'price' => '[Price_PLC_Syn]',
                ],
            ],
        ],

        'bypass_vfd' => [
            'template_name' => 'Байпас ВПЧ',
            'template_key'  => 'bypass_vfd',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Опция 8',
                    'price'     => '[Price_bypassVFD]',
                    'drawing'   => '[Drawing_bypassVFD]',
                    'dimension' => '[Dimension_bypassVFD]',
                    'weight'    => '[Weight_bypassVFD]',
                ],
            ],
        ],

        'interface' => [
            'template_name' => 'Интерфейс',
            'template_key'  => 'interface',
            'base' => [
                'value'  => '[Interface]',
                'rename_title' => '[Interface_S]',
                'rename_description' => '[Interface]',
            ],
            'variants' => [
                [
                    'value'  => '[Profibus]',
                    'price'  => '[Price_Profibus]',
                    'rename_title' => '[Profibus_S]',
                    'rename_description' => '[Profibus]',
                ],
                [
                    'value'  => '[ModbusTCP]',
                    'price'  => '[Price_ModbusTCP]',
                    'rename_title' => '[ModbusTCP_S]',
                    'rename_description' => '[ModbusTCP]',
                ],
                [
                    'value'  => '[Profinet]',
                    'price'  => '[Price_Profinet]',
                    'rename_title' => '[Profinet_S]',
                    'rename_description' => '[Profinet]',
                ],
            ],
        ],
        
        'section_in_out' => [
            'template_name' => 'Секция ввода/вывода сверху',
            'template_key'  => 'section_in_out',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_InOut]',
                    'drawing'   => '[Drawing_InOut]',
                    'dimension' => '[Dimension_InOut]',
                    'service'   => '[Service_InOut]',
                    'weight'    => '[Weight_InOut]',
                ],
            ],
        ],

        'plc_pt_100' => [
            'template_name' => 'ПЛК и датчики контроля температуры обмоток и подшипников ЭД',
            'template_key'  => 'plc_pt_100',
            'variants' => [
                [
                    'value'     => 'Нет',
                ],
                [
                    'value'     => 'Да',
                    'price'     => '[Price_PLC_Pt100]',
                ],
            ],
        ],
    ];

    /**
     * Технические поля, которые можно сохранить как обычные scalar-опции.
     * Всё, что входит в FR_GROUPS, сюда НЕ попадает.
     */
    private const FR_SCALAR_OPTION_MAP = [
        '[S_trans]'                => ['name' => 'Номинальная полная мощность ТН, кВА', 'key' => 's_trans'],
        '[P_Output]'               => ['name' => 'Мощность подключаемого электродвигателя, кВт', 'key' => 'p_output'],
        '[V_input]'                => ['name' => 'Входное напряжение, В', 'key' => 'v_input'],
        '[V_output]'               => ['name' => 'Выходное напряжение, В', 'key' => 'v_output'],
        '[Freq_output]'            => ['name' => 'Выходная частота, Гц', 'key' => 'freq_output'],
        '[I_output]'               => ['name' => 'Выходной ток, А', 'key' => 'i_output'],
        '[Count_power_cell]'       => ['name' => 'Кол-во силовых ячеек ВПЧ', 'key' => 'count_power_cell'],
        '[Airflow_rate]'           => ['name' => 'Производительность вентиляторов охлаждения, м3/час', 'key' => 'airflow_rate'],
        '[Dimension_VFD_standart]' => ['name' => 'Габаритные размеры стандартного ЧРП, мм', 'key' => 'dimension_vfd_standard'],
        '[VFD_Weight]'             => ['name' => 'Масса, кг', 'key' => 'vfd_weight'],
        '[PrechargeFunction]'      => ['name' => 'Наличие функции предзаряда', 'key' => 'precharge_function'],
        '[PrechargeFunctionExec]'  => ['name' => 'Исполнение функции предзаряда', 'key' => 'precharge_function_exec'],
        '[Drawing]'                => ['name' => 'Стандартный чертеж ЧРП', 'key' => 'drawing_default'],
        '[Service_VFD]'            => ['name' => 'Способ обслуживания', 'key' => 'service_vfd'],
    ];

    public function listSheets(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        if ($reader instanceof BaseReader) {
            return $reader->listWorksheetNames($path);
        }

        $spreadsheet = $reader->load($path);
        return $spreadsheet->getSheetNames();
    }

    /**
     * Для ЧРП preview можно сделать максимально простым.
     */
    public function preview(string $path, string $sheetName, int $templateId, int $limit = 10): array
    {
        [$sheet, $highestRow, $highestCol] = $this->openSheet($path, $sheetName);

        $labelRow = $sheet->rangeToArray(
            'A' . self::LABEL_ROW . ":{$highestCol}" . self::LABEL_ROW,
            null,
            true,
            false
        )[0];

        $techIndex = $this->buildFrTechIndex($labelRow);
        $mergeMap  = $this->buildMergeMap($sheet);

        $rows = [];
        $shown = 0;

        for ($r = self::DATA_ROW; $r <= $highestRow; $r++) {
            $rowByTech = $this->readFrRowByTechKey($sheet, $r, $techIndex, $mergeMap);
            if ($this->isFrDataRowEmpty($rowByTech)) {
                continue;
            }

            $rowByTech = $this->enrichFrDerivedValues($rowByTech);
            $nameTemplate = $this->getMergedAwareCellValue($sheet, $r, 5, $mergeMap);
            $descTemplate = $this->getMergedAwareCellValue($sheet, $r, 6, $mergeMap);

            $rows[] = [
                '_status'     => 'UPSERT_BY_FR_HASH',
                'fr_block_id' => $this->getMergedAwareCellValue($sheet, $r, 4, $mergeMap ?? []),
                'name'        => $this->renderTemplateString($nameTemplate, $rowByTech),
                'description' => $this->renderTemplateString($descTemplate, $rowByTech),
                'price'       => $this->toDecimal($rowByTech['[Price_VFD]'] ?? null),
                'S_trans'     => $rowByTech['[S_trans]'] ?? null,
                'P_Output'    => $rowByTech['[P_Output]'] ?? null,
                'V_input'     => $rowByTech['[V_input]'] ?? null,
                'V_output'    => $rowByTech['[V_output]'] ?? null,
                'Count_cell'  => $rowByTech['[Count_power_cell]'] ?? null,
            ];

            $shown++;
            if ($shown >= $limit) {
                break;
            }
        }
        
        return [
            'ok' => true,
            'plan' => [
                'mode' => 'fr_template',
                'header_row' => self::HEADER_ROW,
                'label_row' => self::LABEL_ROW,
                'data_row' => self::DATA_ROW,
                'upsert_key' => 'hash',
                'grouped_options' => array_keys(self::FR_GROUPS),
            ],
            'columns' => array_keys($rows[0] ?? ['_status' => null]),
            'rows' => $rows,
        ];
    }

    /**
     * Импорт ЧРП.
     *
     * Важно:
     * - ID из файла не используем
     * - update/create делаем по fr_hash
     * - имя/описание рендерим из merge-шаблонов E/F
     */
    public function import(string $path, string $sheetName, int $templateId): array
    {
        Template::query()->findOrFail($templateId);

        [$sheet, $highestRow, $highestCol] = $this->openSheet($path, $sheetName);

        $labelRow = $sheet->rangeToArray(
            'A' . self::LABEL_ROW . ":{$highestCol}" . self::LABEL_ROW,
            null,
            true,
            false
        )[0];

        $techIndex = $this->buildFrTechIndex($labelRow);
        

        $this->ensureFrTemplateOptionsExist($templateId);

        $scanned = 0;
        $createdProducts = 0;
        $updatedProducts = 0;
        $updatedOptionCells = 0;
        $createdIdsSample = [];

        DB::transaction(function () use (
            $sheet,
            $highestRow,
            $techIndex,
            $templateId,
            &$scanned,
            &$createdProducts,
            &$updatedProducts,
            &$updatedOptionCells,
            &$createdIdsSample
        ) {
            $mergeMap  = $this->buildMergeMap($sheet);

            for ($r = self::DATA_ROW; $r <= $highestRow; $r++) {
                $rowByTech = $this->readFrRowByTechKey($sheet, $r, $techIndex, $mergeMap);
                if ($this->isFrDataRowEmpty($rowByTech)) {
                    continue;
                }

                $scanned++;

                $rowByTech = $this->enrichFrDerivedValues($rowByTech);

                $blockId = $this->getMergedAwareCellValue($sheet, $r, 4, $mergeMap);
                $blockTitle = $this->getMergedAwareCellValue($sheet, $r, 2, $mergeMap);
                $nameTemplate = $this->getMergedAwareCellValue($sheet, $r, 5, $mergeMap);
                $descTemplate = $this->getMergedAwareCellValue($sheet, $r, 6, $mergeMap);

                //$name = $this->renderTemplateString((string)$nameTemplate, $rowByTech);
                $name = (string)$nameTemplate;
                //$description = $this->renderTemplateString((string)$descTemplate, $rowByTech);
                $description = (string)$descTemplate;
                
                $hash = $this->makeFrHash($blockId, $blockTitle, $rowByTech);

                $product = $this->findExistingFrProduct($templateId, $hash);

                $payload = [
                    'template_id'      => $templateId,
                    'name'             => $name,
                    'description'      => $description,
                    'currency'         => 'CNY',
                    'price'            => $this->toDecimal($rowByTech['[Price_VFD]'] ?? null),
                    'drawing'          => $rowByTech['[Drawing]'] ?? null,
                    'hash'          => $hash,
                ];

                if (!$product) {
                    $product = new Product();
                    $product->fill($payload);
                    $product->save();

                    $createdProducts++;
                    if (count($createdIdsSample) < 50) {
                        $createdIdsSample[] = $product->id;
                    }
                } else {
                    $product->fill($payload);
                    if ($product->isDirty()) {
                        $product->save();
                        $updatedProducts++;
                    }
                }

                $updatedOptionCells += $this->syncFrGroupedOptions($product, $templateId, $rowByTech);
                $updatedOptionCells += $this->syncFrScalarOptions($product, $templateId, $rowByTech);
            }
        });

        return [
            'ok' => true,
            'sheet' => $sheetName,
            'template_id' => $templateId,
            'scanned_rows' => $scanned,
            'created_products' => $createdProducts,
            'updated_products' => $updatedProducts,
            'updated_option_cells' => $updatedOptionCells,
            'created_product_ids_sample' => $createdIdsSample,
            'mode' => 'FR',
            'upsert_key' => '$hashId',
        ];
    }

    /**
     * Экспорт под FR-формат лучше делать отдельным методом.
     * Ниже пока оставлена безопасная заготовка.
     */
    public function exportData(int $templateId): StreamedResponse
    {
        $template = Template::query()->findOrFail($templateId);

        $templateOptions = TemplateOption::query()
            ->where('template_id', $templateId)
            ->get(['id', 'template_id', 'key', 'name'])
            ->keyBy('id');

        $optionKeyById = $templateOptions
            ->mapWithKeys(fn ($opt) => [(int)$opt->id => (string)$opt->key])
            ->all();

        $products = Product::query()
            ->where('template_id', $templateId)
            ->select([
                'id',
                'hash',
                'template_id',
                'name',
                'description',
                'currency',
                'price',
                'delivery',
                'drawing',
                'engineering',
            ])
            ->orderBy('id')
            ->get();

        $productIds = $products->pluck('id')->map(fn ($v) => (int)$v)->all();
        $optionIds  = array_keys($optionKeyById);

        $poMap = [];
        if (!empty($productIds) && !empty($optionIds)) {
            $poRows = ProductOption::query()
                ->whereIn('product_id', $productIds)
                ->whereIn('template_option_id', $optionIds)
                ->get([
                    'id',
                    'product_id',
                    'template_option_id',
                    'value'
                ]);

            foreach ($poRows as $row) {
                $key = $optionKeyById[(int)$row->template_option_id] ?? null;
                if (!$key) {
                    continue;
                }

                $poMap[(int)$row->product_id][$key] = $row;
            }
        }

        $priceMap = [];
        if (!empty($productIds) && !empty($optionIds)) {
            $priceRows = ProductOptionPrice::query()
                ->whereIn('product_id', $productIds)
                ->whereIn('template_option_id', $optionIds)
                ->orderBy('id')
                ->get([
                    'id',
                    'product_id',
                    'template_option_id',
                    'value',
                    'price',
                    'drawing',
                    'dimension',
                    'weight',
                    'airflow',
                    'service',
                    'rename_title',
                    'rename_title_end',
                    'rename_description',
                ]);

            foreach ($priceRows as $row) {
                $key = $optionKeyById[(int)$row->template_option_id] ?? null;
                if (!$key) {
                    continue;
                }

                $priceMap[(int)$row->product_id][$key][] = $row;
            }
        }

        [$spreadsheet, $sheet] = $this->openFrExportWorkbook($template->name ?: 'FR');

        
        $fallbackRowNum = max($sheet->getHighestDataRow(), self::DATA_ROW - 1) + 1;
        $fallbackSeq    = 1;

        foreach ($products as $product) {
            $productOptions = $poMap[(int)$product->id] ?? [];
            $productPrices  = $priceMap[(int)$product->id] ?? [];

            $rowByTech = $this->makeFrRowByTechFromProduct($product, $productOptions, $productPrices);
            $rowByTech = $this->enrichFrDerivedValues($rowByTech);

            $rowNum = $templateRowByHash[(string)$product->fr_hash] ?? null;
            $isTemplateRow = (bool)$rowNum;

            if (!$rowNum) {
                $rowNum = $fallbackRowNum;
                $fallbackRowNum++;
            }

            // Если есть строка из исходного шаблона — B/D/E/F не трогаем.
            // Если шаблон не найден и мы в skeleton/fallback — заполняем базовые колонки,
            // чтобы экспорт не был пустым.
            if (!$isTemplateRow) {
                $sheet->setCellValue([2, $rowNum], 'FR');
                $sheet->setCellValue([4, $rowNum], $fallbackSeq);
                $sheet->setCellValue([5, $rowNum], $product->name);
                $sheet->setCellValue([6, $rowNum], $product->description);
                $fallbackSeq++;
            }

            foreach ($this->frExportTechColumns() as $techKey => $colIndex) {
                $sheet->setCellValue(
                    [(int)$colIndex, (int)$rowNum],
                    $rowByTech[$techKey] ?? null
                );
            }
        }

        $fileName = 'fr_export_template_' . $templateId . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $fileName);
    }

    // ---------------------------------------------------------------------
    // FR IMPORT HELPERS
    // ---------------------------------------------------------------------

    private function ensureFrTemplateOptionsExist(int $templateId): void
    {
        $defaultGroupId = $this->defaultGroupId();

        foreach (self::FR_GROUPS as $group) {
            TemplateOption::query()->firstOrCreate(
                [
                    'template_id' => $templateId,
                    'key' => $group['template_key'],
                ],
                [
                    'group_id' => $defaultGroupId,
                    'name' => $group['template_name'],
                    'fields' => [],
                ]
            );
        }

        foreach (self::FR_SCALAR_OPTION_MAP as $techKey => $cfg) {
            TemplateOption::query()->firstOrCreate(
                [
                    'template_id' => $templateId,
                    'key' => $cfg['key'],
                ],
                [
                    'group_id' => $defaultGroupId,
                    'name' => $cfg['name'],
                    'fields' => [],
                ]
            );
        }
    }

    private function syncFrGroupedOptions(Product $product, int $templateId, array $rowByTech): int
    {
        $updated = 0;

        foreach (self::FR_GROUPS as $groupCode => $group) {
            $templateOption = TemplateOption::query()->where([
                'template_id' => $templateId,
                'key' => $group['template_key'],
            ])->first();

            if (!$templateOption) {
                continue;
            }

            $base = $this->extractMappedData($group['base'] ?? [], $rowByTech);

            $productOption = ProductOption::query()->firstOrNew([
                'product_id' => $product->id,
                'template_option_id' => $templateOption->id,
            ]);

            $productOption->fill([
                'value'     => $base['value'] ?? '',
                'price'     => $this->toDecimal($base['price'] ?? null),
                'drawing'   => $base['drawing'] ?? null,
                'airflow'   => $this->toInt($base['airflow'] ?? null),
                'dimension' => $base['dimension'] ?? null,
                'weight'    => $this->toInt($base['weight'] ?? null),
                'service'   => $base['service'] ?? null,
            ]);
            $productOption->save();
            $updated++;

            /*ProductOptionPrice::query()
                ->where('product_id', $product->id)
                ->where('template_option_id', $templateOption->id)
                ->delete();*/

            $fields = $templateOption->fields;
            
            // default/base variant — важен для rename/rename_end
            if (!$this->isFrVariantEmpty($base)) {
                $base['value'] = $this->safeSheetTitle($base['value']);
                ProductOptionPrice::query()->updateOrCreate([
                    'product_id'         => $product->id,
                    'template_option_id' => $templateOption->id,
                    'value'              => $base['value'] ?? null,
                ],[
                    'price'              => $this->toDecimal($base['price'] ?? null),
                    'drawing'            => $base['drawing'] ?? null,
                    'airflow'            => $this->toInt($base['airflow'] ?? null),
                    'dimension'          => $base['dimension'] ?? null,
                    'weight'             => $this->toInt($base['weight'] ?? null),
                    'service'            => $base['service'] ?? null,
                    'rename_title'       => $base['rename_title'] ?? null,
                    'rename_title_end'   => $base['rename_title_end'] ?? null,
                    'rename_description' => $base['rename_description'] ?? null,
                ]);
                $updated++;
            }

            if (!empty($base['value'])) {
                if(!in_array($base['value'], $fields, true)) {
                     $fields[] = (string)$base['value'];
                }
            }

            foreach (($group['variants'] ?? []) as $variantMap) {
                $variant = $this->extractMappedData($variantMap, $rowByTech);

                if ($this->isFrVariantEmpty($variant)) {
                    continue;
                }

                $variant['value'] = $this->safeSheetTitle($variant['value']);
                
                ProductOptionPrice::query()->updateOrCreate([
                    'product_id'         => $product->id,
                    'template_option_id' => $templateOption->id,
                    'value'              => $variant['value'] ?? null,
                ],[
                    'price'              => $this->toDecimal($variant['price'] ?? null),
                    'drawing'            => $variant['drawing'] ?? null,
                    'airflow'            => $this->toInt($variant['airflow'] ?? null),
                    'dimension'          => $variant['dimension'] ?? null,
                    'weight'             => $this->toInt($variant['weight'] ?? null),
                    'service'            => $variant['service'] ?? null,
                    'rename_title'       => $variant['rename_title'] ?? null,
                    'rename_title_end'   => $variant['rename_title_end'] ?? null,
                    'rename_description' => $variant['rename_description'] ?? null,
                ]);
                $updated++;

                if (!empty($variant['value'])) {
                    if(!in_array($variant['value'], $fields, true)) {
                        $fields[] = (string)$variant['value'];
                    }
                }

                /*if ($variantMap['value'] == '[VFD_Series (Minprom)]') {
                    dd($variantMap, $variant['value'],  $fields);
                }*/
            }

            $templateOption->fields = array_values(array_unique(array_filter($fields)));
            
            $templateOption->save();
        }

        return $updated;
    }

    private function syncFrScalarOptions(Product $product, int $templateId, array $rowByTech): int
    {
        $updated = 0;

        foreach (self::FR_SCALAR_OPTION_MAP as $techKey => $cfg) {
            $value = $rowByTech[$techKey] ?? null;
            if ($this->isEmpty($value)) {
                continue;
            }
            $value = $this->safeSheetTitle($value);
            $templateOption = TemplateOption::query()->where([
                'template_id' => $templateId,
                'key' => $cfg['key'],
            ])->first();

            if (!$templateOption) {
                continue;
            }
            
            
            $po = ProductOption::query()->firstOrNew([
                'product_id' => $product->id,
                'template_option_id' => $templateOption->id,
            ]);

            /*if($cfg['key'] === 'service_vfd' && $product->id === 97 && $value != 'Одностороннее') {
                dd( $product->id, $value);
            }*/

            $newValue = (string)$value;
            if ($po->exists && (string)$po->value === $newValue) {
                continue;
            }

            $po->value = $newValue;
            $po->save();
            $updated++;

            $fields = $templateOption->fields ?? [];
            if (!in_array($newValue, $fields, true)) {
                $fields[] = $newValue;
                $templateOption->fields = array_values(array_unique($fields));
                $templateOption->save();
            }
        }

        return $updated;
    }

    private function findExistingFrProduct(int $templateId, string $frHash): ?Product
    {
        return Product::query()
            ->where('template_id', $templateId)
            ->where('hash', $frHash)
            ->first();
    }

    private function makeFrHash(mixed $blockId, mixed $blockTitle, array $rowByTech): string
    {
        $signature = [
            //'block_id'           => (string)($blockId ?? ''),
            's_trans'            => (string)($rowByTech['[S_trans]'] ?? ''),
            'p_output'           => (string)($rowByTech['[P_Output]'] ?? ''),
            'v_input'            => (string)($rowByTech['[V_input]'] ?? ''),
            'v_output'           => (string)($rowByTech['[V_output]'] ?? ''),
            'freq_output'        => (string)($rowByTech['[Freq_output]'] ?? ''),
            'i_output'           => (string)($rowByTech['[I_output]'] ?? ''),
            'count_power_cell'   => (string)($rowByTech['[Count_power_cell]'] ?? ''),
            'vfd_series'         => (string)($rowByTech['[VFD_Series]'] ?? ''),
            'motor_type'         => (string)($rowByTech['[Motor_type]'] ?? ''),
            'ip'                 => (string)($rowByTech['[IP]'] ?? ''),
            'interface'          => (string)($rowByTech['[Interface]'] ?? ''),
            'service_vfd'        => (string)($rowByTech['[Service_VFD]'] ?? ''),
        ];

        return md5(json_encode($signature, JSON_UNESCAPED_UNICODE));
    }

    private function buildFrTechIndex(array $labelRow): array
    {
        $map = [];

        foreach ($labelRow as $idx => $cell) {
            $key = trim((string)$cell);
            if ($key === '') {
                continue;
            }

            $map[$key] = $idx + 1; // 1-based column index for PhpSpreadsheet
        }

        return $map;
    }

    private function readFrRowByTechKey(
        Worksheet $sheet,
        int $row,
        array $techIndex,
        array $mergeMap = []
    ): array {
        $result = [];

        foreach ($techIndex as $techKey => $colIndex) {
            $result[$techKey] = $this->getMergedAwareCellValue($sheet, $row, (int)$colIndex, $mergeMap);
        }

        return $result;
    }


    private function getMergedAwareCellValue(
        Worksheet $sheet,
        int $row,
        int $col,
        array $mergeMap = []
    ): mixed {
        $value = $sheet->getCell([$col, $row])->getValue();

        if ($value !== null && $value !== '') {
            return $value;
        }

        if (isset($mergeMap[$row][$col])) {
            $startCol = $mergeMap[$row][$col]['start_col'];
            $startRow = $mergeMap[$row][$col]['start_row'];

            return $sheet->getCell([$startCol, $startRow])->getValue();
        }

        return null;
    }


    private function enrichFrDerivedValues(array $rowByTech): array
    {
        $vInput = (string)($rowByTech['[V_input]'] ?? '');
        $vOutput = (string)($rowByTech['[V_output]'] ?? '');

        $rowByTech['[V_input_name]'] = match ($vInput) {
            '6000'  => '60',
            '10000' => '10',
            default => $vInput,
        };

        $rowByTech['[V_output_name]'] = match ($vOutput) {
            '6000'  => '60',
            '10000' => '10',
            default => $vOutput,
        };

        $countPowerCell = (int)($rowByTech['[Count_power_cell]'] ?? 0);
        $rowByTech['[PWM_level]'] = $countPowerCell > 0
            ? (string)(2 * $countPowerCell + 1)
            : '';

        return $rowByTech;
    }

    private function renderTemplateString(?string $template, array $rowByTech): ?string
    {
        if ($template === null || trim($template) === '') {
            return null;
        }

        $result = $template;

        foreach ($rowByTech as $techKey => $value) {
            $result = str_replace($techKey, (string)($value ?? ''), $result);
        }

        // Локальная очистка служебных if-конструкций из шаблона.
        $result = preg_replace('/If\s+\[[^\]]+\].*?then\s+print\s+/iu', '', $result);
        $result = preg_replace('/\s+/u', ' ', trim((string)$result));

        return $result;
    }

    private function extractMappedData(array $map, array $rowByTech): array
    {
        $result = [];

        foreach ($map as $field => $source) {
            if (is_string($source) && str_starts_with(trim($source), '[')) {
                $result[$field] = $rowByTech[trim($source)] ?? null;
            } else {
                $result[$field] = $source;
            }
        }

        return $result;
    }

    private function isFrVariantEmpty(array $variant): bool
    {
        foreach ($variant as $value) {
            if ($value === null) {
                continue;
            }

            if (is_string($value) && trim($value) === '') {
                continue;
            }

            return false;
        }

        return true;
    }

    private function isFrDataRowEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if (is_string($v) && trim($v) !== '') {
                return false;
            }

            if (is_numeric($v)) {
                return false;
            }
        }

        return true;
    }

    private function openSheet(string $path, string $sheetName): array
    {
        $reader = IOFactory::createReaderForFile($path);
        
        // для merged cells нужен полный load, без ReadDataOnly.
        $reader->setReadDataOnly(false);

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if (!$sheet) {
            throw new \RuntimeException("Лист '{$sheetName}' не найден.");
        }

        return [$sheet, $sheet->getHighestDataRow(), $sheet->getHighestDataColumn()];
    }

    private function defaultGroupId(): int
    {
        $g = GroupOption::query()->orderBy('id')->first();
        if ($g) {
            return (int)$g->id;
        }

        return (int)GroupOption::query()->create(['name' => 'Общее'])->id;
    }

    private function isEmpty(mixed $v): bool
    {
        if ($v === null) return true;
        if (is_string($v) && trim($v) === '') return true;
        return false;
    }

    private function toDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float)$value;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }

    private function safeSheetTitle(string $title): string
    {
        $title = preg_replace('/[\[\]\*\/\\\\\?\:]/', ' ', $title);
        $title = trim(preg_replace('/\s+/', ' ', $title));
        return mb_substr($title, 0, 31);
    }

    private function openFrExportWorkbook(string $sheetTitle = 'FR'): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->safeSheetTitle($sheetTitle));

        $this->buildFrExportSkeleton($sheet);

        return [$spreadsheet, $sheet];
    }

    private function buildFrExportSkeleton($sheet): void
    {
        $headers = $this->frHeaderRowValues();
        $labels  = $this->frLabelRowValues();

        foreach ($headers as $col => $value) {
            $sheet->setCellValue([$col, self::HEADER_ROW], $value);
        }

        foreach ($labels as $col => $value) {
            $sheet->setCellValue([$col, self::LABEL_ROW], $value);
        }

        $sheet->freezePane('A' . self::DATA_ROW);
    }

    private function frHeaderRowValues(): array
    {
        return [
            5  => 'Наименование',
            6  => 'Описание',
            7  => 'Номинальная полная мощность ТН, кВА',
            8  => 'Мощность подключаемого электродвигателя, кВт',
            9  => 'Входное напряжение, В',
            10 => 'Выходное напряжение, В',
            11 => 'Выходная частота, Гц',
            12 => 'Выходной ток, А',
            13 => 'Стандартный чертеж ЧРП',
            14 => 'Базовая цена ВПЧ',
            15 => 'Материал обмоток ТН ВПЧ',
            16 => 'Кол-во силовых ячеек ВПЧ',
            17 => 'Серия ВПЧ',
            20 => 'Тип ЭД',
            22 => 'Способ обслуживания',
            23 => 'IP',
            24 => 'Интерфейс',
            26 => 'Производительность вентиляторов охлаждения, м3/час',
            27 => 'Габаритные размеры стандартного ЧРП, мм',
            28 => 'Масса, кг',
            29 => 'Наличие функции предзаряда',
            30 => 'Исполнение функции предзаряда',
            32 => 'Опция 1: Материал обмоток ТН ВПЧ - Алюминий',
            34 => 'Опция 2.1: Байпас неисправной силовой ячейки (Механический)',
            40 => 'Опция 3: Синхронизация на сеть',
            46 => 'Опция 4.1: IP41',
            48 => 'Опция 4.2: IP42',
            50 => 'Опция 5: Предзаряд силовых ячеек',
            55 => 'Опция 6: Серия ВПЧ - Минпромторг',
            57 => 'Опция 7: Тип ЭД - синхронный',
            59 => 'Опция 7.1: Наличие ПЛК управления системой возбуждения',
            60 => 'Опция 8: Байпас ВПЧ (автоматический)',
            64 => 'Опция 10.1: Интерфейс = RS-485, Profibus',
            67 => 'Опция 10.2: Интерфейс = Ethernet, Modbus TCP',
            70 => 'Опция 10.3: Интерфейс = Ethernet, Profinet',
            73 => 'Опция 11: Секция ввода/вывода сверху',
            78 => 'Опция 12: ПЛК и датчики контроля температуры обмоток и подшипников ЭД (8-10 датчиков PT100)',
        ];
    }

    private function frLabelRowValues(): array
    {
        return [
            5  => 'Наименование формируется исходя из выбранного ВПЧ',
            6  => 'Описание формируется исходя из выбранного ВПЧ',
            7  => '[S_trans]',
            8  => '[P_Output]',
            9  => '[V_input]',
            10 => '[V_output]',
            11 => '[Freq_output]',
            12 => '[I_output]',
            13 => '[Drawing]',
            14 => '[Price_VFD]',
            15 => '[Material_trans]',
            16 => '[Count_power_cell]',
            17 => '[VFD_Series]',
            18 => '[VFD_Series_Start]',
            19 => '[VFD_Series_End]',
            20 => '[Motor_type]',
            21 => '[Motor_type_full]',
            22 => '[Service_VFD]',
            23 => '[IP]',
            24 => '[Interface_S]',
            25 => '[Interface]',
            26 => '[Airflow_rate]',
            27 => '[Dimension_VFD_standart]',
            28 => '[VFD_Weight]',
            29 => '[PrechargeFunction]',
            30 => '[PrechargeFunctionExec]',
            32 => '[Price_VFD_AlumTrans]',
            33 => '[Material_trans_AlumTrans]',
            34 => '[Price_VFD_mechBypass]',
            35 => '[Drawing_VFD_mechBypass]',
            36 => '[Airflow_rate_VFD_mechBypass]',
            37 => '[Dimension_VFD_mechBypass]',
            38 => '[VFD_mechBypass_Weight]',
            39 => '[Service_VFD_mechBypass]',
            40 => '[Price_SynchrOption]',
            41 => '[Drawing_Reactor]',
            42 => '[Dimension_Reactor]',
            43 => '[Airflow_rate_Reactor]',
            44 => '[Service_Reactor]',
            45 => '[Weight_reactor]',
            46 => '[IP41]',
            47 => '[Price_IP41]',
            48 => '[IP42]',
            49 => '[Price_IP42]',
            50 => '[Price_PreCharge]',
            51 => '[Drawing_PreCharge]',
            52 => '[Dimension_PreCharge]',
            53 => '[Service_PreCharge]',
            54 => '[Weight_PreCharge]',
            55 => '[VFD_Series (Minprom)]',
            56 => '[VFD_Series_Start (Minprom)]',
            57 => '[Motor_type_Syn]',
            58 => '[Motor_type_full_Syn]',
            59 => '[Price_PLC_Syn]',
            60 => '[Price_bypassVFD]',
            61 => '[Drawing_bypassVFD]',
            62 => '[Dimension_bypassVFD]',
            63 => '[Weight_bypassVFD]',
            64 => '[Profibus_S]',
            65 => '[Profibus]',
            66 => '[Price_Profibus]',
            67 => '[ModbusTCP_S]',
            68 => '[ModbusTCP]',
            69 => '[Price_ModbusTCP]',
            70 => '[Profinet_S]',
            71 => '[Profinet]',
            72 => '[Price_Profinet]',
            73 => '[Price_InOut]',
            74 => '[Drawing_InOut]',
            75 => '[Dimension_InOut]',
            76 => '[Service_InOut]',
            77 => '[Weight_InOut]',
            78 => '[Price_PLC_Pt100]',
        ];
    }

    private function frExportTechColumns(): array
    {
        return array_flip($this->frLabelRowValues());
    }


    private function makeFrRowByTechFromProduct(Product $product, array $productOptions, array $productPrices): array
    {
        $row = [];

        // базовые поля products
        $row['[Price_VFD]'] = $product->price;
        $row['[Drawing]']   = $product->drawing;

        // scalar-опции
        foreach (self::FR_SCALAR_OPTION_MAP as $techKey => $cfg) {
            $po = $productOptions[$cfg['key']] ?? null;
            if (!$po) {
                continue;
            }

            $row[$techKey] = $po->value;
        }

        // grouped-опции
        foreach (self::FR_GROUPS as $groupCode => $group) {
            $templateKey = $group['template_key'];
            $basePo      = $productOptions[$templateKey] ?? null;
            $priceRows   = array_values($productPrices[$templateKey] ?? []);

            $basePriceRow = $this->resolveFrBasePriceRow($group, $basePo, $priceRows);

            if (!empty($group['base']) && ($basePo || $basePriceRow)) {
                $this->applyFrExportMap($row, $group['base'], $basePo, $basePriceRow);
            }

            $variantRows = $this->resolveFrVariantRows($groupCode, $group, $basePo, $priceRows, $basePriceRow);

            foreach (($group['variants'] ?? []) as $idx => $variantMap) {

                $variantPriceRow = $variantRows[$idx] ?? null;
                if (!$variantPriceRow) {
                    continue;
                }

                $this->applyFrExportMap($row, $variantMap, null, $variantPriceRow);
            }
        }

        // drawing приоритет: если пусто в product.drawing, взять scalar drawing_default
        if ($this->isEmpty($row['[Drawing]'] ?? null)) {
            $drawingPo = $productOptions[self::FR_SCALAR_OPTION_MAP['[Drawing]']['key']] ?? null;
            if ($drawingPo && !$this->isEmpty($drawingPo->value)) {
                $row['[Drawing]'] = $drawingPo->value;
            }
        }
        
        return $row;
    }



    private function applyFrExportMap(array &$row, array $map, ?ProductOption $basePo, mixed $priceRow): void
    {
        foreach ($map as $field => $techKey) {

            if (!is_string($techKey) || !str_starts_with(trim($techKey), '[')) {
                continue;
            }
            
            $value = match ($field) {
                'value'      => $basePo?->value ?? $priceRow?->value,
                'price'      => $basePo?->price ?? $priceRow?->price,
                'drawing'    => $basePo?->drawing ?? $priceRow?->drawing,
                'airflow'    => $basePo?->airflow ?? $priceRow?->airflow,
                'dimension'  => $basePo?->dimension ?? $priceRow?->dimension,
                'weight'     => $basePo?->weight ?? $priceRow?->weight,
                'service'    => $basePo?->service ?? $priceRow?->service,
                'rename_title'          => $priceRow?->rename_title,
                'rename_title_end'      => $priceRow?->rename_title_end,
                'rename_description'    => $priceRow?->rename_description,
                default      => null,
            };

            if ($value === null || $value === '') {
                continue;
            }

            $row[trim($techKey)] = $value;
        }
    }


    private function resolveFrBasePriceRow(array $group, ?ProductOption $basePo, array $priceRows): mixed
    {
        if (empty($group['base']) || empty($priceRows) || !$basePo) {
            return null;
        }

        if ($this->isEmpty($basePo->value ?? null)) {
            return null;
        }

        foreach ($priceRows as $row) {
            if ((string)($row->value ?? '') === (string)$basePo->value) {
                return $row;
            }
        }

        return null;
    }

    private function resolveFrVariantRows(
        string $groupCode,
        array $group,
        ?ProductOption $basePo,
        array $priceRows,
        mixed $basePriceRow
    ): array {
        $rows = array_values($priceRows);

        if ($basePriceRow) {
            $rows = array_values(array_filter(
                $rows,
                fn ($r) => (int)$r->id !== (int)$basePriceRow->id
            ));
        }

        $resolved = [];

        foreach (($group['variants'] ?? []) as $idx => $variantMap) {
            $resolved[$idx] = $rows[$idx] ?? null;
        }

        return $resolved;
    }

    private function buildMergeMap(Worksheet $sheet): array
    {
        $map = [];

        foreach ($sheet->getMergeCells() as $range) {
            [$start, $end] = Coordinate::rangeBoundaries($range);

            $startCol = (int) $start[0];
            $startRow = (int) $start[1];
            $endCol   = (int) $end[0];
            $endRow   = (int) $end[1];

            for ($row = $startRow; $row <= $endRow; $row++) {
                for ($col = $startCol; $col <= $endCol; $col++) {
                    $map[$row][$col] = [
                        'start_col' => $startCol,
                        'start_row' => $startRow,
                    ];
                }
            }
        }

        return $map;
    }
}
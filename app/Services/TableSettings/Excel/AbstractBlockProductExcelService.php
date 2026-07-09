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

/**
 * Общая механика импорта/экспорта «блочных» шаблонов (ЧРП, УПП):
 * чтение листа с учётом merged-ячеек, апсерт продуктов по хэшу технических
 * параметров, синхронизация групповых и scalar-опций, экспорт в скелет файла.
 *
 * Наследники задают только конфигурацию: группы опций, маппинг scalar-полей,
 * ключ цены, сигнатуру хэша и раскладку колонок экспорта.
 */
abstract class AbstractBlockProductExcelService
{
    protected const HEADER_ROW = 3;
    protected const LABEL_ROW  = 4;
    protected const DATA_ROW   = 5;

    /**
     * Бизнес-группы шаблона. Каждая группа = одна TemplateOption.
     * base = базовое значение опции, variants = подварианты этой же опции.
     */
    abstract protected function groups(): array;

    /**
     * Технические поля, которые сохраняются как обычные scalar-опции.
     * Всё, что входит в groups(), сюда НЕ попадает.
     */
    abstract protected function scalarOptionMap(): array;

    /** Тех. ключ базовой цены продукта, например '[Price_VFD]'. */
    abstract protected function priceTechKey(): string;

    /** Метка режима в результате импорта ('FR' / 'UPP'). */
    abstract protected function importMode(): string;

    /**
     * Сигнатура технических параметров для хэша-апсерта.
     * НЕ менять состав/порядок ключей — иначе повторный импорт
     * не найдёт существующие продукты и создаст дубли.
     */
    abstract protected function hashSignature(array $rowByTech): array;

    /** Дополнительные колонки предпросмотра (после базовых). */
    abstract protected function previewExtraColumns(array $rowByTech): array;

    /** Русские заголовки колонок экспорта (строка HEADER_ROW). */
    abstract protected function headerRowValues(): array;

    /** Тех. ключи колонок экспорта (строка LABEL_ROW). */
    abstract protected function labelRowValues(): array;

    /**
     * Производные значения, вычисляемые из строки файла
     * (используются при рендере шаблонов имени/описания).
     */
    protected function enrichDerivedValues(array $rowByTech): array
    {
        $vInput = (string)($rowByTech['[V_input]'] ?? '');

        $rowByTech['[V_input_name]'] = match ($vInput) {
            '6000'  => '60',
            '10000' => '10',
            default => $vInput,
        };

        return $rowByTech;
    }

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

    public function preview(string $path, string $sheetName, int $templateId, int $limit = 10): array
    {
        [$sheet, $highestRow, $highestCol] = $this->openSheet($path, $sheetName);

        $labelRow = $sheet->rangeToArray(
            'A' . self::LABEL_ROW . ":{$highestCol}" . self::LABEL_ROW,
            null,
            true,
            false
        )[0];

        $techIndex = $this->buildTechIndex($labelRow);
        $mergeMap  = $this->buildMergeMap($sheet);

        $rows = [];
        $shown = 0;

        for ($r = self::DATA_ROW; $r <= $highestRow; $r++) {
            $rowByTech = $this->readRowByTechKey($sheet, $r, $techIndex, $mergeMap);
            if ($this->isDataRowEmpty($rowByTech)) {
                continue;
            }

            $rowByTech = $this->enrichDerivedValues($rowByTech);
            $nameTemplate = $this->getMergedAwareCellValue($sheet, $r, 5, $mergeMap);
            $descTemplate = $this->getMergedAwareCellValue($sheet, $r, 6, $mergeMap);

            $rows[] = array_merge([
                '_status'     => 'UPSERT_BY_FR_HASH',
                'fr_block_id' => $this->getMergedAwareCellValue($sheet, $r, 4, $mergeMap),
                'name'        => $this->renderTemplateString($nameTemplate, $rowByTech),
                'description' => $this->renderTemplateString($descTemplate, $rowByTech),
                'price'       => $this->toDecimal($rowByTech[$this->priceTechKey()] ?? null),
            ], $this->previewExtraColumns($rowByTech));

            $shown++;
            if ($shown >= $limit) {
                break;
            }
        }

        $scan = $this->scanFile($path, $sheetName, $templateId);

        return [
            'ok' => true,
            'plan' => [
                'mode' => 'fr_template',
                'header_row' => self::HEADER_ROW,
                'label_row' => self::LABEL_ROW,
                'data_row' => self::DATA_ROW,
                'upsert_key' => 'hash',
                'grouped_options' => array_keys($this->groups()),
                'full_scan' => [
                    'scanned_rows' => $scan['scanned_rows'],
                    'to_create' => $scan['to_create'],
                    'to_update' => $scan['to_update'],
                    'to_skip_no_name' => $scan['skipped_rows_no_name'],
                ],
                'blocking_duplicates' => $scan['duplicate_hash_groups'],
                'garbage' => $this->buildGarbage($templateId, $scan['hashes_in_file']),
            ],
            'columns' => array_keys($rows[0] ?? ['_status' => null]),
            'rows' => $rows,
        ];
    }

    /**
     * Полный проход по всему файлу (без записи в БД): агрегаты по ВСЕМ строкам
     * (а не только по limit) + поиск дублей hash внутри одного файла.
     */
    private function scanFile(string $path, string $sheetName, int $templateId): array
    {
        [$sheet, $highestRow, $highestCol] = $this->openSheet($path, $sheetName);

        $labelRow = $sheet->rangeToArray(
            'A' . self::LABEL_ROW . ":{$highestCol}" . self::LABEL_ROW,
            null,
            true,
            false
        )[0];

        $techIndex = $this->buildTechIndex($labelRow);
        $mergeMap = $this->buildMergeMap($sheet);

        $scanned = 0;
        $skippedNoName = 0;
        $hashRows = []; // hash => [номера строк Excel]

        for ($r = self::DATA_ROW; $r <= $highestRow; $r++) {
            $rowByTech = $this->readRowByTechKey($sheet, $r, $techIndex, $mergeMap);
            if ($this->isDataRowEmpty($rowByTech)) {
                continue;
            }

            $scanned++;

            $nameTemplate = $this->getMergedAwareCellValue($sheet, $r, 5, $mergeMap);
            if (trim((string)$nameTemplate) === '') {
                // не настоящий товар (легенда/шаблон/мусор) — import() тоже его пропустит
                $skippedNoName++;
                continue;
            }

            $rowByTech = $this->enrichDerivedValues($rowByTech);

            $hash = $this->makeHash($rowByTech);
            $hashRows[$hash][] = $r;
        }

        $duplicateGroups = [];
        foreach ($hashRows as $hash => $rows) {
            if (count($rows) > 1) {
                $duplicateGroups[$hash] = $rows;
            }
        }

        $uniqueHashes = array_keys($hashRows);
        $existingHashes = [];
        if (!empty($uniqueHashes)) {
            $existingHashes = array_flip(
                Product::query()
                    ->where('template_id', $templateId)
                    ->whereIn('hash', $uniqueHashes)
                    ->pluck('hash')
                    ->all()
            );
        }

        $toCreate = 0;
        $toUpdate = 0;
        foreach ($uniqueHashes as $hash) {
            if (isset($existingHashes[$hash])) {
                $toUpdate++;
            } else {
                $toCreate++;
            }
        }

        return [
            'scanned_rows' => $scanned,
            'to_create' => $toCreate,
            'to_update' => $toUpdate,
            'skipped_rows_no_name' => $skippedNoName,
            'duplicate_hash_groups' => $duplicateGroups, // hash => [номера строк]
            'hashes_in_file' => $uniqueHashes,
        ];
    }

    /**
     * Диагностика "мусора": товары в БД, не затронутые текущим файлом импорта,
     * и уже существующие в БД дубли hash (могли накопиться до этого фикса).
     * Только для отображения, ничего не удаляет.
     */
    private function buildGarbage(int $templateId, array $hashesInFile): array
    {
        $notInFileQuery = Product::query()
            ->where('template_id', $templateId)
            ->whereNotNull('hash');

        if (!empty($hashesInFile)) {
            $notInFileQuery->whereNotIn('hash', $hashesInFile);
        }

        $productsNotInFileCount = (clone $notInFileQuery)->count();
        $productsNotInFileSample = $productsNotInFileCount > 0
            ? $notInFileQuery->orderBy('id')->limit(20)->get(['id', 'name'])->toArray()
            : [];

        $duplicateHashGroupsInDb = Product::query()
            ->where('template_id', $templateId)
            ->whereNotNull('hash')
            ->select('hash')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('hash')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('cnt', 'hash')
            ->all();

        return [
            'products_not_in_file_count' => $productsNotInFileCount,
            'products_not_in_file_sample' => $productsNotInFileSample,
            'duplicate_hash_groups_in_db' => $duplicateHashGroupsInDb,
        ];
    }

    /**
     * Импорт блочного шаблона.
     *
     * Важно:
     * - ID из файла не используем
     * - update/create делаем по hash технических параметров
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

        $techIndex = $this->buildTechIndex($labelRow);

        $scan = $this->scanFile($path, $sheetName, $templateId);
        if (!empty($scan['duplicate_hash_groups'])) {
            $parts = [];
            foreach ($scan['duplicate_hash_groups'] as $hash => $rows) {
                $parts[] = "строки " . implode(', ', $rows) . " (одинаковые технические параметры)";
            }
            throw new \RuntimeException(
                "В файле найдены строки-дубли (совпадающие технические параметры) — импорт остановлен: " . implode('; ', $parts)
            );
        }

        $this->ensureTemplateOptionsExist($templateId);

        $scanned = 0;
        $createdProducts = 0;
        $updatedProducts = 0;
        $updatedOptionCells = 0;
        $createdIdsSample = [];
        $skippedNoName = 0;

        DB::transaction(function () use (
            $sheet,
            $highestRow,
            $techIndex,
            $templateId,
            &$scanned,
            &$createdProducts,
            &$updatedProducts,
            &$updatedOptionCells,
            &$createdIdsSample,
            &$skippedNoName
        ) {
            $mergeMap  = $this->buildMergeMap($sheet);

            for ($r = self::DATA_ROW; $r <= $highestRow; $r++) {
                $rowByTech = $this->readRowByTechKey($sheet, $r, $techIndex, $mergeMap);
                if ($this->isDataRowEmpty($rowByTech)) {
                    continue;
                }

                $scanned++;

                $nameTemplate = $this->getMergedAwareCellValue($sheet, $r, 5, $mergeMap);
                $descTemplate = $this->getMergedAwareCellValue($sheet, $r, 6, $mergeMap);

                $name = (string)$nameTemplate;
                $description = (string)$descTemplate;

                // строка без наименования — не настоящий товар (легенда/шаблон/мусор
                // в хвосте файла), пропускаем целиком: не создаём товар, не трогаем опции
                if (trim($name) === '') {
                    $skippedNoName++;
                    continue;
                }

                $rowByTech = $this->enrichDerivedValues($rowByTech);

                $hash = $this->makeHash($rowByTech);

                $product = $this->findExistingProduct($templateId, $hash);

                $payload = [
                    'template_id'      => $templateId,
                    'name'             => $name,
                    'description'      => $description,
                    'currency'         => 'CNY',
                    'price'            => $this->toDecimal($rowByTech[$this->priceTechKey()] ?? null),
                    'drawing'          => $rowByTech['[Drawing]'] ?? null,
                    'hash'             => $hash,
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

                $updatedOptionCells += $this->syncGroupedOptions($product, $templateId, $rowByTech);
                $updatedOptionCells += $this->syncScalarOptions($product, $templateId, $rowByTech);
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
            'skipped_rows_no_name' => $skippedNoName,
            'mode' => $this->importMode(),
            'upsert_key' => '$hashId',
        ];
    }

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

        [$spreadsheet, $sheet] = $this->openExportWorkbook($template->name ?: 'FR');

        $fallbackRowNum = max($sheet->getHighestDataRow(), self::DATA_ROW - 1) + 1;
        $fallbackSeq    = 1;

        foreach ($products as $product) {
            $productOptions = $poMap[(int)$product->id] ?? [];
            $productPrices  = $priceMap[(int)$product->id] ?? [];

            $rowByTech = $this->makeRowByTechFromProduct($product, $productOptions, $productPrices);
            $rowByTech = $this->enrichDerivedValues($rowByTech);

            $rowNum = $fallbackRowNum++;

            $sheet->setCellValue([2, $rowNum], 'FR');
            $sheet->setCellValue([4, $rowNum], $fallbackSeq++);
            $sheet->setCellValue([5, $rowNum], $product->name);
            $sheet->setCellValue([6, $rowNum], $product->description);

            foreach ($this->exportTechColumns() as $techKey => $colIndex) {
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
    // IMPORT HELPERS
    // ---------------------------------------------------------------------

    private function ensureTemplateOptionsExist(int $templateId): void
    {
        $defaultGroupId = $this->defaultGroupId();

        foreach ($this->groups() as $group) {
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

        foreach ($this->scalarOptionMap() as $techKey => $cfg) {
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

    private function syncGroupedOptions(Product $product, int $templateId, array $rowByTech): int
    {
        $updated = 0;

        foreach ($this->groups() as $groupCode => $group) {
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

            // Собираем все значения, которые придут в этом импорте
            $incomingValues = [];
            if (!$this->isVariantEmpty($base)) {
                $incomingValues[] = $this->safeSheetTitle($base['value']);
            }
            foreach (($group['variants'] ?? []) as $variantMap) {
                $variant = $this->extractMappedData($variantMap, $rowByTech);
                if (!$this->isVariantEmpty($variant)) {
                    $incomingValues[] = $this->safeSheetTitle($variant['value']);
                }
            }

            // Удаляем варианты, которых больше нет в файле
            if (!empty($incomingValues)) {
                ProductOptionPrice::query()
                    ->where('product_id', $product->id)
                    ->where('template_option_id', $templateOption->id)
                    ->whereNotIn('value', $incomingValues)
                    ->delete();
            }

            $fields = $templateOption->fields;

            // default/base variant — важен для rename/rename_end
            if (!$this->isVariantEmpty($base)) {
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

                if ($this->isVariantEmpty($variant)) {
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
            }

            $templateOption->fields = array_values(array_unique(array_filter($fields)));

            $templateOption->save();
        }

        return $updated;
    }

    private function syncScalarOptions(Product $product, int $templateId, array $rowByTech): int
    {
        $updated = 0;

        foreach ($this->scalarOptionMap() as $techKey => $cfg) {
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

    private function findExistingProduct(int $templateId, string $hash): ?Product
    {
        return Product::query()
            ->where('template_id', $templateId)
            ->where('hash', $hash)
            ->first();
    }

    private function makeHash(array $rowByTech): string
    {
        return md5(json_encode($this->hashSignature($rowByTech), JSON_UNESCAPED_UNICODE));
    }

    private function buildTechIndex(array $labelRow): array
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

    private function readRowByTechKey(
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
        // getCalculatedValue(), а не getValue(): некоторые ячейки (например, цена)
        // заданы формулой ("=89287+7872") — getValue() вернул бы саму формулу
        // строкой, и (float)"=..." превращался бы в 0.
        $value = $sheet->getCell([$col, $row])->getCalculatedValue();

        if ($value !== null && $value !== '') {
            return $value;
        }

        if (isset($mergeMap[$row][$col])) {
            $startCol = $mergeMap[$row][$col]['start_col'];
            $startRow = $mergeMap[$row][$col]['start_row'];

            return $sheet->getCell([$startCol, $startRow])->getCalculatedValue();
        }

        return null;
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

    private function isVariantEmpty(array $variant): bool
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

    private function isDataRowEmpty(array $row): bool
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

    // ---------------------------------------------------------------------
    // EXPORT HELPERS
    // ---------------------------------------------------------------------

    private function openExportWorkbook(string $sheetTitle = 'FR'): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->safeSheetTitle($sheetTitle));

        $this->buildExportSkeleton($sheet);

        return [$spreadsheet, $sheet];
    }

    private function buildExportSkeleton($sheet): void
    {
        $headers = $this->headerRowValues();
        $labels  = $this->labelRowValues();

        foreach ($headers as $col => $value) {
            $sheet->setCellValue([$col, self::HEADER_ROW], $value);
        }

        foreach ($labels as $col => $value) {
            $sheet->setCellValue([$col, self::LABEL_ROW], $value);
        }

        $sheet->freezePane('A' . self::DATA_ROW);
    }

    private function exportTechColumns(): array
    {
        return array_flip($this->labelRowValues());
    }

    private function makeRowByTechFromProduct(Product $product, array $productOptions, array $productPrices): array
    {
        $row = [];

        // базовые поля products
        $row[$this->priceTechKey()] = $product->price;
        $row['[Drawing]']   = $product->drawing;

        // scalar-опции
        foreach ($this->scalarOptionMap() as $techKey => $cfg) {
            $po = $productOptions[$cfg['key']] ?? null;
            if (!$po) {
                continue;
            }

            $row[$techKey] = $po->value;
        }

        // grouped-опции
        foreach ($this->groups() as $groupCode => $group) {
            $templateKey = $group['template_key'];
            $basePo      = $productOptions[$templateKey] ?? null;
            $priceRows   = array_values($productPrices[$templateKey] ?? []);

            $basePriceRow = $this->resolveBasePriceRow($group, $basePo, $priceRows);

            if (!empty($group['base']) && ($basePo || $basePriceRow)) {
                $this->applyExportMap($row, $group['base'], $basePo, $basePriceRow);
            }

            $variantRows = $this->resolveVariantRows($group, $priceRows, $basePriceRow);

            foreach (($group['variants'] ?? []) as $idx => $variantMap) {

                $variantPriceRow = $variantRows[$idx] ?? null;
                if (!$variantPriceRow) {
                    continue;
                }

                $this->applyExportMap($row, $variantMap, null, $variantPriceRow);
            }
        }

        // drawing приоритет: если пусто в product.drawing, взять scalar drawing_default
        if ($this->isEmpty($row['[Drawing]'] ?? null)) {
            $drawingPo = $productOptions[$this->scalarOptionMap()['[Drawing]']['key']] ?? null;
            if ($drawingPo && !$this->isEmpty($drawingPo->value)) {
                $row['[Drawing]'] = $drawingPo->value;
            }
        }

        return $row;
    }

    private function applyExportMap(array &$row, array $map, ?ProductOption $basePo, mixed $priceRow): void
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

    private function resolveBasePriceRow(array $group, ?ProductOption $basePo, array $priceRows): mixed
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

    private function resolveVariantRows(
        array $group,
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

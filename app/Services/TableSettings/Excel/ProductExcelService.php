<?php

namespace App\Services\TableSettings\Excel;

use App\Models\TableSettings\GroupOption;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\Template;
use App\Models\TableSettings\TemplateOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\BaseReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductExcelService
{
    private const HEADER_ROW = 1; // русские заголовки (читаем/пишем)
    private const LABEL_ROW  = 1; // тех. ключи (пишем, импорт не использует)
    private const DATA_ROW   = 2;

    private const BASE_FIELDS = [
        'id',
        'name',
        'description',
        'manufacturer_id',
        'currency',
        'price',
        'delivery',
        'engineering',
    ];

    /**
     * Русские заголовки базовых полей (как будет в Excel).
     * Можно править под ваш шаблон.
     */
    private const BASE_FIELD_LABELS_RU = [
        'id'              => 'ID',
        'name'            => 'Наименование',
        'description'     => 'Описание',
        'manufacturer_id' => 'Производитель',
        'currency'        => 'Валюта',
        'price'           => 'Цена оборудования',
        'delivery'        => 'Цена доставки',
        'engineering'     => 'Инжиниринг',
    ];

    /**
     * Маппинг заголовков из Excel -> поле products.
     * Ключи в НОРМАЛИЗОВАННОМ виде (нижний регистр, пробелы схлопнуты).
     * Добавляйте сюда ваши варианты заголовков.
     */
    private const HEADER_ALIASES_TO_BASE = [
        'id' => 'id',
        'ид' => 'id',
        'номер' => 'id',

        'наименование' => 'name',
        'название' => 'name',
        'имя' => 'name',
        'name' => 'name',

        'описание' => 'description',
        'description' => 'description',

        'производитель' => 'manufacturer_id',
        'manufacturer_id' => 'manufacturer_id',
        'manufacturer' => 'manufacturer_id',

        'валюта' => 'currency',
        'currency' => 'currency',

        'цена оборудования' => 'price',
        'цена' => 'price',
        'стоимость' => 'price',
        'price' => 'price',

        'цена доставки' => 'delivery',
        'доставка' => 'delivery',
        'delivery' => 'delivery',

        'инжиниринг' => 'engineering',
        'engineering' => 'engineering',
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
     * Экспорт ДАННЫХ по template_id.
     * - LABEL_ROW: тех. ключи (products + template_options.key)
     * - HEADER_ROW: русские названия (BASE_FIELD_LABELS_RU + template_options.name)
     * - DATA_ROW: значения
     */
    public function exportData(int $templateId): StreamedResponse
    {
        $template = Template::query()
            ->with(['options' => fn($q) => $q->select(['id','template_id','name','key','group_id'])->orderBy('id')])
            ->findOrFail($templateId);

        $options = $template->options;
        $optionIds = $options->pluck('id')->map(fn($v) => (int)$v)->all();

        $products = Product::query()
            ->where('template_id', $templateId)
            ->select(['id','template_id','name','description','manufacturer_id','currency','price','delivery','engineering'])
            ->orderBy('id')
            ->get();

        // product_id -> option_id -> value
        $poMap = [];
        if ($products->isNotEmpty() && !empty($optionIds)) {
            $po = ProductOption::query()
                ->whereIn('product_id', $products->pluck('id')->all())
                ->whereIn('template_option_id', $optionIds)
                ->get(['product_id','template_option_id','value']);

            foreach ($po as $row) {
                $poMap[(int)$row->product_id][(int)$row->template_option_id] = (string)$row->value;
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->safeSheetTitle($template->name ?: 'products'));

        // LABEL_ROW (technical)
        $labels = self::BASE_FIELDS; // products keys
        foreach ($options as $opt) {
            $labels[] = (string)$opt->key; // technical option key
        }

        // HEADER_ROW (human RU)
        $header = [];
        foreach (self::BASE_FIELDS as $bf) {
            $header[] = self::BASE_FIELD_LABELS_RU[$bf] ?? $bf;
        }
        foreach ($options as $opt) {
            $header[] = (string)$opt->name; // RU header of option
        }

        $sheet->fromArray($labels, null, 'A' . self::LABEL_ROW);
        $sheet->fromArray($header, null, 'A' . self::HEADER_ROW);
        $sheet->freezePane('A' . self::DATA_ROW);

        $rowNum = self::DATA_ROW;
        foreach ($products as $p) {
            $row = [
                $p->id,
                $p->name,
                $p->description,
                $p->manufacturer_id,
                $p->currency,
                $p->price,
                $p->delivery,
                $p->engineering ? json_encode($p->engineering, JSON_UNESCAPED_UNICODE) : null,
            ];

            foreach ($optionIds as $optId) {
                $row[] = $poMap[(int)$p->id][$optId] ?? '';
            }

            $sheet->fromArray($row, null, 'A' . $rowNum);
            $rowNum++;
        }

        $fileName = "products_export_template_{$templateId}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $fileName);
    }

    /**
     * Предпросмотр: первые N строк + план
     * _status:
     * - UPDATE (есть product id в этом template_id)
     * - CREATE (нет product id вообще -> создадим с этим id)
     * - WRONG_TEMPLATE (id есть, но template_id другой -> пропустим)
     * - NO_ID (нет id -> пропустим)
     */
    public function preview(string $path, string $sheetName, int $templateId, int $limit = 10): array
    {
        $template = Template::query()
            ->with(['options' => fn($q) => $q->select(['id','template_id','name','key','group_id'])->orderBy('id')])
            ->findOrFail($templateId);

        [$sheet, $highestRow, $highestCol] = $this->openSheet($path, $sheetName);

        $headerRow = $sheet->rangeToArray(
            'A' . self::HEADER_ROW . ":{$highestCol}" . self::HEADER_ROW,
            null,
            true,
            false
        )[0];

        $parsed = $this->parseHeader($headerRow);
        $plan = $this->buildPlan($template, $parsed);

        if (!in_array('id', $parsed['base_present'], true)) {
            return [
                'ok' => false,
                'error' => "В файле нет колонки ID (строка " . self::HEADER_ROW . "). Нужен столбец ID для импорта по id.",
                'plan' => $plan,
                'columns' => [],
                'rows' => [],
            ];
        }

        $rows = [];
        $start = self::DATA_ROW;
        $end = min($highestRow, $start + max(0, $limit - 1));

        for ($r = $start; $r <= $end; $r++) {
            $raw = $sheet->rangeToArray("A{$r}:{$highestCol}{$r}", null, true, false)[0];
            $assoc = $this->rowToAssoc($raw, $parsed['columns']);
            if ($this->isRowEmpty($assoc)) continue;
            $rows[] = $assoc;
        }

        $ids = collect($rows)
            ->pluck('id')
            ->filter(fn($v) => is_numeric($v))
            ->map(fn($v) => (int)$v)
            ->unique()
            ->values()
            ->all();

        $byIdTemplate = [];
        if (!empty($ids)) {
            $byIdTemplate = Product::query()
                ->whereIn('id', $ids)
                ->pluck('template_id', 'id')
                ->mapWithKeys(fn($tpl, $id) => [(int)$id => (int)$tpl])
                ->all();
        }

        $previewRows = array_map(function ($row) use ($byIdTemplate, $templateId) {
            $id = $row['id'] ?? null;

            if (!is_numeric($id) || (int)$id === 0) {
                $row['_status'] = 'CREATE_AUTO';
                return $row;
            }

            $id = (int)$id;

            if (!isset($byIdTemplate[$id])) {
                $row['_status'] = 'CREATE';
            } elseif ($byIdTemplate[$id] === (int)$templateId) {
                $row['_status'] = 'UPDATE';
            } else {
                $row['_status'] = 'WRONG_TEMPLATE';
            }

            return $row;
        }, $rows);

        $columns = array_values($parsed['preview_columns']);
        array_unshift($columns, '_status');

        return [
            'ok' => true,
            'plan' => $plan,
            'columns' => $columns,
            'rows' => $previewRows,
        ];
    }

    /**
     * Импорт:
     * - заголовки на русском распознаются через HEADER_ALIASES_TO_BASE
     * - опции: name = русский заголовок, key = английский snake_case (транслит + _)
     * - manufacturer_id: если пусто/нет колонки -> 1
     * - по id: UPDATE если найден в этом template_id, CREATE если нет, SKIP если id принадлежит другому template_id
     */
    public function import(string $path, string $sheetName, int $templateId): array
    {
        $template = Template::query()
            ->with(['options' => fn($q) => $q->select(['id','template_id','name','key','group_id'])->orderBy('id')])
            ->findOrFail($templateId);

        [$sheet, $highestRow, $highestCol] = $this->openSheet($path, $sheetName);

        $headerRow = $sheet->rangeToArray(
            'A' . self::HEADER_ROW . ":{$highestCol}" . self::HEADER_ROW,
            null,
            true,
            false
        )[0];

        $parsed = $this->parseHeader($headerRow);

        if (!in_array('id', $parsed['base_present'], true)) {
            throw new \RuntimeException("В файле нет колонки ID (строка " . self::HEADER_ROW . "). Нужен столбец ID.");
        }

        $defaultGroupId = $this->defaultGroupId();

        // existing options by normalized name + existing keys set (чтобы делать уникальные key)
        $existingOptions = TemplateOption::query()
            ->where('template_id', $templateId)
            ->get(['id','name','key']);

        $optIdByNormName = [];
        $existingKeys = [];
        foreach ($existingOptions as $o) {
            $optIdByNormName[$this->normalizeHeader((string)$o->name)] = (int)$o->id;
            $existingKeys[(string)$o->key] = true;
        }

        // 1) Создаём новые TemplateOption для новых русских заголовков опций
        $createdOptions = 0;
        $newOptionsCreated = []; // для плана/логов

        DB::transaction(function () use (
            $templateId, $parsed, $defaultGroupId,
            &$optIdByNormName, &$existingKeys,
            &$createdOptions, &$newOptionsCreated
        ) {
            foreach ($parsed['option_headers'] as $optHeaderRaw) {
                $norm = $this->normalizeHeader($optHeaderRaw);

                if (isset($optIdByNormName[$norm])) {
                    continue;
                }

                $baseKey = $this->makeOptionKey($optHeaderRaw);
                $uniqueKey = $this->uniqueKey($baseKey, $existingKeys);

                $opt = TemplateOption::create([
                    'template_id' => $templateId,
                    'group_id' => $defaultGroupId,
                    'name' => $optHeaderRaw,   // как в Excel (RU)
                    'key' => $uniqueKey,       // EN snake_case
                    'fields' => [],
                ]);

                $optIdByNormName[$norm] = (int)$opt->id;
                $existingKeys[$uniqueKey] = true;

                $createdOptions++;
                $newOptionsCreated[] = ['name' => $optHeaderRaw, 'key' => $uniqueKey];
            }
        });

        $scanned = 0;
        $createdProducts = 0;
        $updatedProducts = 0;

        $skippedNoId = 0;
        $skippedWrongTemplate = 0;
        $skippedNoNameOnCreate = 0;
        $wrongTemplateIdsSample = [];
        $updatedOptionCells = 0;
        $createdIdsSample = [];
        $createdProductsWithId = 0;
        $createdProductsAutoId = 0;
        
        DB::transaction(function () use (&$createdProductsWithId, &$createdProductsAutoId, &$createdIdsSample,
            $sheet, $highestRow, $highestCol, $templateId,
            $parsed, $optIdByNormName,
            &$scanned, &$createdProducts, &$updatedProducts,
            &$skippedNoId, &$skippedWrongTemplate, &$skippedNoNameOnCreate, &$wrongTemplateIdsSample,
            &$updatedOptionCells
        ) {
            
            
            
            for ($r = self::DATA_ROW; $r <= $highestRow; $r++) {
                $raw = $sheet->rangeToArray("A{$r}:{$highestCol}{$r}", null, true, false)[0];
                $row = $this->rowToAssoc($raw, $parsed['columns']);
                if ($this->isRowEmpty($row)) continue;

                $scanned++;

                $idRaw = $row['id'] ?? null;
                $hasId = is_numeric($idRaw) && (int)$idRaw > 0;

                $product = null;

                // payload базовых полей
                $payload = ['template_id' => $templateId];

                foreach ($parsed['base_columns'] as $baseField) {
                    if ($baseField === 'id') continue;
                    if (!array_key_exists($baseField, $row)) continue;
                    $payload[$baseField] = $row[$baseField];
                }

                // price/delivery/engineering нормализация
                if (array_key_exists('price', $payload)) {
                    $payload['price'] = $this->nullIfEmpty($payload['price']);
                    if ($payload['price'] !== null) $payload['price'] = (float)$payload['price'];
                }
                if (array_key_exists('delivery', $payload)) {
                    $payload['delivery'] = $this->nullIfEmpty($payload['delivery']);
                    if ($payload['delivery'] !== null) $payload['delivery'] = (float)$payload['delivery'];
                }
                if (array_key_exists('engineering', $payload)) {
                    $payload['engineering'] = $this->parseEngineering($payload['engineering']);
                }

                // manufacturer_id:
                // - если колонка есть и пустая -> ставим 1
                // - если колонки нет -> ставим 1 ТОЛЬКО при создании
                $manufacturerProvided = array_key_exists('manufacturer_id', $payload);
                if ($manufacturerProvided) {
                    if ($this->isEmpty($payload['manufacturer_id'])) {
                        $payload['manufacturer_id'] = 1;
                    } else {
                        $payload['manufacturer_id'] = (int)$payload['manufacturer_id'];
                    }
                }

                if ($hasId) {
                    $id = (int)$idRaw;

                    $product = Product::query()->where('id', $id)->first();

                    // если id существует, но другой template -> пропускаем (и считаем!)
                    if ($product && (int)$product->template_id !== (int)$templateId) {
                        $skippedWrongTemplate++;
                        if (count($wrongTemplateIdsSample) < 200) $wrongTemplateIdsSample[] = $id;
                        continue;
                    }

                    // CREATE с указанным id
                    if (!$product) {
                        $name = trim((string)($payload['name'] ?? ''));
                        if ($name === '') {
                            $skippedNoNameOnCreate++;
                            continue;
                        }

                        if (!$manufacturerProvided) {
                            $payload['manufacturer_id'] = 1;
                        }

                        $product = new Product();
                        $product->id = $id; // создаём с этим id
                        $product->fill($payload);
                        $product->save();

                        $createdProductsWithId++;
                        $createdProducts++;
                        if (count($createdIdsSample) < 50) $createdIdsSample[] = $product->id;
                    } else {
                        // UPDATE
                        $product->fill($payload);
                        if ($product->isDirty()) {
                            $product->save();
                            $updatedProducts++;
                        }
                    }
                } else {
                    // CREATE без id -> auto increment
                    $name = trim((string)($payload['name'] ?? ''));
                    if ($name === '') {
                        $skippedNoNameOnCreate++;
                        continue;
                    }

                    if (!$manufacturerProvided) {
                        $payload['manufacturer_id'] = 1;
                    }

                    $product = new Product();
                    $product->fill($payload);
                    $product->save();

                    $createdProductsAutoId++;
                    $createdProducts++;
                    if (count($createdIdsSample) < 50) $createdIdsSample[] = $product->id;
                }

                // Опции
                foreach ($parsed['option_headers'] as $optHeaderRaw) {
                    $norm = $this->normalizeHeader($optHeaderRaw);
                    $optId = $optIdByNormName[$norm] ?? null;
                    if (!$optId) continue;

                    $valKey = $this->optionValueKey($optHeaderRaw);
                    $val = (string)($row[$valKey] ?? '');

                    $po = ProductOption::query()->firstOrNew([
                        'product_id' => $product->id,
                        'template_option_id' => $optId,
                    ]);

                    if ($po->exists && (string)$po->value === $val) continue;

                    $po->value = $val;
                    $po->save();
                    $updatedOptionCells++;
                }
            }

        });

        $plan = $this->buildPlan(
            Template::query()->with(['options' => fn($q) => $q->select(['id','template_id','name','key','group_id'])->orderBy('id')])->findOrFail($templateId),
            $parsed
        );

        return [
            'ok' => true,
            'sheet' => $sheetName,
            'template_id' => $templateId,

            'created_options' => $createdOptions,
            'created_options_list' => $newOptionsCreated,

            'scanned_rows' => $scanned,

            'created_products' => $createdProducts,
            'created_products_with_id' => $createdProductsWithId,
            'created_products_auto_id' => $createdProductsAutoId,
            'created_product_ids_sample' => $createdIdsSample,

            'updated_products' => $updatedProducts,

            'skipped_rows_wrong_template' => $skippedWrongTemplate,
            'wrong_template_ids_sample' => $wrongTemplateIdsSample,

            'skipped_rows_no_name_on_create' => $skippedNoNameOnCreate,

            'updated_option_cells' => $updatedOptionCells,
            'plan' => $plan,
        ];
    }

    // ---------------- PARSING ----------------

    /**
     * Разбирает HEADER_ROW и строит описание колонок.
     * Возвращает:
     * - columns: массив дескрипторов колонок для чтения rowToAssoc()
     * - base_present: какие базовые поля есть
     * - base_missing: каких нет
     * - option_headers: список русских заголовков-опций (как в Excel)
     * - preview_columns: колонки для таблицы предпросмотра (как человек видит)
     * - base_columns: список base field names, которые присутствуют
     */
    private function parseHeader(array $headerRow): array
    {
        $seen = [];
        $duplicates = [];

        $columns = [];         // [{idx, kind, base_field?, option_header?}]
        $basePresent = [];
        $optionHeaders = [];

        $previewCols = []; // для UI: показываем реальные заголовки + базовые поля (внутренние) можно не светить

        foreach ($headerRow as $idx => $cell) {
            $raw = trim((string)$cell);
            if ($raw === '') {
                continue;
            }

            $norm = $this->normalizeHeader($raw);
            if (isset($seen[$norm])) {
                $duplicates[] = $raw;
            }
            $seen[$norm] = true;

            $baseField = $this->mapHeaderToBaseField($raw);

            if ($baseField) {
                $columns[] = [
                    'idx' => $idx,
                    'kind' => 'base',
                    'base_field' => $baseField,
                    'raw_header' => $raw,
                ];
                $basePresent[] = $baseField;
                $previewCols[] = $raw;
            } else {
                $columns[] = ['idx' => $idx, 'kind' => 'option', 'option_header' => $raw];
                $optionHeaders[] = $raw;
                $previewCols[] = $raw;
            }
        }

        $basePresent = array_values(array_unique($basePresent));
        $baseMissing = array_values(array_diff(self::BASE_FIELDS, $basePresent));

        $optionHeaders = array_values(array_unique($optionHeaders));
        $duplicates = array_values(array_unique($duplicates));

        return [
            'columns' => $columns,
            'base_present' => $basePresent,
            'base_missing' => $baseMissing,
            'option_headers' => $optionHeaders,
            'duplicates' => $duplicates,
            'preview_columns' => $previewCols,
            'base_columns' => $basePresent,
        ];
    }

    private function buildPlan(Template $template, array $parsed): array
    {
        // сравниваем по name (русский заголовок)
        $dbNames = $template->options->pluck('name')->map(fn($v) => trim((string)$v))->all();
        $dbNorm = array_fill_keys(array_map([$this,'normalizeHeader'], $dbNames), true);

        $excelNames = $parsed['option_headers'];
        $newInExcel = [];
        foreach ($excelNames as $n) {
            $norm = $this->normalizeHeader($n);
            if (!isset($dbNorm[$norm])) {
                $newInExcel[] = ['name' => $n, 'key' => $this->makeOptionKey($n)];
            }
        }

        // какие опции в БД, но нет в Excel (по name)
        $excelNorm = array_fill_keys(array_map([$this,'normalizeHeader'], $excelNames), true);
        $missingInExcel = [];
        foreach ($dbNames as $dbName) {
            $norm = $this->normalizeHeader($dbName);
            if (!isset($excelNorm[$norm])) {
                $missingInExcel[] = $dbName;
            }
        }

        return [
            'header_row' => self::HEADER_ROW,
            'data_row' => self::DATA_ROW,
            'duplicates_in_excel' => $parsed['duplicates'],

            'base_fields_present_in_excel' => $parsed['base_present'],
            'base_fields_missing_in_excel' => $parsed['base_missing'],

            'new_option_columns_will_be_created' => $newInExcel, // [{name, key}]
            'db_option_columns_missing_in_excel_not_updated' => $missingInExcel,
        ];
    }

    // ---------------- ROW READING ----------------

    /**
     * Собирает строку в ассоц. массив:
     * - базовые поля кладём по ключам products (name/price/...)
     * - опции кладём по ключу option:<RU_HEADER> чтобы избежать конфликтов
     */
    private function rowToAssoc(array $row, array $columns): array
    {
        $assoc = [];

        foreach ($columns as $c) {
            $idx = $c['idx'];
            $val = $row[$idx] ?? null;

            if ($c['kind'] === 'base') {
                $assoc[$c['base_field']] = $val;

                // для preview — чтобы по русскому заголовку тоже было значение
                if (!empty($c['raw_header'])) {
                    $assoc[(string)$c['raw_header']] = $val;
                }
            } else {
                $h = (string)$c['option_header'];
                $assoc[$this->optionValueKey($h)] = $val;
                $assoc[$h] = $val; // для preview
            }
        }

        return $assoc;
    }

    private function optionValueKey(string $header): string
    {
        return 'option:' . $this->normalizeHeader($header);
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $k => $v) {
            if ($k === '_status') continue;
            if (is_string($v) && trim($v) !== '') return false;
            if (is_numeric($v)) return false;
            if (is_array($v) && !empty($v)) return false;
        }
        return true;
    }

    // ---------------- HEADER MAPPING / KEY GEN ----------------

    private function mapHeaderToBaseField(string $header): ?string
    {
        $h = trim((string)$header);
        if ($h === '') return null;

        // если вдруг в Excel уже английские ключи
        if (in_array($h, self::BASE_FIELDS, true)) {
            return $h;
        }

        $norm = $this->normalizeHeader($h);
        return self::HEADER_ALIASES_TO_BASE[$norm] ?? null;
    }

    private function normalizeHeader(string $s): string
    {
        $s = trim($s);
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = mb_strtolower($s);
        // убираем лишнюю пунктуацию, оставляем буквы/цифры/пробел/underscore/дефис
        $s = preg_replace('/[^\p{L}\p{N}\s_\-]+/u', '', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    /**
     * key для TemplateOption:
     * - транслит RU->EN
     * - snake_case с underscore
     */
    private function makeOptionKey(string $nameRu): string
    {
        $nameRu = trim($nameRu);
        if ($nameRu === '') return 'option';

        // пробуем стандартный slug (если intl/iconv настроен)
        $slug = Str::slug($nameRu, '_');
        $slug = strtolower($slug);

        if ($slug === '' || $slug === '_') {
            $slug = strtolower($this->transliterateRuToEn($nameRu));
            $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
            $slug = trim(preg_replace('/_+/', '_', $slug), '_');
        }

        return $slug ?: 'option';
    }

    private function uniqueKey(string $baseKey, array $existingKeys): string
    {
        $key = $baseKey;
        $i = 2;
        while (isset($existingKeys[$key])) {
            $key = $baseKey . '_' . $i;
            $i++;
        }
        return $key;
    }

    private function transliterateRuToEn(string $s): string
    {
        $map = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y',
            'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
            'х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        ];
        $s = mb_strtolower($s);
        $out = '';
        $len = mb_strlen($s);
        for ($i=0; $i<$len; $i++) {
            $ch = mb_substr($s, $i, 1);
            $out .= $map[$ch] ?? $ch;
        }
        return $out;
    }

    // ---------------- SHEET / VALUE HELPERS ----------------

    private function openSheet(string $path, string $sheetName): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName($sheetName);

        if (!$sheet) {
            throw new \RuntimeException("Лист '{$sheetName}' не найден.");
        }

        return [$sheet, $sheet->getHighestDataRow(), $sheet->getHighestDataColumn()];
    }

    private function nullIfEmpty(mixed $v): mixed
    {
        if ($v === null) return null;
        $s = trim((string)$v);
        return $s === '' ? null : $v;
    }

    private function isEmpty(mixed $v): bool
    {
        if ($v === null) return true;
        if (is_string($v) && trim($v) === '') return true;
        return false;
    }

    private function parseEngineering(mixed $v): mixed
    {
        if ($v === null) return null;
        if (is_array($v)) return $v;

        $s = trim((string)$v);
        if ($s === '') return null;

        $decoded = json_decode($s, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $v;
    }

    private function safeSheetTitle(string $title): string
    {
        $title = preg_replace('/[\[\]\*\/\\\\\?\:]/', ' ', $title);
        $title = trim(preg_replace('/\s+/', ' ', $title));
        return mb_substr($title, 0, 31);
    }

    private function defaultGroupId(): int
    {
        $g = GroupOption::query()->orderBy('id')->first();
        if ($g) return (int)$g->id;

        return (int)GroupOption::query()->create(['name' => 'Общее'])->id;
    }
}

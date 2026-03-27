<?php

namespace App\Services\TableSettings\Excel;

use App\Models\TableSettings\Template;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductExcelService
{
    public function __construct(
        private GenericProductExcelService $generic,
        private FrProductExcelService $fr,
    ) {}

    public function listSheets(string $path): array
    {
        return $this->generic->listSheets($path);
    }

    public function exportData(int $templateId): StreamedResponse
    {
        return $this->isFrTemplate($templateId)
            ? $this->fr->exportData($templateId)
            : $this->generic->exportData($templateId);
    }

    public function preview(string $path, string $sheetName, int $templateId, int $limit = 10): array
    {
        return $this->isFrTemplate($templateId)
            ? $this->fr->preview($path, $sheetName, $templateId, $limit)
            : $this->generic->preview($path, $sheetName, $templateId, $limit);
    }

    public function import(string $path, string $sheetName, int $templateId): array
    {
        return $this->isFrTemplate($templateId)
            ? $this->fr->import($path, $sheetName, $templateId)
            : $this->generic->import($path, $sheetName, $templateId);
    }

    private function isFrTemplate(int $templateId): bool
    {
        return (int)$templateId === 1;
        // или:
        // return str_contains(mb_strtolower((string) Template::find($templateId)?->name), 'чрп');
    }
}
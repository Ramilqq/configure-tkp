<?php

namespace App\Services\TableSettings\Excel;

use App\Models\TableSettings\Template;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductExcelService
{
    public function __construct(
        private GenericProductExcelService $generic,
        private FrProductExcelService $fr,
        private UppProductExcelService $upp,
    ) {}

    public function listSheets(string $path): array
    {
        return $this->generic->listSheets($path);
    }

    public function exportData(int $templateId): StreamedResponse
    {
        switch ($templateId) {
            case 1:
                return $this->fr->exportData($templateId);
            case 4:
                return $this->upp->exportData($templateId);
            default:
                return $this->generic->exportData($templateId);
        }
    }

    public function preview(string $path, string $sheetName, int $templateId, int $limit = 10): array
    {
        switch ($templateId) {
            case 1:
                return $this->fr->preview($path, $sheetName, $templateId, $limit);
            case 4:
                return $this->upp->preview($path, $sheetName, $templateId, $limit);
            default:
                return $this->generic->preview($path, $sheetName, $templateId, $limit);
        }
    }

    public function import(string $path, string $sheetName, int $templateId): array
    {
        switch ($templateId) {
            case 1:
                return $this->fr->import($path, $sheetName, $templateId);
            case 4:
                return $this->upp->import($path, $sheetName, $templateId);
            default:
                return $this->generic->import($path, $sheetName, $templateId);
        }
    }

    private function isFrTemplate(int $templateId): bool
    {
        return (int)$templateId === 1;
        // или:
        // return str_contains(mb_strtolower((string) Template::find($templateId)?->name), 'чрп');
    }
}
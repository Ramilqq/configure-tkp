<?php

namespace App\Services\TableSettings\Excel;

use App\Models\TableSettings\ProductImportLog;
use App\Models\TableSettings\Template;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @param string|null $originalFileName исходное имя загруженного файла
     *        (для журнала импортов — временный путь на диске ничего не говорит пользователю)
     */
    public function import(string $path, string $sheetName, int $templateId, ?string $originalFileName = null): array
    {
        try {
            switch ($templateId) {
                case 1:
                    $result = $this->fr->import($path, $sheetName, $templateId);
                    break;
                case 4:
                    $result = $this->upp->import($path, $sheetName, $templateId);
                    break;
                default:
                    $result = $this->generic->import($path, $sheetName, $templateId);
                    break;
            }
        } catch (\Throwable $e) {
            $this->logImport($templateId, $sheetName, $originalFileName, 'error', null, $e->getMessage());
            throw $e;
        }

        $this->logImport($templateId, $sheetName, $originalFileName, 'success', $result, null);

        return $result;
    }

    private function logImport(
        int $templateId,
        string $sheetName,
        ?string $originalFileName,
        string $status,
        ?array $result,
        ?string $errorMessage
    ): void {
        ProductImportLog::create([
            'user_id' => Auth::id(),
            'template_id' => $templateId,
            'file_name' => $originalFileName,
            'sheet' => $sheetName,
            'mode' => $result['mode'] ?? null,
            'status' => $status,
            'result' => $result,
            'error_message' => $errorMessage,
        ]);
    }

    private function isFrTemplate(int $templateId): bool
    {
        return (int)$templateId === 1;
        // или:
        // return str_contains(mb_strtolower((string) Template::find($templateId)?->name), 'чрп');
    }
}
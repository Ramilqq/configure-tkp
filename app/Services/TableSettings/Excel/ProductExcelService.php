<?php

namespace App\Services\TableSettings\Excel;

use App\Enums\TemplateType;
use App\Models\TableSettings\ProductImportLog;
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
        return $this->serviceFor($templateId)->exportData($templateId);
    }

    public function preview(string $path, string $sheetName, int $templateId, int $limit = 10): array
    {
        return $this->serviceFor($templateId)->preview($path, $sheetName, $templateId, $limit);
    }

    /**
     * @param string|null $originalFileName исходное имя загруженного файла
     *        (для журнала импортов — временный путь на диске ничего не говорит пользователю)
     */
    public function import(string $path, string $sheetName, int $templateId, ?string $originalFileName = null): array
    {
        try {
            $result = $this->serviceFor($templateId)->import($path, $sheetName, $templateId);
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

    private function serviceFor(int $templateId): FrProductExcelService|UppProductExcelService|GenericProductExcelService
    {
        return match (TemplateType::tryFromTemplateId($templateId)) {
            TemplateType::Fr  => $this->fr,
            TemplateType::Upp => $this->upp,
            default           => $this->generic,
        };
    }
}
<?php

namespace App\Livewire\TableSettings;

use App\Models\TableSettings\ProductImportLog;
use App\Models\TableSettings\Template;
use App\Services\TableSettings\Excel\ProductExcelService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductExcelImport extends Component
{
    use WithFileUploads;

    public ?int $templateId = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $file = null;

    public array $sheets = [];
    public ?string $sheet = null;

    public int $previewLimit = 10;

    public array $plan = [];
    public array $previewColumns = [];
    public array $previewRows = [];

    public array $importResult = [];
    public ?string $error = null;

    public function updatedTemplateId(): void
    {
        $this->resetStateAfterConfigChange();
    }

    public function updatedSheet(): void
    {
        $this->resetStateAfterConfigChange();
    }

    public function updatedFile(ProductExcelService $svc): void
    {
        $this->resetStateAfterConfigChange();
        $this->reset(['sheets', 'sheet', 'error']);

        if (!$this->file) return;

        $this->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        try {
            $this->sheets = $svc->listSheets($this->file->getRealPath());
            $this->sheet = $this->sheets[0] ?? null;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function exportData(ProductExcelService $svc)
    {
        $this->error = null;

        $this->validate([
            'templateId' => ['required', 'integer', 'exists:templates,id'],
        ]);

        return $svc->exportData($this->templateId);
    }

    public function makePreview(ProductExcelService $svc): void
    {
        $this->reset(['plan','previewColumns','previewRows','importResult','error']);

        $this->validate([
            'templateId' => ['required', 'integer', 'exists:templates,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
            'sheet' => ['required', 'string'],
            'previewLimit' => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        try {
            $data = $svc->preview(
                path: $this->file->getRealPath(),
                sheetName: $this->sheet,
                templateId: $this->templateId,
                limit: $this->previewLimit
            );

            if (!($data['ok'] ?? false)) {
                $this->error = $data['error'] ?? 'Ошибка предпросмотра';
            }

            $this->plan = $data['plan'] ?? [];
            $this->previewColumns = $data['columns'] ?? [];
            $this->previewRows = $data['rows'] ?? [];
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function import(ProductExcelService $svc): void
    {
        $this->reset(['importResult','error']);

        $this->validate([
            'templateId' => ['required', 'integer', 'exists:templates,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
            'sheet' => ['required', 'string'],
        ]);

        if (empty($this->previewRows) || empty($this->plan)) {
            $this->error = "Сначала сделайте предпросмотр.";
            return;
        }

        if (!empty($this->plan['blocking_duplicates'] ?? [])) {
            $this->error = "В файле есть повторяющиеся строки (одинаковый id/технические параметры). Исправьте файл и сделайте предпросмотр заново.";
            return;
        }

        try {
            $this->importResult = $svc->import(
                path: $this->file->getRealPath(),
                sheetName: $this->sheet,
                templateId: $this->templateId,
                originalFileName: $this->file->getClientOriginalName(),
            );
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    private function resetStateAfterConfigChange(): void
    {
        $this->reset(['plan','previewColumns','previewRows','importResult','error']);
    }

    public function render()
    {
        return view('livewire.table-settings.product-excel-import', [
            'templates' => Template::query()->select(['id','name'])->orderBy('id')->get(),
            'importLogs' => ProductImportLog::with(['template:id,name', 'user:id,name'])
                ->latest()
                ->limit(20)
                ->get(),
        ]);
    }
}

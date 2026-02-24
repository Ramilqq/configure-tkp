<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\TemplateDimensionSchemeForm;
use App\Livewire\Forms\TableSettings\TemplateDimensionSchemeImageForm;
use App\Models\TableSettings\TemplateDimensionScheme;
use App\Models\TableSettings\TemplateDimensionSchemeImage;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\TemplatePriceRule;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class TemplateDimensionSchemeModal extends Component
{
    use WithFileUploads;

    public TemplateDimensionSchemeForm $form;
    public TemplateDimensionSchemeImageForm $formImage;

    /** @var array<int,array{id:int,name:string,key:string}> */
    public array $options = [];

    /** @var array<int,array{id:int,name:string,key:string}> */
    public array $rules = [];

    /** @var array<int,array{id:int,title:?string,file_path:string,sort:int}> */
    public array $images = [];

    /** @var array<int,\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $newImages = [];

    protected $listeners = [
        'dimensionSchemeInit' => 'dimensionSchemeInit',
        'dimensionSchemeEditOpenForm' => 'dimensionSchemeEditOpenForm',
        'dimensionSchemeDelete' => 'dimensionSchemeDelete',
        'dimensionSchemeRemoveImage' => 'dimensionSchemeRemoveImage',
    ];

    public function dimensionSchemeInit(int $template_id): void
    {
        $this->resetValidation();
        $this->form->reset();

        $this->form->template_id = $template_id;
        $this->form->enabled = true;
        $this->form->sort = 100;
        $this->form->match_mode = 'all';
        $this->form->conditions = [['_k' => (string) Str::uuid(), 'option_key' => '', 'op' => 'equals', 'value' => '']];
        $this->form->rule_conditions = [['_k' => (string) Str::uuid(), 'rule_key' => '', 'op' => 'equals', 'value' => '']];
        $this->form->meta = [];

        $this->images = [];
        $this->newImages = [];

        $this->loadOptionsAndRules($template_id);
    }

    public function dimensionSchemeEditOpenForm(int $id): void
    {
        //$this->form->reset();

        $scheme = TemplateDimensionScheme::with('images')->findOrFail($id);

        $this->loadOptionsAndRules((int)$scheme->template_id);

        $this->form->editForm($id);

        $this->images = $scheme->images
            ->map(fn($img) => [
                'id' => (int)$img->id,
                'title' => $img->title,
                'file_path' => $img->file_path,
                'sort' => (int)$img->sort,
            ])->toArray();
        $this->formImage->images = $this->images;
        $this->newImages = [];
    }

    public function addConditionRow(): void
    {
        $this->form->addConditionRow();
    }

    public function removeConditionRow(int $index): void
    {
        $this->form->removeConditionRow($index);
    }

    public function addRuleConditionRow(): void
    {
        $this->form->addRuleConditionRow();
    }

    public function removeRuleConditionRow(int $index): void
    {
        $this->form->removeRuleConditionRow($index);
    }

    public function updatedNewImages(): void
    {
        $this->validate([
            'newImages.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
    }

    public function saveForm(): void
    {
        $scheme = $this->form->saveForm();
        $this->formImage->saveForm();
        
        // сохраним новые изображения (в public/assets/image/dimensions/{template}/{scheme}/)
        if (!empty($this->newImages)) {
            $baseDirRel = 'assets/image/dimensions/' . $scheme->template_id . '/' . $scheme->id;
            $baseDirAbs = public_path($baseDirRel);

            if (!is_dir($baseDirAbs)) {
                @mkdir($baseDirAbs, 0775, true);
            }

            $maxSort = (int)TemplateDimensionSchemeImage::query()
                ->where('scheme_id', $scheme->id)
                ->max('sort');

            foreach ($this->newImages as $file) {
                $orig = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension() ?: 'png';
                $name = pathinfo($orig, PATHINFO_FILENAME);
                $name = Str::slug($name) ?: 'image';

                $filename = $name . '_' . now()->timestamp . '_' . Str::random(6) . '.' . $ext;

                $rel = $baseDirRel . '/' . $filename;
                $abs = public_path($rel);

                // копируем из временного файла в public
                @file_put_contents($abs, file_get_contents($file->getRealPath()));

                $maxSort++;

                TemplateDimensionSchemeImage::create([
                    'scheme_id' => $scheme->id,
                    'title' => $name,
                    'file_path' => $rel,
                    'sort' => $maxSort,
                    'meta' => [],
                ]);
            }
        }

        // перечитать
        $this->dimensionSchemeEditOpenForm((int)$scheme->id);

        $this->dispatch('dimensionSchemeUpdateList');
    }

    public function dimensionSchemeDelete(int $id): void
    {
        $scheme = TemplateDimensionScheme::with('images')->find($id);
        if (!$scheme) return;

        foreach ($scheme->images as $img) {
            $this->deleteImageFile((string)$img->file_path);
        }

        $scheme->delete();

        $this->dispatch('dimensionSchemeUpdateList');
    }

    public function dimensionSchemeRemoveImage(int $image_id): void
    {
        $img = TemplateDimensionSchemeImage::find($image_id);
        if (!$img) return;

        $this->deleteImageFile((string)$img->file_path);
        $img->delete();

        // refresh current form images if editing
        if ($this->form->id) {
            $this->dimensionSchemeEditOpenForm((int)$this->form->id);
        }

        $this->dispatch('dimensionSchemeUpdateList');
    }

    private function deleteImageFile(string $relPath): void
    {
        $abs = public_path($relPath);
        if ($relPath && file_exists($abs)) {
            @unlink($abs);
        }
    }

    private function loadOptionsAndRules(int $template_id): void
    {
        $this->options = TemplateOption::query()
            ->where('template_id', $template_id)
            ->orderBy('id')
            ->get(['id', 'name', 'key'])
            ->toArray();

        $this->rules = TemplatePriceRule::query()
            ->where('template_id', $template_id)
            ->orderBy('sort')
            ->orderBy('id')
            ->get(['id', 'name', 'key'])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.table-settings.template-dimension-scheme-modal');
    }
}

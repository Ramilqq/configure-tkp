<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\TemplateDimensionScheme;
use App\Models\TableSettings\TemplateDimensionSchemeImage;
use Livewire\Form;

class TemplateDimensionSchemeImageForm extends Form
{
    public int $id = 0;
    public int $scheme_id = 0;
    public string $title = '';
    public int $sort = 100;
    public array $meta = [];
    public array $images = [];

    protected function rules()
    {
        return [
            'id' => 'required|integer|exists:template_dimension_scheme_images,id',
            'title' => 'required|min:2|max:255',
            'sort' => 'required|integer|min:0|max:100000',
            'meta' => 'nullable|array',
        ];
    }

    public function saveForm(): void
    {
        foreach ($this->images as $image) {
            $this->fill($image);

            $validated = $this->validate();

            $scheme_image = TemplateDimensionSchemeImage::find($validated['id']);
            
            if ($scheme_image) {
                $scheme_image->update($validated);
            }
        }
    }
}

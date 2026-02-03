<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\TemplatePriceRule;
use App\Services\StringTranslit;
use Livewire\Form;

class TemplatePriceRuleForm extends Form
{
    public int $id = 0;
    public int $template_id = 0;

    public string $name = '';
    public string $key = '';

    public bool $enabled = true;
    public int $sort = 100;

    public string $target_field = 'price'; // price|delivery
    public string $mode = 'replace';       // replace|add|multiply

    // условие (проверяем значение драйвера)
    public string $condition_operator = 'exists'; // exists|filled|equals|not_equals
    public ?string $condition_value = null;

    public ?int $driver_option_id = null;

    /** @var array<int, array{from: mixed, to: mixed, value: mixed}> */
    public array $mapping = [
        ['from' => '', 'to' => '', 'value' => ''],
    ];

    public array $meta = [];

    protected function rules()
    {
        return [
            'template_id' => 'required|integer|exists:templates,id',

            'name' => 'required|min:2|max:150',
            'key'  => 'required|min:2|max:200|unique:template_price_rules,key,' . $this->id . ',id,template_id,' . $this->template_id,

            'enabled' => 'boolean',
            'sort' => 'required|integer|min:0|max:100000',

            'target_field' => 'required|in:price,delivery',
            'mode' => 'required|in:replace,add,multiply',

            'condition_operator' => 'required|in:exists,filled,equals,not_equals',
            'condition_value' => 'nullable|max:255',

            'driver_option_id' => 'nullable|integer|exists:template_options,id',

            'mapping' => 'nullable|array',
            'mapping.*.from' => 'nullable',
            'mapping.*.to' => 'nullable',
            'mapping.*.value' => 'nullable',
        ];
    }

    public function addMappingRow(): void
    {
        $this->mapping[] = ['from' => '', 'to' => '', 'value' => ''];
    }

    public function removeMappingRow(int $index): void
    {
        unset($this->mapping[$index]);
        $this->mapping = array_values($this->mapping);
        if (count($this->mapping) === 0) {
            $this->mapping[] = ['from' => '', 'to' => '', 'value' => ''];
        }
    }

    public function saveForm(): TemplatePriceRule
    {
        $this->key = StringTranslit::transliterate($this->name);

        // подчистим mapping
        $this->mapping = array_values(array_filter($this->mapping, function ($row) {
            $from = trim((string)($row['from'] ?? ''));
            $to   = trim((string)($row['to'] ?? ''));
            $val  = trim((string)($row['value'] ?? ''));
            return ($from !== '' || $to !== '' || $val !== '');
        }));

        $validated = $this->validate();

        $rule = TemplatePriceRule::find($this->id);
        if ($rule) {
            $rule->update($validated);
        } else {
            $rule = TemplatePriceRule::create($validated);
        }

        return $rule;
    }

    public function editForm(int $id): void
    {
        $rule = TemplatePriceRule::findOrFail($id);

        $data = $rule->toArray();

        

        // важное: json-cast вернёт null, если в БД NULL
        $data['meta'] = $data['meta'] ?? [];
        $data['mapping'] = $data['mapping'] ?? [];

        $this->fill($data);

        //dd($data, $this);
        if (empty($this->mapping)) {
            $this->mapping = [['from' => '', 'to' => '', 'value' => '']];
        }
    }
}

<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\TemplateDimensionScheme;
use Livewire\Form;
use Illuminate\Support\Str;

class TemplateDimensionSchemeForm extends Form
{
    public int $id = 0;
    public int $template_id = 0;

    public string $name = '';
    public bool $enabled = true;
    public int $sort = 100;
    public string $match_mode = 'all'; // all|any

    /** @var array<int, array{option_key:string, op:string, value:mixed}> */
    public array $conditions = [
        ['_k' => '', 'option_key' => '', 'op' => 'equals', 'value' => ''],
    ];

    /** @var array<int, array{rule_key:string, op:string, value:mixed}> */
    public array $rule_conditions = [
        ['_k' => '', 'rule_key' => '', 'op' => 'equals', 'value' => ''],
    ];

    public array $meta = [];

    protected function rules()
    {
        return [
            'template_id' => 'required|integer|exists:templates,id',
            'name' => 'required|min:2|max:255',
            'enabled' => 'boolean',
            'sort' => 'required|integer|min:0|max:100000',
            'match_mode' => 'required|in:all,any',

            'conditions' => 'nullable|array',
            'conditions.*._k' => 'nullable|string|max:255',
            'conditions.*.option_key' => 'nullable|string|max:255',
            'conditions.*.op' => 'nullable|string|in:exists,filled,equals,not_equals,in,not_in,contains',
            'conditions.*.value' => 'nullable',

            'rule_conditions' => 'nullable|array',
            'rule_conditions.*._k' => 'nullable|string|max:255',
            'rule_conditions.*.rule_key' => 'nullable|string|max:255',
            'rule_conditions.*.op' => 'nullable|string|in:exists,filled,equals,not_equals,in,not_in,contains',
            'rule_conditions.*.value' => 'nullable',

            'meta' => 'nullable|array',
        ];
    }

    public function addConditionRow(): void
    {
        $this->conditions[] = ['_k' => (string) Str::uuid(), 'option_key' => '', 'op' => 'equals', 'value' => ''];
    }

    public function removeConditionRow(int $index): void
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);
        if (count($this->conditions) === 0) {
            $this->addConditionRow();
        }
    }

    public function addRuleConditionRow(): void
    {
        $this->rule_conditions[] = ['_k' => (string) Str::uuid(), 'rule_key' => '', 'op' => 'equals', 'value' => ''];
    }

    public function removeRuleConditionRow(int $index): void
    {
        unset($this->rule_conditions[$index]);
        $this->rule_conditions = array_values($this->rule_conditions);
        if (count($this->rule_conditions) === 0) {
            $this->addRuleConditionRow();
        }
    }

    public function saveForm(): TemplateDimensionScheme
    {
        // normalize: drop empty rows, normalize in/not_in to array
        $this->conditions = $this->normalizeConditions($this->conditions, 'option_key');
        $this->rule_conditions = $this->normalizeConditions($this->rule_conditions, 'rule_key');

        $validated = $this->validate();

        $scheme = TemplateDimensionScheme::find($this->id);
        if ($scheme) {
            $scheme->update($validated);
        } else {
            $scheme = TemplateDimensionScheme::create($validated);
        }

        return $scheme;
    }

    public function editForm(int $id): void
    {
        $scheme = TemplateDimensionScheme::with('images')->findOrFail($id);
        $data = $scheme->toArray();

        $data['conditions'] = $data['conditions'] ?? [];
        $data['rule_conditions'] = $data['rule_conditions'] ?? [];
        $data['meta'] = $data['meta'] ?? [];

        $this->fill($data);

        if (empty($this->conditions)) {
            $this->conditions = [['_k' => (string) Str::uuid(), 'option_key' => '', 'op' => 'equals', 'value' => '']];
        }
        if (empty($this->rule_conditions)) {
            $this->rule_conditions = [['_k' => (string) Str::uuid(), 'rule_key' => '', 'op' => 'equals', 'value' => '']];
        }
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     */
    private function normalizeConditions(array $rows, string $keyField): array
    {
        $out = [];

        foreach ($rows as $row) {
            $key = trim((string)($row[$keyField] ?? ''));
            if ($key === '') continue;

            $k = (string)($row['_k'] ?? Str::uuid());
            $op = (string)($row['op'] ?? 'equals');
            $val = $row['value'] ?? null;

            if (in_array($op, ['in','not_in'], true)) {
                if (is_string($val)) {
                    $val = array_values(array_filter(array_map('trim', explode(',', $val)), fn($v) => $v !== ''));
                }
                if (!is_array($val)) {
                    $val = $val === null ? [] : [(string)$val];
                }
            } else {
                // keep scalar string (for equals/contains/etc)
                if (is_array($val)) {
                    $val = implode(',', array_map('strval', $val));
                }
                if ($val !== null) $val = (string)$val;
            }

            $out[] = [
                '_k' => $k,
                $keyField => $key,
                'op' => $op,
                'value' => $val,
            ];
        }

        return $out;
    }
}

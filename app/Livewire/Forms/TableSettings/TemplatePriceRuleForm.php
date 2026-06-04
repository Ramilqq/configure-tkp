<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\Currency;
use App\Models\TableSettings\TemplatePriceRule;
use Livewire\Form;

class TemplatePriceRuleForm extends Form
{
    public int    $id          = 0;
    public int    $template_id = 0;

    public string  $name        = '';
    public ?string $description = null;

    public bool $enabled = true;
    public int  $sort    = 100;

    public string $target_field = 'price'; // price|delivery
    public string $mode         = 'add';   // replace|add|multiply

    public ?float  $value    = null;
    public string  $currency = 'RUB';

    /** @var array<int, array{template_option_id: int|string, operator: string, value: string}> */
    public array $option_conditions = [];

    /** @var array<int, array{template_option_id: int|string, operator: string, value: string}> */
    public array $option_price_conditions = [];

    // -------------------------------------------------------------------------

    protected function rules(): array
    {
        return [
            'template_id' => 'required|integer|exists:templates,id',
            'name'        => 'required|min:2|max:150',
            'description' => 'nullable|max:250',
            'enabled'     => 'boolean',
            'sort'        => 'required|integer|min:0|max:100000',
            'target_field' => 'required|in:price,delivery',
            'mode'         => 'required|in:replace,add,multiply',
            'value'        => 'nullable|numeric',
            'currency'     => 'required|in:' . implode(',', Currency::VALUE),

            'option_conditions'               => 'nullable|array',
            'option_conditions.*.template_option_id' => 'required|integer|exists:template_options,id',
            'option_conditions.*.operator'    => 'required|in:>,>=,=,<,<=',
            'option_conditions.*.value'       => 'nullable|string|max:255',

            'option_price_conditions'               => 'nullable|array',
            'option_price_conditions.*.template_option_id' => 'required|integer|exists:template_options,id',
            'option_price_conditions.*.operator'    => 'required|in:>,>=,=,<,<=',
            'option_price_conditions.*.value'       => 'nullable|numeric',
        ];
    }

    // -------------------------------------------------------------------------

    public function addOptionCondition(): void
    {
        $this->option_conditions[] = [
            'template_option_id' => '',
            'operator'           => '=',
            'value'              => '',
        ];
    }

    public function removeOptionCondition(int $index): void
    {
        unset($this->option_conditions[$index]);
        $this->option_conditions = array_values($this->option_conditions);
    }

    public function addOptionPriceCondition(): void
    {
        $this->option_price_conditions[] = [
            'template_option_id' => '',
            'operator'           => '>=',
            'value'              => '',
        ];
    }

    public function removeOptionPriceCondition(int $index): void
    {
        unset($this->option_price_conditions[$index]);
        $this->option_price_conditions = array_values($this->option_price_conditions);
    }

    // -------------------------------------------------------------------------

    public function saveForm(): TemplatePriceRule
    {
        $validated = $this->validate();

        $validated['conditions'] = [
            'option_conditions'       => $this->option_conditions,
            'option_price_conditions' => $this->option_price_conditions,
        ];

        // убираем временные поля — в БД их нет
        unset($validated['option_conditions'], $validated['option_price_conditions']);

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

        $this->id          = $rule->id;
        $this->template_id = (int)$rule->template_id;
        $this->name        = (string)$rule->name;
        $this->description = $rule->description;
        $this->enabled     = (bool)$rule->enabled;
        $this->sort        = (int)$rule->sort;
        $this->target_field = (string)$rule->target_field;
        $this->mode        = (string)$rule->mode;
        $this->value       = $rule->value !== null ? (float)$rule->value : null;
        $this->currency    = (string)($rule->currency ?? 'RUB');

        $conditions = $rule->conditions ?? [];
        $this->option_conditions       = $conditions['option_conditions']       ?? [];
        $this->option_price_conditions = $conditions['option_price_conditions'] ?? [];
    }
}

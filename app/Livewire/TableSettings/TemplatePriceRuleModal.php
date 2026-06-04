<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\TemplatePriceRuleForm;
use App\Models\TableSettings\Currency;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\TemplatePriceRule;
use Livewire\Component;

class TemplatePriceRuleModal extends Component
{
    public TemplatePriceRuleForm $form;

    /** Опции шаблона для выбора в условиях */
    public array $options   = [];
    /** Доступные валюты */
    public array $currencies = [];

    protected $listeners = [
        'templatePriceRuleInit'         => 'templatePriceRuleInit',
        'templatePriceRuleEditOpenForm'  => 'templatePriceRuleEditOpenForm',
        'templatePriceRuleDelete'        => 'templatePriceRuleDelete',
    ];

    public function templatePriceRuleInit(int $template_id): void
    {
        $this->form->reset();
        $this->form->template_id  = $template_id;
        $this->form->enabled      = true;
        $this->form->sort         = 100;
        $this->form->target_field = 'price';
        $this->form->mode         = 'add';
        $this->form->currency     = 'RUB';
        $this->form->option_conditions       = [];
        $this->form->option_price_conditions = [];

        $this->loadOptions($template_id);
    }

    public function templatePriceRuleEditOpenForm(int $id): void
    {
        $rule = TemplatePriceRule::findOrFail($id);
        $this->loadOptions((int)$rule->template_id);
        $this->form->editForm($id);
    }

    public function templatePriceRuleDelete(int $id): void
    {
        TemplatePriceRule::find($id)?->delete();
        $this->dispatch('templateUpdateList');
    }

    public function addOptionCondition(): void
    {
        $this->form->addOptionCondition();
    }

    public function removeOptionCondition(int $index): void
    {
        $this->form->removeOptionCondition($index);
    }

    public function addOptionPriceCondition(): void
    {
        $this->form->addOptionPriceCondition();
    }

    public function removeOptionPriceCondition(int $index): void
    {
        $this->form->removeOptionPriceCondition($index);
    }

    public function saveForm(): void
    {
        $rule = $this->form->saveForm();
        $this->loadOptions($this->form->template_id);
        $this->form->editForm($rule->id);
        $this->dispatch('templateUpdateList');
    }

    private function loadOptions(int $template_id): void
    {
        $this->options = TemplateOption::query()
            ->where('template_id', $template_id)
            ->orderBy('id')
            ->get(['id', 'name', 'key'])
            ->toArray();

        $this->currencies = Currency::VALUE;
    }

    public function render()
    {
        return view('livewire.table-settings.template-price-rule-modal');
    }
}

<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\TemplatePriceRuleForm;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\TemplatePriceRule;
use Livewire\Component;

class TemplatePriceRuleModal extends Component
{
    public TemplatePriceRuleForm $form;

    public array $options = []; // для выбора driver_option_id

    protected $listeners = [
        'templatePriceRuleInit' => 'templatePriceRuleInit',
        'templatePriceRuleEditOpenForm' => 'templatePriceRuleEditOpenForm',
        'templatePriceRuleDelete' => 'templatePriceRuleDelete',
    ];

    public function mount(): void
    {
        if (empty($this->form->mapping)) {
            $this->form->mapping = [['from' => '', 'to' => '', 'condition' => '', 'text' => '', 'value' => '']];
        }
    }

    public function templatePriceRuleInit(int $template_id): void
    {
        $this->form->reset();
        $this->form->template_id = $template_id;
        $this->form->meta = [];
        $this->form->enabled = true;
        $this->form->sort = 100;
        $this->form->target_field = 'price';
        $this->form->mode = 'add';
        $this->form->generation_name_status = false;
        $this->form->generation_name_text = null;

        // условие по драйверу
        $this->form->condition_operator = 'equals';
        $this->form->condition_value = null;

        $this->form->mapping = [['from' => '', 'to' => '', 'condition' => '', 'text' => '', 'value' => '']];

        $this->loadOptions($template_id);
    }

    public function templatePriceRuleEditOpenForm(int $id): void
    {
        $rule = TemplatePriceRule::query()->findOrFail($id);

        // 1) сначала наполняем список опций
        $this->loadOptions((int)$rule->template_id);

        // 2) потом заполняем форму (driver_option_id уже попадёт в готовый <select>)
        $this->form->editForm($id);

        //dd($this->form);
    }

    public function templatePriceRuleDelete(int $id): void
    {
        TemplatePriceRule::find($id)?->delete();
        $this->dispatch('templateUpdateList');
    }

    public function addMappingRow(): void
    {
        $this->form->addMappingRow();
    }

    public function removeMappingRow(int $index): void
    {
        $this->form->removeMappingRow($index);
    }

    public function saveForm(): void
    {
        $rule = $this->form->saveForm();              // сохранили
        $this->loadOptions($this->form->template_id); // на всякий случай обновили список опций
        $this->form->editForm($rule->id);             // перечитали из БД -> driver_option_id точно будет
        $this->dispatch('templateUpdateList');        // обновили список шаблонов
    }

    private function loadOptions(int $template_id): void
    {
        $this->options = TemplateOption::query()
            ->where('template_id', $template_id)
            ->orderBy('id')
            ->get(['id', 'name', 'key'])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.table-settings.template-price-rule-modal');
    }
}

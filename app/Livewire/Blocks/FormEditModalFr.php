<?php

namespace App\Livewire\Blocks;

use App\Models\Tkp\Manufacturer;
use Livewire\Attributes\On;
use Livewire\Component;

class FormEditModalFr extends Component
{
    public array $product_filter_select = [];
    public array $product_manufacturer_select = [];
    public array $getData = [];

    public string $message_success = '';
    public string $message_error = '';

    // обновляем список правил цены при выборе шаблона в модальном окне
    #[On('editModalFr.updateFilterFR')]
    public function updateFilterFR($template_id, $node_id = null, $conn_id = null, $product_filter_select)
    {
        $this->product_manufacturer_select = Manufacturer::get()->toArray();
        $this->product_filter_select = $product_filter_select;
    }

    // обновляем данные в модальном окне при открытии, получая их из конфигурации
    #[On('editModalFr.syncModalData')]
    public function syncModalData($getData)
    {
        $this->getData = $getData;
    }

    #[On('editModalFr.getMessage')]
    public function getMessage($message_success, $message_error)
    {
        $this->message_success = $message_success;
        $this->message_error = $message_error;
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateValueKpd($value)
    {
        if ($value === '' || $value === null) {
            $value = 0;
        }
        if ($value < 0)   $this->getData['kpd'] = 0;
        if ($value > 100) $this->getData['kpd'] = 100;
        
        $this->getData['kpd'] = $value;
        $this->updateValueCurent();
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateValueCosPhi($value)
    {
        if ($value === '' || $value === null) {
            $value = 0;
        }
        if ($value < 0) $this->getData['cos_phi'] = 0;
        if ($value > 1) $this->getData['cos_phi'] = 1;
        
        $this->getData['cos_phi'] = $value;
        $this->updateValueCurent();
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateValuePower($value)
    {
        if ($value < 0) $this->getData['p_output'] = 0;
        if ($value > 100000) $this->getData['p_output'] = 100000;
        
        $this->getData['p_output'] = $value;
        $this->updateValueCurent();
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateValueVoltage($value)
    {
        if ($value < 0) $this->getData['v_output'] = 0;
        if ($value > 11000) $this->getData['v_output'] = 11000;

        if ($value == 6000) {
            if ($this->getData['count_power_cell'] > 6) $this->getData['count_power_cell'] = 5;
        }

        if ($value == 10000) {
            if ($this->getData['count_power_cell'] < 8) $this->getData['count_power_cell'] = 8;
        }
        
        $this->getData['v_output'] = $value;
        $this->updateValueCurent();
    }

    // функция для пересчета номинального тока при изменении характеристик электродвигателя
    public function updateValueCurent($value = null)
    {
        $p = $this->getData['p_output'] ?? 0;
        $u = $this->getData['v_output'] ?? 0;
        $cos_phi = $this->getData['cos_phi'] ?? 0;
        $kpd = ($this->getData['kpd'] ?? 0) / 100;

        if ($value !== null) {
            if ($u != 0 && $cos_phi != 0 && $kpd != 0) {
                $p = ( sqrt(3) * $u * $cos_phi * $kpd * $value) / 1000;
                $p = round($p, 2);
            }
            $this->getData['p_output'] = $p;
            
        } else {
            if ($u != 0 && $cos_phi != 0 && $kpd != 0) {
                $i = ($p * 1000) / ( sqrt(3) * $u * $cos_phi * $kpd);
                $i = round($i, 2);
                $this->getData['nominalnyi_tok_ed_a'] = $i;
            }
        }

        // обновляем данные в конфигурации
        $this->save();
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateValueManufacturer($value)
    {
        $this->getData['manufacturer'] = $value;
        $this->updateValueCurent();
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateData($key, $value)
    {
        foreach ($this->getData as $k => $v) {
            if ($v === '') {
                $this->getData[$k] = null;
            }
        }

        // отключаем "Сервисный интерфейс" для серии "Стандарт" и "Минпромторг"
        if ($key == 'vfd_series' && ($value == 'Стандарт' || $value == 'Стандарт (Минпромторг)')) 
        {
            $this->getData['service_vfd'] = '';
        }
        elseif ($key == 'vfd_series' && ($value != 'Стандарт' && $value != 'Стандарт (Минпромторг)')) 
        {
            $this->getData['service_vfd'] = $this->getData['service_vfd'] ?? 'Одностороннее';
        }
        elseif (($key == 'plc_syn' && $this->getData['motor_type'] == 'A') || ($key == 'motor_type' && $this->getData['plc_syn'] == 'Да'))
        {
            $this->getData['plc_syn'] = 'Нет';
        }

        $this->save();
    }

    // сохраняем данные в конфигурации
    public function save()
    {
        $this->dispatch('syncModalDataBack', $this->getData)->to('configuration.configuration');
    }

    public function maunt()
    {
        ;
    }
    
    public function render()
    {
        return view('livewire.blocks.form-edit-modal-fr');
    }
}



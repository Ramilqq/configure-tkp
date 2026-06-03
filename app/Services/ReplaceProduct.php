<?php

namespace App\Services;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Numeric;

class ReplaceProduct
{
    private $name = '';
    private $description = '';
    private $product = '';
    private $option_price_applied = [];

    public function apply(Product $product, $option_applied = []): array
    {
        if ($product->template_id == 1) {
            $this->fr($product, $option_applied);
        }
        elseif ($product->template_id == 4) {
            $this->upp($product, $option_applied);
        }
        else {
            $this->product = $product;
            $this->name = $product->name;
            $this->description = $product->description;
        }

        return [$this->name, $this->description, $this->product->price, $this->option_price_applied ?? []];
    }

    public function fr(Product $product, $option_applied = []): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->description = 'Высоковольтный преобразователь частоты. Серия: [VFD_Series]. Технология: Мультиуровневая ШИМ [PWM_level] уровней. 
Полная мощность трансформатора напряжения: [S_trans] кВА. 
Входное напряжение: [V_input] В +/-10% 50Гц +/-5%.
Выходное напряжение: 0- [V_output] В. Выходная частота: 0- [Freq_output] Гц. Выходной ток: [I_output] А.
Тип двигателя: [Motor_type_full]. 
Перегрузочная способность: 120% - 60 секунд, 150% - моментально. Количество квадрантов управления: 2.
Материал обмоток трансформатора: [Material_trans]
Количество силовые ячеек на фазу: [Count_power_cell]. Пульсность:  [Count_power_cell_pulse]. Тип охлаждения: Воздушное.
Степень защиты: IP [IP]. Способ обслуживания: [Service_VFD]. Температура окружающей среды: 0-40 градусов по Цельсию.
Отображение: Сенсорная панель. Интерфейс связи с АСУ ТП: [Interface]. Производительность вентиляторов ВПЧ: [Airflow_rate]. 
Габаритные размеры ВПЧ: [Dimension_VFD_standart] мм. Масса ВПЧ: [VFD_Weight] кг.
Функция предзаряда по умолчанию: [PrechargeFunction].
[PrechargeFunctionExec]
Опции:[Power_cell_bypass][Sync_to_grid][Precharge][Plc_syn][Bypass_vfd][Section_in_out][Plc_pt_100]';

        $newPrice = 0;

        // собираем в массив ключи для замены из описания
        preg_match_all('/\[[^\]]+\]/', $this->description, $matches);
        $description_keys = $matches[0];
        // собираем в массив ключи для замены из наименования
        preg_match_all('/\[[^\]]+\]/', $this->product->name, $matches);
        $this->name = $this->product->name;
        $title_keys = $matches[0];
        //dd($description_keys, $option_applied);
        // замена ключей в описании на данные продукта
        foreach ($description_keys as $description_key) {
            $this->description = str_replace(
                $description_key, 
                $this->descriptionRules($description_key)($option_applied), 
                $this->description
            );
        }

        foreach ($title_keys as $title_key) {
            if ($title_key == '[VFD_Series_Start]') $title_key = '[VFD_Series_Start]-';
            if ($title_key == '[VFD_Series_End]') $title_key = '-[VFD_Series_End]';
            $this->name = str_replace(
                $title_key, 
                $this->titleRules($title_key)($option_applied), 
                $this->name
            );
        }

        // перерасчет цены
        foreach ($option_applied as $option) {
            if ((float)$option['price'] > 0 && $option['key'] == 'material_trans') {
                $this->product->price = (float)$option['price'];
                $this->option_price_applied[$option['key']] = (float)$option['price'];
            }elseif ((float)$option['price'] > 0) {
                $newPrice = $newPrice + (float)$option['price'];
                $this->option_price_applied[$option['key']] = (float)$option['price'];
            }
        }

        $this->product->price = $this->product->price + $newPrice;
    }

    public function upp(Product $product, $option_applied = []): void
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->description = 'Устройство плавного пуска высокого напряжения.
Технология: Силовые тиристоры с управлением по оптоволокну. 
Входное напряжение: [V_input] В +/-10% 50Гц +/-2%.
Номинальный ток: [I_rated] А.
Мощность подключаемого электродвигателя: [P_Output] кВт. 
Тип двигателя: [Motor_type_full].
Напряжение оперативного питания: [V_Control].
Силовой контур: Силовые тиристоры [Count_power_thyristors] шт,  Байпасный [Bypass].
Степень защиты: IP[IP]. Тип охлаждения: Воздушное естественное охлаждение. Температура окружающей среды: 0-40°С.
Габаритные размеры УПП (ДхГхВ): [Dimension_SMV]мм. Вес:[Weight_SMV]кг. Способ обслуживания: [Service_SMV].
Интерфейс связи с АСУ ТП: [Interface].

Опции:[Reverse][WSK]';

        $newPrice = 0;

        // собираем в массив ключи для замены из описания
        preg_match_all('/\[[^\]]+\]/', $this->description, $matches);
        $description_keys = $matches[0];
        // собираем в массив ключи для замены из наименования
        preg_match_all('/\[[^\]]+\]/', $this->product->name, $matches);
        $this->name = $this->product->name;
        $title_keys = $matches[0];
        //dd($description_keys, $option_applied);
        // замена ключей в описании на данные продукта
        foreach ($description_keys as $description_key) {
            $this->description = str_replace(
                $description_key, 
                $this->descriptionRules($description_key)($option_applied), 
                $this->description
            );
        }

        foreach ($title_keys as $title_key) {
            if ($title_key == '[VFD_Series_Start]') $title_key = '[VFD_Series_Start]-';
            if ($title_key == '[VFD_Series_End]') $title_key = '-[VFD_Series_End]';
            $this->name = str_replace(
                $title_key, 
                $this->titleRules($title_key)($option_applied), 
                $this->name
            );
        }

        // перерасчет цены
        foreach ($option_applied as $option) {
            if ((float)$option['price'] > 0 && $option['key'] == 'material_trans') {
                $this->product->price = (float)$option['price'];
                $this->option_price_applied[$option['key']] = (float)$option['price'];
            }elseif ((float)$option['price'] > 0) {
                $newPrice = $newPrice + (float)$option['price'];
                $this->option_price_applied[$option['key']] = (float)$option['price'];
            }
        }

        $this->product->price = $this->product->price + $newPrice;
    }

    // правила для замены в описании
    public function descriptionRules ($key = null)
    {
        $description_name = [
            '[VFD_Series]' => function ($value = []) {
                return (string)$value['vfd_series']['value'];
            },
            '[PWM_level]' => function ($value = []) {
                return 2 * (int)$value['count_power_cell']['value'] + 1;
            },
            '[S_trans]' => function ($value = []) {
                return (int)$value['s_trans']['value'];
            },
            '[V_input]' => function ($value = []) {
                return (int)$value['v_input']['value'];
            },
            '[V_output]' => function ($value = []) {
                return (int)$value['v_output']['value'];
            },
            '[Freq_output]' => function ($value = []) {
                return (int)$value['freq_output']['value'];
            },
            '[I_output]' => function ($value = []) {
                return (int)$value['i_output']['value'];
            },
            '[Motor_type_full]' => function ($value = []) {
                return (string)$value['motor_type']['value'] == 'A' ? 'Асинхронный' : 'Синхронный';
            },
            '[Material_trans]' => function ($value = []) {
                return (string)$value['material_trans']['value'];
            },
            '[Count_power_cell]' => function ($value = []) {
                return (int)$value['count_power_cell']['value'];
            },
            '[Count_power_cell_pulse]' => function ($value = []) {
                return (int)$value['count_power_cell']['value'] * 6;
            },
            '[IP]' => function ($value = []) {
                return (string)$value['ip']['value'];
            },
            '[Service_VFD]' => function ($value = []) {
                return (string)$value['service_vfd']['value'];
            },
            '[Interface]' => function ($value = []) {
                return (string)$value['interface']['value'];
            },
            '[Airflow_rate]' => function ($value = []) {
                return (string)$value['airflow_rate']['value'];
            },
            '[Dimension_VFD_standart]' => function ($value = []) {
                return (string)$value['dimension_vfd_standard']['value'];
            },
            '[VFD_Weight]' => function ($value = []) {
                return (string)$value['vfd_weight']['value'];
            },
            '[PrechargeFunction]' => function ($value = []) {
                return (string)$value['precharge_function']['value'];
            },
            '[PrechargeFunctionExec]' => function ($value = []) {
                if ($value['precharge_function']['value'] == 'Да') {
                    return PHP_EOL .'Исполнение функции предзаряда.';
                }
                return null;
            },
            '[Power_cell_bypass]' => function ($value = []) {
                if ($value['power_cell_bypass']['value'] == 'Механический') {
                    return PHP_EOL .'Байпас неисправной силовой ячейки.';
                }
                return null;
            },
            '[Sync_to_grid]' => function ($value = []) {
                if ($value['sync_to_grid']['value'] == 'Да') {
                    return PHP_EOL .'Синхронизация на Сеть. Добавляется секция реактора (СР) стандартно справа от ВПЧ. Габаритные размеры СР: '.$value['sync_to_grid']['dimension'].' мм. Производительность вентиляторов СР: '.$value['sync_to_grid']['airflow'].' м3/ч. Способ обслуживания СР: '.$value['sync_to_grid']['service'].'. Вес СР: '.$value['sync_to_grid']['weight'].'кг.';   
                }
                return null;
            },
            '[Precharge]' => function ($value = []) {
                if ($value['precharge']['value'] == 'Да') {
                    return PHP_EOL .'Предзаряд силовых ячеек. Добавляется секция предзаряда (СП) стандартно слева от ВПЧ. Габаритные размеры СП: '.$value['precharge']['dimension'].' мм. Способ обслуживания СП: '.$value['precharge']['service'].'. Вес СП: '.$value['precharge']['weight'].'кг';
                }
                return null;
            },
            '[Plc_syn]' => function ($value = []) {
                if ($value['plc_syn']['value'] == 'Да') {
                    return PHP_EOL .'ПЛК управления системой возбуждения.';
                }
                return null;
            },
            '[Bypass_vfd]' => function ($value = []) {
                if ($value['bypass_vfd']['value'] == 'Да') {
                    return PHP_EOL .'Байпас ВПЧ. Добавляется секция коммутации (СК) стандартно слева от ВПЧ. Габаритные размеры СК: '.$value['bypass_vfd']['dimension'].' мм. Способ обслуживания СК: '.$value['bypass_vfd']['service'].'. Вес СК: '.$value['bypass_vfd']['weight'].'кг.';
                }
                return null;
            },
            '[Section_in_out]' => function ($value = []) {
                if ($value['section_in_out']['value'] == 'Да') {
                    return PHP_EOL .'Секция ввода/вывода сверху. Добавляется секция ввода/вывода (СВ) стандартно слева от ВПЧ. Габаритные размеры СВ: '.$value['section_in_out']['dimension'].' мм. Способ обслуживания СВ: '.$value['section_in_out']['service'].'. Вес СВ: '.$value['section_in_out']['weight'].'кг.';
                }
                return null;
            },
            '[Plc_pt_100]' => function ($value = []) {
                if ($value['plc_pt_100']['value'] == 'Да') {
                    return PHP_EOL .'ПЛК и датчики контроля температуры обмоток и подшипников ЭД (8-10 датчиков PT100.';
                }
                return null;
            },

            //upp
            '[I_rated]' => function ($value = []) {
                return (int)$value['i_rated']['value'];
            },
            '[P_Output]' => function ($value = []) {
                return (int)$value['p_output']['value'];
            },
            '[V_Control]' => function ($value = []) {
                return (string)$value['v_control']['value'];
            },
            '[Count_power_thyristors]' => function ($value = []) {
                return (int)$value['count_power_thyristors']['value'];
            },
            '[Bypass]' => function ($value = []) {
                return (string)$value['bypass']['value'];
            },
            '[Dimension_SMV]' => function ($value = []) {
                return (string)$value['dimension_smv_standard']['value'];
            },
            '[Weight_SMV]' => function ($value = []) {
                return (string)$value['smv_weight']['value'];
            },
            '[Service_SMV]' => function ($value = []) {
                return (string)$value['service_smv']['value'];
            },
            '[Reverse]' => function ($value = []) {
                if ($value['motor_reverse']['value'] == 'Да') {
                    return PHP_EOL .'Реверс двигателя (Секция реверса). Добавляется секция реверса (СР) стандартно слева от УПП. Габаритные размеры СР: '.$value['motor_reverse']['dimension'].'мм. Способ обслуживания СР: '.$value['motor_reverse']['service'].'. Вес СР: '.$value['motor_reverse']['weight'].'кг.';
                }
                return null;
            },
            '[WSK]' => function ($value = []) {
                if ($value['wsk']['value'] == 'Да') {
                    return PHP_EOL .'Контроллер температуры и влажности.';
                }
                return null;
            },
        ];

        if (isset($description_name[$key])) {
            return $description_name[$key];
        }

        return function ($value = []) {return '';};
    }

    // правила для замены в описании
    public function titleRules ($key = null)
    {
        $title_name = [
            '[VFD_Series_Start]-' => function ($value = []) {
                if ((string)$value['vfd_series']['rename_title'] == 'Empty') return null;
                if ((string)$value['vfd_series']['rename_title'] == '') return null;
                return (string)$value['vfd_series']['rename_title'] . '-';
            },
            '[S_trans]' => function ($value = []) {
                return (string)$value['s_trans']['value'];
            },
            '[V_input_name]' => function ($value = []) {
                return substr((string)$value['v_input']['value'], 0, 2);
            },
            '[V_output_name]' => function ($value = []) {
                return substr((string)$value['v_output']['value'], 0, 2);
            },
            '[Motor_type]' => function ($value = []) {
                return (string)$value['motor_type']['value'];
            },
            '[Count_power_cell]' => function ($value = []) {
                return (string)$value['count_power_cell']['value'];
            },
            '[IP]' => function ($value = []) {
                return (string)$value['ip']['value'];
            },
            '[Interface_S]' => function ($value = []) {
                return (string)$value['interface']['rename_title'];
            },
            '-[VFD_Series_End]' => function ($value = []) {
                if ((string)$value['vfd_series']['rename_title_end'] == 'Empty') return null;
                if ((string)$value['vfd_series']['rename_title_end'] == '') return null;
                return '-'.(string)$value['vfd_series']['rename_title_end'];
            },
            '[SMV_Series_Start]' => function ($value = []) {
                if ((string)$value['smv_series']['rename_title'] == 'Empty') return null;
                if ((string)$value['smv_series']['rename_title'] == '') return null;
                return (string)$value['smv_series']['rename_title'];
            },
            '[I_rated]' => function ($value = []) {
                return (int)$value['i_rated']['value'];
            },
            
        ];

        if (isset($title_name[$key])) {
            return $title_name[$key];
        }

        return function ($value = []) {return '';};
    }

}

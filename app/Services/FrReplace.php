<?php

namespace App\Services;

use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FrReplace
{
    private $name;
    private $description;
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->description();  
    }

    public function title(array $filter): array
    {
        $option_price_applied = [];
        $option_drawing_applied = [];
        $option_name_applied = [];
        $newPrice = 0;

        // опции:
        $precharge = 'Нет';
        $bypass_vfd = 'Нет';
        $power_cell_bypass = 'Нет';
        $sync_to_grid = 'Нет';

        foreach ($this->product->productOptionPrice as $productOptionPrice) {

            if ($productOptionPrice->value == $filter[$productOptionPrice->templateOption->key]) {

                if ($productOptionPrice->price > 0 && $productOptionPrice->templateOption->key == 'material_trans') {
                    $this->name->price = $productOptionPrice->price;
                    $option_price_applied[$productOptionPrice->templateOption->key] = $productOptionPrice->price;
                }elseif ($productOptionPrice->price > 0) {
                    $newPrice = $newPrice + $productOptionPrice->price;
                    $option_price_applied[$productOptionPrice->templateOption->key] = $productOptionPrice->price;
                }


                if ($productOptionPrice->templateOption->key == 'motor_type') {
                    $this->name = str_replace('[Motor_type]', $productOptionPrice->rename_title, $this->name);
                }
                elseif ($productOptionPrice->templateOption->key == 'interface') {
                    $this->name = str_replace('[Interface_S]', $productOptionPrice->rename_title, $this->name);
                    $this->description = str_replace('[Interface]', $productOptionPrice->value, $this->description);
                }
                elseif ($productOptionPrice->templateOption->key == 'plc_syn') {
                    
                }
                elseif ($productOptionPrice->templateOption->key == 'vfd_series') {
                    if ($productOptionPrice->rename_title == 'Empty') {
                        $productOptionPrice->rename_title = null;
                    } else {
                        $productOptionPrice->rename_title = $productOptionPrice->rename_title . '-';
                    }
                    $this->name = str_replace('[VFD_Series_Start]-', $productOptionPrice->rename_title, $this->name);


                    if ($productOptionPrice->rename_title_end == 'Empty') {
                        $productOptionPrice->rename_title_end = null;
                    }
                    $this->name = str_replace('[VFD_Series_End]', $productOptionPrice->rename_title_end, $this->name);
                    $this->description = str_replace('[VFD_Series]', $productOptionPrice->value, $this->description);
                }
                elseif ($productOptionPrice->templateOption->key == 'material_trans') {
                    $this->name = str_replace('[Material_trans]', $productOptionPrice->rename_title, $this->name);
                }
                elseif ($productOptionPrice->templateOption->key == 'power_cell_bypass') {
                    $productOptionPrice->value == 'Нет' ?: $power_cell_bypass = $productOptionPrice->templateOption->name . ':' . $productOptionPrice->value;
                }
                elseif ($productOptionPrice->templateOption->key == 'sync_to_grid') {
                    $productOptionPrice->value == 'Нет' ?: $sync_to_grid = $productOptionPrice->templateOption->name . ':' . $productOptionPrice->value . '. Добавляется секция реактора (СР) стандартно справа от ВПЧ. Габаритные размеры СР: '.$productOptionPrice->dimension.' мм.
Производительность вентиляторов СР: '.$productOptionPrice->airflow.' м3/ч. Способ обслуживания СР: '.$productOptionPrice->service.'. Вес СР: '.$productOptionPrice->weight.'.';
                }
                elseif ($productOptionPrice->templateOption->key == 'ip') {
                    $this->name = str_replace('[IP]', $productOptionPrice->rename_title, $this->name);
                    $this->description = str_replace('[IP]', $productOptionPrice->value, $this->description);
                }
                elseif ($productOptionPrice->templateOption->key == 'precharge') {
                    $precharge = $productOptionPrice->value == 'Да' ? 'Да' : 'Нет';
                    $productOptionPrice->value == 'Нет' ?: $precharge = $productOptionPrice->templateOption->name . ':' . $productOptionPrice->value . '. Добавляется секция предзаряда (СП) стандартно слева от ВПЧ. Габаритные размеры СП: '.$productOptionPrice->dimension.' мм.
Способ обслуживания СП: '.$productOptionPrice->service.'. Вес СП: '.$productOptionPrice->weight.'.';
                }
                elseif ($productOptionPrice->templateOption->key == 'bypass_vfd') {
                    $productOptionPrice->value == 'Нет' ?: $bypass_vfd = $productOptionPrice->templateOption->name . ':' . $productOptionPrice->value . '. Добавляется секция коммутации (СК) стандартно слева от ВПЧ. Габаритные размеры СК: '.$productOptionPrice->dimension.' мм.
Способ обслуживания СК: '.$productOptionPrice->service.'. Вес СК: '.$productOptionPrice->weight.'.';
                    if ($productOptionPrice->value == 'Механический') {
                        $this->description = str_replace('[Airflow_rate]', $productOptionPrice->airflow, $this->description);
                    }
                }
                
            }
        }

        $precharge_function = 'Нет';
        $precharge_function_exec = 'Нет';

        //dd($this->product->productOption);
        foreach ($this->product->productOption as $productOption) {
            // замена в названии товара наименования опции, если она является переименовываемой
            if ($productOption->templateOption->key == 's_trans') {
                $this->name = str_replace('[S_trans]', $productOption->value, $this->name);
                $this->description = str_replace('[S_trans]', $productOption->value, $this->description);
            }elseif ($productOption->templateOption->key == 'v_input') {
                $this->name = str_replace('[V_input_name]',  substr($productOption->value, 0, 2), $this->name);
                $this->description = str_replace('[V_input]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'v_output') {
                $this->name = str_replace('[V_output_name]', '-'. substr($productOption->value, 0, 2), $this->name);
                $this->description = str_replace('[V_output]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'count_power_cell') {
                $this->name = str_replace('[Count_power_cell]', $productOption->value, $this->name);
                $this->description = str_replace('[PWM_level]', 2 * $productOption->value +1, $this->description);
                $this->description = str_replace('[Count_power_cell] * 6', $productOption->value * 6, $this->description);
                $this->description = str_replace('[Count_power_cell]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'freq_output') {
                $this->description = str_replace('[Freq_output]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'i_output') {
                $this->description = str_replace('[I_output]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'p_output') {
                $this->description = str_replace('[P_output]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'service_vfd') {
                $this->description = str_replace('[Service_VFD]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'airflow_rate') {
                $this->description = str_replace('[Airflow_rate]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'dimension_vfd_standard') {
                $this->description = str_replace('[Dimension_VFD_standart]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'vfd_weight') {
                $this->description = str_replace('[VFD_Weight]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'precharge_function') {
                $precharge_function = $productOption->value == 'Да' ? 'Да' : 'Нет';
                $this->description = str_replace('[PrechargeFunction]', $productOption->value, $this->description);
            }
            elseif ($productOption->templateOption->key == 'precharge_function_exec') {
                $precharge_function_exec = $productOption->value == 'Да' ? 'Да' : 'Нет';
                $this->description = str_replace('[PrechargeFunctionExec]', $productOption->value, $this->description);
            }
            
        }

        $this->descriptionAdd('Функция предзаряда по умолчанию: '.$precharge_function.'.');
        if ($precharge_function == 'Да') {
            $this->descriptionAdd('Исполнение функции предзаряда: '.$precharge_function_exec.'.');
        }  

        $this->descriptionAdd('Опции:
');
        if ($power_cell_bypass != 'Нет') {
            $this->descriptionAdd($power_cell_bypass);
        }

        if ($sync_to_grid != 'Нет') {
            $this->descriptionAdd($sync_to_grid);
        }

        if ($bypass_vfd != 'Нет') {
            $this->descriptionAdd($bypass_vfd);
        }

        if ($precharge != 'Нет') {
            $this->descriptionAdd($precharge);
        }

        $this->product->price = $this->product->price + $newPrice;

        return [$this->name, $this->description, $newPrice, $option_drawing_applied ?? [], $option_price_applied ?? [], $option_name_applied ?? []];
    }



    public function description(): void
    {
        $descriptionExample = 'Высоковольтный преобразователь частоты. Серия: [VFD_Series]. Технология: Мультиуровневая ШИМ [PWM_level] уровней. 
Полная мощность трансформатора напряжения: [S_trans] кВА. 
Входное напряжение: [V_input] В +/-10% 50Гц +/-5%.
Выходное напряжение: 0- [V_output] В. Выходная частота: 0- [Freq_output] Гц. Выходной ток: [I_output] А.
Тип двигателя: [Motor_type_full]. 
Перегрузочная способность: 120% - 60 секунд, 150% - моментально. Количество квадрантов управления: 2.
Материал обмоток трансформатора: [Material_trans]
Количество силовые ячеек на фазу: [Count_power_cell]. Пульсность:  [Count_power_cell] * 6. Тип охлаждения: Воздушное.
Степень защиты: IP [IP]. Способ обслуживания: [Service_VFD]. Температура окружающей среды: 0-40 градусов по Цельсию.
Отображение: Сенсорная панель. Интерфейс связи с АСУ ТП: [Interface]. Производительность вентиляторов ВПЧ: [Airflow_rate]. 
Габаритные размеры ВПЧ: [Dimension_VFD_standart] мм. Масса ВПЧ: [VFD_Weight] кг.
Функция предзаряда по умолчанию: [PrechargeFunction]. If [PrechargeFunction] = Да, then print Исполнение функции предзаряда: [PrechargeFunctionExec].

Опции:
Наименование опции 2.1 (Если она есть) 
Наименование опции 2.2 (Если она есть) 

Наименование опции 3 (Если она есть). Добавляется секция реактора (СР) стандартно справа от ВПЧ. Габаритные размеры СР: [Dimension_Reactor] мм.
Производительность вентиляторов СР: [Airflow_rate_Reactor] м3/ч. Способ обслуживания СР: [Service_Reactor]. Вес СР: [Weight_reactor].

Наименование опции 5 (Если она есть). Добавляется секция предзаряда (СП) стандартно слева от ВПЧ. Габаритные размеры СП: [Dimension_PreCharge] мм.
Способ обслуживания СП: [Service_PreCharge]. Вес СП: [Weight_PreCharge]. 

Наименование опции 8 (Если она есть). Добавляется секция коммутации (СК) стандартно слева от ВПЧ. Габаритные размеры СК: [Dimension_bypassVFD] мм.
Способ обслуживания СК: [Service_bypassVFD]. Вес СК: [Weight_bypassVFD]. 

Наименование опции 9 (Если она есть). Добавляется секция коммутации (СК) стандартно слева от ВПЧ. Габаритные размеры СК: [Dimension_manbypassVFD] мм.
Способ обслуживания СК: [Service_manbypassVFD]. Вес СК: [Weight_manbypassVFD].
        ';


        $this->description = 'Высоковольтный преобразователь частоты. Серия: [VFD_Series]. Технология: Мультиуровневая ШИМ [PWM_level] уровней. 
Полная мощность трансформатора напряжения: [S_trans] кВА. 
Входное напряжение: [V_input] В +/-10% 50Гц +/-5%.
Выходное напряжение: 0- [V_output] В. Выходная частота: 0- [Freq_output] Гц. Выходной ток: [I_output] А.
Тип двигателя: [Motor_type_full]. 
Перегрузочная способность: 120% - 60 секунд, 150% - моментально. Количество квадрантов управления: 2.
Материал обмоток трансформатора: [Material_trans]
Количество силовые ячеек на фазу: [Count_power_cell]. Пульсность:  [Count_power_cell] * 6. Тип охлаждения: Воздушное.
Степень защиты: IP [IP]. Способ обслуживания: [Service_VFD]. Температура окружающей среды: 0-40 градусов по Цельсию.
Отображение: Сенсорная панель. Интерфейс связи с АСУ ТП: [Interface]. Производительность вентиляторов ВПЧ: [Airflow_rate]. 
Габаритные размеры ВПЧ: [Dimension_VFD_standart] мм. Масса ВПЧ: [VFD_Weight] кг.';
    }

    public function descriptionAdd(string $text): void
    {
        $this->description = $this->description . '
' .$text;
    }

}

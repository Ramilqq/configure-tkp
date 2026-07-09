<?php

namespace App\Services\Pdf;

/**
 * Таблица технических характеристик ЧРП для PDF
 * (страница pdf/fr/technical) из применённых опций продукта.
 */
class FrSpecSheetBuilder
{
    public function build(array $option_applied = []): array
    {
        foreach ($option_applied as &$option_arr) {
            $option_arr['dimension'] = $option_arr['dimension'] ?? '0х0х0';
        }
        unset($option_arr);

        $dimension_all = $this->totalDimensions($option_applied);

        $airflowTotal = (int)($option_applied['airflow_rate']['value'] ?? 0)
            + (int)($option_applied['sync_to_grid']['airflow'] ?? 0);

        $weightTotal = (int)($option_applied['vfd_weight']['value'] ?? 0)
            + (int)($option_applied['sync_to_grid']['weight'] ?? 0)
            + (int)($option_applied['power_cell_bypass']['weight'] ?? 0)
            + (int)($option_applied['precharge']['weight'] ?? 0)
            + (int)($option_applied['bypass_vfd']['weight'] ?? 0);

        return [
            'Входные параметры ПЧ' => [
                'Полная мощность' => ($option_applied['s_trans']['value'] ?? 0) . 'кВА',
                'Входное напряжение' => ($option_applied['v_input']['value'] ?? 0) . 'В АС, 3 фазы',
                'Допустимые отклонения входного напряжения' => '±10% (до -35% снижения напряжения питающей сети с корректировкой выходных характеристик)',
                'Номинальная частота питающей сети' => '50Гц ±5%',
                'Напряжение оперативного питания' => '400В АС, 3 фазы',
                'Допустимые отклонения напряжения оперативного питания' => '±10%',
                'Суммарный коэффициент гармонических искажения по току THDi' => '≤4%, отсутствует необходимость в входном фильтре гармоник',
                'Пульсность' => '30',
            ],
            'Выходные параметры ПЧ' => [
                'Напряжение' => '0 ~ ' . ($option_applied['v_output']['value'] ?? 0) . 'В',
                'Ток' => '0 ~ ' . ($option_applied['i_output']['value'] ?? 0) . 'А',
                'Частота' => '0 ~ 50',
                'Мощность подключаемого двигателя' => ($option_applied['p_output']['value'] ?? 0) . 'кВт',
                'Перегрузочная способность' => '120% - 60с; 150% - авария',
                'Длина кабеля электродвигателя' => 'до 1000 м',
                'Минимальный шаг частоты' => '0,01Гц',
                'Форма выходной волны du/dt' => '≤1000В/мс отсутствует необходимость в выходном фильтре',
            ],
            'Прочие параметры' => [
                'КПД (без учета трансформатора)' => 'не ниже 96% при 100% нагрузке',
                'Коэффициент мощности' => '≥ 0,95 в диапазоне изменения нагрузки от 20% до 100%',
                'Время разгона/торможения' => '1 - 3600с',
                'Пульсация момента, не более' => '0,01%',
                'Производительность вентиляторов охлаждения ВПЧ' => ($option_applied['airflow_rate']['value'] ?? 0) . 'м3/ч',
                'Общая производительность вентиляторов охлаждения' => $airflowTotal . 'м3/ч',
                'Количество ячеек на фазу (всего)' => '5 (15 всего)',
                'Сейсмостойкость' => '9 баллов',
                'Температура эксплуатации без снижения характеристик' => '+0…+40°С',
                'Материал обмоток трансформатора напряжения' => $option_applied['material_trans']['value'] ?? 'Нет',
            ],
            'Опции' => [
                'Байпас неисправной силовой ячейка (Механический)' => ($option_applied['power_cell_bypass']['value'] ?? null) == 'Механический' ? 'Да' : 'Нет',
                'Байпас неисправной силовой ячейка (Электронный)' => ($option_applied['power_cell_bypass']['value'] ?? null) == 'Электронный' ? 'Да' : 'Нет',
                'Синхронизация на сеть (Секция реактора)' => ($option_applied['sync_to_grid']['value'] ?? null) == 'Да' ? 'Да' : 'Нет',
                'Предзаряд силовых ячеек' => $option_applied['precharge_function']['value'] ?? 'Нет',
                'ПЛК управления системой возбуждения' => $option_applied['plc_syn']['value'] ?? 'Нет',
                'Байпас ВПЧ (автоматический)' => ($option_applied['bypass_vfd']['value'] ?? null) == 'Опция 8' ? 'Да' : 'Нет',
                'Байпас ВПЧ (ручной)' => ($option_applied['bypass_vfd']['value'] ?? null) == 'Опция 9' ? 'Да' : 'Нет',
            ],
            'Управление' => [
                'Режим управления' => 'Векторное регулирование без датчика / Векторное регулирование с датчиком / Регулирование по U/f',
                'Тип нагрузки' => 'Синхронные и асинхронные двигатели',
                'ПЛК' => 'Цифровая обработка сигналов, модульная гибкая система на микропроцессоре и ПЛИС',
                'Функция ПИД-регулирования' => 'Программируемая',
                'Протокол связи' => $option_applied['interface']['value'] ?? 'Нет',
                'Устройство человеко-машинного интерфейса' => '10-дюймовая сенсорная панель',
                'Язык человеко-машинного интерфейса' => 'Русский / Английский',
                'Сигнализация' => 'Звуковая, световая',
                'Метод изоляции высокого/низкого напряжения' => 'Оптоволоконные кабели',
            ],
            'Корпус' => [
                'Общий габаритный размер (ДхГхВ)' => $dimension_all[0] . 'x' . $dimension_all[1] . 'x' . $dimension_all[2] . 'мм',
                'Общая масса' => $weightTotal . 'кг',
                'Ввод/вывод кабеля' => 'Снизу',
                'Тип охлаждения' => 'Воздушное',
                'Степень защиты' => isset($option_applied['ip']['value']) ? 'IP' . $option_applied['ip']['value'] : 'Нет',
                'Цвет' => 'RAL7035',
                'Способ обслуживания' => $option_applied['service_vfd']['value'] ?? 'Нет',
            ],
        ];
    }

    /**
     * Суммарные габариты (ДхГхВ) из размеров ВПЧ и опциональных секций:
     * длина складывается (секции стоят в ряд), глубина и высота — максимум.
     *
     * @return array{0:int,1:int,2:int}
     */
    private function totalDimensions(array $option_applied): array
    {
        $dimension_arr = [
            explode('х', (string)($option_applied['dimension_vfd_standard']['value'] ?? '0х0х0')),
            explode('х', (string)($option_applied['sync_to_grid']['dimension'] ?? '0х0х0')),
            explode('х', (string)($option_applied['power_cell_bypass']['dimension'] ?? '0х0х0')),
            explode('х', (string)($option_applied['precharge']['dimension'] ?? '0х0х0')),
            explode('х', (string)($option_applied['bypass_vfd']['dimension'] ?? '0х0х0')),
        ];

        $dimension_all = [0, 0, 0];

        foreach ($dimension_arr as $dimension) {
            $dimension_all[0] += (int)($dimension[0] ?? 0);
            $dimension_all[1] = max($dimension_all[1], (int)($dimension[1] ?? 0));
            $dimension_all[2] = max($dimension_all[2], (int)($dimension[2] ?? 0));
        }

        return $dimension_all;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Configuration\Configuration;
use App\Models\TableSettings\GroupOption;
use App\Models\Tkp\Tkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spipu\Html2Pdf\Html2Pdf;
use App\Services\TableSettings\DimensionSchemeResolver;

class PdfController extends Controller
{
    use AuthorizesRequests;

    public function preview()
    {
        $data = [
            'items' => [
                ['#' => 1, 'name' => 'Товар А', 'qty' => 2, 'price' => 1200],
                ['#' => 2, 'name' => 'Товар Б', 'qty' => 1, 'price' => 5800],
                ['#' => 3, 'name' => 'Очень длинное название товара В', 'qty' => 7, 'price' => 250],
            ],
            'total' => 1200*2 + 5800 + 7*250,
        ];

        $content = view('pdf.preview', compact('data'))->render();

        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
        $html2pdf->setDefaultFont('dejavusans');
        
        $html2pdf->writeHTML($content);
        $pdf = $html2pdf->output('TKP.pdf', 'S');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TKP.pdf"',
        ]);
    }

    public function show($id, $tkp_version)
    {
        $content = null;

        $tkp = Tkp::where('tkp_version', $tkp_version)->firstOrFail();
        
        $this->authorize('view', $tkp);
        
        $user = $tkp->user()->toArray();
        $tkp = $tkp->toArray();
        
        $configuration = Configuration::where('tkp_version', $tkp_version)->firstOrFail();
        $configuration = $configuration->toArray();

        $groupOptions = GroupOption::all()->toArray();
        $groupOptions = collect($groupOptions);
        
        // --- схемы габаритов (картинки) ---
        $dimensionSchemes = [];
        $resolver = app(DimensionSchemeResolver::class);

        foreach (($configuration['saved_schema']['nodes'] ?? []) as $key => $node) {
            $pid = $node['product']['hash'] ?? null;
            
            if (!empty($node['product']['price_rules_applied'])) {
                foreach($node['product']['price_rules_applied'] as $rules_key => $rules_value) {
                    $pid .= trim($rules_value['rule_key']);
                }
            }
            if (!$pid) continue;
            if (array_key_exists($pid, $dimensionSchemes)) continue;
        
            $schemes = $resolver->resolveForNode($node);

            if ($node['template_id'] == 1) {
                $configuration['saved_schema']['nodes'][$key]['product']['table_params'] = $this->frTableParams($node['product']['option_applied']);
            } else {
                $configuration['saved_schema']['nodes'][$key]['product']['table_params'] = [];
            }

            foreach ($schemes as $scheme) {
                $arr = $scheme->toArray();
                $arr['images'] = array_map(function ($img) {
                    
                    $img['abs_path'] = public_path($img['file_path']);
                    return $img;
                }, $arr['images'] ?? []);
                $dimensionSchemes[$pid][] = $arr;
            }

            if (!$schemes) {
                $dimensionSchemes[$pid] = null;
            }
        }

        // титульный лист
        $content .= view('pdf.title', compact('tkp'))->render();
        // страница с таблицей цен и характеристик
        $content .= view('pdf.table', compact('user', 'tkp', 'configuration'))->render();
        // страница с конфигурацией/схемой подулючения
        $content .= view('pdf.configuration', compact('user', 'tkp', 'configuration'))->render();

        $node_repeat = [];
        
        foreach ($configuration['saved_schema']['nodes'] ?? [] as $node) {
            //пропускаем повторяющиеся продукты (по фр хэшу) - чтобы не дублировать страницы для одинаковых продуктов
            $pid = $node['product']['hash'] ?? null;
            if(array_search($pid, $node_repeat) !== false) {
                continue;
            }
            $node_repeat[] = $pid;
            //для продуктов с шаблоном 1 (ПЧ) добавляем страницы информации, подключения и характеристик
            if ($node['template_id'] == 1) {
                //страница ифнормация чрп
                $content .= view('pdf.fr.info', compact('user', 'tkp', 'node'))->render();
                //страница подключения
                $content .= view('pdf.fr.connection', compact('user', 'tkp', 'node'))->render();
                //страница характеристик
                $content .= view('pdf.fr.technical', compact('user', 'tkp', 'node', 'groupOptions', 'dimensionSchemes'))->render();
            }
        }
        // заключительная страница
        $content .= view('pdf.end_page', [])->render();

        // генерация PDF из HTML
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
        $html2pdf->setDefaultFont('dejavusans');
        $html2pdf->writeHTML($content);
        $pdf = $html2pdf->output('TKP.pdf', 'S');

        // отдача PDF в браузер
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TKP.pdf"',
        ]);

    }

    // --- формирование массива с параметрами для таблицы технических характеристик ПЧ ---
    public function frTableParams($option_applied = [])
    {
        //dd($option_applied);
        foreach($option_applied as &$option_arr) {
            $option_arr['dimension'] = $option_arr['dimension'] ?? '0х0х0';
        }
        $dimension_arr[] = explode('х', $option_applied['dimension_vfd_standard']['value']);
        $dimension_arr[] = explode('х', $option_applied['sync_to_grid']['dimension']);
        $dimension_arr[] = explode('х', $option_applied['power_cell_bypass']['dimension']);
        $dimension_arr[] = explode('х', $option_applied['precharge']['dimension']);
        $dimension_arr[] = explode('х', $option_applied['bypass_vfd']['dimension']);
        $dimension_arr[] = explode('х', $option_applied['section_in_out']['dimension']);
        
        $dimension_all[0] = 0;
        $dimension_all[1] = 0;
        $dimension_all[2] = 0;
        //dd($dimension_arr);
        foreach ($dimension_arr as $dimension){
            $dimension_all[0] = $dimension_all[0] + (int)$dimension[0] ?? 0;
            $dimension_all[1] = $dimension_all[1] + (int)$dimension[1] ?? 0;
            $dimension_all[2] = $dimension_all[2] + (int)$dimension[2] ?? 0;
        }
        //dd($dimension_all);
        return [
            'Входные параметры ПЧ' => [
                'Полная мощность' => $option_applied['s_trans']['value'] . 'кВА' ?? 0 . 'кВА',
                'Входное напряжение' => $option_applied['v_input']['value'] . 'В АС, 3 фазы' ?? 0 . 'В АС, 3 фазы',
                'Допустимые отклонения входного напряжения' => '±10% (до -35% снижения напряжения питающей сети с корректировкой выходных характеристик)',
                'Номинальная частота питающей сети' => '50Гц ±5%',
                'Напряжение оперативного питания' => '400В АС, 3 фазы',
                'Допустимые отклонения напряжения оперативного питания' => '±10%',
                'Суммарный коэффициент гармонических искажения по току THDi' => '≤4%, отсутствует необходимость в входном фильтре гармоник',
                'Пульсность' => '30',
            ],
            'Выходные параметры ПЧ' => [
                'Напряжение' => '0 ~ ' . $option_applied['v_output']['value'] . 'В' ?? 0 . 'В',
                'Ток' => '0 ~ ' . $option_applied['i_output']['value'] . 'А' ?? 0 . 'А',
                'Частота' => '0 ~ 50',
                'Мощность подключаемого двигателя' => $option_applied['p_output']['value'] . 'кВт' ?? 0 . 'кВт',
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
                'Производительность вентиляторов охлаждения ВПЧ' => $option_applied['airflow_rate']['value'] . 'м3/ч' ?? 0 . 'м3/ч',
                'Общая производительность вентиляторов охлаждения' => (int)$option_applied['airflow_rate']['value'] ?? 0 + (int)$option_applied['sync_to_grid_airflow']['value'] ?? 0 . 'м3/ч' ?? 0 . 'м3/ч',
                'Количество ячеек на фазу (всего)' => '5 (15 всего)',
                'Сейсмостойкость' => '9 баллов',
                'Температура эксплуатации без снижения характеристик' => '+0…+40°С',
                'Материал обмоток трансформатора напряжения' => $option_applied['material_trans']['value'] ?? 'Нет',
            ],
            'Опции' => [
                'Байпас неисправной силовой ячейка (Механический)' => $option_applied['power_cell_bypass']['value'] == 'Механический' ? 'Да' : 'Нет',
                'Байпас неисправной силовой ячейка (Электронный)' => $option_applied['power_cell_bypass']['value'] == 'Электронный' ? 'Да' : 'Нет',
                'Синхронизация на сеть (Секция реактора)' => $option_applied['sync_to_grid']['value'] == 'Да' ? 'Да' : 'Нет',
                'Предзаряд силовых ячеек' => $option_applied['precharge_function']['value'] ?? 'Нет',
                'ПЛК управления системой возбуждения' => $option_applied['plc_syn']['value'] ?? 'Нет',
                'Байпас ВПЧ (автоматический)' => $option_applied['bypass_vfd']['value'] == 'Опция 8' ? 'Да' : 'Нет',
                'Байпас ВПЧ (ручной)' => $option_applied['bypass_vfd']['value'] == 'Опция 9' ? 'Да' : 'Нет',
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
                'Габаритные размеры ВПЧ (ДхГхВ)' => $option_applied['dimension_vfd_standard']['value'] . 'мм' ?? 0 . 'мм',
                'Масса ВПЧ' => $option_applied['vfd_weight']['value'] . 'кг' ?? 0 . 'кг',
                'Общий габаритный размер (ДхГхВ)' => $dimension_all[0] . 'x' . $dimension_all[1] . 'x' . $dimension_all[2] . 'мм' ?? 0 . 'мм',
                'Общая масса' => (int)$option_applied['vfd_weight']['value'] ?? 0
                    + (int)$option_applied['sync_to_grid']['weight'] ?? 0 
                    + (int)$option_applied['power_cell_bypass']['weight'] ?? 0 
                    + (int)$option_applied['precharge']['weight'] ?? 0
                    + (int)$option_applied['bypass_vfd']['weight'] ?? 0
                    + (int)$option_applied['section_in_out']['weight'] ?? 0 . 'кг' ?? 0 . 'кг',
                'Ввод/вывод кабеля' => 'Снизу',
                'Тип охлаждения' => 'Воздушное',
                'Степень защиты' => 'IP' . $option_applied['ip']['value'] ?? 'Нет',
                'Цвет' => 'RAL7035',
                'Способ обслуживания' => $option_applied['service_vfd']['value'] ?? 'Нет',
            ],
            'ЗИП' => [
                'Силовая ячейка 690В 96А' => '1 шт.',
                'Вентилятор охлаждения серии F-400' => '1 шт.',
                'Вентилятор охлаждения серии F-450' => '1 шт.',
                'Вентилятор охлаждения серии F-560' => '1 шт.',
                'Платы управления' => '1 комплект',
                'Делитель напряжения' => '1 шт.',
                'Фильтр очистки воздуха' => '1 комплект',
            ],
        ];
    }

}

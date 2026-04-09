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
        //dd($configuration);
        // --- схемы габаритов (картинки) ---
        $dimensionSchemes = [];
        $resolver = app(DimensionSchemeResolver::class);

        foreach (($configuration['saved_schema']['nodes'] ?? []) as $key => $node) {
            $pid = $node['product']['fr_hash'] ?? null;
            
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
        //dd($tkp, $configuration, $groupOptions, $dimensionSchemes);
        $content .= view('pdf.title', compact('tkp'))->render();
        $content .= view('pdf.table', compact('user', 'tkp', 'configuration'))->render();
        $content .= view('pdf.configuration', compact('user', 'tkp', 'configuration'))->render();

        //страница ифнормация чрп
        $content .= view('pdf.fr.info', compact('user', 'tkp', 'configuration'))->render();
        //страница подключения
        $content .= view('pdf.fr.connection', compact('user', 'tkp', 'configuration'))->render();
        //страница характеристик
        $content .= view('pdf.fr.technical', compact('user', 'tkp', 'configuration', 'groupOptions', 'dimensionSchemes'))->render();

        $content .= view('pdf.end_page', [])->render();


        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [0, 0, 0, 0]);
        $html2pdf->setDefaultFont('dejavusans');
        
        $html2pdf->writeHTML($content);
        $pdf = $html2pdf->output('TKP.pdf', 'S');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="TKP.pdf"',
        ]);

    }







    public function frTableParams($option_applied = [])
    {
        return [
            'Входные параметры ПЧ' => [
                'Полная мощность' => $option_applied['p_output'] . 'кВА' ?? 0 . 'кВА',
                'Входное напряжение' => $option_applied['v_input'] . 'В' ?? 0 . 'В',
                'Допустимые отклонения входного напряжения' => '±10% (до -35% снижения напряжения питающей сети с корректировкой выходных характеристик)',
                'Номинальная частота питающей сети' => '50Гц ±5%',
                'Напряжение оперативного питания' => '400В АС, 3 фазы',
                'Допустимые отклонения напряжения оперативного питания' => '±10%',
                'Суммарный коэффициент гармонических искажения по току THDi' => '≤4%, отсутствует необходимость в входном фильтре гармоник',
                'Пульсность' => '30',
            ],
            'Выходные параметры ПЧ' => [
                'Напряжение' => '0 ~ ' . $option_applied['v_output'] . 'В' ?? 0 . 'В',
                'Ток' => '0 ~ ' . $option_applied['i_output'] . 'А' ?? 0 . 'А',
                'Частота' => '0 ~ 50 / 60Гц',
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
                'Производительность вентиляторов охлаждения' => $option_applied['airflow_rate'] . 'м3/ч' ?? 0 . 'м3/ч',
                'Тепловыделения' => 'до 32кВт',
                'Количество ячеек на фазу (всего)' => '5 (15 всего)',
                'Сейсмостойкость' => '9 баллов',
                'Температура эксплуатации без снижения характеристик' => '+0…+40°С',
                'Материал обмоток трансформатора напряжения' => $option_applied['material_trans'] ?? 'Нет',
            ],
            'Опции' => [
                'Байпас неисправной силовой ячейка (Механический)' => $option_applied['power_cell_bypass'] == 'Механический' ? 'Да' : 'Нет',
                'Байпас неисправной силовой ячейка (Электронный)' => $option_applied['power_cell_bypass'] == 'Электронный' ? 'Да' : 'Нет',
                'Синхронизация на сеть (Секция реактора)' => $option_applied['sync_to_grid'] == 'Да' ? 'Да' : 'Нет',
                'Предзаряд силовых ячеек' => $option_applied['precharge_function'] ?? 'Нет',
                'ПЛК управления системой возбуждения' => $option_applied['plc_syn'] ?? 'Нет',
                'Байпас ВПЧ (автоматический)' => $option_applied['bypass_vfd'] == 'Опция 8' ? 'Да' : 'Нет',
                'Байпас ВПЧ (ручной)' => $option_applied['bypass_vfd'] == 'Опция 9' ? 'Да' : 'Нет',
            ],
            'Управление' => [
                'Режим управления' => 'Векторное регулирование без датчика / Векторное регулирование с датчиком / Регулирование по U/f',
                'Тип нагрузки' => 'Синхронные и асинхронные двигатели',
                'ПЛК' => 'Цифровая обработка сигналов, модульная гибкая система на микропроцессоре и ПЛИС',
                'Функция ПИД-регулирования' => 'Программируемая',
                'Протокол связи' => $option_applied['interface'] ?? 'Нет',
                'Устройство человеко-машинного интерфейса' => '10-дюймовая сенсорная панель',
                'Язык человеко-машинного интерфейса' => 'Русский / Английский',
                'Сигнализация' => 'Звуковая, световая',
                'Метод изоляции высокого/низкого напряжения' => 'Оптоволоконные кабели',
            ],
            'Корпус' => [
                'Габаритные размеры ВПЧ (ДхГхВ)' => $option_applied['dimension_vfd_standard'] . 'мм' ?? 0 . 'мм',
                'Масса' => $option_applied['vfd_weight'] . 'кг' ?? 0 . 'кг',
                'Ввод/вывод кабеля' => 'Снизу',
                'Тип охлаждения' => 'Воздушное',
                'Степень защиты' => 'IP' . $option_applied['ip'] ?? 'Нет',
                'Цвет' => 'RAL7035',
                'Способ обслуживания' => $option_applied['service_vfd'] ?? 'Нет',
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

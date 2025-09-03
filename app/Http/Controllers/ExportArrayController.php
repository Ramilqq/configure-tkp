<?php
namespace App\Http\Controllers;

use App\Models\TableSettings\Template;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\TemplateOption;

class ExportArrayController extends Controller
{

    public function export()
    {
        
        require '../app/ExportTable/block_boks.php';
        require '../app/ExportTable/kso.php';
        require '../app/ExportTable/powercell.php';
        require '../app/ExportTable/table_9.php';
        require '../app/ExportTable/upp.php';
        require '../app/ExportTable/ventilator.php';

        //dd($block_boks[0], $kso[0], $powercell[0], $table_9[0], $upp[0], $ventilator[0]);

        foreach(array_merge($kso, $table_9) as $table)
        {
            //создать или найти шаблон
            $template = Template::firstOrCreate(
                ['name' => $table['template']],
                ['description' => 'Шаблон для ' . $table['template']]
            );
            $template_id = $template->id;

            if(!isset($table['description'])) $table['description'] = $table['name'];

            // создать или найти продукт
            $product = Product::firstOrCreate(
                ['template_id' => $template_id, 'name' => $table['name']],
                [
                    'manufacturer_id' => '1', 
                    'description' => $table['description'],
                    //'currency' => $table['currency'],
                    'price' => $table['price'],
                    'delivery' => $table['delivery'],
                ]
            );

            //убираем поля не нужных в опциях продукта
            unset(
                $table['name'],
                $table['description'],
                $table['price'],
                $table['delivery'],
                $table['cost'],
                $table['manufacturer'],
                $table['id'],
                $table['template_id'],
                $table['template'],
            );

            $engineering = $product->engineering;

            foreach($table as $key => $value)
            {

                switch ($key) {
                    case 'PNR':
                        $engineering['pnr'] = $value;
                        break;
                    case 'PO':
                        $engineering['po'] = $value;
                        break;
                    case 'kd':
                        $engineering['kd'] = $value;
                        break;
                    case 'mounting':
                        $engineering['mounting'] = $value;
                        break;
                    case 'assembly':
                        $engineering['assembly'] = $value;
                        break;
                    case 'smr_shmr':
                        $engineering['smr_shmr'] = $value;
                        break;
                    case 'pnr_po':
                        $engineering['pnr_po'] = $value;
                        break;
                    case 'pir':
                        $engineering['pir'] = $value;
                        break;
                    default:
                        
                        // 1) берём/создаём TemplateOption
                        $templateOption = TemplateOption::firstOrCreate(
                            ['template_id' => $template_id, 'key' => $key],
                            ['name' => $key, 'fields' => [$value], 'group_id' => 3]
                        );

                        // 2) если значение новое — добавим в fields
                        $fields = (array) ($templateOption->fields ?? []);
                        if (!in_array($value, $fields, true)) {
                            $fields[] = $value;
                            $templateOption->fields = $fields; // JSON cast
                            $templateOption->save();
                        }

                        // 3) создаём/обновляем ProductOption
                        ProductOption::updateOrCreate(
                            [
                                'template_option_id' => $templateOption->id,
                                'product_id'         => $product->id,
                            ],
                            ['value' => $value]
                        );

                        break;
                }

                $product->engineering = $engineering;
                $product->save();
                
            }

            //dd($product, $table);
        }

    }

    


}

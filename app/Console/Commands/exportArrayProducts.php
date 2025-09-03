<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TableSettings\Template;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\TemplateOption;

class exportArrayProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-array-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Получение данных');


        require 'app/ExportTable/block_boks.php';
        require 'app/ExportTable/kso.php';
        require 'app/ExportTable/powercell.php';
        require 'app/ExportTable/table_9.php';
        require 'app/ExportTable/upp.php';
        require 'app/ExportTable/ventilator.php';

        $tables = array_merge($block_boks, $kso, $powercell, $table_9, $upp, $ventilator);

        $this->info('Полученные данные='.count($tables));


        foreach($tables as $table)
        {

            $this->info('Создание= '.$table['name']);

            //создать или найти шаблон
            $template = Template::firstOrCreate(
                ['name' => $table['template']],
                ['description' => 'Шаблон для ' . $table['template']]
            );
            $template_id = $template->id;

            if(!isset($table['description'])) $table['description'] = $table['name'];
            $table['price'] = str_replace(',', '.', $table['price']);



            // создать или найти продукт
            $product = Product::firstOrCreate(
                ['name' => $table['name']],
                [
                    'template_id' => $template_id, 
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
                        if (isset($engineering['pnr'])) $engineering['pnr'] = $value;
                        break;
                    case 'PO':
                        if (isset($engineering['po'])) $engineering['po'] = $value;
                        break;
                    case 'kd':
                        if (isset($engineering['kd'])) $engineering['kd'] = $value;
                        break;
                    case 'mounting':
                        if (isset($engineering['mounting'])) $engineering['mounting'] = $value;
                        break;
                    case 'assembly':
                        if (isset($engineering['assembly'])) $engineering['assembly'] = $value;
                        break;
                    case 'smr_shmr':
                        if (isset($engineering['smr_shmr'])) $engineering['smr_shmr'] = $value;
                        break;
                    case 'pnr_po':
                        if (isset($engineering['pnr_po'])) $engineering['pnr_po'] = $value;
                        break;
                    case 'pir':
                        if (isset($engineering['pir'])) $engineering['pir'] = $value;
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

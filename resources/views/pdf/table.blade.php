<?php 
    function utf8_wordwrap($string, $width=75, $break="\n", $cut=false){
        if($cut) {
            // Match anything 1 to $width chars long followed by whitespace or EOS,
            // otherwise match anything $width chars long
            $search = '/(.{1,'.$width.'})(?:\s|$)|(.{'.$width.'})/uS';
            $replace = '$1$2'.$break;
        }
        else {
            // Anchor the beginning of the pattern with a lookahead
            // to avoid crazy backtracking when words are longer than $width
            $pattern = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
            $replace = '$1'.$break;
        }
        return preg_replace($search, $replace, $string);
    }
?>

<style>
    .pdf_page_header {
        width: 100%;
        border: none;
        /*background-color: #DDDDFF;
        border-bottom: solid 1mm #AAAADD;*/
        padding: 20px; 
        padding-top: 12mm;
        font-size: 12px;
        left:80px;
        font-family: dejavusans;
    }
    .tkp_table {
        position: relative;
        table-layout: fixed;
        font-size: 9pt;
        padding-top:0px;
        padding-bottom:10px;
        display: table;
        /*width: 500px;*/
        border: 1px solid black;
        border-collapse: collapse;
        width:100%;
        border-spacing:0;
        padding: 50px;
    }
    
    .table_header {
        background-color: #119897;
        text-align: center;
    }
    .tkp_table td,th {
        border: 1px solid black;
        pading:2px;
    }
    
</style>
<page orientation="L" backtop="20mm" backbottom="10mm" backleft="0mm" backright="0mm">
    <page_header>
        <div class="pdf_page_header">
            <span><strong><b>ПРОЕКТ:</b> </strong><?php echo $tkp['project_name'];?></span><br>
            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
            <div style="position: absolute; right: 10mm;top:30px;">
                <img src="<?= public_path('assets/image/pdf/logo.png') ?>" style="width:100px; height:50px;">
            </div>
        </div>
    </page_header>



    <table class="tkp_table" ыstyle="width: 100%; table-layout: fixed;">
        <thead>
            <col style="width:2%">
            <col style="width:10%;overflow-wrap: break-word;">
            <col style="width:40%">
            <col style="width:5%">
            <col style="width:10%">
            <col style="width:10%">
            <col style="width:5%">
            <col style="width:10%">
            <col style="width:10%">
            <tr style="text-align: center;">
                <td colspan="9" style="border: none;text-align: center;">ТЕХНИКО-КОММЕРЧЕСКОЕ ПРЕДЛОЖЕНИЕ- СПЕЦИФИКАЦИЯ ОБОРУДОВАНИЯ И РАБОТ №1</td>
            </tr>
            
            <tr style="text-align: center;">
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
                <th style="border-top: none;border-right: none;border-left: none;text-align: left;"> </th>
            </tr>
            <tr style="text-align: center;" class="table_header">
                <th>№ пп</th>
                <th>Тип, марка изделия</th>
                <th>Наименование</th>
                <th>Кол-во</th>
                <th>Стоимость единицы, в валюте. без НДС</th>
                <th>Стоимость единицы, руб. без НДС</th>
                <th>Курс</th>
                <th>Общая сумма, руб. без НДС</th>
                <th>Примечание</th>
            </tr>
        </thead>
        <tbody>
            <!--VFD-->
            <?php
                $i = 1;
                $summ_vfd = 0;
                $summ_opt = 0;
                $summ_kso = 0;
                $summ_other = 0;
                $summ_service = 0;
                $summ_block_boks = 0;
                $summ_upp = 0;
                
                $product_data = [];

                $products = array_merge($configuration['saved_schema']['nodes'], $configuration['saved_schema']['connections'], $configuration['saved_schema']['other']);

                foreach($products as $key => $val) {
                    $id = $val['product']['hash'] ?? $val['product']['id'];
                    if (isset($val['params'])) $val = $val['params'];

                    if (isset($product_data[$id]))
                    {
                        $product_data[$id]['product']['count'] += 1;
                        continue;
                    }

                    $val['product']['count'] = 1;
                    $product_data[$id] = $val;
                }
                
                foreach($product_data as $key => $val) {
            ?>


            <tr style="vertical-align: top;">
                <td style="text-align: center;"><?php echo $i;?></td>
                <td style=""><?php echo wordwrap($val['product']['name'], 12, "<br />\n", true); ?></td>
                <td><?php echo $val['product']['description'];?></td>
                <td style="text-align: center;"><?php echo (int)$val['product']['count'];?></td>
                <td style="text-align: center;"><?php echo $val['product']['currency'] .' '. number_format(ceil($val['product']['price']),0,',',' ');?></td>
                <td style="text-align: center;"><?php echo number_format(ceil((float)$val['product']['price'] * (float)$val['product']['currency_val']),0,',',' ');?>р.</td>
                <td style="text-align: center;"><?php echo $val['product']['currency_val'];?></td>
                <td style="text-align: center;"><?php echo number_format(ceil((($val['product']['price'] * (float)$val['product']['currency_val']) * (int)$val['product']['count'])),0,',',' ');?>р.</td>
                <td><?php echo 'Comment';?></td>
            </tr>
            

            <?php
                $i++;
                }  
            ?>
            
            <!--ИТОГО ОБОРУДОВАНИЕ-->
            <tr class="table_header" style="vertical-align: top;">
                <td></td>
                <td></td>
                <td style="text-align: left;">ИТОГО ОБОРУДОВАНИЕ :</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;"><?php echo number_format(ceil($tkp['pay_params']['resault_total']),0,',',' ');?>р.</td>
                <td></td>
            </tr>
            <!--ИТОГО НДС-->
            <tr class="table_header" style="vertical-align: top;">
                <td></td>
                <td></td>
                <td style="text-align: left;">ИТОГО</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;"><?php echo number_format(ceil($tkp['pay_params']['resault_total']),0,',',' ');?>р.</td>
                <td></td>
            </tr>
            <tr style="vertical-align: top;">
                <td></td>
                <td></td>
                <td style="text-align: left;">НДС <?php echo $tkp['pay_params']['nds'];?>%</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;"><?php echo number_format(ceil(($tkp['pay_params']['resault_nds'])),0,',',' ');?>р.</td>
                <td></td>
            </tr>
            <tr class="table_header" style="vertical-align: top;">
                <td></td>
                <td></td>
                <td style="text-align: left;">ИТОГО С УЧЕТОМ НДС</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: center;"><?php echo number_format(ceil($tkp['pay_params']['resault_total_nds']),0,',',' ');?>р.</td>
                <td></td>
            </tr>
        </tbody>
        <tbody>
            <tr style="text-align: center;">
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;">Дата:</td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"><?php echo date('d.m.Y', strtotime($tkp['created_at'])); ?></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
            </tr>
            <!--tr style="text-align: center;">
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;">Валюта:</td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"><?php echo '-' ?></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
                <td style="border-top: none;border-right: none;border-left: none;text-align: left; border-bottom: none;"></td>
            </tr-->
        </tbody>
    </table>
    
    
    <table class="tkp_table">
        <tbody>
            <col style="width:100%">
            <tr>
                <td style="border:0px"><b>Cрок поставки(дней):</b> <?php echo $tkp['delivery_params']['delivery_time'];?> рабочик дней от даты подписания договора между сторонами и получением аванса</td>
            </tr>
            <tr>
                <td style="border:0px"><b>Место поставки:</b> <?php echo $tkp['delivery_params']['delivery_location'];?></td>
            </tr>
            <tr>
                <td style="border:0px"><b>Условия оплаты оборудования:</b> <?php echo $tkp['delivery_params']['payment_scheme'];?></td>
            </tr>
            <tr>
                <td style="border:0px"><b>Условия оплаты услуг:</b> <?php echo $tkp['delivery_params']['payment_scheme'];?></td>
            </tr>
            <tr>
                <td style="border:0px"><b>Гарантийный срок:</b> <?php echo $tkp['delivery_params']['offer_is_valid'];?></td>
            </tr>
            <tr>
                <td style="border:0px"><b>Примечание:</b> Предложение действительно в течении 30 дней при курсе ЦБ, не превышающем <?php echo '-' ;?> руб. за 1 <?php echo '-';?>, при неизменности технических требований.</td>
            </tr>
        </tbody>
    </table>
    
    <page_footer>
        <table style="position: relative;border: none;padding: 10px;table-layout: fixed;display: table;border-collapse: collapse;width:100%;border-spacing:0;font-family: dejavusans;">
            <tr>
                <td style="padding-bottom: 5px;"><?php echo $user['name']." E-mail: ".$user['email']." Телефон: "/*. $user['phone']*/ ;?></td>
            </tr>
            <tr>
                <td style="border: none;font-size:10px;vertical-align: top;"><?php echo 'Комментарий';?></td>
            </tr>
        </table>
    </page_footer>
</page>
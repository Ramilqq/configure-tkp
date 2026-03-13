<style>
    .table_header2 {
        text-align: center;
    }
    .vfd_info_table {
        display: table;
        width: 900px;
        border: 1px solid black;
        border-collapse: collapse;
        width:100%;
        border-spacing:0;
        padding: 50px;
        font-family: dejavusans;
    }
    .vfd_info_table td {
        border: 1px solid black;
        vertical-align: top;
    }
</style>


    
    
            
            <?php
                $node_repeat = [];
                foreach($configuration['saved_schema']['nodes'] as $node){
                    //dd($configuration['saved_schema']['nodes']);
                    
                    $pid = $node['product']['id'] ?? null;
                    if (!empty($node['product']['price_rules_applied'])) {
                        foreach($node['product']['price_rules_applied'] as $rules_key => $rules_value) {
                            $pid .= trim($rules_value['rule_key']);
                        }
                    }

                    if(array_search($pid, $node_repeat) !== false) {
                        continue;
                    }
                    $node_repeat[] = $pid;

                    $table = [];
                    foreach($node['product']['product_option'] as $oprion){
                        $name = $groupOptions->where('id', $oprion['get_name']['group_id'])->first() ?: ['name' => 'Прочее'];
                        $table[$name['name']][$oprion['get_name']['name']] = $oprion['value'];       
                    }
            ?>
            <page orientation="portrait" backimg="<?= public_path('assets/image/pdf/bg6.png') ?>" backtop="22mm" backbottom="0mm" backleft="10mm" backright="10mm">
                <page_header>
                    <div class="pdf_page_header">
                        <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
                        <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
                        <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
                    </div>
                </page_header>
                    <h4 style="text-align: center;">Технические данные</h4>
                    <h4 style="text-align: center;width: 100%;">{{$node['product']['name']}}</h4>
            <?php
                    
                    foreach($table as $title => $row){
            ?>
                        
                        <table class="vfd_info_table">
                            <col style="width:50%">
                            <col style="width:50%">
                            <tbody>
                                <tr class="table_header">
                                    <td colspan=2>{{$title}}</td>
                                </tr>
                                <tr class="table_header2">
                                    <td>Параметр</td>
                                    <td>Значение</td>
                                </tr>

            <?php
                        foreach($row as $key => $val){
            ?>
                            <tr>
                                <td>{{$key}}</td>
                                <td>{{$val}}</td>
                            </tr>
            <?php
                        }
            ?>
                            </tbody>
                        </table>
                
            <?php
                    }
            ?>
                </page>
            
            <?php
            
            $schemes_prod = $pid ? ($dimensionSchemes[$pid] ?? []) : [];

            foreach($schemes_prod as $scheme) {
                
                if ($scheme && !empty($scheme['images'])) {
                    $printed = 0;
                    
                    foreach ($scheme['images'] as $img) {
            ?>
                <page orientation="portrait" backimg="<?= public_path('assets/image/pdf/bg6.png') ?>" backtop="22mm" backbottom="0mm" backleft="10mm" backright="10mm">
                    <page_header>
                        <div class="pdf_page_header">
                            <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
                            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
                            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
                        </div>
                    </page_header>
                    
                        <h4 style="text-align: center;">Габаритный чертеж: {{$node['product']['name']}}</h4>
                        <?php
                        
                            $abs = $img['abs_path'] ?? '';
                            if (!$abs || !file_exists($abs)) continue;
                
                            $title = $img['title'] ?? '';
                            
                            echo '<div style="text-align:center; margin-top: 8mm;">';
                            if ($title) {
                                echo '<div style="font-size:12px; margin-bottom:3mm; margin-right:3mm;"><b>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</b></div>';
                            }
                            echo '<img src="' . $abs . '" style="width:180mm; height:auto;" />';
                            echo '</div>';
                
                            $printed++;
                        ?>

                </page>
            <?php
                    }
                        
                    if ($printed === 0) {
                        echo '<div style="text-align:center; color:#666; margin-top:30mm;">Схема габаритов не найдена (файлы отсутствуют)</div>';
                    }
                } else {
                    echo '<div style="text-align:center; color:#666; margin-top:30mm;">Схема габаритов не задана</div>';
                }
            }
            
            ?>
            <?php
                }
            ?>

            
        




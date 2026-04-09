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
                    $pid = $node['product']['fr_hash'] ?? null;

                    if(array_search($pid, $node_repeat) !== false) {
                        continue;
                    }
                    $node_repeat[] = $pid;
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
                    <h4 style="text-align: center;">{{$node['product']['name']}}</h4>
                    <table class="vfd_info_table">
                        <col style="width:50%">
                        <col style="width:50%">
                        <tbody>
                            <tr class="table_header2">
                                <td>Параметр</td>
                                <td>Значение</td>
                            </tr>
                    <?php
                        foreach($node['product']['table_params'] as $table_title => $table_data){
                    ?>
                            
                                    <tr class="table_header">
                                        <td colspan=2>{{$table_title}}</td>
                                    </tr>
                    <?php
                            foreach($table_data as $key => $val){
                    ?>
                                    <tr>
                                        <td>{{$key}}</td>
                                        <td>{{$val}}</td>
                                    </tr>
                    <?php
                            }
                        }
                        
                    ?>
                        </tbody>
                    </table>








                    <h4 style="text-align: center;">Показатели надежности и гарантия</h4>
                    <table class="vfd_info_table">
                        <col style="width:50%">
                        <col style="width:50%">
                        <tr class="table_header2">
                            <td>Параметр</td>
                            <td>Значение</td>
                        </tr>
                        <tr class="table_header">
                            <td colspan=2>Показатели надежности</td>
                        </tr>
                        <tbody>
                    <?php
                        if (!empty($node['product']['indicators_reliability'][0])) {
                            foreach($node['product']['indicators_reliability'][0]['indicators'] as $key => $value) {
                                echo '<tr>
                                    <td>'.$value['name'].'</td>
                                    <td>'.$value['value'].'</td>
                                </tr>';
                            }
                        }

                    
                    ?>
                        </tbody>
                    </table>
            </page>










            
            


            <?php
            if ($node['product']['drawing']) {
                $img = public_path('storage/drawing/'.$node['product']['drawing'].'.jpg');

                if (file_exists($img)) {

                    ?>
                        <page orientation="L" backimg="<?= public_path('assets/image/pdf/bg6.png') ?>" backtop="22mm" backbottom="0mm" backleft="10mm" backright="10mm">
                            <page_header>
                                <div class="pdf_page_header">
                                    <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
                                    <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
                                    <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
                                </div>
                            </page_header>
                            
                                <h4 style="text-align: center;">Габаритный чертеж: {{$node['product']['name']}}</h4>
                                <?php
                                
                                    echo '<div style="text-align:center; margin-top: 8mm;">';
                                    if ($node['product']['name']) {
                                        echo '<div style="font-size:12px; margin-bottom:3mm; margin-right:3mm;"><b>' . htmlspecialchars($node['product']['name'], ENT_QUOTES, 'UTF-8') . '</b></div>';
                                    }
                                    echo '<img src="'.$img.'" style="width:180mm; height:auto;" />';
                                    echo '</div>';
                                ?>

                        </page>

                    <?php
                }
            }
            ?>


            <?php 
            if ($node['product']['option_drawing_applied']) {
                foreach ($node['product']['option_drawing_applied'] as $img) {
                    
                    $img = public_path('storage/drawing/'.$img.'.jpg');
                    if (!file_exists($img)) continue;

                    ?>
                        <page orientation="L" backimg="<?= public_path('assets/image/pdf/bg6.png') ?>" backtop="22mm" backbottom="0mm" backleft="10mm" backright="10mm">
                            <page_header>
                                <div class="pdf_page_header">
                                    <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
                                    <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
                                    <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
                                </div>
                            </page_header>
                            
                                <h4 style="text-align: center;">Габаритный чертеж: {{$node['product']['name']}}</h4>
                                <?php
                                
                                    echo '<div style="text-align:center; margin-top: 8mm;">';
                                    if ($node['product']['name']) {
                                        echo '<div style="font-size:12px; margin-bottom:3mm; margin-right:3mm;"><b>' . htmlspecialchars($node['product']['name'], ENT_QUOTES, 'UTF-8') . '</b></div>';
                                    }
                                    echo '<img src="'.$img.'" style="width:180mm; height:auto;" />';
                                    echo '</div>';
                                ?>

                        </page>
                    <?php
                }
            }
            ?>
                



            <?php
                }
            ?>

            
        




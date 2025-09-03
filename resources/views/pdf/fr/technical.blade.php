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
                    if(array_search($node['product']['id'], $node_repeat) !== false) {
                        continue;
                    }
                    $node_repeat[] = $node['product']['id'];

                    $table = [];
                    foreach($node['product']['product_option'] as $oprion){
                        $name = $groupOptions->where('id', $oprion['get_name']['group_id'])->first() ?: ['name' => 'Прочее'];
                        $table[$name['name']][$oprion['get_name']['name']] = $oprion['value'];       
                    }
            ?>
            <page orientation="portrait" backimg="assets/image/pdf/bg6.png" backtop="22mm" backbottom="0mm" backleft="10mm" backright="10mm">
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
                    //dd($table);
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

                <page orientation="portrait" backimg="assets/image/pdf/bg6.png" backtop="22mm" backbottom="0mm" backleft="10mm" backright="10mm">
                    <page_header>
                        <div class="pdf_page_header">
                            <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
                            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
                            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
                        </div>
                    </page_header>
                    <h4 style="text-align: center;">Габаритный чертеж: {{$node['product']['name']}}</h4>
                    <!--img src="assets/image/size/<?php echo '$fullvfd->dimension_draw';?>.jpg" style="width:180mm;height:200mm;"-->
                </page>
            <?php
                }
            ?>

            
        




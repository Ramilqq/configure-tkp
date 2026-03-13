
<?php
    foreach($data['items'] as $item) {
?>
    <page backimg="<?= public_path('assets/image/pdf/bg2_shu.png') ?>" backtop="0mm" backbottom="0mm" backleft="0mm" backright="0mm">

        <div class="title_name">
            <p>
                
            <span><strong><h1>ТЕХНИКО-КОММЕРЧИСКОЕ<br/>ПРЕДЛОЖЕНИЕ</h1></strong></span>
            
            </p>
        </div>
        
        <div class="title_page">
            <p>
                <span><strong>ПРОЕКТ: </strong><?php echo $item['name'];?></span>
            </p>
            <p>
                <span><strong>ЗАКАЗЧИК: </strong><?php echo $item['price'];?></span>
            </p>
        </div>
    </page>
<?php
    }
?>
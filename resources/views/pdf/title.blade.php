<style>
    .title_name{
        position: relative;
        top: 240px;
        width: 700px;
        left: 66px;
    }
    .title_name span {
        color:#fff;
    }
    .title_page{
        position: relative;
        top: 450px;
        width: 700px;
        left: 66px;
    }
    .title_page span {
        color:#fff;
    }
</style>
<page backimg="<?= public_path('assets/image/pdf/bg2_shu.png') ?>" backtop="0mm" backbottom="0mm" backleft="0mm" backright="0mm">

    <div class="title_name">
        <p>
            
        <span><strong><h1>ТЕХНИКО-КОММЕРЧИСКОЕ<br/>ПРЕДЛОЖЕНИЕ</h1></strong></span>
        
        </p>
    </div>


    <div class="title_page">
        <p>
            <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span>
        </p>
        <p>
            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span>
        </p>
        <p>
            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span>
        </p>
    </div>
</page>

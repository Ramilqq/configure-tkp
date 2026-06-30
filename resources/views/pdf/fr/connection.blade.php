<style>
    .pdf_page_header {
        width: 100%;
        border: none;
        /*background-color: #DDDDFF;
        border-bottom: solid 1mm #AAAADD;*/
        padding: 20px; 
        padding-top: 12mm;
        padding-right: 120px;
        font-size: 12px;
        left:80px;
        font-family: dejavusans;
    }
</style>
<page orientation="portrait" backtop="20mm" backbottom="0mm" backleft="0mm" backright="0mm">
    <page_header>
        <div class="pdf_page_header">
            <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
            <div style="position: absolute; right: 10mm;top:30px;">
                <img src="<?= public_path('assets/image/pdf/logo.png') ?>" style="width:100px; height:50px;">
            </div>
        </div>
    </page_header>
    <h4 style="text-align: center;">9. Условная схема подключения</h4>
    
    @if($node['product']['option_applied']['vfd_series'] == 'Стандарт' || $node['product']['option_applied']['vfd_series'] == 'Стандарт (Минпромторг)')
    {{-- scheme.jpg 1832x3104 (соотношение ~0.59) — задаём ширину пропорционально высоте, чтобы не растягивать --}}
    <div style="text-align:center;">
        <img src="<?= public_path('assets/image/pdf/scheme.jpg') ?>" style="width:531px;height:900px;">
    </div>
    @else
    {{-- scheme_c.jpg 669x1164 (соотношение ~0.575) --}}
    <div style="text-align:center;">
        <img src="<?= public_path('assets/image/pdf/scheme_c.jpg') ?>" style="width:518px;height:900px;">
    </div>
    @endif
</page>

<page orientation="L" backimg="" backtop="20mm" backbottom="0mm" backleft="10mm" backright="10mm">
    <page_header>
        <div class="pdf_page_header" style="font-family: dejavusans;">
            <span><strong>ПРОЕКТ:<b>ПРОЕКТ:</b> </strong><?php echo $tkp['project_name'];?></span><br>
            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
            <div style="position: absolute; right: 10mm;top:30px;">
                <img src="<?= public_path('assets/image/pdf/logo.png') ?>" style="width:100px; height:50px;">
            </div>
        </div>
    </page_header>
    <h4 style="text-align: center;">Схема коммутации оборудования</h4>
    <table>
        <tbody>
            <tr>
                <td style="border: 0px;text-align: center;height:140mm;width:277mm;">
                    <?php if(file_exists($configuration['image'])){ ?>
                        <img src="<?php echo $configuration['image'];?>" style="height:140mm;margin: 0 auto;">
                    <?php }?>
                </td>
            </tr>
        </tbody>
    </table>
</page>

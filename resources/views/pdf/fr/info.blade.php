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
    .protect_table {
        position: relative;
        padding: 0px;
        width: 700px;
        display: table;
        table-layout: fixed;
        
        border-collapse: collapse;
        border-spacing:0;
    }
    .protect_table td {
        border: 1px solid black;
    }
    ul li { 
        padding-bottom: 2mm;
    }
</style>
<page orientation="portrait" backtop="25mm" backbottom="0mm" backleft="10mm" backright="10mm">
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
    <h4 style="text-align: center;">1. Назначение</h4>
    <p>RU-DRIVE VFD – это универсальные преобразователи частоты, предназначенные для управления частотой вращения трехфазных асинхронных и синхронных двигателей мощностью от 200 кВт до 80 МВт и с номинальным напряжением от 3.0 до 13,8 кВ. Управление частотой вращения электродвигателей осуществляется за счет создания на выходе преобразователя частоты напряжения заданной частоты и амплитуды.</p>
    <h4 style="text-align: center;">2. Топология преобразователя частоты</h4>
    <p>Преобразователи частоты серии RU-DRIVE VFD реализованы по схеме многоуровневого инвертора напряжения с интегрированным многообмоточным фазосдвигающим трансформатором.</p>
    <table style="position: relative;padding: 0px;display: table;width: 100%;">
        <col style="width: 20%;">
        <col style="width: 60%;">
        <col style="width: 20%;">
        <tbody>
            <tr>
                <td style="border: none;"></td>
                <td style="border: none;">
                    <img src="<?= public_path('assets/image/pdf/pdf_static_ch2.jpg') ?>" style="width:500px;height:250px;">
                </td>
                <td style="border: none;"></td>
            </tr>
        </tbody>
    </table>
    <p>Первичная обмотка многообмоточного фазосдвигающего трансформатора сухого типа подключается непосредственно к трехфазной сети. Трансформатор осуществляет преобразование напряжение сети в систему трехфазных напряжений, сдвинутых друг относительно друга по фазе. Каждая вторичная обмотка трансформатора сдвинута по фазе и питает свою силовую ячейку.</p>
    <h4 style="text-align: center;">3. Принцип формирования выходного напряжения</h4>
    <p>Выходное напряжение RU-DRIVE VFD формируется путем суммирования выходных напряжений силовых ячеек на основе IGBT-модулей низкого напряжения, соединенных друг с другом последовательно и равных по количеству для каждой фазы.</p>
    <table style="position: relative;padding: 0px;display: table;width: 100%;">
        <tbody>
            <tr>
               <!-- <td style="width: 5%; text-align: left;border: none;"></td>-->
                <td style="width: 100%; text-align: center;border: none;">
                    <img src="<?= public_path('assets/image/pdf/pdf_static_ch3.jpg') ?>" style="width:700px;height:300px;">
                </td>
               <!--  <td style="width: 5%; text-align: right;border: none;"></td>-->
            </tr>
        </tbody>
    </table>
</page>
<page orientation="portrait" backtop="25mm" backbottom="0mm" backleft="10mm" backright="10mm"  backimg="<?= public_path('assets/image/pdf/bg6.png') ?>">
    <page_header>
        <div class="pdf_page_header">
            <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
        </div>
    </page_header>
    <h4 style="text-align: center;">4. Влияние на питающую сеть</h4>
    <p>Использование входного силового многообмоточного фазосдвигающего трансформатора и многопульсная схема выпрямления позволяет реализовать гальваническую развязку силовых ячеек с питающей сетью и обеспечивает малые гармонические искажения входного тока и напряжения. Каждая силовая ячейка представляет собой 6-импульсный неуправляемый диодный выпрямитель.</p>
    <table style="position: relative;padding: 0px;display: table;width: 100%;">
        <tbody>
            <tr>
               <td style="width: 20%; text-align: left;border: none;"></td>
                <td style="width: 60%; text-align: center;border: none;">
                    <img src="<?= public_path('assets/image/pdf/pdf_static_ch4.jpg') ?>" style="width:300px;height:200px;">
                </td>
               <td style="width: 20%; text-align: right;border: none;"></td>
            </tr>
            <tr>
                <td style="width: 20%; text-align: left;border: none;"></td>
                <td style="width: 60%; text-align: center;border: none;">Форма напряжения и тока на входе преобразователя частоты</td>
                <td style="width: 20%; text-align: right;border: none;"></td>
            </tr>
        </tbody>
    </table>
    <p>Величина коэффициента искажения синусоидальности кривой напряжения и тока соответствует самым строгим требованиям стандарта IEEE519-1992 на содержание гармоник в силовых электрических системах.</p>
    <p>Преобразователь частоты не требует установки фильтра на входе, а также устройств для компенсации реактивной мощности.</p>
    <h4 style="text-align: center;">5.	Влияние выходного напряжения преобразователя частоты на двигатель</h4>
    <p>Использование многоуровневой схемы формирования выходного напряжения позволяет:</p>
    <ul>
        <li>Обеспечить низкий уровень выходных гармоник и практически синусоидальную форму выходного напряжения, без применения выходного фильтра;</li>
        <li>Исключить нагрев двигателя, вызываемый гармоническими составляющими;</li>
        <li>Снизить колебания крутящего момента на валу электродвигателя;</li>
        <li>Обеспечить низкое значение du/dt и малый шаг формирования кривой напряжения, и как следствие, малое воздействие на двигатель и изоляцию кабеля;</li>
        <li>Формировать высокое напряжение на выходе преобразователя частоты без повышающего трансформатора.</li>
    </ul>
    <table style="position: relative;padding: 0px;display: table;width: 100%;">
        <tbody>
            <tr>
               <td style="width: 20%; text-align: left;border: none;"></td>
                <td style="width: 60%; text-align: center;border: none;">
                    <img src="<?= public_path('assets/image/pdf/pdf_static_ch5.jpg') ?>" style="width:300px;height:200px;">
                </td>
               <td style="width: 20%; text-align: right;border: none;"></td>
            </tr>
            <tr>
                <td style="width: 20%; text-align: left;border: none;"></td>
                <td style="width: 60%; text-align: center;border: none;">Форма напряжения и тока на выходе преобразователя частоты</td>
                <td style="width: 20%; text-align: right;border: none;"></td>
            </tr>
        </tbody>
    </table>
</page>
<page orientation="portrait" backtop="25mm" backbottom="0mm" backleft="10mm" backright="10mm"  backimg="<?= public_path('assets/image/pdf/bg6.png') ?>">
    <page_header>
        <div class="pdf_page_header">
            <span><strong>ПРОЕКТ: </strong><?php echo $tkp['project_name'];?></span><br>
            <span><strong>ЗАКАЗЧИК: </strong><?php echo $tkp['client_name'];?></span><br>
            <span><strong>ОБЪЕКТ: </strong><?php echo $tkp['implementation_object'];?></span><br>
        </div>
    </page_header>
    <h4 style="text-align: center;">6. Конструкция преобразователя частоты</h4>
    <p>Стандартный преобразователь частоты Ru-Drive VFD состоит из секции управления, секции силовых ячеек и секции трансформатора.</p>
    <table style="position: relative;padding: 0px;display: table;width: 100%;">
        <tbody>
            <tr>
               <!-- <td style="width: 5%; text-align: left;border: none;"></td>-->
                <td style="width: 100%; text-align: center;border: none;">
                    <img src="<?= public_path('assets/image/pdf/pdf_static_ch6_2.png') ?>" style="width:480px;height:300px;">
                </td>
               <!--  <td style="width: 5%; text-align: right;border: none;"></td>-->
            </tr>
        </tbody>
    </table>
    <p><b>Секция управления</b> является основным управляющим органом управления преобразователя частота. Секция управления содержит компоненты управления преобразователем частоты, имеет сенсорную панель для отображения рабочих параметров и ввода управляющих воздействий</p>
    <p><b>Секция трансформатора</b> предназначена для установки многообмоточного фазосдвигающего трансформатора, а также клемм подключения силового питания и электродвигателя.</p>
    <p><b>Секция силовых ячеек</b> предназначена для размещения силовых ячеек. </p>
    <h4 style="text-align: center;">7. Основные функции</h4>
    <ul>
        <li>Плавное регулирование скорости вращения электродвигателя</li>
        <li>Плавный пуск/останов двигателя</li>
        <li>Автоматическое регулирование технологического параметра в требуемом диапазоне с использованием встроенного ПИД-регулятора</li>
        <li>Скалярное управление</li>
        <li>Векторное управление с датчиком обратной связи/ без датчика обратной связи</li>
        <li>Возможность пуска на вращающийся двигатель</li>
        <li>Эффективная система защиты</li>
        <li>Автоматическая самодиагностика</li>
        <li>Аварийная звуковая и световая сигнализация</li>
        <li>Отображение состояние оборудования во время работы</li>
        <li>Местное/Дистанционное управление преобразователем частоты</li>
        <li>Работа при пониженном напряжении сети</li>
        <li>Восстановление работы после падения напряжения</li>
        <li>Обход резонансных частот</li>
        <li>Управление «ведущий-ведомый»</li>
        <li>Реверс направления вращения двигателя</li>
    </ul>
</page>
<page orientation="portrait" backtop="25mm" backbottom="0mm" backleft="10mm" backright="10mm">
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
    <h4 style="text-align: center;">8. Типы встроенных защит преобразователя частоты</h4>
    <table class="protect_table">
        <thead>
            <col style="width:10%">
            <col style="width:30%">
            <col style="width:60%">
            <tr style="background-color: #9fc5e8;">
                <th></th>
                <th style="padding: 5px;text-align: center;">Наименование защиты</th>
                <th style="padding: 5px;text-align: center;">Описание</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="11" style="background-color: #fce8b2;text-align: center;">По входу</td>
                <td style="text-align: center;">Превышение напряжения 1</td>
                <td style="text-align: left;padding: 1px;">При превышении напряжения на входе ПЧ 1.1*Uном в течение более 10с выдается сигнал «Предупреждение ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Превышение напряжения 2</td>
                <td style="text-align: left;padding: 1px;">При превышении напряжения на входе ПЧ 1.15*Uном в течение более 5с выдается сигнал «Предупреждение ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Превышение напряжения 3</td>
                <td style="text-align: left;padding: 1px;">При превышении напряжения на входе ПЧ 1.2*Uном в течение более 2с выдается сигнал «Авария ПЧ», останов ПЧ.</td>
            </tr>
            <tr>
                <td style="text-align: center;">Мгновенное превышение напряжения</td>
                <td style="text-align: left;padding: 1px;">При превышении напряжения на входе ПЧ 1.5*Uном в течение более 1000мс выдается сигнал «Авария ПЧ», останов ПЧ.</td>
            </tr>
            <tr>
                <td style="text-align: center;">Низкий уровень напряжения 1</td>
                <td style="text-align: left;padding: 1px;">При понижении напряжения на входе ПЧ 0,85*Uном в течение более 10с выдается сигнал «Предупреждение ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Низкий уровень напряжения 2</td>
                <td style="text-align: left;padding: 1px;">При понижении напряжения на входе ПЧ 0,75*Uном в течение более 10с выдается сигнал «Предупреждение ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Низкий уровень напряжения 3</td>
                <td style="text-align: left;padding: 1px;">При понижении напряжения на входе ПЧ 0,65*Uном в течение более 10с выдается сигнал «Авария ПЧ», останов ПЧ.</td>
            </tr>
            <tr>
                <td style="text-align: center;">Дисбаланс напряжения</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при дисбалансе напряжения на входе выше заданной уставки в течение более 10с, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Потеря фазы питающей сети</td>
                <td style="text-align: left;padding: 1px;">Отключение ПЧ при потере любой фазы питающей цепи в течение более 10с, выдается сигнал «Авария ПЧ». </td>
            </tr>
            <tr>
                <td style="text-align: center;">Замыкание на землю</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при однофазном замыкании на землю на входе ПЧ в течение более 1800с. Данная защита активируется по усмотрению пользователя через панель управления ПЧ.</td>
            </tr>
            <tr>
                <td style="text-align: center;">Высокий ток</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при превышении тока на входе выше 1.2 * Iном в течение более 10с, выдается сигнал «Авария ПЧ». Данная защита активируется по усмотрению пользователя через панель управления ПЧ.</td>
            </tr>
            <tr>
                <td rowspan="6" style="background-color: #fce8b2;text-align: center;">По выходу</td>
                <td style="text-align: center;">Перегрузка по току 1</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при превышении выходного тока ПЧ 1.2*Iном в течение более 60с, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Перегрузка по току 2</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при превышении выходного тока ПЧ 1.3*Iном в течение более 10с, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Перегрузка по току 3</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при превышении выходного тока ПЧ 1.4*Iном в течение более 1с, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Мгновенная перегрузка по току</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при превышении выходного тока ПЧ 1.5*Iном в течение более 1мс, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Дисбаланс тока</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при дисбалансе тока на выходе выше заданной уставки в течение более 10с, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Потеря фазы выходной цепи</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при потере фазы в течение более 2с, выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td rowspan="7" style="background-color: #fce8b2;text-align: center;">Прочие</td>
                <td style="text-align: center;">Неисправность вентиляторов охлаждения</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при неисправности вентиляторов в течение более 10с, выдается сигнал «Авария ПЧ». Данная защита активируется по усмотрению пользователя через панель оператора ПЧ.</td>
            </tr>
            <tr>
                <td style="text-align: center;">Аварийный останов</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при нажатой кнопке «Аварийный останов» или про подаче сигнала с внешней системы управления «Аварийный останов ПЧ», выдается сигнал «Авария ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Неисправность контактора</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при нарушении логики управления входным и выходным контакторами ПЧ (отсутствует обратная связь о состоянии контактора, несанкционированное включение контактора и т.д.)</td>
            </tr>
            <tr>
                <td style="text-align: center;">Потеря питания секции управления ПЧ</td>
                <td style="text-align: left;padding: 1px;">При потере питания в секции управления выдается сигнал «Предупреждение ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Открытие дверей ПЧ при поданном высоком напряжении</td>
                <td style="text-align: left;padding: 1px;">Данная защита активируется по усмотрению пользователя через панель оператора ПЧ. В случае активации данной защиты, при открытии дверей ПЧ произойдет останов ПЧ, выдается сигнал «Авария ПЧ». В случае если данная защита не активирована, ПЧ продолжит работу,</td>
            </tr>
            <tr>
                <td style="text-align: center;">Высокая температура трансформатора</td>
                <td style="text-align: left;padding: 1px;">При повышении температуры трансформатора до 110 градусов, ПЧ выдаст сигнал «Предупреждение ПЧ».</td>
            </tr>
            <tr>
                <td style="text-align: center;">Аварийная температура трансформатора</td>
                <td style="text-align: left;padding: 1px;">Останов ПЧ при повышении температуры трансформатора до 130 градусов, выдается сигнал «Авария ПЧ».</td>
            </tr>
        </tbody>
    </table>
</page>


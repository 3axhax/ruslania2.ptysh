<style>

</style>

<?php

//array(3) { [0]=> array(6) { ["type"]=> string(7) "Economy" ["id"]=> int(3) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(20.6) ["deliveryTime"]=> string(4) "5-15" } [1]=> array(6) { ["type"]=> string(8) "Priority" ["id"]=> int(2) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(29.1) ["deliveryTime"]=> string(4) "3-10" } [2]=> array(6) { ["type"]=> string(7) "Express" ["id"]=> int(1) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(50.1) ["deliveryTime"]=> string(3) "3-7" } }

echo '<span class="texterror delerror" style="display: none; padding: 8px 60px; margin-bottom: 10px; border: 1px solid rgb(237, 29, 36); border-radius: 6px; background-color: rgb(255, 192, 203) !important;">Выберите тариф доставки</span>';

$i = 1;

foreach ($items as $item) {
    
    $oncange = '';
    
    
    
    echo '<div class="rows_checkbox_delivery">';
    echo '<div>';
    echo '<label rel="'.$item['value'].'" valute="'.Currency::ToSign($item['currency']).'" onclick="check_delivery($(this));">';
    echo '<input type="radio" value="'.$item['id'].'" name="dtid" rel="'.$item['value'].''.$item['currencyName'].'"'.$oncange.'/> '.$item['type'] . ' - '.$item['deliveryTime']. ' дней (+'.$item['value'].''.Currency::ToSign($item['currency']).')';
    echo '</label>';
    echo '</div>';
    echo '</div>';
    
    if ($i == 1 AND $item['value'] == 7.0 AND ( $_POST['id_country']== 68 OR $_POST['id_country'] == 62)) {
        
        ?>
        
        <div class="select_dd_box" style="margin: 20px 0; display: block;">
            
            <div class="select_dd">
                
                <div class="p2" style="font-size: 16px; font-weight: bold;margin-bottom: 5px; display: inline-block">Введите свой индекс</div> <div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer;" onclick="$('.info_box_smart').toggle(); $('.info_box_smart').css('left', $(this).offset().left); $('.info_box_smart').css('top', '6px'); $.get('/text_info_smart.html', { id : 1 }, function (data) { $('.info_box_smart').html(data) })" class="qbtn"> ? </div>
                
                <div style="background-color: #fff; position: absolute; display: none; padding: 20px; width: 800px; z-index: 99999; border-radius: 2px; box-shadow: 0 0 10px rgba(0,0,0,0.3)" class="info_box_smart"></div>
                <div></div>
                
                Вы можете выбрать для вас удобный пункт выдачи. Введите ваш почтовый индекс в окно и нажмите "Найти". Вам покажем список пунктов выдачи и пунктов СмартПост. Если не найдете себе удобного место, попробуйте ввести другой почтовый индекс.


                <div style="height: 10px;"></div>
                <input class="smartpost_index" type="text" placeholder="" style="margin: 0;" onclick="$('input[name=dtid]').slice(0,1).attr('checked', 'true')">
                
                
                
                <a href="javascript:;" class="btn btn-success start-search-smartpost" style="margin-left: 10px;" onclick="search_smartpost()">Найти</a>
            
            </div>
                
            <div class="box_smartpost"></div>                   
                
            <input class="sel_smartpost" name="pickpoint_address" type="hidden" value=""/>
            
            
            </div>

        <?php
        
    }
    
    $i++;

}


?>
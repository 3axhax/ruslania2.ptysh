<style>

</style>

<?php

//array(3) { [0]=> array(6) { ["type"]=> string(7) "Economy" ["id"]=> int(3) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(20.6) ["deliveryTime"]=> string(4) "5-15" } [1]=> array(6) { ["type"]=> string(8) "Priority" ["id"]=> int(2) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(29.1) ["deliveryTime"]=> string(4) "3-10" } [2]=> array(6) { ["type"]=> string(7) "Express" ["id"]=> int(1) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(50.1) ["deliveryTime"]=> string(3) "3-7" } }

echo '<span class="texterror delerror" style="display: none; padding: 8px 60px; margin-bottom: 10px; border: 1px solid rgb(237, 29, 36); border-radius: 6px; background-color: rgb(255, 192, 203) !important;">Выберите тариф доставки</span>';

echo '<div class="row">';



$i = 1;

$use = false;

foreach ($items as $item) {
    
    if ($i == 1 AND $item['value'] == '7' AND ($_POST['id_country'] == 68 OR $_POST['id_country'] == 62)) {
        
     $use = true;
     $oncange = ' onchange="$(\'.smartpost_index\').val(\'\'); $(\'.box_smartpost\').html(\'\'); $(\'.select_dd_box\').show(); $(\'.selt .check\').removeClass(\'active\'); $(\'.check\', $(this).parent()).addClass(\'active\');"';
     
    } else {
        
        $oncange = ' onchange="$(\'.smartpost_index\').val(\'\'); $(\'.box_smartpost\').html(\'\'); $(\'.select_dd_box\').hide(); $(\'.selt .check\').removeClass(\'active\'); $(\'.check\', $(this).parent()).addClass(\'active\');"';
    
    }
    
   
    
    
   echo '<div style="position: relative; display: inline-block; width: 298px; height: 120px;">';
   
   echo '<div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$(\'.info_box\').hide(); $(\'.info_box.info_box_smart'.$i.'\').toggle();" class="qbtn2"> ? </div>';
   
 echo '<div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart'.$i.'">';
 
 switch ($item['type']) {
     
     case 'Economy': echo 'Nouto omasta tai valitsemastasi Postista tai Postin Noutopisteestä'; break;
     case 'Priority':  echo 'kotiin posotitse,Kotipaketti, Priority-käsittely';  break;
     case 'Express': echo 'kotiin Postitse pikana, Express-paketti, Posti sopii kanssasi toimitusajankohdan, Express-käsittely';  break;
     
 }
 
 
 echo '</div>';
   
   
   echo '<label class="selt span3" rel="'.$item['value'].'" valute="'.Currency::ToSign($item['currency']).'" onclick="check_delivery($(this))">';
   
   //if ($i != 1) : 
   
   
   
  // endif; 
   
   echo '<div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span> 
            </div>';
    echo '<input type="radio" value="'.$item['id'].'" name="dtid" rel="'.$item['value'].''.$item['currencyName'].'"'.$oncange.' style="display: none;"/> '.$item['type'] . ' '.$item['deliveryTime']. ' дней <br /><span style="color: #70C67C; font-weight: bold;">+'.$item['value'].''.Currency::ToSign($item['currency']).'</span>';
    echo '</label></div>';
   
    
    
    
    $i++;

}

if ($use) {

echo '<div class="clearfix"></div>
    <div class="select_dd_box" style="margin: 20px 0; margin-left: 20px; display: none;">
            
            <div class="select_dd">
                
                <div class="p2" style="font-size: 16px; font-weight: bold;margin-bottom: 5px; display: inline-block">Введите свой индекс</div> <div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer;" onclick="$(\'.info_box_smart\').toggle(); $(\'.info_box_smart\').css(\'left\', $(this).offset().left); $(\'.info_box_smart\').css(\'top\', \'6px\'); $.get(\'/text_info_smart.html\', { id : 1 }, function (data) { $(\'.info_box_smart\').html(data) })" class="qbtn"> ? </div>
                
                <div style="background-color: #fff; position: absolute; display: none; padding: 20px; width: 800px; z-index: 99999; border-radius: 2px; box-shadow: 0 0 10px rgba(0,0,0,0.3)" class="info_box_smart"></div>
                <div></div>
                
                Вы можете выбрать для вас удобный пункт выдачи. Введите ваш почтовый индекс в окно и нажмите "Найти". Вам покажем список пунктов выдачи и пунктов СмартПост. Если не найдете себе удобного место, попробуйте ввести другой почтовый индекс.


                <div style="height: 10px;"></div>
                <input class="smartpost_index" type="text" placeholder="" style="margin: 0;" onclick="$(\'input[name=dtid]\').slice(0,1).attr(\'checked\', \'true\')">
                
                
                
                <a href="javascript:;" class="btn btn-success start-search-smartpost" style="margin-left: 10px;" onclick="search_smartpost()">Найти</a>
            
            </div>
                
            <div class="box_smartpost"></div>                   
                
            <input class="sel_smartpost" name="pickpoint_address" type="hidden" value=""/>
            
            
            </div>';

}

echo '</div>';
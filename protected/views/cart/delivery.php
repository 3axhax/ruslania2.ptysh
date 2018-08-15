<?php

//array(3) { [0]=> array(6) { ["type"]=> string(7) "Economy" ["id"]=> int(3) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(20.6) ["deliveryTime"]=> string(4) "5-15" } [1]=> array(6) { ["type"]=> string(8) "Priority" ["id"]=> int(2) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(29.1) ["deliveryTime"]=> string(4) "3-10" } [2]=> array(6) { ["type"]=> string(7) "Express" ["id"]=> int(1) ["currency"]=> int(1) ["currencyName"]=> string(3) "EUR" ["value"]=> float(50.1) ["deliveryTime"]=> string(3) "3-7" } }

echo '<span class="texterror delerror" style="display: none; padding: 8px 60px; margin-bottom: 10px; border: 1px solid rgb(237, 29, 36); border-radius: 6px; background-color: rgb(255, 192, 203) !important;">Выберите тариф доставки</span>';

foreach ($items as $item) {

    echo '<div class="rows_checkbox_delivery">';
    echo '<div>';
    echo '<label rel="'.$item['value'].'" valute="'.Currency::ToSign($item['currency']).'" onclick="check_delivery($(this));">';
    echo '<input type="radio" value="'.$item['id'].'" name="dtid" rel="'.$item['value'].''.$item['currencyName'].'"/> '.$item['type'] . ' - '.$item['deliveryTime']. ' дней (+'.$item['value'].''.Currency::ToSign($item['currency']).')';
    echo '</label>';
    echo '</div>';
    echo '</div>';

}



<?php

if ($items) {

$i = 1;

$use = false;

foreach ($items as $item) {

    $text_placegolder = '';
	
	$vals = Currency::GetRates();
	
	
    if (($items[0]['value'] == 7 OR $items[0]['value'] == round((7 * $vals[2]), 1) OR $items[0]['value'] == round((7 * $vals[3]), 1) ) AND ($_POST['id_country'] == 68 OR $_POST['id_country'] == 62) AND $i == 1) {

     $text_placegolder1 = $ui->item('DELIVERY_ECONOMY_FINEST');

     $use = true;
     $oncange = ' $(\'.smartpost_index\').val(\'\'); $(\'.box_smartpost\').html(\'\');  $(\'.select_dd_box\').show(); $(\'.selt .check\').removeClass(\'active\'); $(\'.check\', $(this).parent()).addClass(\'active\'); $(\'.address.addr2, label.addr_buyer, div.addr_buyer\').hide()';
     
    } elseif (($items[0]['value'] == 7 OR $items[0]['value'] == round((7 * $vals[2]), 1) OR $items[0]['value'] == round((7 * $vals[3]), 1) )  AND ($_POST['id_country'] == 68 OR $_POST['id_country'] == 62) AND $i > 1) {

        $text_placegolder2 = $ui->item('DELIVERY_PRIORITY_FINEST');
        $text_placegolder3 = $ui->item('DELIVERY_EXPRESS_FINEST');

       $oncange = ' $(\'.smartpost_index\').val(\'\'); $(\'.box_smartpost\').html(\'\'); $(\'.select_dd_box\').hide(); $(\'.selt .check\').removeClass(\'active\'); $(\'.check\', $(this).parent()).addClass(\'active\'); ';

    } else {
        if ($items[0]['value'] < 15) {

            $text_placegolder1 = $ui->item('DELIVERY_ECONOMY_OTHER');
            $text_placegolder2 = $ui->item('DELIVERY_PRIORITY_OTHER');
            $text_placegolder3 = $ui->item('DELIVERY_EXPRESS_OTHER');

        } elseif($items[0]['value'] > 14) {

            $text_placegolder1 = $ui->item('DELIVERY_ECONOMY_OTHER_YES');
            $text_placegolder2 = $ui->item('DELIVERY_PRIORITY_OTHER_YES');
            $text_placegolder3 = $ui->item('DELIVERY_EXPRESS_OTHER_YES');

        }



        $oncange = ' $(\'.smartpost_index\').val(\'\'); $(\'.box_smartpost\').html(\'\'); $(\'.select_dd_box\').hide(); $(\'.selt .check\').removeClass(\'active\'); $(\'.check\', $(this).parent()).addClass(\'active\'); ';
    
    }
   
   
   
   
   
   
   
   $str = '<div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$(\'.info_box\').hide(); $(\'.info_box.info_box_smart'.$i.'\').toggle();" class="qbtn2"> ? </div>';
   
 $str .= '<div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart'.$i.'">';
 

 
 switch ($item['type']) {
     
     case 'Priority':   
	 $str .= $text_placegolder2;  break;
     case 'Express': 
	 $str .= $text_placegolder3;  break;
     default:
	 $str .= $text_placegolder1; break;
 }

 $str .= '</div>';

 $str .= '<label class="seld span3 seld0'.($i+1).'" rel="'.$item['value'].'" valute="'.Currency::ToSign($item['currency']).'" onclick="check_delivery($(this)); check_cart_sel($(this),\'seld\', \'dtype'.($i+1).'\'); showALL(); hide_oplata(1); $(\'.rows_checkbox_delivery input\').prop(\'checked\', false); $(\'.delivery_name\').html(\'' . $ui->item('CARTNEW_DELIVERY_POST_NAME').'\'); $(\'.type_delivery\').val(\'' .$ui->item('CARTNEW_DELIVERY_POST_NAME').'\'); $(\' label.addr_buyer\').show(); $(\'label.addr_buyer input\').attr(\'checked\',\'checked\'); $(\'.selt1\').click();$(\'.oplata3\').click();'.$oncange.'">';

  $str .= '<div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span> 
            </div>';
    $str .= '<input type="radio" value="'.$item['id'].'" name="dtype" rel="'.$item['value'].''.$item['currencyName'].'" style="display: none;" id="dtype'.($i+1).'"/>' . $ui->item('CARTNEW_DELIVERY_POST_NAME'). '<br />'.$item['type'] . ' '.$item['deliveryTime']. ' ' . $ui->item('CARTNEW_DAY_NAME'). ' <br /><span style="color: #70C67C; font-weight: bold;">'.$item['value'].''.Currency::ToSign($item['currency']).'</span>';
    $str .= '</label></div>';
	
	$arrTexts['text'.($i)] = $str;
	
    $i++;

	
	
	
}

if ($use) {

$str = '<div class="clearfix"></div>
    <div class="select_dd_box" style="margin: 20px 0; margin-left: 20px; display: none;">
            
            <div class="select_dd">
                
                <div class="p2" style="font-size: 16px; font-weight: bold;margin-bottom: 5px; display: inline-block">'.$ui->item('CARTNEW_SMARTPOST_TITLE').'</div> <div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer;" onclick="$(\'.info_box_smart\').toggle(); $(\'.info_box_smart\').css(\'top\', \'6px\'); $.get(\'/text_info_smart.html\', { id : 1 }, function (data) { $(\'.info_box_smart\').html(data) })" class="qbtn"> ? </div>
                
                <div style="background-color: #fff; position: absolute; display: none; padding: 20px; width: 580px; right: 0; z-index: 99999; border-radius: 2px; box-shadow: 0 0 10px rgba(0,0,0,0.3)" class="info_box_smart"></div>
                <div></div>
              <br />  
 '.$ui->item('CARTNEW_SMARTPOST_SUBTITLE').'


                <div style="height: 10px;"></div>
                <input class="smartpost_index" type="text" placeholder="'.$ui->item('CARTNEW_SMARTPOST_INPUT_INDEX_PLACEHOLDER').'" style="margin: 0;" onclick="$(\'input[name=dtid]\').slice(0,1).attr(\'checked\', \'true\')" onkeyup="if (event.keyCode==13) { search_smartpost() }">
 
                <a href="javascript:;" class="btn btn-success start-search-smartpost" style="margin-left: 10px;" onclick="search_smartpost()">'.$ui->item('BTN_SEARCH_ALT').'</a>
            
            </div>
                
            <div class="box_smartpost"></div>                   
                
            <input class="sel_smartpost" name="pickpoint_address" type="hidden" value=""/>
            
            
            </div>';

} else { $str = ''; }




$arrTexts['smartpost'] = $str;


echo json_encode($arrTexts);


 }
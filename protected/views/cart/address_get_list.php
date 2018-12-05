<?php

$ch = new CommonHelper();
$array = array();

if (count($items) > 1) {

    $array['items'][] =  '<option value="">Выберите адрес доставки</option>';

}
//var_dump($items);



foreach($items as $item){

    $array['items'][] = '<option value="'.$item['id'].'">'.$ch->FormatAddress($item).'</option>';

}

$array['ida'] = $ida;

echo json_encode($array);
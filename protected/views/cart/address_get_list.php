<?php

$ch = new CommonHelper();

echo '<option value="">Выберите адрес доставки</option>';

//var_dump($items);

foreach($items as $item){
    
    echo '<option value="'.$item['id'].'">'.$ch->FormatAddress($item).'</option>';
   
}


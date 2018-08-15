<?php

    foreach ($points as $p) {
        
        ?>

<div class="row_smartpost">
    
    <a href="javascript:;" class="btn btn-success" style="float: right; margin-top: -10px;" onclick="select_smartpost_row($(this))">Выбрать</a>
    
    <div><b><?=$p['type']?></b> <?=$p['labelName']['fi']?></div>
    <div class="addr_name"><?=$p['address']['fi']['address']?>, <?=$p['address']['fi']['streetName']?>, <?=$p['address']['fi']['streetNumber']?>, <?=$p['address']['fi']['postalCode']?> <?=$p['address']['fi']['postalCodeName']?></div>
</div>


        <?
        
        
    }


?>

<?php

    foreach ($points as $p) {
        
        ?>

<div class="popup0 popup<?=$p['id']?>" style="background-color: rgba(0,0,0,0.3); position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 99999; opacity: 0.3; display: none;" onclick="$('.popup0').hide();"></div>
<div style="background-color: rgb(255, 255, 255); position: fixed; padding: 20px; width: 650px; z-index: 99999; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px; left: 50%; margin-left: -335px; top: 12%; display: none; z-index: 999991;" class="popup0 popup<?=$p['id']?>">
    <div style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="$('.popup0').hide();">X</div>
    <h2>Tieto</h2>
    
    <div><b><?=$p['labelName']['fi']?></b>
        <div><?=$p['locationName']['fi']?></div>
    </div>
    <div style="margin-top: 20px;"><b><?=$p['address']['fi']['address']?></b></div>
    <div style="margin-bottom: 20px;"><b><?=$p['address']['fi']['postalCode']?> <?=$p['address']['fi']['postalCodeName']?></b></div>
    <div style=""><b>Avoinna:</b></div>
    <div style="margin-bottom: 20px;"><?=$p['availability']?></div>
    <?php if ($p['dropOfTimeParcel']) : ?>
    <div style=""><b>Viimeiset postiinjättöajat:</b></div>
    <div style="margin-bottom: 20px;">Kirjeet: klo. <?=$p['dropOfTimeParcel']?></div>
    <?php endif; ?>
    
</div>


<div class="row_smartpost">
    
    <a href="javascript:;" class="btn btn-success" style="float: right; margin-top: -10px;" onclick="select_smartpost_row($(this))">Выбрать</a>
    
    <div><b><?=$p['type']?></b> <a href="javascript:;" onclick="$('.popup0').hide();$('.popup<?=$p['id']?>').show();"><?=$p['labelName']['fi']?></a></div>
    <div class="addr_name"><?=$p['address']['fi']['address']?>, <?=$p['address']['fi']['streetName']?>, <?=$p['address']['fi']['streetNumber']?>, <?=$p['address']['fi']['postalCode']?> <?=$p['address']['fi']['postalCodeName']?></div>
</div>


        <?php
        
        
    }
    
    if (count($points)) {
    
        echo '<div style="display: none; text-align: right;" class="more_points">';
        echo '<a href="javascript:;" onclick="$(\'.row_smartpost\').removeClass(\'act\'); $(\'.row_smartpost\').show(); $(this).parent().hide(); $(\'.row_smartpost .btn.btn-success\').html(\'Выбрать\');">Показать все</a>';
        echo '</div>';
    }


?>

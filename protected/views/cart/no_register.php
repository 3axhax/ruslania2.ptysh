<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        
        position: relative;
    }
    
    label.seld .red_checkbox {
        position: absolute;
        right: 20px;
        top: 20px;
    }
    
    .cartorder .p2, .cartorder .p1, .cartorder .p3 { font-size: 22px; }
    
    .cartorder .p1 { margin:0 0 15px 0;}
    .cartorder .p2 { margin: 15px 0;}
    .cartorder .p3 { margin: 15px 0;}
    
</style>

<script>
    
    $(document).ready(function() {
        
        $('.cartorder .row label').slice(0).css('border', '1px solid #64717f');
        $('input[type=radio]', $('.cartorder .row label').slice(0)).attr('checked', 'true');
        $('.check', $('.cartorder .row label').slice(0)).addClass('active');
    })
    
    function check_cart_sel(cont, inputId) {
        
        $('.seld .check').removeClass('active');
        $('.seld input[type=radio]').removeAttr('checked');
        $('.seld').css('border', '1px solid #ccc');
        
        if ($('.check', cont).hasClass('active')) {
            $('.check', cont).removeClass('active');
            $(cont).css('border', '1px solid #ccc');
            if (inputId == undefined) $('.avail', cont).val('');
            else $('#'+inputId).removeAttr('checked', 'true');
        } else {
            $('.check', cont).addClass('active');
            $(cont).css('border', '1px solid #64717f');
            if (inputId == undefined) $('.avail', cont).val('1');
            else $('#'+inputId).attr('checked', 'true');
        }
        
    }

</script>

<div class="container cartorder">
    
    <div class="p1">1. Где и как вы хотите получить заказ?</div>
    
    <div class="row">
        
        <?php
            $country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
            if ($country == 'FI' or $country == 'fi') {
        ?>
        
        <label class="seld span3" onclick="check_cart_sel($(this), 'dtype1')">
            Забрать в магазине
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="1" name="dtype" style="display: none;" />
        </label>
        <label class="seld span3" onclick="check_cart_sel($(this), 'dtype3')">
            Smart Post
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype3" value="2" name="dtype" style="display: none;" />
        </label> <?php  } ?>
        <label class="seld span3" onclick="check_cart_sel($(this), 'dtype2')">
            Доставка почтой
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="0" name="dtype" style="display: none;" />
        </label>
</div>        
        <div class="clearfix"></div>
        
        <div class="p2">2. Как вам будет удобнее оплатить заказ?</div>
        
        <div class="p3">3. Укажите ваши личные данные</div>
        
        <?php

    $user = Yii::app()->user->GetModel();
    $address = new Address;
    $address->receiver_title_name = $user['title_name'];
    $address->receiver_last_name = $user['last_name'];
    $address->receiver_first_name  = $user['first_name'];
    $address->receiver_middle_name = $user['middle_name'];
    $address->contact_email = $user['login'];
    $address->type = 2;
    $this->renderPartial('/site/address_form2', array('model' => $address,
                                                           'mode' => 'new',
                                                           'afterAjax' => 'addrInserted')); ?>
        
    
</div>


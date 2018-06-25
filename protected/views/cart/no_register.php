<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        border-radius: 2px;
        position: relative;
    }
    
    label.seld .red_checkbox {
        position: absolute;
        right: 5px;
        top: 20px;
    }
    
    label.selp {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        height: 70px;
        position: relative;
        padding-right: 55px;
        width: 187px;
        border-radius: 2px;
    }
    
    label.selp .red_checkbox {
        position: absolute;
        right: 5px;
        top: 20px;
    }
    
    .cartorder .p2, .cartorder .p1, .cartorder .p3 { font-size: 22px; }
    
    .cartorder .p1 { margin:0 0 15px 0;}
    .cartorder .p2 { margin: 15px 0;}
    .cartorder .p3 { margin: 15px 0;}
    
</style>

<script>
    
    $(document).ready(function() {
        
        $('.cartorder .row label.seld').slice(0,1).css('border', '1px solid #64717f');
        $('input[type=radio]', $('.cartorder .row label.seld').slice(0,1)).attr('checked', 'true');
        $('.check', $('.cartorder .row label.seld').slice(0,1)).addClass('active');
        
        $('.cartorder .row label.selp').slice(0,1).css('border', '1px solid #64717f');
        $('input[type=radio]', $('.cartorder .row label.selp').slice(0,1)).attr('checked', 'true');
        $('.check', $('.cartorder .row label.selp').slice(0,1)).addClass('active');
    })
    
    function check_cart_sel(cont,cont2,inputId) {
        
        $('.'+cont2+' .check').removeClass('active');
        $('.'+cont2+' input[type=radio]').removeAttr('checked');
        $('.'+cont2+'').css('border', '1px solid #ccc');
        
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
    
    function  show_all() {
        
        $('.oplata1,.oplata2,.oplata3,.oplata4,.oplata5,.oplata6,.oplata7,.oplata8').show();
        
        $('.check',$('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');
        
        $('.check',$('.selp:visible').slice(0,1)).addClass('active');
        $('input[type=radio]', $('.selp:visible').slice(0,1)).attr('checked','true');
        $('.selp:visible').slice(0,1).css('border', '1px solid #64717f');
    }
    
    function hide_oplata(oplata) {
        
        $('.oplata'+oplata).hide();
        
        $('.check',$('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');
        
        //$('.selp:visible').slice(0,1).hide();
        
        $('.check',$('.selp:visible').slice(0,1)).addClass('active');
        $('input[type=radio]', $('.selp:visible').slice(0,1)).attr('checked','true');
        $('.selp:visible').slice(0,1).css('border', '1px solid #64717f');
        
    }
    
    function sendforma() {
        
        var frm1 = $('form.no_register1').serialize();
        var frm2 = $('form.address.text').serialize();
        
        var frmall = frm1+'&'+frm2;
        
        $.post('/cart/result/', frmall, function(data) {
            
            alert(data);
            
        });
        
        
        
    }
</script>

<div class="container cartorder">
    <form class="no_register1" method="post">
    <div class="p1">1. Где и как вы хотите получить заказ?</div>
    
    <div class="row">
        
        <?php
            $country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
            //if ($country == 'FI' or $country == 'fi') {
        ?>
        
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype1'); show_all();">
            Забрать в магазине
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="1" name="dtype" style="display: none;" />
        </label>
        
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype3'); hide_oplata(1)">
            Smart Post
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype3" value="2" name="dtype" style="display: none;" />
        </label> <?//php  } ?>
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype2'); hide_oplata(1)">
            Доставка почтой
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="0" name="dtype" style="display: none;" />
        </label>
</div>        
        <div class="clearfix"></div>
        
        <div class="p2">2. Как вам будет удобнее оплатить заказ?</div>
        
        <div class="row spay">
        
        <label class="selp span3 oplata1" onclick="check_cart_sel($(this),'selp', 'dtype0')">
            Оплата в магазине
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype0" value="1" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata2" onclick="check_cart_sel($(this),'selp', 'dtype1')">
            PayPal
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype1" value="2" name="ptype" style="display: none;" />
        </label>
            
        <label class="selp span3 oplata3" onclick="check_cart_sel($(this),'selp', 'dtype2')">
            PayTrail
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="3" name="ptype" style="display: none;" />
        </label>
            
        <label class="selp span3 oplata4" onclick="check_cart_sel($(this),'selp', 'dtype3')">
            Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС 
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype3" value="4" name="ptype" style="display: none;" />
        </label>
        
        <label class="selp span3 oplata5" onclick="check_cart_sel($(this),'selp', 'dtype4')">
            Alipay
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype4" value="5" name="ptype" style="display: none;" />
        </label> 
        
        <label class="selp span3 oplata6" onclick="check_cart_sel($(this),'selp', 'dtype5')">
            ApplePay
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype5" value="6" name="ptype" style="display: none;" />
        </label> 
            
        <label class="selp span3 oplata7" onclick="check_cart_sel($(this),'selp', 'dtype6')">
            Предоплата на банковский счет в Финляндии
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype6" value="7" name="ptype" style="display: none;" />
        </label>
            
        <label class="selp span3 oplata8" onclick="check_cart_sel($(this),'selp', 'dtype7')">
            Предоплата на банковский счет в России
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype7" value="8" name="ptype" style="display: none;" />
        </label>    
            
        </div>    
          </form>  
         <div class="clearfix"></div>
        
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
   
        
        
   <a href="javascript:;" class="order_start" style="width: 248px; margin-left: 320px; display: block" onclick="sendforma()">Оформить заказ</a>     
    
   
   
</div>


<?php $isPaid = isset($isPaid) && $isPaid; ?>

<?php if ($isPaid) : ?>

    <div class="information info-box">
        <?= $ui->item('ALREADY_PAID'); ?>
    </div>
<?php endif; ?>



<?php $this->renderPartial('/client/_one_order', array('order' => $order, 'onlyContent' => !$isPaid,
    'class' => $isPaid ? '' : 'bordered',
    'enableSlide' => !$isPaid)); ?>

    <script type="text/javascript">
        function slideContents(id) {
            $('#cnt' + id).fadeToggle();
        }
    </script>

    <?php $pid = $order['payment_type_id']; ?>
    

<?php if (!$isPaid) : ?>
    
    <style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        border-radius: 2px;
        position: relative;
        width: 212px;
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
        width: 190px;
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
    
    
    .select_dd { position: relative; }
    .select_dd_popup { position: absolute; top: 29px; background: #fff; max-height: 400px; width: 340px; border: 1px solid #ccc; z-index: 9999; display: none; overflow-y: auto; }
        
    .select_dd_popup .item {
        padding: 5px 10px;
    }
    
    .select_dd_popup .item:hover {
        background-color: #ccc;
        cursor: pointer;
    }
    
    .cart_header {
        
        background: #f8f8f8;
        padding: 8px 10px;
        font-weight: bold;
        border-left: 1px solid #ececec;
        border-top: 1px solid #ececec;
        border-right: 1px solid #ececec;
        
        
    }
    
    table.cart {
       border: 1px solid #ececec; 
    }
    
    .cart tbody tr td {
        
        padding: 10px;
        border-bottom: 1px solid #ececec;
        
    }
    
    .cart tbody tr td .minitext { color: #81807C; }
    .cart tbody tr td span.a { color: #005580; }
    
    .cart_box {
        overflow: auto;
        max-height: 565px;
    }
    
    .cart_footer { float: right; }
    
     .footer3 { margin-bottom: 25px; border-bottom: 1px solid #ececec; }
    
    .footer1, .footer3 {
        background: #f8f8f8;
        padding: 8px 10px;
        font-weight: bold;
        border-left: 1px solid #ececec;
        border-top: 1px solid #ececec;
        border-right: 1px solid #ececec;
    }
    
    .footer2 {
        
        padding: 8px 10px;
        color: #81807C;
        border-left: 1px solid #ececec;
        border-right: 1px solid #ececec;
    
    }
    
    a.order_start { background-color: #5bb75b; }
    
    .order_start.disabled {
        
        opacity: 0.5;
        cursor: default;
    
    }
    
    label.selp img {
        -webkit-filter: grayscale(100%);
        -moz-filter: grayscale(100%);
        -ms-filter: grayscale(100%);
        -o-filter: grayscale(100%);
        filter: grayscale(100%);
        filter: gray; /* IE 6-9 */
    }
    
    label.selp.act img {
        -webkit-filter: grayscale(0%);
        -moz-filter: grayscale(0%);
        -ms-filter: grayscale(0%);
        -o-filter: grayscale(0%);
        filter: grayscale(0%);
        filter: none; /* IE 6-9 */
    }
    
    
    input.error {
        border: 1px solid #ed1d24;
        margin-bottom: 0;
    }
    
    .texterror { color: #ed1d24; font-size: 11px; display: block; }
    
    td.maintxt-vat { padding: 5px 5px; }
    
    .redtext {
    
        color: #ed1d24;
    
    }
    
    .box_smartpost {
        display: none;
        width: 760px;
        margin-top: 20px;
    }
    
    .row_smartpost {
      padding: 15px 5px;  
      border: 1px solid #ccc;
      margin: 5px 0;
    }
    
    .row_smartpost.act {
        background-color: #eaeaea;
    }
    
    .info_order div div.span11 {
        
        width: 500px;
        
    }
    
</style>

<script>
    
    function check_cart_sel(cont,cont2,inputId) {
        $('label.seld').removeClass('act');
        $('.'+cont2+' .check').removeClass('active');
        $('.'+cont2+' input[type=radio]').removeAttr('checked');
        $('.'+cont2+'').css('border', '1px solid #ccc');
        $('label.selp').removeClass('act');
        
        if ($('.check', cont).hasClass('active')) {
            $('.check', cont).removeClass('active');
            $(cont).removeClass('act');
            $(cont).css('border', '1px solid #ccc');
            $('#'+inputId, cont).removeAttr('checked');
        } else {
            $('.check', cont).addClass('active');
            $(cont).addClass('act');
            $(cont).css('border', '1px solid #64717f');
            $('#'+inputId, cont).attr('checked', 'true');
        }
        
        $('a.order_start').attr('href', '/ru/cart/orderPay/?id=<?=$order['id']?>&ptype='+$('#'+inputId).val())                
        
        $('.selp span.check.active').parent().parent().parent().addClass('act');
        
       
        
    }
    
</script>
    
<?php

    $href = Yii::app()->createUrl('cart/orderPay').'?id='.$order['id'].'&ptype='.$pid;

?>
    
      <div class="row spay">

          <?  if ($pid == 0 OR $pid == 999) : ?>

        <label class="selp span3 oplata1<?php if ($pid == 1) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype0')" style="<?php if ($pid == 1) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            Оплата в магазине
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 1) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype0" value="1" name="ptype" style="display: none;"  <?php if ($pid == 1) { echo 'checked="checked"';}?>/>
        </label>
          <? endif; ?>
        <label class="selp span3 oplata3 <?php if ($pid == 25) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype2')" style="width: 490px;<?php if ($pid == 25) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            
            <img src="/images/pt2.png" style="margin-top: -3px;" />
            <span style="display: block; margin-top: 5px;">Кредитные карты и Финские банки</span>
            
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 25) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="25" name="ptype" style="display: none;"  <?php if ($pid == 25) { echo 'checked="checked"';}?>/>
        </label>    
            
        <label class="selp span3 oplata2<?php if ($pid == 8) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype1')" style="<?php if ($pid == 8) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            <img src="/images/pp.jpg" width="150" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 8) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype1" value="8" name="ptype" style="display: none;"  <?php if ($pid == 8) { echo 'checked="checked"';}?>/>
        </label>
            
        
            
        <label class="selp span3 oplata4<?php if ($pid == 7) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype3')" style="<?php if ($pid == 7) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            <div style="margin-top: -8px;"><b>Cчет-фактура</b><br /> Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС </div>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 7) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype3" value="7" name="ptype" style="display: none;"  <?php if ($pid == 27) { echo 'checked="checked"';}?>/>
        </label>
        
        <label class="selp span3 oplata5<?php if ($pid == 26) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype4')" style="<?php if ($pid == 26) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            <img src="/images/ap.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 26) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype4" value="26" name="ptype" style="display: none;"  <?php if ($pid == 26) { echo 'checked="checked"';}?>/>
        </label> 
        
        <label class="selp span3 oplata6<?php if ($pid == 27) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype5')" style="<?php if ($pid == 27) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            <img src="/images/app.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 27) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype5" value="27" name="ptype" style="display: none;"  <?php if ($pid == 27) { echo 'checked="checked"';}?>/>
        </label> 
            
        <label class="selp span3 oplata7<?php if ($pid == 13) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype6')" style="<?php if ($pid == 13) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            Предоплата на банковский счет в Финляндии
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 13) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype6" value="13" name="ptype" style="display: none;"  <?php if ($pid == 13) { echo 'checked="checked"';}?>/>
        </label>
            
        <label class="selp span3 oplata8<?php if ($pid == 14) { echo ' act';}?>" onclick="check_cart_sel($(this),'selp', 'dtype7')" style="<?php if ($pid == 14) { echo 'border: 1px solid rgb(100, 113, 127);'; }?>">
            Предоплата на банковский счет в России
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?php if ($pid == 14) { echo ' active';}?>"></span></span> 
            </div>
            <input type="radio" id="dtype7" value="14" name="ptype" style="display: none;" <?php if ($pid == 14) { echo 'checked="checked"';}?>/>
        </label>    
            
        </div>    
        
<div class="clearfix" style="margin-bottom: 10px;"></div>
         
<a href="<?=$href?>" class="order_start" style="float: right;">Оплатить</a>
         
         
<?php endif; ?>
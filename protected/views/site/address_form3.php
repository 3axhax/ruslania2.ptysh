<?php $form = $this->beginWidget('KnockoutForm', array(
                                                      'model' => $model,
                                                      'action' => $mode == 'new' ? Yii::app()->createUrl('cart'). 'result/' : Yii::app()->createUrl('cart'). 'result/',
                                                      'id' => 'add-address',
                                                      'viewModel' => 'addressVM',
                                                      'afterAjaxSubmit' => $afterAjax,
                                                      'htmlOptions' => array('class' => 'address text'),
                                                 )); ?>

<input type="hidden" value="" name="country_id" class="country_id" />

<?php
$PH = new ProductHelper();
function decline_goods($num) {
        $count = $num;

        $num = $num % 100;

        if ($num > 19) {
            $num = $num % 10;
        }

        switch ($num) {

            case 1: {
                    return $count . ' товар';
                }

            case 2: case 3: case 4: {
                    return $count . ' товара';
                }

            default: {
                    return $count . ' товаров';
                }
        }
    }

?>



<div class="span7" style="margin-left: 0;">
<div class="p3">1. Укажите адрес доставки и плательщика</div>
<?php if($mode == 'edit')  { echo $form->hiddenField('id'); } ?>

<style>
    .address input[type=text] {
        width: 350px;
    }
    .address textarea {
        width: 350px;
        height: 100px;
    }
    
    .address select {
        width: 99%;
    }
</style>

<?php

$addr = new Address();

$addr_list = array();

$addr_list = $addr->GetAddresses($this->uid);

//var_dump($addr_list);
echo ' <span class="err_addr" style="color: #ff0000; font-size: 12px;"></span><div class="clearfix" style="height: 10px;"></div><div class="addr_delivery">';
if (count($addr_list)) {
    
    echo '<select name="id_address" style="margin-bottom: 0;margin-right: 8px;" onchange="checked_sogl()"><option value="">Выберите адрес доставки</option>';

    $ch = new CommonHelper();
    
    
    
    
    foreach ($addr_list as $addr) {
        
        $adr_str = $ch->FormatAddress($addr);
        
        echo '<option value="'.$addr['address_id'].'">'.$adr_str.'</option>';

    }

    echo '</select>';
    
}

?>

<?php
echo '<div class="addr_buyer" style="margin-top: 10px">';
if (count($addr_list)) {
    
    echo '<select name="id_address_b" style="margin-bottom: 0;margin-right: 8px;" onchange="checked_sogl()"><option value="">Выберите адрес плательщика</option>';

    $ch = new CommonHelper();
    
      
    
    foreach ($addr_list as $addr) {
        
        $adr_str = $ch->FormatAddress($addr);
        
        echo '<option value="'.$addr['address_id'].'">'.$adr_str.'</option>';

    }

    echo '</select>';
    
}

echo '</div>';

?>

<a href="javascript:;" onclick="$('select, input').removeClass('error'); $('span.texterror').html(''); $('table.addr1, .btn.btn-success.addr1').toggle('fade');" class="btn btn-success" style="margin-top: 10px;">Добавить адрес</a></div>

<table class="address addr1" style="display: none; margin-top: 10px;">
    <tbody>
        
    <tr>
        <td style="width: 200px;"><b><?=$ui->item("address_type"); ?></b></td>
        <td class="maintxt">
            <label><?=$form->radioButton('type', array('value' => 1, 'uncheckValue' => null, 'onclick'=>'save_form()')); ?>
            <?= $ui->item("MSG_PERSONAL_ADDRESS_COMPANY"); ?></label>
            <label><?=$form->radioButton('type', array('value' => 2, 'uncheckValue' => null, 'onclick'=>'save_form()')); ?>
            <?=$ui->item("MSG_PERSONAL_ADDRESS_PERSON"); ?></label></td>
    </tr>
    <tr data-bind="visible: type()==1">
        <td nowrap="" class="maintxt"><?=$ui->item("address_business_title"); ?>
        </td>
        <td class="maintxt-vat" data-bind="visible: type()==1">
            <?=$form->textField('business_title', array('data-bind' => array('enable' => 'type()==1'),'oninput'=>'save_form()')); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr data-bind="visible: type()==1">
        <td nowrap="" class="maintxt"><?=$ui->item("address_business_number1"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('business_number1', array('data-bind' => array('enable' => 'type()==1'),'oninput'=>'save_form()')); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><?=$ui->item("regform_titlename"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_title_name', array('placeholder'=>$ui->item("MSG_REGFORM_TITLENAME_TIP_1"),'oninput'=>'save_form()')); ?>
        </td>
        
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("regform_lastname"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_last_name', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("regform_firstname"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_first_name', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><?=$ui->item("regform_middlename"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_middle_name', array('oninput'=>'save_form()')); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt">
            <span style="width: 5pt" class="redtext">*</span><?=$ui->item("address_country"); ?>
        </td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->dropDownList('country', CHtml::listData(Country::GetCountryList(), 'id', 'title_en'),
            array(
                'data-bind' => array('optionsCaption' => "'---'"),
                'onclick' => 'change_city($(this)); save_form();',
                'onchange' => 'change_city($(this));'
                )); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    
    <tr>
        <td nowrap="" class="maintxt"><?=$ui->item("address_state"); ?></td>
        <td class="maintxt-vat select_states">
            <select name="Address[state_id]" disabled onclick="save_form();"><option value="">---</option></select>
        </td>
        
    </tr>
    
    
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("address_city"); ?>
        </td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('city', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_postindex"); ?></td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('postindex', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_streetaddress"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('streetaddress',array('placeholder'=>$ui->item("MSG_PERSONAL_ADDRESS_COMMENT_2"))); ?>
            <span class="texterror"></span>
        </td>
        
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span>
            <?=$ui->item("address_contact_email", array('oninput'=>'save_form()')); ?>
        </td>
        <td class="maintxt-vat" colspan="2" style="position: relative;">
            <?= $form->textField('contact_email', array('onblur' => 'checkEmail(this)')); ?>
            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_contact_phone"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('contact_phone', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1">
            
            <?//<img width="8" height="7" src="/pic1/arr3.gif">=$ui->item('MSG_PERSONAL_ADDRESS_COMMENT_PHONE'); ?>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><?=$ui->item("address_contact_notes"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textArea('notes', array('oninput'=>'save_form()')); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    
    <tr>
        <td nowrap="" class="maintxt">&nbsp;</td>
        <td class="maintxt-vat">
            
            <img src="/pic1/loader.gif" data-bind="visible: disableSubmitButton" />
        </td>
        <td class="smalltxt1"></td>
    </tr>
   
    
    <tr>
   
    </tr>
    
    </tbody>
</table>


<a href="javascript:;" class="btn btn-success addr1" style="float: right; display: none; margin-right: 5px;" onclick="add_address(1)">Добавить</a>

<div class="clearfix" style="margin: 5px 0;"></div>


 </div>

<div class="span7" style="float: right; width: 575px; margin-top: 21px;">

        <div class="cart_header" style="width: 553px;">
            В корзине <?=decline_goods($cart['fullInfo']['count'])?> на сумму <?=$PH->FormatPrice($cart['fullInfo']['cost']);?>
        </div>
    
    <div class="cart_box">
        <?//var_dump($cart);?>
        <table class="cart" style="width: 100%;">
        <tbody>
            
            
            <?php foreach ($cart['items'] as $id => $item) : ?>

            <?//var_dump($cart);?>

            <tr>
                
                <td style="width: 31px;">
            <span class="entity_icons"><i class="fa e<?= $item['entity'] ?>"></i></span>
                <?php /*
                <img width="31" height="31" align="middle" alt="" style="vertical-align: middle" data-bind="attr: { alt: Title}" src="/pic1/cart_ibook.gif">
 */ ?>
                </td>
                <td>
                    <span class="a"><?=$item['title']?></span>
                    <div class="minitext"><?=$item['quantity']?> <?if ($item['entity'] == 30) { echo 'мес.'; } else {?>шт.<? } ?> x <?=$PH->FormatPrice($item['price']);?><br /> Вес: <?=($item['weight'])?> кг</div>
                </td>
                
            </tr>
            
            <?php endforeach; ?>
            
        </tbody>
        
    </table>
</div>
</div>
 <div class="clearfix"></div>
 
 <label for="confirm" onclick=" checked_sogl();" style="margin-top: 12px;">
     
     <input type="checkbox" value="1" name="confirm" id="confirm" required="required">        Отметьте, что Вы согласны с <a href="http://www.ruslania.com/language-2/context-2120.html" target="_blank">условиями пользования</a> виртуальным магазином Руслания и с обработкой персональных данных (<a href="https://ruslania.com/download/Rekisteriseloste_ruslania_eng.pdf" target="_blank">заявление о  конфиденциальности Руслании</a> на английском языке) </label>
     <span style="color: #ff0000; font-size: 12px;" class="err_confirm"></span>

<div style="height: 20px;"></div>
 
 <div class="clearfix"></div>
 
 
 

 <div class="box_opacity" style="position: relative; margin: -10px; padding: 10px;">
     
     <div class="op" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.6; background: #eee; z-index: 999; "></div>
 
<div class="p1">2. Где и как вы хотите получить заказ?</div>
    
    <div class="row dtypes">
        
        <?php
            $country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
            //if ($country == 'FI' or $country == 'fi') {
        ?>
        
        <label class="seld span3 seld1" onclick="check_cart_sel($(this),'seld', 'dtype1'); show_all(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box,.delivery_box_sp').hide(); $('.delivery_name').html('Забрать в магазине'); sbros_delev()">
            <span class="zabr_market">Забрать в магазине</span>
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="1" name="dtype" style="display: none;" />
        </label>
        
         <?//php  } ?>
        <label class="seld span3 seld2" onclick="check_cart_sel($(this),'seld', 'dtype2'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('Доставка почтой'); sbros_delev();">
            Доставка почтой
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="0" name="dtype" style="display: none;" />
        </label>
</div>        
        <div class="clearfix"></div>
        
       
        
        <div class="delivery_box" style="display: none; margin: 15px 0;"></div>
        
        
        <div class="p2">3. Как вам будет удобнее оплатить заказ?</div>
        
        <div class="row spay">
        
        <label class="selp span3 oplata1" onclick="check_cart_sel($(this),'selp', 'dtype0')">
            Оплата в магазине
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype0" value="0" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata3" onclick="check_cart_sel($(this),'selp', 'dtype2')" style="width: 484px;">
            
            <img src="/images/pt2.png" style="margin-top: -3px;" />
            <span style="display: block; margin-top: 5px;">Кредитные карты и Финские банки</span>
            
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="25" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata2" onclick="check_cart_sel($(this),'selp', 'dtype1')">
            <img src="/images/pp.jpg" width="150" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype1" value="8" name="ptype" style="display: none;" />
        </label>
            
        
            
        <label class="selp span3 oplata4" onclick="check_cart_sel($(this),'selp', 'dtype3')">
            <div style="margin-top: -8px;"><b>Cчет-фактура</b><br /> Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС </div>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype3" value="7" name="ptype" style="display: none;" />
        </label>
        
        <label class="selp span3 oplata5" onclick="check_cart_sel($(this),'selp', 'dtype4')">
            <img src="/images/ap.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype4" value="26" name="ptype" style="display: none;" />
        </label> 
        
        <label class="selp span3 oplata6" onclick="check_cart_sel($(this),'selp', 'dtype5')">
            <img src="/images/app.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype5" value="27" name="ptype" style="display: none;" />
        </label> 
            
        <label class="selp span3 oplata7" onclick="check_cart_sel($(this),'selp', 'dtype6')">
            Предоплата на банковский счет в Финляндии
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype6" value="13" name="ptype" style="display: none;" />
        </label>
            
        <label class="selp span3 oplata8" onclick="check_cart_sel($(this),'selp', 'dtype7')">
            Предоплата на банковский счет в России
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype7" value="14" name="ptype" style="display: none;" />
        </label>    
            
        </div>    
        
         <div class="clearfix"></div>
        
        </div> 
<?php $this->endWidget(); ?>
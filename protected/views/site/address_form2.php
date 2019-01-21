<?php $form = $this->beginWidget('KnockoutForm', array(
                                                      'model' => $model,
                                                      'action' => $mode == 'new' ? Yii::app()->createUrl('cart'). 'result/' : Yii::app()->createUrl('cart'). 'result/',
                                                      'id' => 'add-address',
                                                      'viewModel' => 'addressVM',
                                                      'afterAjaxSubmit' => $afterAjax,
                                                      'htmlOptions' => array('class' => 'address text'),
                                                 )); ?>


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
<div class="p3">1. Укажите ваши личные данные</div>
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
        width: 360px;
    }
</style>

<table class="address">
    <tbody>
        
    <tr>
        <td style="width: 200px;"><b><?=$ui->item("address_type"); ?></b></td>
        <td class="maintxt">
            <label><?=$form->radioButton('type', array('value' => 1, 'uncheckValue' => null, 'onclick'=>'save_form()', 'class'=>'checkbox_custom')); ?><span class="checkbox-custom"></span>
            <?= $ui->item("MSG_PERSONAL_ADDRESS_COMPANY"); ?></label>
            <label><?=$form->radioButton('type', array('value' => 2, 'uncheckValue' => null, 'onclick'=>'save_form()', 'class'=>'checkbox_custom')); ?><span class="checkbox-custom"></span>
            <?=$ui->item("MSG_PERSONAL_ADDRESS_PERSON"); ?></label></td>
    </tr>
	
	 <tr>
        <td colspan="2"><label><input type="checkbox" onchange="check_desc_address($(this))" name="check_addressa" value="1" class="check_addressa checkbox_custom"/><span class="checkbox-custom"></span> Отметьте, если хотите забрать заказ в магазине. В этом случае адрес указывать не обязательно.</label></td>
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
        <td nowrap="" class="maintxt country_lbl">
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
        <td nowrap="" class="maintxt city_lbl"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("address_city"); ?>
        </td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('city', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt postindex_lbl"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_postindex"); ?></td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('postindex', array('oninput'=>'save_form()')); ?>
            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt streetaddress_lbl"><span style="width: 5pt"
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
        <td nowrap="" class="maintxt contact_phone_lbl"><span style="width: 5pt"
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
        <td nowrap="" class="maintxt">&nbsp;</td>
        <td class="maintxt-vat">
            
            <img src="/pic1/loader.gif" data-bind="visible: disableSubmitButton" />
        </td>
        <td class="smalltxt1"></td>
    </tr>


   
    
    </tbody>
</table>



</div>


 

<div class="span7" style="float: right; width: 575px; margin-top: 21px;">

        <div class="cart_header" style="width: 553px;">
            В корзине <?=decline_goods($cart['fullInfo']['count'])?> на сумму <?=$PH->FormatPrice($cart['fullInfo']['cost']);?>
        </div>
    
    <div class="cart_box">
    
        <table class="cart" style="width: 100%;">
        <tbody>
            
            
            <?php foreach ($cart['items'] as $id => $item) : ?>
            
            <tr>
                
                <td style="width: 35px; height: 35px;">
                    <span class="entity_icons"><i class="fa e<?= $item['entity'] ?>"></i></span>

                    <?php /*
                    <img width="31" height="31" align="middle" alt="" style="vertical-align: middle" src="/pic1/cart_ibook.gif">
 */ ?>
                </td>
                <td>
                    <span class="a"><?=$item['title']?></span>
                    <div class="minitext"><?=$item['month_count']?> <?if ($item['entity'] == 30) { echo 'мес.'; } else {?>шт.<? } ?> x <?=$PH->FormatPrice($item['price']);?><br /> Вес: <?=($item['weight'])?> кг</div>
                </td>
                
            </tr>
            
            <?php endforeach; ?>
            
        </tbody>
        
    </table>
</div>
</div>
 <div class="clearfix"></div>
 
 <label for="confirm" onclick=" checked_sogl();">
     <input type="checkbox" class="checkbox_custom" value="1" name="confirm" id="confirm"><span class="checkbox-custom"></span>     Отметьте, что Вы согласны с <a href="javascript:;" onclick="$('.popup0').show(); $('.popup1').css('top', ($(window).scrollTop() + 40) + 'px')">условиями пользования</a> виртуальным магазином Руслания и с обработкой персональных данных (<a href="https://ruslania.com/download/Rekisteriseloste_ruslania_eng.pdf" target="_blank">заявление о  конфиденциальности Руслании</a> на английском языке) </label>
<span style="color: #ff0000; font-size: 12px;" class="err_confirm"></span>

<div class="popup0 popup<?=$p['id']?>" style="background-color: rgba(0,0,0,0.3); position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 99999; opacity: 0.3; display: none;" onclick="$('.popup0').hide();"></div>
    <div style="background-color: rgb(255, 255, 255); position: absolute; padding: 1%; width: 88%; z-index: 99999; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px; left: 5%; top: 5%; display: none; z-index: 999991; margin-bottom: 5%;" class="popup0 popup1">
        
        <div style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="$('.popup0').hide();">X</div>
        
        <style> .popup0 div { width: auto !important; } </style>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . '/pictures/templates-static/conditions_'.Yii::app()->language.'.html.php'); ?>
    
    </div>

<div style="height: 20px;"></div>
 
 <div class="clearfix"></div>
 
 
 <input class="valute" type="hidden" value="<?=Currency::ToSign();?>" />

 <div class="box_opacity" style="position: relative; margin: -10px; padding: 10px;">
     
     <div class="op" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.6; background: #eee; z-index: 999; "></div>
 
<div class="p1">2. Где и как вы хотите получить заказ?</div>
    
    <div class="row dtypes">
        
		<div class="texterror"></div>
		
        <div style="position: relative; display: inline-block; width: auto; height: 160px;float: left; margin-left: 0;" class="span3 row_del1"><div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$('.info_box').hide(); $('.info_box.info_box_smart1').toggle();" class="qbtn2"> ? </div><div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart1">Доставка на дом или на адрес доставки почтовым письмом/бандеролью, без отслеживания (without tracking number)</div><label class="seld span3 seld02" rel="8.3" valute="$" onclick="check_delivery($(this)); check_cart_sel($(this),'seld', 'dtype2'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('Доставка почтой'); $('.type_delivery').val('Доставка почтой'); $('.selt1').click();$('.oplata3').click();" style="border: 1px solid rgb(204, 204, 204);"><div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span> 
            </div><input type="radio" value="3" name="dtype" rel="8.3USD" onchange="$('.smartpost_index').val(''); $('.box_smartpost').html(''); $('.select_dd_box').hide(); $('.selt .check').removeClass('active'); $('.check', $(this).parent()).addClass('active');" style="display: none;" id="dtype2">Доставка почтой<br>Economy <br>2-5 дней <br><span style="color: #70C67C; font-weight: bold;">8.3$</span></label></div>
			
			<div style="position: relative; display: inline-block; width: auto; height: 160px; float: left; margin-left: 0;" class="span3 row_del2"><div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$('.info_box').hide(); $('.info_box.info_box_smart2').toggle();" class="qbtn2"> ? </div><div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart2">Доставка на дом или на адрес доставки почтовым Priority-письмом/бандеролью, без отслеживания (without tracking number), обслуживание Priority</div><label class="seld span3 seld03" rel="11.8" valute="$" onclick="check_delivery($(this)); check_cart_sel($(this),'seld', 'dtype3'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('Доставка почтой'); $('.type_delivery').val('Доставка почтой'); $('.selt1').click();$('.oplata3').click();" style="border: 1px solid rgb(204, 204, 204);"><div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span> 
            </div><input type="radio" value="2" name="dtype" rel="11.8USD" onchange="$('.smartpost_index').val(''); $('.box_smartpost').html(''); $('.select_dd_box').hide(); $('.selt .check').removeClass('active'); $('.check', $(this).parent()).addClass('active');" style="display: none;" id="dtype3">Доставка почтой<br>Priority <br>1-3 дней <br><span style="color: #70C67C; font-weight: bold;">11.8$</span></label></div>
			
			
			<div style="position: relative; display: inline-block; width: auto; height: 160px; float: left; margin-left: 0;" class="span3 row_del3"><div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$('.info_box').hide(); $('.info_box.info_box_smart3').toggle();" class="qbtn2"> ? </div><div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart3">Доставка на дом или на адрес доставки почтовым срочным письмом/бандеролью, c отслеживанием (with tracking number), обслуживание Express</div><label class="seld span3 seld04 act" rel="23.6" valute="$" onclick="check_delivery($(this)); check_cart_sel($(this),'seld', 'dtype4'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('Доставка почтой'); $('.type_delivery').val('Доставка почтой'); $('.selt1').click();$('.oplata3').click();" style="border: 1px solid rgb(100, 113, 127);"><div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check active"></span></span> 
            </div><input type="radio" value="1" name="dtype" rel="23.6USD" onchange="$('.smartpost_index').val(''); $('.box_smartpost').html(''); $('.select_dd_box').hide(); $('.selt .check').removeClass('active'); $('.check', $(this).parent()).addClass('active');" style="display: none;" id="dtype4" checked="checked">Доставка почтой<br>Express <br>1-2 дней <br><span style="color: #70C67C; font-weight: bold;">23.6$</span></label></div>
        
        <label class="seld span3 seld1" onclick="check_cart_sel($(this),'seld', 'dtype0'); show_all(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box,.delivery_box_sp').hide(); $('.delivery_name').html('Забрать в магазине'); sbros_delev(); hide_oplata(4); hide_oplata(5); hide_oplata(7); <?if (Yii::app()->Language != 'ru' AND Yii::app()->Language != 'rut') : ?>hide_oplata(8)<? endif; ?>" style="height: 40px;">
            <span class="zabr_market">Забрать в магазине в Хельсинки</span>
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype0" value="0" name="dtype" style="display: none;" />
        </label>
         
		  <div class="clearfix"></div>
		 <div class="delivery_box" style="display: none; margin: 15px 0;"></div>
		 
</div>        
        <div class="clearfix"></div>
        
               
      
        
        
        <div class="p2">3. Как вам будет удобнее оплатить заказ?</div>
        
        <div class="row spay">
        
        <label class="selp span3 oplata1" onclick="check_cart_sel($(this),'selp', 'ptype0')">
            Оплата в магазине
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype0" value="0" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata3" onclick="check_cart_sel($(this),'selp', 'ptype2')" style="width: 484px;">
            
            <img src="/images/pt2.png" style="margin-top: -3px;" />
            <span style="display: block; margin-top: 5px;">Кредитные карты и Финские банки</span>
            
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype2" value="25" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata2" onclick="check_cart_sel($(this),'selp', 'ptype1')">
            <img src="/images/pp.jpg" width="150" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype1" value="8" name="ptype" style="display: none;" />
        </label>
            
        
            
        <label class="selp span3 oplata4" onclick="check_cart_sel($(this),'selp', 'ptype3')">
            <div style="margin-top: -8px;"><b>Cчет-фактура</b><br /> Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС </div>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype3" value="7" name="ptype" style="display: none;" />
        </label>
        
        <label class="selp span3 oplata5" onclick="check_cart_sel($(this),'selp', 'ptype4')">
            <img src="/images/ap.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype4" value="26" name="ptype" style="display: none;" />
        </label> 
        
        <label class="selp span3 oplata6" onclick="check_cart_sel($(this),'selp', 'ptype5')">
            <img src="/images/app.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype5" value="27" name="ptype" style="display: none;" />
        </label> 
            
        <label class="selp span3 oplata7" onclick="check_cart_sel($(this),'selp', 'ptype6')">
            Предоплата на банковский счет в Финляндии
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype6" value="13" name="ptype" style="display: none;" />
        </label>
            
        <label class="selp span3 oplata8" onclick="check_cart_sel($(this),'selp', 'ptype7')">
            Предоплата на банковский счет в России
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype7" value="14" name="ptype" style="display: none;" />
        </label>    
            
        </div>    
        
         <div class="clearfix"></div>
        
        </div> 
<?php $this->endWidget(); ?>
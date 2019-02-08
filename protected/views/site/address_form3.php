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
	
	$pickpoint = false;
	
	foreach ($cart['items'] as $id => $item) :
	
		if ($item['entity'] != 30) {
			
			$pickpoint = true;
			break;
			
		}
	
	endforeach;
	
?>



<div class="span12" style="margin-left: 0;">
<div class="p3 step1"><?=$ui->item('CARTNEW_REG_STEP1_TITLE')?></div>
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
    
    echo '<select name="id_address" style="margin-bottom: 0;margin-right: 8px; width: 60%" onchange="checked_sogl()">'.((count($addr_list) > 1) ? '<option value="">'.$ui->item('CARTNEW_ERROR_SELECT_ADDR_DELIVERY').'</option>' : '' );

    $ch = new CommonHelper();

    foreach ($addr_list as $addr) {
        
        $adr_str = $ch->FormatAddress($addr);
        
        echo '<option value="'.$addr['address_id'].'">'.$adr_str.'</option>';

    }

    echo '</select>';
    
}else {

    echo '<select name="id_address" style="margin-bottom: 0;margin-right: 8px;" onchange="checked_sogl()"><option value="">'.$ui->item('CARTNEW_ADDR_DELIVERY_LABEL2').'</option>';

    echo '</select>';

}

?>
<!--
<?php
echo '<div class="addr_buyer" style="margin-top: 10px">';
if (count($addr_list)) {
    
    echo '<label style="font-weight: bold;">'.$ui->item('CARTNEW_ADDR_BUYER_LABEL').'</label><select name="id_address_b" style="margin-bottom: 0;margin-right: 8px;" onchange="checked_sogl()">'.((count($addr_list) > 1) ? '<option value="">'.$ui->item('CARTNEW_ERROR_SELECT_ADDR_BUYER').'</option>' : '' );

    $ch = new CommonHelper();
    
      
    
    foreach ($addr_list as $addr) {
        
        $adr_str = $ch->FormatAddress($addr);

        echo '<option value="'.$addr['address_id'].'">'.$adr_str.'</option>';

    }

    echo '</select>';
    
} else {

    echo '<label style="font-weight: bold;">'.$ui->item('ORDER_MSG_BILLING_ADDRESS').'</label><select name="id_address_b" style="margin-bottom: 0;margin-right: 8px;" onchange="checked_sogl()"><option value="">'.$ui->item('CARTNEW_ADDR_BUYER_LABEL2').'</option>';

    echo '</select>';

}

echo '</div>';

?>
-->
    <!--<a href="javascript:;" onclick="hide_dostavka($(this))" class="btn btn-link" style="margin-top: 10px;">Доставка не нужна</a>-->
    <? $user = User::getUserID(Yii::app()->user->id); ?>

<a href="javascript:;" onclick="$('select, input').removeClass('error'); $('span.texterror').html(''); $('table.addr1, .btn.btn-success.addr1,.cancel_add_adr').toggle('fade');" class="order_start" style="margin-top: 0; padding: 6px 0; background-color: #28618E; width: 40px;">+</a></div>
<div style="clear: both"></div>
<table class="address addr1" style="display: none; margin-top: 10px; width: 570px;">
    <tbody>
        
    <tr>
        <td style="width: 200px;"><b><?=$ui->item("address_type"); ?></b></td>
        <td class="maintxt">
            <label style="float: left; margin-right: 20px;"><?=$form->radioButton('type', array('value' => 1, 'uncheckValue' => null, 'onclick'=>'save_form()', 'class'=>'checkbox_custom')); ?><span class="checkbox-custom"></span>
            <?= $ui->item("MSG_PERSONAL_ADDRESS_COMPANY"); ?></label>
            <label style="float: left; "><?=$form->radioButton('type', array('value' => 2, 'uncheckValue' => null, 'onclick'=>'save_form()', 'class'=>'checkbox_custom')); ?><span class="checkbox-custom"></span>
            <?=$ui->item("MSG_PERSONAL_ADDRESS_PERSON"); ?></label></td>
    </tr>
	
	 <tr>
        <td colspan="2"><label><input type="checkbox" onchange="check_desc_address($(this))" class="check_addressa checkbox_custom"/><span class="checkbox-custom"></span> <?=$ui->item("CARTNEW_CHECK_NO_ADDR"); ?></label></td>
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
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("address_contact_email", array('oninput'=>'save_form()')); ?>
        </td>
        <td class="maintxt-vat" colspan="2" style="position: relative;">
            <?= $form->textField('contact_email', array('disabled'=>"true")); ?>
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
        <td nowrap="" class="maintxt"><?=$ui->item("address_contact_notes2"); ?></td>
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
   
    
   
    
    </tbody>
</table>

<div style="width: 570px;">

<a href="javascript:;" class="btn btn-success addr1" style="float: right; display: none; margin-right: 5px;" onclick="add_address(1)"><?=$ui->item('CARTNEW_BTN_ADD_ADDRESS')?></a>
<a href="javascript:;" onclick="$('table.addr1, .btn.btn-success.addr1, .cancel_add_adr').toggle('fade');" class="cancel_add_adr btn btn-link" style="display: none; float: right;"><?=$ui->item('CARTNEW_BTN_CANCEL_ADDRESS')?></a>
 </div>
<div class="clearfix" style="margin: 5px 0;"></div>


 </div>

 <?php
		$return = true;
		$hide_delivery = 'Y';
		$sum_weight = 0;
		 foreach ($cart['items'] as $id => $item) : ?>
			
            <?
		
		$sum_weight += $item['weight'];

		?>

           
            
            <?php endforeach; ?>
 <div class="clearfix"></div>
 
<script>

	<? if ($sum_weight == '0') { ?>

	$(document).ready(function() {

		$('.row_del2, .row_del3, .seld1').hide();

	})

	<? } else { ?>
		
		$(document).ready(function() {

			$('.row_del2, .row_del3, .seld1').show();

		})


	<? } ?>

</script>
 <label for="confirm" onclick=" checked_sogl();" style="margin-top: 12px;">
     
     <input type="checkbox" class="checkbox_custom" value="1" name="confirm" id="confirm" required="required"> <span class="checkbox-custom"></span>       <?=$ui->item('CARTNEW_CHECKBOX_TERMS_OF_USE')?> </label>
     <span style="color: #ff0000; font-size: 12px;" class="err_confirm"></span>

	 <div class="popup0 popup<?=$p['id']?>" style="background-color: rgba(0,0,0,0.3); position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 99999; opacity: 0.3; display: none;" onclick="$('.popup0').hide();"></div>
    <div style="background-color: rgb(255, 255, 255); position: absolute; padding: 1%; width: 88%; z-index: 99999; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px; left: 5%; top: 5%; display: none; z-index: 999991; margin-bottom: 5%;" class="popup0 popup1">
        
        <div style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="$('.popup0').hide();">X</div>
        
        <style> .popup0 div { width: auto !important; } </style>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . '/pictures/templates-static/conditions_'.Yii::app()->language.'.html.php'); ?>
    
    </div>
	 
<div style="height: 20px;"></div>
 
 <div class="clearfix"></div>
 
 
 

 <div class="box_opacity" style="position: relative; margin: -10px; padding: 10px;">
     
     <div class="op" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.6; background: #eee; z-index: 999; "></div>
 
<div class="p1">
<? if ($pickpoint) : ?>
<?=$ui->item('CARTNEW_NOREG_STEP2_TITLE')?>
<? else : ?>
2. Доставка включена в стоимость
<? endif;  ?>

</div>

   <div class="row dtypes">
         
			<div style="position: relative; display: inline-block; width: auto; height: 160px;float: left; margin-left: 0;" class="span3 row_del1"><div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$('.info_box').hide(); $('.info_box.info_box_smart1').toggle();" class="qbtn2"> ? </div><div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart1"><?=$ui->item('DELIVERY_ECONOMY_OTHER');?></div><label class="seld span3 seld02" rel="8.3" valute="$" onclick="check_delivery($(this)); check_cart_sel($(this),'seld', 'dtype2'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>'); $('.type_delivery').val('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>'); $('.selt1').click();$('.oplata3').click();" style="border: 1px solid rgb(204, 204, 204);"><div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span> 
            </div><input type="radio" value="3" name="dtype" rel="8.3USD" onchange="$('.smartpost_index').val(''); $('.box_smartpost').html(''); $('.select_dd_box').hide(); $('.selt .check').removeClass('active'); $('.check', $(this).parent()).addClass('active');" style="display: none;" id="dtype2"><?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?><br>Economy <br>2-5 дней <br><span style="color: #70C67C; font-weight: bold;">0<?=Currency::ToSign()?></span></label></div>
			
			<div style="position: relative; display: inline-block; width: auto; height: 160px; float: left; margin-left: 0;" class="span3 row_del2"><div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$('.info_box').hide(); $('.info_box.info_box_smart2').toggle();" class="qbtn2"> ? </div><div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart2"><?=$ui->item('DELIVERY_PRIORITY_OTHER')?></div><label class="seld span3 seld03" rel="11.8" valute="$" onclick="check_delivery($(this)); check_cart_sel($(this),'seld', 'dtype3'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>'); $('.type_delivery').val('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>'); $('.selt1').click();$('.oplata3').click();" style="border: 1px solid rgb(204, 204, 204);"><div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span> 
            </div><input type="radio" value="2" name="dtype" rel="11.8USD" onchange="$('.smartpost_index').val(''); $('.box_smartpost').html(''); $('.select_dd_box').hide(); $('.selt .check').removeClass('active'); $('.check', $(this).parent()).addClass('active');" style="display: none;" id="dtype3"><?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?><br>Priority <br>1-3 дней <br><span style="color: #70C67C; font-weight: bold;">0<?=Currency::ToSign()?></span></label></div>
			
			
			<div style="position: relative; display: inline-block; width: auto; height: 160px; float: left; margin-left: 0;" class="span3 row_del3"><div style="display: inline-block;border-radius: 50%;background-color: #edb421;padding: 5px;width: 18px;font-weight:  bold;height: 18px;font-size: 17px;text-align: center;line-height: 18px;margin-left: 15px; cursor: pointer; float: right;margin-right: 38px; position: absolute;z-index: 99999;left: 195px;top: 40px;" onclick="$('.info_box').hide(); $('.info_box.info_box_smart3').toggle();" class="qbtn2"> ? </div><div style="background-color: rgb(255, 255, 255);position: absolute;padding: 20px;width: 300px;z-index: 999991111;border-radius: 2px;box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px;left: 250px; top: 40px;display: none" class="info_box info_box_smart3"><?=$ui->item('DELIVERY_EXPRESS_OTHER');?></div><label class="seld span3 seld04 act" rel="23.6" valute="$" onclick="check_delivery($(this)); check_cart_sel($(this),'seld', 'dtype4'); showALL(); hide_oplata(1); $('.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>'); $('.type_delivery').val('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>'); $('.selt1').click();$('.oplata3').click();" style="border: 1px solid rgb(100, 113, 127);"><div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check active"></span></span> 
            </div><input type="radio" value="1" name="dtype" rel="23.6USD" onchange="$('.smartpost_index').val(''); $('.box_smartpost').html(''); $('.select_dd_box').hide(); $('.selt .check').removeClass('active'); $('.check', $(this).parent()).addClass('active');" style="display: none;" id="dtype4" checked="checked"><?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?><br>Express <br>1-2 дней <br><span style="color: #70C67C; font-weight: bold;">0<?=Currency::ToSign()?></span></label></div>
		
		
		
		
        <label class="seld span3 seld1" onclick="check_cart_sel($(this),'seld', 'dtype1'); show_all(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_name').html('<?=$ui->item('MSG_DELIVERY_TYPE_0');?>'); $('.type_delivery').val('<?=$ui->item('MSG_DELIVERY_TYPE_0');?>'); sbros_delev(); $('.oplata3').click(); hide_oplata(4); hide_oplata(5); hide_oplata(7); <?if (Yii::app()->Language != 'ru' AND Yii::app()->Language != 'rut') : ?>hide_oplata(8)<? endif; ?>;$('.address.addr2, label.addr_buyer, div.addr_buyer').hide()">
            <span class="zabr_market"><?=$ui->item('CARTNEW_PICK_UP_STORE1');?></span>
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="0" name="dtype" style="display: none;" />
        </label>
        
        <div class="clearfix"></div>
		 <div class="delivery_box" style="display: none; margin: 15px 0;"></div>
        
</div>        
        
		
		
		<div class="clearfix"></div>
        
       
        
        
        
        
        <div class="p2"><?=$ui->item('CARTNEW_NOREG_STEP3_TITLE');?></div>
        
		<div><label class="addr_buyer"><input type="checkbox" class="checkbox_custom" value="1" name="addr_buyer" id="addr_buyer" checked="checked" onclick="$('div.addr_buyer').toggle(); $('.block_addr_add_2').hide();"><span class="checkbox-custom"></span> Данные Плательщика совпадают с Получателем </label>
   
   <?php
echo '<div class="addr_buyer" style="margin-top: 10px; display: none;">';
if (count($addr_list)) {
    
    echo '<label style="font-weight: bold;">'.$ui->item('CARTNEW_ADDR_BUYER_LABEL').'</label><select name="id_address_b" style="margin-bottom: 0;margin-right: 8px; width: 400px;" onchange="checked_sogl()">'.((count($addr_list) > 1) ? '<option value="">'.$ui->item('CARTNEW_ERROR_SELECT_ADDR_BUYER').'</option>' : '' );

    $ch = new CommonHelper();
    
      
    
    foreach ($addr_list as $addr) {
        
        $adr_str = $ch->FormatAddress($addr);

        echo '<option value="'.$addr['address_id'].'">'.$adr_str.'</option>';

    }

    echo '</select>';
    
} else {

    echo '<label style="font-weight: bold;">'.$ui->item('ORDER_MSG_BILLING_ADDRESS').'</label><select name="id_address_b" style="margin-bottom: 0;margin-right: 8px;" onchange="checked_sogl()"><option value="">'.$ui->item('CARTNEW_ADDR_BUYER_LABEL2').'</option>';

    echo '</select>';

}

echo '<a href="javascript:;" onclick="$(\'select, input\').removeClass(\'error\'); $(\'span.texterror\').html(\'\'); $(\'.block_addr_add_2, .address.addr2\').show(\'fade\'); " class="order_start" style="padding: 6px 0; background-color: #28618E; width: 40px;">+</a></div>';

?>
   <div class="block_addr_add_2" style="display: none; width: 582px;">
   <table class="address addr2" style=" margin-top: 10px; width: 570px; display: none">
    <tbody>
        
    <tr>
        <td style="width: 200px;"><b>Плательщик:</b></td>
        <td class="maintxt">
            <label style="float: left; margin-right: 20px;"><input value="1" onclick="$('.l1').show()" class="checkbox_custom" name="Address2[type]" id="Address2_type" type="radio"><span class="checkbox-custom"></span>
            Организация</label>
            <label style="float: left; "><input value="2" onclick="$('.l1').hide()" class="checkbox_custom"  name="Address2[type]" id="Address2_type" type="radio" checked="checked"><span class="checkbox-custom"></span>
            Частное лицо</label></td>
    </tr>
	<tr class="l1" style="display: none;">
        <td nowrap="" class="maintxt">Название организации        </td>
        <td class="maintxt-vat">
            <input name="Address2[business_title]" id="Address2_business_title" type="text" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr class="l1" style="display: none;">
        <td nowrap="" class="maintxt">Номер VAT</td>
        <td class="maintxt-vat">
            <input name="Address2[business_number1]" id="Address2_business_number1" type="text" value="" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span>Фамилия</td>
        <td class="maintxt-vat">
            <input name="Address2[receiver_last_name]" id="Address2_receiver_last_name" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span>Имя</td>
        <td class="maintxt-vat">
            <input name="Address2[receiver_first_name]" id="Address2_receiver_first_name" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt">Отчество</td>
        <td class="maintxt-vat">
            <input oninput="save_form()" name="Address2[receiver_middle_name]" id="Address2_receiver_middle_name" type="text" value="" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt country_lbl">
            <span style="width: 5pt" class="redtext">*</span>Cтрана</td>
        <td colspan="2" class="maintxt-vat">
            <select onclick="change_city2($(this)); save_form();" onchange="change_city2($(this));" name="Address2[country]" id="Address2_country" class=""><option value="">---</option>
			
			<?
			
			$list = CHtml::listData(Country::GetCountryList(), 'id', 'title_en');
			
			
			foreach ($list as $k=>$v) {
			?>
			
			<option value="<?=$k?>"><?=$v?></option>
			
			<?
			}			
			?>
			
			
			</select>            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    
    <tr class="states_list2" style="display: none">
        <td nowrap="" class="maintxt">Штат</td>
        <td class="maintxt-vat select_states2"><select name="Address[state_id]" onclick="save_form();"><option value="">---</option></select></td>
        
    </tr>
    
    
    <tr>
        <td nowrap="" class="maintxt city_lbl"><span style="width: 5pt" class="redtext">*</span>Город        </td>
        <td colspan="2" class="maintxt-vat">
            <input name="Address2[city]" id="Address2_city" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt postindex_lbl"><span style="width: 5pt" class="redtext">*</span>Почтовый индекс</td>
        <td colspan="2" class="maintxt-vat">
            <input name="Address2[postindex]" id="Address2_postindex" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt streetaddress_lbl"><span style="width: 5pt" class="redtext">*</span>Адрес</td>
        <td class="maintxt-vat">
            <input placeholder="Улица, дом, квартира, и т.д., в любом порядке" name="Address2[streetaddress]" id="Address2_streetaddress" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span>Контактный e-mail        </td>
        <td class="maintxt-vat" colspan="2" style="position: relative;">
            <input name="Address2[contact_email]" id="Address2_contact_email" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt contact_phone_lbl"><span style="width: 5pt" class="redtext">*</span>Контактный телефон</td>
        <td class="maintxt-vat">
            <input name="Address2[contact_phone]" id="Address2_contact_phone" type="text" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1">
            
                    </td>
    </tr>    
    <tr>
        <td nowrap="" class="maintxt">&nbsp;</td>
        <td class="maintxt-vat">
            
            <img src="/pic1/loader.gif" data-bind="visible: disableSubmitButton" style="display: none;">
        </td>
        <td class="smalltxt1"></td>
    </tr>
   
    
   
    
    </tbody>
</table>	
		
<a href="javascript:;" class="btn btn-success addr2" style="float: right; margin-right: 5px;" onclick="add_address2(2)"><?=$ui->item('CARTNEW_BTN_ADD_ADDRESS')?></a>
<a href="javascript:;" onclick="$('.block_addr_add_2').hide();" class="cancel_add_adr2 btn btn-link" style=" float: right;"><?=$ui->item('CARTNEW_BTN_CANCEL_ADDRESS')?></a>

</div>

<div class="clearfix" style="margin: 5px 0;"></div>
		
        <div class="row spay">
        
        <label class="selp span3 oplata1" onclick="check_cart_sel($(this),'selp', 'ptype0')">
            <?=$ui->item('CARTNEW_PAY_IN_STORE');?>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype0" value="0" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata3" onclick="check_cart_sel($(this),'selp', 'ptype2')" style="width: 484px;">
            
            <img src="/images/pt2.png" style="margin-top: -3px;" />
            <span style="display: block; margin-top: 5px;"><?=$ui->item('CARTNEW_PAYTRAYL_LABEL');?></span>
            
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
            <?=$ui->item('CARTNEW_PAY_INVOICE_LABEL')?>
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
            <?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT1')?>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="ptype6" value="13" name="ptype" style="display: none;" />
        </label>
            
        <label class="selp span3 oplata8" onclick="check_cart_sel($(this),'selp', 'pype7')">
            <?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT2')?>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="pype7" value="14" name="ptype" style="display: none;" />
        </label>    
            
        </div>    
        
         <div class="clearfix"></div>
        
        </div> 
<?php $this->endWidget(); ?>

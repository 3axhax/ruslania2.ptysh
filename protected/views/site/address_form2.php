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

<div class="p3">1. Укажите ваши личные данные</div>

<div class="span7">

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
            <label><?=$form->radioButton('type', array('value' => 1, 'uncheckValue' => null)); ?>
            <?= $ui->item("MSG_PERSONAL_ADDRESS_COMPANY"); ?></label>
            <label><?=$form->radioButton('type', array('value' => 2, 'uncheckValue' => null)); ?>
            <?=$ui->item("MSG_PERSONAL_ADDRESS_PERSON"); ?></label></td>
    </tr>
    <tr data-bind="visible: type()==1">
        <td nowrap="" class="maintxt"><?=$ui->item("address_business_title"); ?>
        </td>
        <td class="maintxt-vat" data-bind="visible: type()==1">
            <?=$form->textField('business_title', array('data-bind' => array('enable' => 'type()==1'))); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr data-bind="visible: type()==1">
        <td nowrap="" class="maintxt"><?=$ui->item("address_business_number1"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('business_number1', array('data-bind' => array('enable' => 'type()==1'))); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><?=$ui->item("regform_titlename"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_title_name', array('placeholder'=>$ui->item("MSG_REGFORM_TITLENAME_TIP_1"))); ?>
        </td>
        
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("regform_lastname"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_last_name'); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("regform_firstname"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_first_name'); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><?=$ui->item("regform_middlename"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_middle_name'); ?>
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
                'onchange' => 'change_city($(this))'
                )); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><?=$ui->item("address_state"); ?></td>
        <td class="maintxt-vat">
            <?=$form->dropDownList('state_id', CHtml::listData(Country::GetStatesList(), 'id', 'title_long'),
            array(
                 'data-bind' => array('enable' => 'country()==225',
                                      'optionsCaption' => "'---'")
            )); ?><br/>
        </td>
        
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("address_city"); ?>
        </td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('city'); ?>
            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_postindex"); ?></td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('postindex'); ?>
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
            <?=$ui->item("address_contact_email"); ?>
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
            <?=$form->textField('contact_phone'); ?>
            <span class="texterror"></span>
        </td>
        <td class="smalltxt1">
            
            <?//<img width="8" height="7" src="/pic1/arr3.gif">=$ui->item('MSG_PERSONAL_ADDRESS_COMMENT_PHONE'); ?>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><?=$ui->item("address_contact_notes"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textArea('notes'); ?>
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



</div>


 

<div class="span7" style="float: right; width: 575px;">

        <div class="cart_header" style="width: 553px;">
            В корзине <?=decline_goods($cart['fullInfo']['count'])?> на сумму <?=$PH->FormatPrice($cart['fullInfo']['cost']);?>
        </div>
    
    <div class="cart_box">
    
        <table class="cart" style="width: 100%;">
        <tbody>
            
            
            <?php foreach ($cart['items'] as $id => $item) : ?>
            
            <tr>
                
                <td style="width: 31px;"><img width="31" height="31" align="middle" alt="" style="vertical-align: middle" data-bind="attr: { alt: Title}" src="/pic1/cart_ibook.gif"></td>
                <td>
                    <span class="a"><?=$item['title']?></span>
                    <div class="minitext"><?=$item['quantity']?> шт. x <?=$PH->FormatPrice($item['price']);?><br /> Вес: <?=($item['weight']/1000)?> кг</div>
                </td>
                
            </tr>
            
            <?php endforeach; ?>
            
        </tbody>
        
    </table>
</div>
</div>
 <div class="clearfix"></div>
 
 <label for="confirm" onclick=" checked_sogl();">
     <input type="checkbox" value="1" name="confirm" id="confirm">        Отметьте, что Вы согласны с <a href="http://www.ruslania.com/language-2/context-2120.html" target="_blank">условиями пользования</a> виртуальным магазином Руслания и с обработкой персональных данных (<a href="https://ruslania.com/download/Rekisteriseloste_ruslania_eng.pdf" target="_blank">заявление о  конфиденциальности Руслании</a> на английском языке) </label>

<div style="height: 20px;"></div>
 
 <div class="clearfix"></div>
 
 
 

 <div class="box_opacity" style="position: relative; margin: -10px; padding: 10px;">
     
     <div class="op" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.6; background: #eee; z-index: 999; "></div>
 
<div class="p1">2. Где и как вы хотите получить заказ?</div>
    
    <div class="row">
        
        <?php
            $country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
            //if ($country == 'FI' or $country == 'fi') {
        ?>
        
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype1'); show_all(); $('.select_dd_box').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box,.delivery_box_sp').hide(); $('.delivery_name').html('Забрать в магазине'); sbros_delev()">
            Забрать в магазине
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="1" name="dtype" style="display: none;" />
        </label>
        
        <label class="seld span3" onclick="showALL(); hide_oplata(1); hide_oplata(8); check_cart_sel($(this),'seld', 'dtype3'); $('.delivery_box_sp').show(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box, .select_dd_box').hide(); $('.delivery_name').html('SmartPost'); sbros_delev()">
            SmartPost
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype3" value="2" name="dtype" style="display: none;" />
            
        </label> <?//php  } ?>
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype2'); showALL(); hide_oplata(1); $('.select_dd_box,.delivery_box_sp').hide(); $('.rows_checkbox_delivery input').prop('checked', false); $('.delivery_box').show(); $('.delivery_name').html('Доставка почтой'); sbros_delev();">
            Доставка почтой
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="0" name="dtype" style="display: none;" />
        </label>
</div>        
        <div class="clearfix"></div>
        
        
            
   
        
        <div class="delivery_box_sp" style="display: none; margin: 15px 0;"></div>
        
        <div class="delivery_box" style="display: none; margin: 15px 0;"></div>
        
        
        <div class="p2">3. Как вам будет удобнее оплатить заказ?</div>
        
        <div class="row spay">
        
        <label class="selp span3 oplata1" onclick="check_cart_sel($(this),'selp', 'dtype0')">
            Оплата в магазине
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype0" value="1" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata3" onclick="check_cart_sel($(this),'selp', 'dtype2')" style="width: 484px;">
            
            <img src="/images/pt2.png" style="margin-top: -3px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="3" name="ptype" style="display: none;" />
        </label>    
            
        <label class="selp span3 oplata2" onclick="check_cart_sel($(this),'selp', 'dtype1')">
            <img src="/images/pp.jpg" width="150" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype1" value="2" name="ptype" style="display: none;" />
        </label>
            
        
            
        <label class="selp span3 oplata4" onclick="check_cart_sel($(this),'selp', 'dtype3')">
            <div style="margin-top: -8px;"><b>Cчет-фактура</b><br /> Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС </div>
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype3" value="4" name="ptype" style="display: none;" />
        </label>
        
        <label class="selp span3 oplata5" onclick="check_cart_sel($(this),'selp', 'dtype4')">
            <img src="/images/ap.png" width="100" style="margin-top: -15px;" />
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype4" value="5" name="ptype" style="display: none;" />
        </label> 
        
        <label class="selp span3 oplata6" onclick="check_cart_sel($(this),'selp', 'dtype5')">
            <img src="/images/app.png" width="100" style="margin-top: -15px;" />
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
        
         <div class="clearfix"></div>
        
        </div> 
<?php $this->endWidget(); ?>
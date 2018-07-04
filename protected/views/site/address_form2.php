<?php $form = $this->beginWidget('KnockoutForm', array(
                                                      'model' => $model,
                                                      'action' => $mode == 'new' ? '/cart/result/' : '/cart/result/',
                                                      'id' => 'add-address',
                                                      'viewModel' => 'addressVM',
                                                      'afterAjaxSubmit' => $afterAjax,
                                                      'htmlOptions' => array('class' => 'address text'),
                                                 )); ?>

<div class="p1">1. Где и как вы хотите получить заказ?</div>
    
    <div class="row">
        
        <?php
            $country = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
            //if ($country == 'FI' or $country == 'fi') {
        ?>
        
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype1'); show_all(); $('.select_dd_box').hide()">
            Забрать в магазине
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="1" name="dtype" style="display: none;" />
        </label>
        
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype3'); showALL(); hide_oplata(1); hide_oplata(8); $('.select_dd_box').show()">
            Smart Post
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype3" value="2" name="dtype" style="display: none;" />
        </label> <?//php  } ?>
        <label class="seld span3" onclick="check_cart_sel($(this),'seld', 'dtype2'); showALL(); hide_oplata(1); $('.select_dd_box').hide()">
            Доставка почтой
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="0" name="dtype" style="display: none;" />
        </label>
</div>        
        <div class="clearfix"></div>
        
        <div class="select_dd_box" style="margin: 20px 0; display: none; ">
            
            <div class="select_dd">
                <div class="select_dd_input"><input type="text" placeholder="Введите индекс где хотие забрать отправление" onkeyup="postindex_input($(this))"></div>
                
                <div class="select_dd_popup">
                    
                     
                    
                </div>
                
                
            </div>
            
        </div>
        
        
        
        
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
        
         <div class="clearfix"></div>
        
        <div class="p3">3. Укажите ваши личные данные</div>

<table cellspacing="3" border="0" data-bind="visible: errors().length > 0">
    <tbody>
    <tr>
        <td valign="top"><img width="18" height="18" src="/pic1/warning1.gif"></td>
        <td width="100%" class="maintxt"><b><?=$ui->item('MSG_FORM_VALIDATE_ERROR'); ?>:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="maintxt">
            <ul class="err1" data-bind="foreach: errorStr">
                <li class="err1" data-bind="text: $data"></li>
            </ul>
        </td>
    </tr>
    </tbody>
</table>

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
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span><?=$ui->item("regform_firstname"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_first_name'); ?>
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
            array('data-bind' => array('optionsCaption' => "'---'"))); ?>
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
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_postindex"); ?></td>
        <td colspan="2" class="maintxt-vat">
            <?=$form->textField('postindex'); ?>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_streetaddress"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('streetaddress',array('placeholder'=>$ui->item("MSG_PERSONAL_ADDRESS_COMMENT_2"))); ?>
        </td>
        
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span>
            <?=$ui->item("address_contact_email"); ?>
        </td>
        <td class="maintxt-vat" colspan="2">
            <?=$form->textField('contact_email'); ?>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt"
                                            class="redtext">*</span><?=$ui->item("address_contact_phone"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('contact_phone'); ?>
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

    </tbody>
</table>
<?php $this->endWidget(); ?>
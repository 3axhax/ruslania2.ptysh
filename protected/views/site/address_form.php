<?php $form = $this->beginWidget('KnockoutForm', array(
                                                      'model' => $model,
                                                      'action' => $mode == 'new' ? '/ru/site/addaddress' : '/ru/site/editaddress',
                                                      'id' => 'add-address',
                                                      'viewModel' => 'addressVM',
                                                      'afterAjaxSubmit' => $afterAjax,
                                                      'htmlOptions' => array('class' => 'address text'),
                                                 )); ?>

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

<?php if($mode == 'edit')  echo $form->hiddenField('id'); ?>

<table class="address">
    <tbody>
    <tr>
        <td><b><?=$ui->item("address_type"); ?></b></td>
        <td class="maintxt">
            <label><?=$form->radioButton('type', array('value' => 1, 'uncheckValue' => null)); ?>
            <?= $ui->item("MSG_PERSONAL_ADDRESS_COMPANY"); ?></label>
            <label><?=$form->radioButton('type', array('value' => 2, 'uncheckValue' => null)); ?>
            <?=$ui->item("MSG_PERSONAL_ADDRESS_PERSON"); ?></label></td>
    </tr>
    <tr>
        <td style="padding-left: 10pt;" class="maintxt"><?=$ui->item("regform_titlename"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('receiver_title_name'); ?>
        </td>
        <td class="smalltxt1"><img width="8" height="7" src="/pic1/arr3.gif">
            <?=$ui->item("MSG_REGFORM_TITLENAME_TIP_1"); ?>
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
        <td style="padding-left: 10pt;" class="maintxt"><?=$ui->item("regform_middlename"); ?></td>
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
        <td class="smalltxt1"><img width="8" height="7" src="/pic1/arr3.gif"> <?=$ui->item("address_state"); ?></td>
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
            <?=$form->textField('streetaddress'); ?>
        </td>
        <td class="smalltxt1"><img width="8" height="7" src="/pic1/arr3.gif">
            <?=$ui->item("MSG_PERSONAL_ADDRESS_COMMENT_2"); ?>
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
        <td class="smalltxt1"><img width="8" height="7" src="/pic1/arr3.gif">
            <?=$ui->item('MSG_PERSONAL_ADDRESS_COMMENT_PHONE'); ?>
        </td>
    </tr>
    <tr>
        <td nowrap="" style="padding-left: 10pt;" class="maintxt"><?=$ui->item("address_contact_notes"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textArea('notes'); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt; visibility: hidden;" id="business_title_asterisk"
                                            class="redtext">*</span><?=$ui->item("address_business_title"); ?>
        </td>
        <td class="maintxt-vat">
            <?=$form->textField('business_title', array('data-bind' => array('enable' => 'type()==1'))); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" style="padding-left: 10pt;" class="maintxt"><?=$ui->item("address_business_number1"); ?></td>
        <td class="maintxt-vat">
            <?=$form->textField('business_number1', array('data-bind' => array('enable' => 'type()==1'))); ?>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" style="padding-left: 10pt;" class="maintxt">&nbsp;</td>
        <td class="maintxt-vat">

            <style>

                input.order_start {
                    display: inline-block;
                    width: 180px;
                    border-radius: 4px;
                    background-color: rgb(117, 132, 149);
                    border: 0;
                    padding: 9px 0;
                    text-align: center;
                    font-size: 14px;
                    color: rgb(255, 255, 255);
                    font-weight: bold;
                }


            </style>

            <input data-bind="visible: !disableSubmitButton()" type="submit" class="order_start" style="background-color: #5bb75b; padding: 9px; width:auto;" value="Сохранить"/>

            <img src="/pic1/loader.gif" data-bind="visible: disableSubmitButton" />
        </td>
        <td class="smalltxt1"></td>
    </tr>

    </tbody>
</table>
<?php $this->endWidget(); ?>
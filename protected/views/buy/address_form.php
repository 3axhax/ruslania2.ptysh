<?php /*Created by Кирилл (24.02.2019 20:17)
https://dfaktor.bitrix24.ru/workgroups/group/130/tasks/task/view/9065/?MID=39577&IFRAME=Y&IFRAME_TYPE=SIDE_SLIDER#com39577
когда новый адрес, не принимаем без адреса.
Но, когда редактируем уже имеющийся, то надо допустить редактирование даже без адреса.
Тут у нас клиент который делает отложенные заказы, у нее нет адреса. Но в имени ошибка. Мы не можем ошибку исправить без адреса.

Надо сделать так, чтобы по редактированию не требовала никаких пунктов.
 */
/**@var $this MyController*/
/**@var $form CActiveForm */
$countrys = Country::GetCountryList();
array_unshift($countrys, array('id'=>'','title_en'=>'---'));
if (empty($alias)) $alias = 'Address';
if (empty($userType)) $userType = 'destination';
switch ($userType) {
	case 'destination': $userName = trim($ui->item("address_type"),':'); break;
	case 'payer': $userName = trim($ui->item("payer_type"),':'); break;
	default: $userName = ''; break;
}
$form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($addrModel->getAttribute('id')?'buy/editaddr':'buy/newaddr'),
	'id' => $alias,
));
if (empty($showNotes)) $showNotes = false;
?>
<table class="address">
	<tbody>
	<tr>
		<td><?php if (!empty($userName)): ?><b><?= $userName ?>:</b><?php endif; ?></td>
		<td>
			<label style="float: left; margin-right: 20px;">
			<input type="radio" value="1" name="<?= $alias ?>[type]" class="checkbox_custom js_userType" <? if ($addrModel->getAttribute('type') == 1) { ?>checked<? } ?>>
				<span class="checkbox-custom"></span>
				<?= $ui->item("MSG_PERSONAL_ADDRESS_COMPANY"); ?>
			</label>
			<label style="float: left;">
				<input type="radio" value="2" name="<?= $alias ?>[type]" class="checkbox_custom js_userType" <? if ($addrModel->getAttribute('type') == 2 OR ($addrModel->getAttribute('type') == '')) { ?>checked<? } ?>>
				<span class="checkbox-custom"></span>
				<?= $ui->item("MSG_PERSONAL_ADDRESS_PERSON"); ?>
			</label>
		</td>
	</tr>
<?php if (Yii::app()->user->isGuest&&($userType == 'destination')&&!$onlyPereodic): ?>
	<tr><?php //TODO:: если товары только подписка, то строку таблицы убрать?>
		<td colspan="2"><label>
			<input type="checkbox" name="check_addressa" id="check_addressa" value="1" class="check_addressa checkbox_custom" />
			<span class="checkbox-custom"></span> <?= $ui->item("TAKE_IN_THE_STORE") ?>
		</label></td>
	</tr>
<?php endif; ?>
	<tr class="js_firm">
		<td nowrap="" class="maintxt">
			<span style="width: 5pt" class="redtext">*</span>
			<?= $ui->item("address_business_title"); ?>
		</td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'business_title', array('name'=>'' . $alias . '[business_title]')); ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
		</td>
	</tr>
	<tr class="js_firm">
		<td nowrap="" class="maintxt"><?= $ui->item("address_business_number1"); ?></td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'business_number1', array('name'=>'' . $alias . '[business_number1]')); ?>
		</td>
	</tr>

	<tr>
		<td class="maintxt">
			<span style="width: 5pt" class="redtext">*</span>
			<span class="js_userName"><?= $ui->item("regform_lastname"); ?></span><span class="js_firmName" style="display: none;"><?= $ui->item("DELEGATE_FIRSTNAME"); ?></span></td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'receiver_last_name', array('name'=>'' . $alias . '[receiver_last_name]')); ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
		</td>
	</tr>
	<tr>
		<td class="maintxt">
			<span style="width: 5pt" class="redtext">*</span>
			<span class="js_userName"><?= $ui->item("regform_firstname"); ?></span><span class="js_firmName" style="display: none;"><?= $ui->item("DELEGATE_LASTNAME"); ?></span></td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'receiver_first_name', array('name'=>'' . $alias . '[receiver_first_name]')); ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
		</td>
	</tr>
	<tr class="js_delivery">
		<td class="maintxt"><span class="js_userName"><?= $ui->item("regform_middlename"); ?></span><span class="js_firmName" style="display: none;"><?= $ui->item("DELEGATE_MIDDLENAME"); ?></span></td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'receiver_middle_name', array('name'=>'' . $alias . '[receiver_middle_name]')); ?>
		</td>
	</tr>
	<tr class="js_delivery">
		<td nowrap="" class="maintxt country_lbl">
			<span style="width: 5pt" class="redtext">*</span>
			<?= $ui->item(($alias == 'Reg')?"address_country":'COUNTRY'); ?>
		</td>
		<td class="maintxt-vat">
			<?= $form->dropDownList($addrModel, 'country', CHtml::listData($countrys, 'id', 'title_en'), array('name'=>'' . $alias . '[country]')) ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
		</td>
	</tr>
	<tr class="states_list js_delivery" style="display: none">
		<td nowrap="" class="maintxt">
			<span style="width: 5pt" class="redtext">*</span>
			<?= $ui->item("address_state"); ?>
		</td>
		<td class="maintxt-vat select_states">
			<?= $form->dropDownList($addrModel, 'state_id', CHtml::listData(array(0=>array('id'=>'','title_en'=>'---')), 'id', 'title_en'), array('name'=>'' . $alias . '[state_id]')) ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
		</td>
	</tr>
<?php if ($userType != 'destination'): ?>
	<tr class="js_firm verkkolasku">
		<td nowrap="" class="maintxt city_lbl">
			Verkkolaskuosoite
		</td>
		<td colspan="2" class="maintxt-vat">
			<?= $form->textField($addrModel, 'verkkolaskuosoite', array('name'=>'' . $alias . '[verkkolaskuosoite]')); ?>
		</td>
	</tr>
	<tr class="js_firm verkkolasku">
		<td nowrap="" class="maintxt postindex_lbl">
			Operaattoritunnus
		</td>
		<td colspan="2" class="maintxt-vat">
			<?= $form->textField($addrModel, 'operaattoritunnus', array('name'=>'' . $alias . '[operaattoritunnus]')); ?>
		</td>
	</tr>
<?php endif; ?>
	<tr class="js_delivery">
		<td nowrap="" class="maintxt city_lbl">
	<?php if (!$addrModel->getAttribute('id')): ?>
			<span style="width: 5pt" class="redtext">*</span>
	<?php endif; ?>
			<?= $ui->item('address_city') ?>
		</td>
		<td colspan="2" class="maintxt-vat">
			<?= $form->textField($addrModel, 'city', array('name'=>'' . $alias . '[city]')); ?>
	<?php if (!$addrModel->getAttribute('id')): ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
	<?php endif; ?>
		</td>
	</tr>
	<tr class="js_delivery">
		<td nowrap="" class="maintxt postindex_lbl">
	<?php if (!$addrModel->getAttribute('id')): ?>
			<span style="width: 5pt" class="redtext">*</span>
	<?php endif; ?>
			<?= $ui->item('address_postindex') ?>
		</td>
		<td colspan="2" class="maintxt-vat">
			<?= $form->textField($addrModel, 'postindex', array('name'=>'' . $alias . '[postindex]')); ?>
	<?php if (!$addrModel->getAttribute('id')): ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
	<?php endif; ?>
		</td>
	</tr>
	<tr class="js_delivery">
		<td nowrap="" class="maintxt streetaddress_lbl">
	<?php if (!$addrModel->getAttribute('id')): ?>
			<span style="width: 5pt" class="redtext">*</span>
	<?php endif; ?>
			<?= $ui->item('address_streetaddress') ?>
		</td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'streetaddress', array('name'=>'' . $alias . '[streetaddress]', 'placeholder'=>$ui->item('MSG_PERSONAL_ADDRESS_COMMENT_2'))); ?>
	<?php if (!$addrModel->getAttribute('id')): ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
	<?php endif; ?>
		</td>

	</tr>
	<tr>
		<td nowrap="" class="maintxt">
	<?php if (($alias == 'Reg')&&(Yii::app()->user->isGuest)): ?>
			<span style="width: 5pt" class="redtext">*</span>
	<?php endif; ?>
			<?= $ui->item("address_contact_email"); ?>
		</td>
		<td class="maintxt-vat" style="position: relative;">
			<?= $form->textField($addrModel, 'contact_email', array('name'=>'' . $alias . '[contact_email]', 'class'=>'js_contactEmail', 'placeholder'=>empty($emailPlaceholder)?'':$emailPlaceholder)); ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_ERROR_WRONG_EMAIL') ?></span>
			<?php if (($alias == 'Reg')&&(Yii::app()->user->isGuest)): ?><div class="info_box" style="display: none;"></div><?php endif; ?>
		</td>
	</tr>
	<tr>
		<td nowrap="" class="maintxt contact_phone_lbl">
			<span style="width: 5pt" class="redtext">*</span>
			<?= $ui->item("address_contact_phone"); ?>
		</td>
		<td class="maintxt-vat">
			<?= $form->textField($addrModel, 'contact_phone', array('name'=>'' . $alias . '[contact_phone]', 'class'=>'js_contactPhone', 'placeholder'=>$ui->item('PHONE_WITH_CODE'))); ?>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
		</td>
	</tr>
	<?php if ($showNotes): ?>
		<tr>
			<td nowrap="" class="maintxt">
				<?= $ui->item("address_contact_notes2"); ?>
			</td>
			<td class="maintxt-vat">
				<?= $form->textArea($addrModel, 'notes', array('name'=>'' . $alias . '[notes]')); ?>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
<?php $this->endWidget();
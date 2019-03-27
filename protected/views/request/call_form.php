<?php /*Created by Кирилл (18.03.2019 21:47)*/ ?>
<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl('request/sendcall'),
	'id' => 'send_call',
));
?>
<span class="close fa">×</span>
<h2><?= Yii::app()->ui->item('SEND_CALL_HEADER') ?></h2>
<div class="title"><?= Yii::app()->ui->item('regform_firstname') ?></div>
<div>
	<?= $form->textField($model, 'face', array('placeholder'=>Yii::app()->ui->item('regform_firstname'))); ?>
	<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_INPUT_ERROR') ?></span>
</div>
<div class="title code"><?= Yii::app()->ui->item('CODE_COUNTRY') ?></div>
<div class="title"><?= Yii::app()->ui->item('NUMBER_PHONE') ?></div>
<div class="clearBoth"></div>
<div class="code"><input type="text" value="" name="Send_calls[code]" placeholder="<?= Yii::app()->ui->item('PLACEHOLDER_PHONE_CODE') ?>"></div>
<div class="phone">
	<?= $form->textField($model, 'face', array('placeholder'=>Yii::app()->ui->item('NUMBER_PHONE'))); ?>
	<span class="texterror" style="display: none;"><?= $ui->item('PHONE_WITH_CODE') ?></span>
</div>
<div class="clearBoth"></div>
<div class="confirm">
	<label>
		<input type="checkbox" value="1" name="Send_calls[confirm]" class="checkbox_custom">
		
		<span class="checkbox-custom"></span>
		
		<?= $ui->item('CHECKBOX_TERMS_OF_USE') ?>
	</label>
	<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_ERROR_AGREE_CONDITION') ?></span>
</div>
<div class="button">
	<a onclick="return false;" class="button_call">
		<span class="fa"></span>
		<span><?=$ui->item('BUTTON_CALL'); ?></span>
	</a>
</div>
<?php $this->endWidget(); ?>


<script>

function send_forma() {
	
}

</script>
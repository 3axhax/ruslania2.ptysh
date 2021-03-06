<?php /*Created by Кирилл (27.02.2019 18:40)*/ ?>
<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAY_IN_STORE') ?>
	</div>
<label class="selp span3">
	<?=$ui->item('MSG_PAYMENT_TYPE_00')?>
	<div class="red_checkbox">
		<input type="radio" value="0" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>

<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAYTRAIL') ?>
	</div>
<label class="selp span3 act" style="width: 484px;">
	<img src="/images/pt2.png" style="margin-top: -3px;" />
	<div style="margin-top: 5px;"><?=$ui->item('CARTNEW_PAYTRAYL_LABEL')?>. <?=$ui->item('CARTNEW_PAYTRAYL_DESC')?></div>

	<div class="red_checkbox">
		<input type="radio" value="25" name="ptype" class="checkbox_custom" checked/>
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>

<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAYPAL') ?>
	</div>
<label class="selp span3">
	<img style="margin-top: -20px;" src="/images/pp.jpg" width="150" />
	<div style="margin-top: 5px;">EUR, USD, GBP etc.</div>
	<div class="red_checkbox">
		<input type="radio" value="8" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>

<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAYTYPE7') ?>
	</div>
<label class="selp span3">
	<?=$ui->item('CARTNEW_PAY_INVOICE_LABEL')?>
	<div class="red_checkbox">
		<input type="radio" value="7" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>
<?php if (in_array(Yii::app()->getLanguage(), array('ru', 'rut', 'en'))): ?>
<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_ALIPAY') ?>
	</div>
<label class="selp span3">
	<img src="/images/ap.png" width="100" style="margin-top: -15px;" />
	<div class="red_checkbox">
		<input type="radio" value="26" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>
<?php endif; ?>

<!--

<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_APPLEPAY') ?>
	</div>
<label class="selp span3">
	<img src="/images/app.png" width="100" style="margin-top: -15px;" />
	<div class="red_checkbox">
		<input type="radio" value="27" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
	<div class="not_supported" style="display: none;"><?=$ui->item('NOT_APPLEPAY')?></div>
</label>
</div> -->

<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAYTYPE13') ?>
	</div>
<label class="selp span3">
	<?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT1')?>
	<div class="red_checkbox">
		<input type="radio" value="13" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>

<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAYTYPE14') ?>
	</div>
<label class="selp span3">
	<div class="js_payRus" style="margin-top: -20px;"><?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT3')?></div>
	<div class="red_checkbox">
		<input type="radio" value="14" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>

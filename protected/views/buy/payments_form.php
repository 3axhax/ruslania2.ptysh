<?php /*Created by Кирилл (27.02.2019 18:40)*/ ?>
<div class="variant">
	<div class="qbtn2">?</div>
	<div class="info_box">
		<?= $ui->item('DESC_PAY_IN_STORE') ?>
	</div>
<label class="selp span3">
	<?=$ui->item('CARTNEW_PAY_IN_STORE')?>
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
	<img src="/images/pp.jpg" width="150" />
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
</div>

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
	<span class="js_payRus"><?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT2')?></span><span class="js_payFin" style="display: none;"><?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT2')?></span>
	<div class="red_checkbox">
		<input type="radio" value="14" name="ptype" class="checkbox_custom" />
		<span class="checkbox-custom"></span>
	</div>
</label>
</div>

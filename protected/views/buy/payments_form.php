<?php /*Created by Кирилл (27.02.2019 18:40)*/ ?>
<label class="selp span3">
	<?=$ui->item('CARTNEW_PAY_IN_STORE')?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype0" value="0" name="ptype" />
</label>
<label class="selp span3" style="width: 484px;">
	<img src="/images/pt2.png" style="margin-top: -3px;" />
	<span style="display: block; margin-top: 5px;"><?=$ui->item('CARTNEW_PAYTRAYL_LABEL')?></span>

	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype2" value="25" name="ptype" />
</label>

<label class="selp span3">
	<img src="/images/pp.jpg" width="150" />
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype1" value="8" name="ptype" />
</label>



<label class="selp span3">
	<?=$ui->item('CARTNEW_PAY_INVOICE_LABEL')?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype3" value="7" name="ptype" />
</label>

<label class="selp span3">
	<img src="/images/ap.png" width="100" style="margin-top: -15px;" />
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype4" value="26" name="ptype" />
</label>

<label class="selp span3">
	<img src="/images/app.png" width="100" style="margin-top: -15px;" />
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype5" value="27" name="ptype" />
</label>

<label class="selp span3">
	<?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT1')?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype6" value="13" name="ptype" />
</label>

<label class="selp span3">
	<?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT2')?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="ptype7" value="14" name="ptype" />
</label>


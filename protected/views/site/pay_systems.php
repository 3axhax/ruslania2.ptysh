<?php /*Created by Кирилл (19.07.2018 21:02)*/ ?>
<? $ui = new RuslaniaUI; ?>
<style>
	label.seld {
		padding: 1.8rem 2rem 2.2rem;
		border: 1px solid #ccc;
		margin-top: 10px;
		border-radius: 2px;
		position: relative;
		width: 212px;
	}

	label.seld .red_checkbox {
		position: absolute;
		right: 5px;
		top: 20px;

	}

	label.selp {
		padding: 1.8rem 2rem 2.2rem;
		border: 1px solid #ccc;
		margin-top: 10px;
		height: 70px;
		position: relative;
		padding-right: 55px;
		width: 188px;
		border-radius: 2px;
	}

	label.selp .red_checkbox {
		position: absolute;
		right: 5px;
		top: 20px;
	}
	label.selp img {
		-webkit-filter: grayscale(100%);
		-moz-filter: grayscale(100%);
		-ms-filter: grayscale(100%);
		-o-filter: grayscale(100%);
		filter: grayscale(100%);
		filter: gray; /* IE 6-9 */
	}

	label.selp.act img {
		-webkit-filter: grayscale(0%);
		-moz-filter: grayscale(0%);
		-ms-filter: grayscale(0%);
		-o-filter: grayscale(0%);
		filter: grayscale(0%);
		filter: none; /* IE 6-9 */
	}

</style>

<?
    $ordClass = new Order();



    $ord = $ordClass->GetOrder((int) $_GET['id']);

    if ($ord['delivery_type_id'] == 0) :

?>

<label class="selp span3 oplata1" onclick="check_cart_sel($(this),'selp', 'dtype1')">
	<?=$ui->item('CARTNEW_PAY_IN_STORE')?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype1" value="1" name="ptype" style="display: none;" />
</label>
<? endif; ?>

<label class="selp span3 oplata2" onclick="check_cart_sel($(this),'selp', 'dtype8')">
	<img src="/images/pp.jpg" width="150" />
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype8" value="8" name="ptype" style="display: none;" />
</label>

<label class="selp span3 oplata3" onclick="check_cart_sel($(this),'selp', 'dtype25')" style="width: 484px;">

	<img src="/images/pt2.png" style="margin-top: 10px;" /><br /><span style="display: block; margin-top: 5px;"><?=$ui->item('CARTNEW_PAYTRAYL_LABEL')?></span>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype25" value="25" name="ptype" style="display: none;" />
</label>

<label class="selp span3 oplata4" onclick="check_cart_sel($(this),'selp', 'dtype7')">
	<?=$ui->item('CARTNEW_PAY_INVOICE_LABEL')?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype7" value="7" name="ptype" style="display: none;" />
</label>

<label class="selp span3 oplata5" onclick="check_cart_sel($(this),'selp', 'dtype26')">
	<img src="/images/ap.png" width="100" style="margin-top: -15px;" />
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype26" value="26" name="ptype" style="display: none;" />
</label>

<label class="selp span3 oplata6" onclick="check_cart_sel($(this),'selp', 'dtype27')">
	<img src="/images/app.png" width="100" style="margin-top: -15px;" />
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype27" value="27" name="ptype" style="display: none;" />
</label>

<label class="selp span3 oplata7" onclick="check_cart_sel($(this),'selp', 'dtype13')">
	<?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT1');?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype13" value="13" name="ptype" style="display: none;" />
</label>

<label class="selp span3 oplata8" onclick="check_cart_sel($(this),'selp', 'dtype14')">
	<?=$ui->item('CARTNEW_PREPAY_TO_BANK_ACCOUNT2');?>
	<div class="red_checkbox" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
	</div>
	<input type="radio" id="dtype14" value="14" name="ptype" style="display: none;" />
</label>



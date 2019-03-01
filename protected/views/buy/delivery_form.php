<?php /*Created by Кирилл (27.02.2019 16:59)*/ ?>
<div class="row dtypes">
	<div class="texterror"></div>
	<div class="variant">
		<div class="qbtn2">?</div>
		<div class="info_box">
			<?= $ui->item('DELIVERY_ECONOMY_OTHER'); ?>
		</div>
		<label class="seld span3 act">
			<div class="red_checkbox">
				<input type="radio" value="3" name="dtype" class="checkbox_custom" checked>
				<span class="checkbox-custom"></span>
			</div>
			<?= $ui->item('CARTNEW_DELIVERY_POST_NAME') ?>
			<br>Economy<?= $ui->item('X_DAYS_3', '<span class="js_xDays"><br>2-5</span>') ?><br>
			<span style="color: #70C67C; font-weight: bold;"><span class="js_price">0</span><?= Currency::ToSign() ?></span>
		</label>
	</div>
	<div class="variant">
		<div class="qbtn2">?</div>
		<div class="info_box">
			<?= $ui->item('DELIVERY_PRIORITY_OTHER') ?>
		</div>
		<label class="seld span3">
			<div class="red_checkbox">
				<input type="radio" value="2" name="dtype" class="checkbox_custom">
				<span class="checkbox-custom"></span>
			</div>
			<?= $ui->item('CARTNEW_DELIVERY_POST_NAME') ?>
			<br>Priority<?= $ui->item('X_DAYS_3', '<span class="js_xDays"><br>1-3</span>') ?><br>
			<span style="color: #70C67C; font-weight: bold;"><span class="js_price">0</span><?= Currency::ToSign() ?></span>
		</label>
	</div>
	<div class="variant">
		<div class="qbtn2">?</div>
		<div class="info_box">
			<?= $ui->item('DELIVERY_EXPRESS_OTHER'); ?>
		</div>
		<label class="seld span3">
			<div class="red_checkbox">
				<input type="radio" value="1" name="dtype" class="checkbox_custom">
				<span class="checkbox-custom"></span>
			</div>
			<?= $ui->item('CARTNEW_DELIVERY_POST_NAME') ?>
			<br>Express<?= $ui->item('X_DAYS_3', '<span class="js_xDays"><br>1-2</span>') ?><br>
			<span style="color: #70C67C; font-weight: bold;"><span class="js_price">0</span><?= Currency::ToSign() ?></span>
		</label>
	</div>
	<div class="variant">
		<label class="seld span3" style="height: 40px;">
			<span class="zabr_market"><?= $ui->item('CARTNEW_PICK_UP_STORE1'); ?></span>
			<div class="red_checkbox">
				<input type="radio" value="0" name="dtype" class="checkbox_custom" />
				<span class="checkbox-custom"></span>
			</div>
		</label>
	</div>
	<div class="clearfix"></div>
	<div class="delivery_box" style="display: none; margin: 15px 0;"></div>
</div>

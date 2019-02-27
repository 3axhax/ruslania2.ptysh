<?php /*Created by Кирилл (27.02.2019 16:59)*/ ?>
<div class="row dtypes">
	<div class="texterror"></div>
	<div class="variant">
		<div class="qbtn2">?</div>
		<div class="info_box">
			<?= $ui->item('DELIVERY_ECONOMY_OTHER'); ?>
		</div>
		<label class="seld span3" rel="8.3" valute="$">
			<div class="red_checkbox" style="float: right;">
				<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
			</div>
			<input type="radio" value="3" name="dtype" rel="8.3USD" id="dtype2">
			<?= $ui->item('CARTNEW_DELIVERY_POST_NAME') ?><br>Economy <br><?= $ui->item('X_DAYS_3', '2-5') ?><br>
			<span style="color: #70C67C; font-weight: bold;">0<?= Currency::ToSign() ?></span>
		</label>
	</div>
	<div class="variant">
		<div class="qbtn2">?</div>
		<div class="info_box">
			<?= $ui->item('DELIVERY_PRIORITY_OTHER') ?>
		</div>
		<label class="seld span3" rel="11.8" valute="$">
			<div class="red_checkbox" style="float: right;">
				<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
			</div>
			<input type="radio" value="2" name="dtype" rel="11.8USD" id="dtype3">
			<?= $ui->item('CARTNEW_DELIVERY_POST_NAME') ?><br>Priority <br><?= $ui->item('X_DAYS_3', '1-3') ?><br>
			<span style="color: #70C67C; font-weight: bold;">0<?= Currency::ToSign() ?></span>
		</label>
	</div>
	<div class="variant">
		<div class="qbtn2">?</div>
		<div class="info_box">
			<?= $ui->item('DELIVERY_EXPRESS_OTHER'); ?>
		</div>
		<label class="seld span3" rel="23.6" valute="$">
			<div class="red_checkbox" style="float: right;">
				<span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check"></span></span>
			</div>
			<input type="radio" value="1" name="dtype" rel="23.6USD" id="dtype4">
			<?= $ui->item('CARTNEW_DELIVERY_POST_NAME') ?>
			<br>Express <br><?= $ui->item('X_DAYS_3', '1-2') ?><br>
			<span style="color: #70C67C; font-weight: bold;">0<?= Currency::ToSign() ?></span>
		</label>
	</div>
	<div class="variant">
		<label class="seld span3" style="height: 40px;">
			<span class="zabr_market"><?= $ui->item('CARTNEW_PICK_UP_STORE1'); ?></span>
			<div class="red_checkbox" style="float: right;">
				<span class="checkbox" style="height: 10px; padding-top: 2px;">
					<span class="check"></span>
				</span>
			</div>
			<input type="radio" id="dtype0" value="0" name="dtype"/>
		</label>
	</div>
	<div class="clearfix"></div>
	<div class="delivery_box" style="display: none; margin: 15px 0;"></div>
</div>

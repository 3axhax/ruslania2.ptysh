<?php /*Created by Кирилл (26.11.2018 21:33)*/ ?>
<div id="js_promocode" class="cart_promocode">
	<div><?php /*
		<label>
			<input class="checkbox_icon" type="checkbox">
			<span><?= Yii::app()->ui->item('PROMOCODE') ?></span>
		</label>
		<div style="display: none;">
			<input type="text" value="">
			<input type="button" value="<?= Yii::app()->ui->item('A_NEW_APPLY') ?>">
		</div>*/ ?>
		<div>
			<input placeholder="<?= Yii::app()->ui->item('PROMOCODE'); ?>" type="text" id="promocode" value="">
			<input type="button" value="<?= Yii::app()->ui->item('A_NEW_APPLY') ?>">
		</div>
	</div>
</div>

<?php /*Created by Кирилл (05.03.2019 22:25)*/ ?>
<style>
	.info_order div div.span11 { width: 400px; }
</style>
<div class="container cartorder">
	<h1><?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?><?php
		switch ((int)$order['delivery_type_id']):
			case 0: ?> <?= $ui->item('IN_SHOP') ?>!<?php break;
		endswitch;
		?></h1>
	<div class="row" id="js_print">
		<div class="span8">
			<?php $this->renderPartial('/client/_one_order', array('order' => $order, 'onlyContent' => 1, 'class' => 'bordered', 'enableSlide' => 1)); ?>
			<script type="text/javascript">
				function slideContents(id) {
					$('#cnt' + id).fadeToggle();
				}
			</script>
			<div style="background-color: #f8f8f8; padding: 20px 25px;">
			<?php
			switch ((int)$order['payment_type_id']):
				case 7:case 13:case 14: ?>
					<h3><?= Yii::app()->ui->item('DESC_PAYTYPE' . $order['payment_type_id'] . '_H') ?></h3>
					<div><?= Yii::app()->ui->item('DESC_PAYTYPE' . $order['payment_type_id'], $order['id']) ?></div>
					<?php break;
				case 26: if ($order['full_price'] > 0): ?>
					<div><?= Yii::app()->ui->item('DESC_ALIPAY', $order['id']) ?></div>
					<div style="" class="hide_block_pay">
						<div style="height: 20px;"></div>
						<img src="/images/alipay.jpg" style="max-width: 100%;">
					</div>
					<?php endif; break;
				case 25:
					//$this->widget('PayTrailWidget', array('order' => $order));
					break;
			endswitch;
			?>
				<div class="clearBoth"></div>

				<div style="margin-top: 20px;">
					<?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
				</div>
			</div>
		</div>
		<div style="height: 20px;"></div>
	</div>
</div>
<?php ?>
<script type="text/javascript" src="/new_js/modules/print.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		ym(53579293, 'reachGoal', 'step_success');
	});
	print = function() { return new _Print(); };
	print().init({$button: $('.printed_btn'), $content: $('#js_print')});
</script>


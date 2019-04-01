<?php /*Created by Кирилл (05.03.2019 22:25)*/ ?>
<style>
	.info_order div div.span11 { width: 400px; }
</style>
<div class="container cartorder">
	
	<? if ($order['payment_type_id'] != 25) :  ?>
	
	<h1><?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?><?php
		switch ((int)$order['delivery_type_id']):
			case 0: ?> <?= $ui->item('IN_SHOP') ?>!<?php break;
		endswitch;
		?></h1><? endif;?>
	<div class="row" id="js_print">
			<?php if ((int)$order['payment_type_id'] === 25): ?>
				<?php $this->widget('PayTrailWidget', array('order' => $order)); ?>
			<?php else: ?>
		<div class="span8">
			<?php $this->renderPartial('/client/_one_order', array('order' => $order, 'onlyContent' => 1, 'class' => 'bordered', 'enableSlide' => 1)); ?>
			<script type="text/javascript">
				function slideContents(id) {
					$('#cnt' + id).fadeToggle();
				}
			</script>
			<?php /*
			<table cellspacing="1" cellpadding="5" border="0" width="100%" class="cart1 items_orders" style="display: table; margin-top: 22px;">
				<tbody>
				<tr>
					<th></th>
					<th width="70%" class="cart1header1"><?= $ui->item("CART_COL_TITLE"); ?></th>
					<th width="10%" class="cart1header1 center"><?= $ui->item("CART_COL_QUANTITY"); ?></th>
					<th width="20%" class="cart1header1 center"><?= $ui->item("CART_COL_SUBTOTAL_PRICE"); ?></th>
				</tr>

				<?php foreach ($order['Items'] as $item) : ?>
					<tr>
						<td class="cart1contents1">
							<span class="entity_icons"><i class="fa e<?= $item['entity'] ?>"></i></span>
						</td>
						<td class="cart1contents1">
							<a class="maintxt" href="<?= ProductHelper::CreateUrl($item); ?>"><?= ProductHelper::GetTitle($item); ?></a>
						</td>
						<td class="cart1contents1 center"><?= $item['quantity']; ?></td>
						<td class="cart1contents1 center"><?= $item['items_price']; ?> <?= Currency::ToSign($order['currency_id']); ?></td>
					</tr>
				<?php endforeach; ?>

				<tr class="footer">
					<td colspan="4">
						<div class="summa" style="padding-left: 10px; padding-right: 10px;">
							<a style="float: left;     height: 43px;line-height: 43px; color: rgb(67, 67, 67)"
							   href="<?= Yii::app()->createUrl('client/printorder', array('oid' => $order['id'])); ?>"
							   class="maintxt"
							   target="_new"><i class="fa fa-print"></i> <?= $ui->item('MSG_ACTION_PRINT_ORDER'); ?>
							</a>
							<div class="itogo" style="margin-top: -3px; height: auto; width: 400px;">
								Полная стоимость<br/> заказа с учетом доставки:
								<b><?= ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b>
								<?php if (($order['currency_id'] != Currency::EUR)&&((int)$order['payment_type_id'] === 25)) : ?>
									<div><?= $ui->item('PRICE_PAYTRAYL_DESC') ?></div>
								<?php endif; ?>
							</div>
							<div class="clearfix"></div>
						</div>
					</td>
					<td align="right" class="cart1contents1" colspan="2"></td>
					<td class="cart1contents1 center" colspan="1">
					</td>
				</tr>
				</tbody>
			</table>
			<?php */ ?>
			<div style="background-color: #f8f8f8; padding: 20px 25px;">
			<?php
			switch ((int)$order['payment_type_id']):
				case 7:case 13:case 14: ?>
					<div><?= Yii::app()->ui->item('DESC_PAYTYPE' . $order['payment_type_id'], $order['id']) ?></div>
					<?php break;
				case 26: ?>
					<div><?= Yii::app()->ui->item('DESC_ALIPAY', $order['id']) ?></div>
					<div style="" class="hide_block_pay">
						<div style="height: 20px;"></div>
						<img src="/images/alipay.jpg" style="max-width: 100%;">
					</div>
					<?php break;
				case 25: $this->widget('PayTrailWidget', array('order' => $order)); break;
			endswitch;
			?>
				<div class="clearBoth"></div>

				<div style="margin-top: 20px;">
					<?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
				</div>
			</div>
		</div>
			<?php endif; ?>
		<div style="height: 20px;"></div>
	</div>
</div>
<?php ?>
<script type="text/javascript">
	function printPopup(data) {
		var printWindow = window.open('', 'Печать', 'height=600,width=800');
		if (printWindow) {
			printWindow.document.write();
			printWindow.document.write('<html><head><title>Печать</title></head><body>');
			printWindow.document.write('<style>'+
			'@media print {'+
			'.bordered {'+
					'margin-top: 10px;'+
					'padding: 5px 5px 5px 5px;'+
				'}'+
			'.printed_btn {display: none;}'+
			'.info_order div.row { margin: 4px 0 4px 0; }'+

			'.info_order div { font-size: 13px; }'+
			'.info_order div span.span1 { display: inline-block; width: 200px; }'+
			'.info_order div div.span11 {'+
					'width: 400px;'+
					'display: inline-block;'+
					'margin: 0;'+
					'font-weight: bold;'+
				'}'+
			'.mbt10 {margin-bottom: 10px;}'+
			'table.items_orders { margin: 25px 0; border: 1px solid #eee; }'+
			'table.items_orders th { text-align: left; }'+
			'table.items_orders tr.footer td div.summa div.itogo { height: 31px; float: right; line-height: 31px; }'+
			'a {text-decoration: none; color: #000; }'+
			'}'+
		'</style>'+
			'');
			printWindow.document.write(data);
			printWindow.document.write('</body></html>');
			printWindow.document.close(); // necessary for IE >= 10
			printWindow.focus(); // necessary for IE >= 10
			printWindow.print();
			printWindow.close();
			return true;
		}
		return false;
	}
	$('.printed_btn').on('click', function(){
		var doc = $('#js_print').html();
		printPopup(doc);
		return false;
	});
</script>

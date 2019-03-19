<?php /*Created by Кирилл (05.03.2019 22:25)*/ ?>
<div class="container cartorder">
	<h1><?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?><?php
		switch ((int)$order['delivery_type_id']):
			case 0: ?> <?= $ui->item('IN_SHOP') ?>!<?php break;
		endswitch;
		?></h1>
	<div class="row">
		<div class="span8">
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
									<?php /*
									(<?php $eur = Currency::ConvertToEUR($order['full_price'], $order['currency_id']);
									echo ProductHelper::FormatPrice($eur, true, Currency::EUR); ?>) */ ?>
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
		<div style="height: 20px;"></div>
	</div>
</div>
<?php

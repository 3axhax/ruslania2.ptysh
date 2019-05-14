<?php /*Created by Кирилл (09.04.2019 22:03)*/
if (empty($PH)) $PH = new ProductHelper();
if (empty($total)) $total = null;
if (empty($countItems)) $countItems = null;
$head = $ui->item('CART_COL_TOTAL_PRICE2');
if (!empty($total)&&!empty($total['isDiscount'])) $head .= ', ' . mb_strtolower($ui->item('PRICE_DISCOUNT_FORMAT'));
$head .= ', ' . mb_strtolower($ui->item('WITHOUT_DELIVERY'));
if (!empty($total)&&empty($total['withVAT'])) $head .= '<span class="items_nds">, ' . $ui->item('WITHOUT_VAT_FULL') . '</span>';
else $head .= '<span class="items_nds"></span>';
?>
<div class="row" style="margin-left: 0; margin-top: 10px;">

	<div class="span6" style="width: 49%; margin-left: 0; margin-right: 1%;">

		<textarea id="Notes" style="width: 100%; margin-bottom: 0; height: 245px; box-sizing: border-box;" placeholder="<?=str_replace('<br />', '', $ui->item("address_contact_notes")); ?>" name="notes"></textarea>

	</div>
	<div class="span6" style="width: 50%; margin: 0;">

		<div class="cart_footer  footer1">
			<?php /*<?= sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), $countItems . ' ' . $PH->endOfWord($countItems, $ui->item('CARTNEW_PRODUCTS_TITLE2'), $ui->item('CARTNEW_PRODUCTS_TITLE1', $ui->item('CARTNEW_PRODUCTS_TITLE3'))), '<span class="items_cost">' . $PH->FormatPrice($total['itemsPrice']) . '</span>') ?>*/ ?>
			<?= $head ?>: <span class="items_cost"><?= $PH->FormatPrice($total['itemsPrice']) ?></span>
		</div>
		<div class="cart_footer  footer1">
			<?=$ui->item('ORDER_MSG_DELIVERY_COST')?> <span class="delivery_cost"><?= $PH->FormatPrice($total['deliveryPrice']); ?></span> <span class="add_cost" style="font-weight: bold; display: none;"><?=$ui->item('CARTNEW_OTHER_PRODUCTS_CART')?></span>
		</div>
		<div class="cart_footer footer2">
			<?=$ui->item('CART_COL_SUBTOTAL_DELIVERY')?>: <span class="delivery_name"><?=$ui->item('MSG_DELIVERY_TYPE_0')?></span><span class="date" style="display: none"></span><?=$ui->item('CARTNEW_TOTAL_WEIGHT_LABEL')?>: <?= $total['fullWeight'] ?> <?=$ui->item('CARTNEW_WEIGHT_NAME')?>
		</div>

		<div class="cart_footer footer_promocode">
			<?php $this->renderPartial('/cart/_promocode', array('priceId'=>'itogo_cost')); ?>
		</div>
		<div class="cart_footer footer3">
			<?=$ui->item('CARTNEW_TOTAL_COST_LABEL')?>: <span class="itogo_cost" id="itogo_cost"><?= $PH->FormatPrice($total['itemsPrice'] + $total['deliveryPrice']); ?></span>
			<?php if (Yii::app()->currency != Currency::EUR): ?><div class="paytail_payment" style="display: none;"><?= $ui->item('PRICE_PAYTRAYL_DESC') ?></div><?php endif; ?>
		</div>

		<div class="cart_footer">
			<div class="pleasewait"><span class="fa fa-spinner fa-pulse"></span></div>
			<div class="order_start">
				<span class="js_orderPay"><?=$ui->item('BUTTON_ORDER_PAY')?></span>
				<span class="js_orderSave" style="display: none;"><?=$ui->item('BUTTON_ORDER_SAVE')?></span>
			</div>
		</div>
		<?php if (Yii::app()->user->isGuest): ?>
		<div class="cart_footer">
			<?=$ui->item('CARTNEW_SEND_INFO_LABEL')?>
		</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
</div>

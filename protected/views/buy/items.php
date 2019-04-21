<?php /*Created by Кирилл (24.02.2019 20:30)*/
if (empty($PH)) $PH = new ProductHelper();
if (empty($total)) $total = null;
if (empty($countItems)) $countItems = null;
$head = $ui->item('CART_COL_TOTAL_PRICE2');
if (!empty($total)&&!empty($total['isDiscount'])) $head .= ', ' . mb_strtolower($ui->item('PRICE_DISCOUNT_FORMAT'));
$head .= ', ' . mb_strtolower($ui->item('WITHOUT_DELIVERY'));
if (!empty($total)&&empty($total['withVAT'])) $head .= '<span class="items_nds">, ' . $ui->item('WITHOUT_VAT_FULL') . '</span>';
else $head .= '<span class="items_nds"></span>';
?>
<?php if ($countItems !== null): ?>
<div class="cart_header" style="width: 553px;">
	<?php /*
	<?= sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), $countItems . ' ' . $PH->endOfWord($countItems, $ui->item('CARTNEW_PRODUCTS_TITLE2'), $ui->item('CARTNEW_PRODUCTS_TITLE1'), $ui->item('CARTNEW_PRODUCTS_TITLE3')), '<span class="items_cost">' . $PH->FormatPrice($total['itemsPrice']) . '</span>') ?>
    */ ?>
	<?= $head ?>: <span class="items_cost"><?= $PH->FormatPrice($total['itemsPrice']) ?></span>
	<?php /*if (Yii::app()->currency != Currency::EUR): ?><div class="paytail_payment" style="display: none;"><?= $ui->item('PRICE_PAYTRAYL_DESC') ?></div><?php endif;*/ ?>
</div>
<?php endif; ?>
<div class="cart_box">
	<table class="cart rows_number" style="width: 100%;">
		<tbody>
		<?php foreach ($items as $id => $item):
			$itemsPrice = 0;
			if (!empty($total)) $itemsPrice = $total['pricesValues'][$item['entity'] . '_' . $item['id']];
			elseif (!empty($item['items_price'])) $itemsPrice = $item['items_price'];
			?>
			<tr class="js_<?= $item['entity'] ?>_<?= $item['id'] ?>">
				<td style="padding-left: 25px; padding-right: 20px;" class="index_number">
					<span class="entity_icons" style="display: block;margin-left: 10px;"><i class="fa e<?= $item['entity'] ?>"></i></span>
				</td>
				<td style="width: 100%;">
					<?php if ($item['InCartUnitWeight'] == 0): ?>
						<div style="float: right; color: #5BB75B;">
							<?= $ui->item('MSG_DELIVERY_TYPE_4') ?>
							<span class="qbtn2">?</span>
							<div class="info_box">
								<?= $ui->item('DELIVERY_ECONOMY_OTHER') ?>
							</div>
						</div>
					<?php endif; ?>
					<span class="a"><?= $PH->GetTitle($item) ?></span>
					<div class="minitext">
						<?= $item['quantity'] ?>
					<?php if ($item['entity'] == 30): ?>
						<?= $ui->item('MONTH_SMALL'); ?>
					<?php else: ?>
						<?= $ui->item('CARTNEW_COUNT_NAME')?>
					<?php endif; ?> x <span class="item_cost"><?= $PH->FormatPrice($itemsPrice); ?></span>
					<?php if ($item['quantity'] > 1): ?>
						= <span class="item_cost_itogo"><?= $PH->FormatPrice(sprintf("%.2f", $itemsPrice*$item['quantity'])); ?></span>
					<?php endif; ?>
					<?php if ($item['InCartUnitWeight'] > 0): ?>
						<br /> <?= $ui->item('CARTNEW_WEIGHT_LABEL') ?>: <?= ($item['InCartUnitWeight'] / 1000) ?> <?= $ui->item('CARTNEW_WEIGHT_NAME') ?>
					<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

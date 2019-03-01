<?php /*Created by Кирилл (24.02.2019 20:30)*/
if (empty($PH)) $PH = new ProductHelper();
?>
<div class="cart_header" style="width: 553px;">
	<?= sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), $countItems . ' ' . $PH->endOfWord($countItems, $ui->item('CARTNEW_PRODUCTS_TITLE2'), $ui->item('CARTNEW_PRODUCTS_TITLE1', $ui->item('CARTNEW_PRODUCTS_TITLE3'))), '<span class="items_cost">' . $PH->FormatPrice($total['itemsPrice']) . '</span>') ?>
</div>
<div class="cart_box">
	<table class="cart" style="width: 100%;">
		<tbody>
		<?php foreach ($items as $id => $item): ?>
			<tr class="js_<?= $item['entity'] ?>_<?= $item['id'] ?>">
				<td style="width: 35px; height: 35px;">
					<span class="entity_icons"><i class="fa e<?= $item['entity'] ?>"></i></span>
				</td>
				<td>
					<?php if ($item['InCartUnitWeight'] == 0): ?>
						<div style="float: right; color: #5BB75B;"><?= $ui->item('MSG_DELIVERY_TYPE_4') ?></div>
					<?php endif; ?>
					<span class="a"><?= $PH->GetTitle($item) ?></span>
					<div class="minitext">
						<?= $item['quantity'] ?>
					<?php if ($item['entity'] == 30): ?>
						<?= $ui->item('MONTH_SMALL'); ?>
					<?php else: ?>
						<?= $ui->item('CARTNEW_COUNT_NAME')?>.
					<?php endif; ?> x <span class="item_cost"><?= $PH->FormatPrice($total['pricesValues'][$item['entity'] . '_' . $item['id']]); ?></span>
					<?php if ($item['InCartUnitWeight'] > 0): ?>
						<br /> <?= $ui->item('CARTNEW_WEIGHT_LABEL') ?>: <?= ($item['InCartUnitWeight']) ?> <?= $ui->item('CARTNEW_WEIGHT_NAME') ?>
					<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

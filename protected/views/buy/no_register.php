<?php /*Created by Кирилл (24.02.2019 10:34)*/
/**@var $this MyController*/
$PH = new ProductHelper();
$addrModel = new Address();
?>
<link rel="stylesheet" href="/new_style/order_buy.css">
<hr />

<div class="container cartorder" style="margin-bottom: 20px;">
	<div class="span7" style="margin-left: 0;">
		<div class="p3"><?=$ui->item('CARTNEW_NOREG_STEP1_TITLE')?></div>
		<?php $this->renderPartial('address_form', array('alias'=>'Reg', 'addrModel'=>$addrModel)); ?>
	</div>

	<div class="span7" style="float: right; width: 575px; margin-top: 21px;">
		<?php $this->renderPartial('items', array('PH'=>$PH, 'total'=>$total, 'items'=>$items)); ?>
	</div>

	<div class="clearBoth"></div>
	<div class="row" style="margin-left: 0; margin-top: 10px;">

		<div class="span6" style="width: 49%; margin-left: 0; margin-right: 1%;">

			<textarea id="Notes" style="width: 100%; margin-bottom: 0; height: 245px; box-sizing: border-box;" placeholder="<?=str_replace('<br />', '', $ui->item("address_contact_notes")); ?>" name="Address[notes]"></textarea>

		</div>
		<div class="span6" style="width: 50%; margin: 0;">

			<div class="cart_footer  footer1" style="width: 553px;">
				<?php //TODO:: вставить товаров с учетов количества каждого товара ?>
				<?= sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), count($items) . ' ' . $PH->endOfWord(count($items), $ui->item('CARTNEW_PRODUCTS_TITLE2'), $ui->item('CARTNEW_PRODUCTS_TITLE1', $ui->item('CARTNEW_PRODUCTS_TITLE3'))), '<span class="items_cost">' . $PH->FormatPrice($total['itemsPrice']) . '</span>') ?>
			</div>
			<div class="cart_footer  footer1" style="width: 553px;">
				<?=$ui->item('ORDER_MSG_DELIVERY_COST')?> <span class="delivery_cost"><?= $PH->FormatPrice($total['deliveryPrice']); ?></span> <span class="add_cost" style="font-weight: bold; display: none;"><?=$ui->item('CARTNEW_OTHER_PRODUCTS_CART')?></span>
			</div>
			<div class="clearfix"></div>
			<div class="cart_footer footer2" style="width: 553px;">
				<?=$ui->item('CART_COL_SUBTOTAL_DELIVERY')?>: <span class="delivery_name"><?=$ui->item('MSG_DELIVERY_TYPE_0')?></span><span class="date" style="display: none"></span><?=$ui->item('CARTNEW_TOTAL_WEIGHT_LABEL')?>: <?= $total['fullWeight'] ?> <?=$ui->item('CARTNEW_WEIGHT_NAME')?>
			</div>
			<div class="clearfix"></div>

			<div class="cart_footer footer_promocode" style="width: 553px;">
				<?php $this->renderPartial('/cart/_promocode', array('priceId'=>'itogo_cost')); ?>
			</div>
			<div class="clearfix"></div>
			<div class="cart_footer footer3" style="width: 553px;">
				<?=$ui->item('CARTNEW_TOTAL_COST_LABEL')?>: <span class="itogo_cost" id="itogo_cost"><?= $PH->FormatPrice($total['itemsPrice'] + $total['deliveryPrice']); ?></span>
			</div>
			<div class="clearfix"></div>

			<div style="float: right; width: 575px; margin-bottom: 10px;">
				<?=$ui->item('CARTNEW_SEND_INFO_LABEL')?>
			</div>

		</div>
		<div class="clearfix"></div>
		<a href="javascript:;" class="order_start" style="margin: 20px auto; width: 360px; display: block" onclick="sendforma();"><?=$ui->item('CARTNEW_SEND_ORDER_BTN')?></a>
	</div>
</div>

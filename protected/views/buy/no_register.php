<?php /*Created by Кирилл (24.02.2019 10:34)*/
/**@var $this MyController*/
$PH = new ProductHelper();
$addrModel = new Address();
?>
<link rel="stylesheet" href="/new_style/order_buy.css">
<hr />

<div class="container cartorder" style="margin-bottom: 20px;">
	<ol>
		<li>
			<span class="step_header"><?=$ui->item('CARTNEW_NOREG_STEP1_TITLE')?></span>
			<div class="items_list">
				<?php $this->renderPartial('items', array('PH'=>$PH, 'total'=>$total, 'items'=>$items)); ?>
			</div>

			<div class="form">
				<?php $this->renderPartial('address_form', array('alias'=>'Reg', 'addrModel'=>$addrModel)); ?>
			</div>

			<div class="clearfix"></div>

			<label for="confirm">
				<input type="checkbox" class="checkbox_custom" value="1" name="confirm" id="confirm">
				<span class="checkbox-custom"></span>
				<?= $ui->item('CHECKBOX_TERMS_OF_USE') ?>
			</label>
		</li>
		<li>
			<span class="step_header"><?=$ui->item('CARTNEW_REG_STEP2_TITLE')?></span>
			<?php $this->renderPartial('delivery_form', array()); ?>
		</li>
		<li>
			<span class="step_header"><?=$ui->item('PAYMENT_METHOD')?></span>
			<div><label class="addr_buyer"><input type="checkbox" class="checkbox_custom" value="1" name="addr_buyer" id="addr_buyer" checked="checked" onclick="$('.address.addr2').toggle(); $('.address.addr2 select, .address.addr2 input, .address.addr2 textarea').val(''); $('.states_list2').hide()"><span class="checkbox-custom"></span> Данные Плательщика совпадают с Получателем </label>
			<div class="form">
			<?php $this->renderPartial('address_form', array('alias'=>'Address', 'userType'=>'payer', 'addrModel'=>$addrModel)); ?>
			</div>
			<div class="row spay">
			<?php $this->renderPartial('payments_form', array()); ?>
			</div>
		</li>
	</ol>

	<div class="clearBoth"></div>
	<div class="row" style="margin-left: 0; margin-top: 10px;">

		<div class="span6" style="width: 49%; margin-left: 0; margin-right: 1%;">

			<textarea id="Notes" style="width: 100%; margin-bottom: 0; height: 245px; box-sizing: border-box;" placeholder="<?=str_replace('<br />', '', $ui->item("address_contact_notes")); ?>" name="Address[notes]"></textarea>

		</div>
		<div class="span6" style="width: 50%; margin: 0;">

			<div class="cart_footer  footer1">
				<?php //TODO:: вставить товаров с учетов количества каждого товара ?>
				<?= sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), count($items) . ' ' . $PH->endOfWord(count($items), $ui->item('CARTNEW_PRODUCTS_TITLE2'), $ui->item('CARTNEW_PRODUCTS_TITLE1', $ui->item('CARTNEW_PRODUCTS_TITLE3'))), '<span class="items_cost">' . $PH->FormatPrice($total['itemsPrice']) . '</span>') ?>
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
			</div>

			<div class="cart_footer"><a class="order_start"><?=$ui->item('CARTNEW_SEND_ORDER_BTN')?></a></div>
			<div class="cart_footer">
				<?=$ui->item('CARTNEW_SEND_INFO_LABEL')?>
			</div>

		</div>
		<div class="clearfix"></div>
	</div>
</div>

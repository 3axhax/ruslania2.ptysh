<?php /*Created by Кирилл (24.02.2019 10:34)*/
/**@var $this MyController*/
$PH = new ProductHelper();
$addrModel = new Address();
?>
<link rel="stylesheet" href="/new_style/order_buy.css">
<hr />

<div class="container cartorder" id="js_orderForm" style="margin-bottom: 20px;">
	<ol>
		<li id="deliveryContactData">
			<span class="step_header"><?=$ui->item('CARTNEW_NOREG_STEP1_TITLE')?></span>
			<div class="items_list">
				<?php $this->renderPartial('items', array('PH'=>$PH, 'total'=>$total, 'items'=>$items, 'countItems'=>$countItems)); ?>
			</div>

			<div class="form">
				<?php $this->renderPartial('address_form', array('alias'=>'Reg', 'addrModel'=>$addrModel, 'onlyPereodic'=>$onlyPereodic, 'existPereodic'=>$existPereodic)); ?>
			</div>

			<div class="clearfix"></div>

			<label for="confirm">
				<input type="checkbox" class="checkbox_custom" value="1" name="confirm" id="confirm">
				<span class="checkbox-custom"></span>
				<?= $ui->item('CHECKBOX_TERMS_OF_USE') ?>
			</label>
			<span class="texterror" style="display: none;"><?= $ui->item('CARTNEW_ERROR_AGREE_CONDITION') ?></span>
		</li>
		<li id="deliveryTypeData">
			<div class="op"></div>
			<span class="step_header"><?=$ui->item('DELIVERY_METHOD')?></span>
			<?php $this->renderPartial('delivery_form', array()); ?>
		</li>
		<li id="paymentsData">
			<div class="op"></div>
			<span class="step_header"><?=$ui->item('PAYMENT_METHOD_PAYER')?></span>
			<div><label class="addr_buyer"><input type="checkbox" class="checkbox_custom" value="1" name="addr_buyer" id="addr_buyer" checked><span class="checkbox-custom"></span> <?= $ui->item('DELIVERY_EQUALLY_PAYER') ?></label>
			<div class="form">
			<?php $this->renderPartial('address_form', array('alias'=>'Address', 'userType'=>'payer', 'addrModel'=>$addrModel, 'onlyPereodic'=>$onlyPereodic)); ?>
			</div>
			<div class="row spay">
			<?php $this->renderPartial('payments_form', array()); ?>
			</div>
			<div class="clearBoth"></div>
			<div class="row" style="margin-left: 0; margin-top: 10px;">

				<div class="span6" style="width: 49%; margin-left: 0; margin-right: 1%;">

					<textarea id="Notes" style="width: 100%; margin-bottom: 0; height: 245px; box-sizing: border-box;" placeholder="<?=str_replace('<br />', '', $ui->item("address_contact_notes")); ?>" name="Address[notes]"></textarea>

				</div>
				<div class="span6" style="width: 50%; margin: 0;">

					<div class="cart_footer  footer1">
						<?= sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), $countItems . ' ' . $PH->endOfWord($countItems, $ui->item('CARTNEW_PRODUCTS_TITLE2'), $ui->item('CARTNEW_PRODUCTS_TITLE1', $ui->item('CARTNEW_PRODUCTS_TITLE3'))), '<span class="items_cost">' . $PH->FormatPrice($total['itemsPrice']) . '</span>') ?>
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

					<?php
					//TODO:: название кнопки исправить на "Завершить оформление заказа" или "Оформить заказ и оплатить"
					//	'BUTTON_ORDER_PAY' => 'Оформить заказ и оплатить',
					//  'BUTTON_ORDER_SAVE' => 'Завершить оформление заказа',
					?>
					<div class="cart_footer"><a class="order_start"><span class="js_orderPay"><?=$ui->item('BUTTON_ORDER_PAY')?></span><span class="js_orderSave" style="display: none;"><?=$ui->item('BUTTON_ORDER_SAVE')?></span></a></div>
					<div class="cart_footer">
						<?=$ui->item('CARTNEW_SEND_INFO_LABEL')?>
					</div>

				</div>
				<div class="clearfix"></div>
			</div>
		</li>
	</ol>

</div>
<script>
	$(function(){
		scriptLoader('/new_js/modules/cart.js').callFunction(function() {
			cart().init({
				onlyPereodic: <?= (int) $onlyPereodic ?>,
				existPereodic: <?= (int) $existPereodic ?>,
				urlRecount: '<?= Yii::app()->createUrl('buy/checkPromocode') ?>',
				urlChangeCountry: '<?= Yii::app()->createUrl('buy/deliveryInfo') ?>',
				urlLoadStates: '<?= Yii::app()->createUrl('buy/loadstates') ?>',
				urlSubmit: '<?= Yii::app()->createUrl('buy/orderAdd') ?>'
			});
		});
	});
</script>

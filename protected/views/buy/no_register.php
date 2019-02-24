<?php /*Created by Кирилл (24.02.2019 10:34)*/ ?>
<link rel="stylesheet" href="/new_style/order_buy.css">
<hr />
<div class="container cartorder" style="margin-bottom: 20px;">
	<div class="row" style="margin-left: 0; margin-top: 10px;">

		<div class="span6" style="width: 49%; margin-left: 0; margin-right: 1%;">

			<textarea id="Notes" style="width: 100%; margin-bottom: 0; height: 245px; box-sizing: border-box;" placeholder="<?=str_replace('<br />', '', $ui->item("address_contact_notes")); ?>" name="Address[notes]"></textarea>

		</div>
		<div class="span6" style="width: 50%; margin: 0;">

			<div class="cart_footer  footer1" style="width: 553px;">
				<?=$ui->item('ORDER_MSG_DELIVERY_COST')?> <span class="delivery_cost">0 &euro;</span> <span class="add_cost" style="font-weight: bold; display: none;"><?=$ui->item('CARTNEW_OTHER_PRODUCTS_CART')?></span>
			</div>
			<div class="clearfix"></div>
			<div class="cart_footer footer2" style="width: 553px;">
				<?=$ui->item('CART_COL_SUBTOTAL_DELIVERY')?>: <span class="delivery_name"><?=$ui->item('MSG_DELIVERY_TYPE_0')?></span><span class="date" style="display: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: 05.07.2018 </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$ui->item('CARTNEW_TOTAL_WEIGHT_LABEL')?>: <?/*= $cartInfo['fullInfo']['weight']*/ ?> <?=$ui->item('CARTNEW_WEIGHT_NAME')?>
			</div>
			<div class="clearfix"></div>

			<div class="cart_footer footer_promocode" style="width: 553px;">
				<?php $this->renderPartial('/cart/_promocode', array('priceId'=>'itogo_cost')); ?>
			</div>
			<div class="clearfix"></div>
			<div class="cart_footer footer3" style="width: 553px;">
				<?=$ui->item('CARTNEW_TOTAL_COST_LABEL')?>: <span class="itogo_cost" id="itogo_cost"><?/*= $PH->FormatPrice($fullprice);*/ ?></span>
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
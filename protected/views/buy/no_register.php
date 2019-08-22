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
			<span class="step_header"><?=$ui->item('PAYMENT_METHOD_DELIVERY')?></span>
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
			<?php $this->renderPartial('itogo', array('PH'=>$PH, 'total'=>$total, 'items'=>$items, 'countItems'=>$countItems)); ?>
		</li>
	</ol>

</div>
<script type="text/javascript" src="/js/stripe.js"></script>
<!--<script type="text/javascript" src="https://js.stripe.com/v2/"></script>-->
<script>
	$(function(){
		
		ym(53579293, 'reachGoal', 'cart_step3');
		scriptLoader('/new_js/modules/cart.js?v=2208').callFunction(function() {
			cart().init({
				userData: <?= json_encode($userInfo) ?>,
				onlyPereodic: <?= (int) $onlyPereodic ?>,
				existPereodic: <?= (int) $existPereodic ?>,
				urlRecount: '<?= Yii::app()->createUrl('buy/checkPromocode') ?>',
				urlChangeCountry: '<?= Yii::app()->createUrl('buy/deliveryInfo') ?>',
				urlGetCountry: '<?= Yii::app()->createUrl('buy/getCountry') ?>',
				urlLoadStates: '<?= Yii::app()->createUrl('buy/loadstates') ?>',
				urlSubmit: '<?= Yii::app()->createUrl('buy/orderAdd') ?>',
				urlCheckEmail: '<?= Yii::app()->createUrl('buy/checkEmail') ?>'
			});
		});
	});
</script>

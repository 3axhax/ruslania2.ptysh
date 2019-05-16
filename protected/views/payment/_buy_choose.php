<?php /*Created by Кирилл (23.03.2019 18:17)*/ ?>
<?php $isPaid = isset($isPaid) && $isPaid; ?>

<?php if ($isPaid) : ?>

	<div class="information info-box">
		<?= $ui->item('ALREADY_PAID'); ?>
	</div>
<?php endif; ?>


<div id="js_print">
<?php $this->renderPartial('/client/_one_order', array('order' => $order, 'onlyContent' => !$isPaid,
	'class' => $isPaid ? '' : 'bordered',
	'enableSlide' => !$isPaid)); ?>
</div>
	<script type="text/javascript">
		function slideContents(id) {
			$('#cnt' + id).fadeToggle();
		}
	</script>

<?php $pid = $order['payment_type_id']; ?>


<?php if (!$isPaid) :
	$dtype = $order['delivery_type_id'];
	if (empty($order['delivery_address_id'])) $dtype = 0;
	?>
	<link rel="stylesheet" href="/new_style/order_buy.css">
	<ol><li class="nonum" id="paymentsData">
		<div class="row spay">
			<?php $this->renderPartial('/buy/payments_form', array()); ?>
		</div>
		<div class="clearBoth"></div>
		<div class="row">
			<div class="cart_footer" style="margin-right: 294px;"><div class="order_start"><span class="js_orderPay"><?=$ui->item('CARTNEW_FINAL_BTN_PAYPAL')?></span><span class="js_orderSave" style="display: none;"><?=$ui->item('SET_PAYMENT_OPTION')?></span></div></div>
		</div>
	</li></ol>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script>
		$(function(){
			scriptLoader('/new_js/modules/cart.js').callFunction(function() {
				repay().init({
					orderId: <?= $order['id'] ?>,
					dtype: <?= $dtype ?>,
					ptype: <?= $order['payment_type_id'] ?>,
					action: 'changePaySystem',
					urlSubmit: '<?= Yii::app()->createUrl('buy/orderEdit') ?>'
				});
			});
		});
	</script>
<?php endif; ?>

<script type="text/javascript" src="/new_js/modules/print.js"></script>
<script type="text/javascript">
	print = function() { return new _Print(); };
	print().init({$button: $('.printed_btn'), $content: $('#js_print')});
</script>


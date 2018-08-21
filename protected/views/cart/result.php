<hr />

<div class="container cartorder">
	<h1><?= Yii::app()->ui->item('HEADER_PAYTYPE' . $ptype) ?></h1>
	<div><?= Yii::app()->ui->item('DESC_PAYTYPE' . $ptype, $number_zakaz) ?></div>

Спасибо за заказ! Номер заказа <?= $number_zakaz . $dop ?>

<div class="clearBoth"></div>
	<div>Или выберите <a style="cursor: pointer;" onclick="openPaySystems('dtype<?= ($ptype-1) ?>'); return false;">другой способ оплаты</a></div>
	<div id="pay_systems" class="row spay" style="display: none;">
		<?php $this->renderPartial('/site/pay_systems', array()); ?>
	</div>
</div>
<script type="text/javascript">
	function openPaySystems(inputId) {
		var $ptypeP = $('#' + inputId).parent();
		$ptypeP.css('border', '1px solid #64717f').addClass('act');
		$('input[type=radio]', $ptypeP).attr('checked', 'true');
		$('.check', $ptypeP).addClass('active');
		$('#pay_systems').show();
	}
	function check_cart_sel(cont,cont2,inputId) {
		document.location.href = '<?= Yii::app()->createUrl('cart/orderPay') ?>?id=<?= $number_zakaz ?>&ptype=' + document.getElementById(inputId).value;
	}
</script>

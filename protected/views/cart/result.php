<hr />

<div class="container cartorder">
    
        <?php if ($ptype == 1 OR $ptype == 0) { $ptype_1 = 999; } else { $ptype_1 = $ptype; } ?>
    
	<h1><?= Yii::app()->ui->item('HEADER_PAYTYPE' . $ptype_1) ?></h1>
        <?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?> 
		
		<?=sprintf($ui->item('CARTNEW_RESULT_TEXT1'), $number_zakaz, ProductHelper::FormatPrice($order['full_price'], $order['currency_id']), $dop)?>
		<br /><br />
	<div><?= Yii::app()->ui->item('DESC_PAYTYPE' . $ptype_1, $number_zakaz) ?></div>
<br />

<div class="clearBoth"></div>

	

	<div><?=sprintf($ui->item('CARTNEW_ORDER_PAY_OTHER_LABEL'), 'dtype'.$ptype )  ?></div>
	<div id="pay_systems" class="row spay" style="display: none;">
		<?php $this->renderPartial('/site/pay_systems', array()); ?>
	</div>


    <div style="margin: 15px 0;">
        <?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
    </div>

	<div style="height: 20px;"></div>
    <a href="<?= Yii::app()->createUrl('/view/'.$number_zakaz)?>" class="order_start" style="background-color: #28618E;  margin-top: -65px;">
                            <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_FINAL_BTN_VIEW_ORDER')?></span>
                        </a>
    <div style="height: 20px;"></div>

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

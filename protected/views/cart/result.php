<hr />

<?

$addrGet = CommonHelper::FormatAddress2($order['DeliveryAddress']);

//var_dump( $addrGet );

$hide_btn_next = 0;

if ($addrGet['streetaddress'] == '' OR $addrGet['postindex'] == '' OR $addrGet['city'] == '') { $hide_btn_next = 1; }

?>

<input type="hidden" value="<?=$hide_btn_next?>" name="hide_btn_next" />

<div class="container cartorder">
    
	<h1><?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?></h1>
	
		<div class="row">
	
	<div class="span8">
		
		<?php $this->renderPartial('/client/_one_order_my2', array('order' => $order, 'show_btn'=>'1')); ?>
	
	</div>
	
	<div class="span6">
		
		<div style="background-color: #f8f8f8; padding: 49px 42px;">
		
		<?php if ($ptype == 1 OR $ptype == 0) { $ptype_1 = 999; } else { $ptype_1 = $ptype; } ?>
		
		<? if (trim($dop) AND trim($dop) != '.') $dop = $dop.'.';?>
	
        <?//=sprintf($ui->item('CARTNEW_RESULT_TEXT1'), $number_zakaz, ProductHelper::FormatPrice($order['full_price'], $order['currency_id']), $dop)?>
		
	<div><?= Yii::app()->ui->item('DESC_PAYTYPE' . $ptype_1, $number_zakaz) ?></div>


<div class="clearBoth"></div>

	

	<br />
	<br />


    <div style="">
        <?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
    </div>


	
	
	
	</div>
</div>	
						
    <div style="height: 20px;"></div>

</div>
</div>
<script type="text/javascript">
	function openPaySystems(inputId) {
		var $ptypeP = $('#' + inputId).parent();
		$ptypeP.css('border', '1px solid #64717f').addClass('act');
		$('input[type=radio]', $ptypeP).attr('checked', 'true');
		$('.check', $ptypeP).addClass('active');
		$('#pay_systems').toggle();
	}
	function check_cart_sel(cont,cont2,inputId) {
		document.location.href = '<?= Yii::app()->createUrl('cart/orderPay') ?>?id=<?= $number_zakaz ?>&ptype=' + document.getElementById(inputId).value;
	}
</script>

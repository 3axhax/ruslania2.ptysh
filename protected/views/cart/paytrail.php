<hr />

<style>

	#pay_systems .selp { width: 100%; text-align: center }
</style>

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
		
		<?php $this->renderPartial('/client/_one_order_my2', array('order' => $order)); ?>
	
	</div>
	
	<div class="span6">


    
    <div>
        <?=$ui->item('CARTNEW_YOUR_SELECT')?>: <?= Yii::app()->ui->item('HEADER_PAYTRAIL') ?><br />

   <div style="height: 20px;"></div>
	
	<? 
		$class1 = ' hide';
		$class2 = '';
	?>
	<? if ($hide_btn_next == '1') : ?>
	
		<? $class1 = '' ?>
		<? $class2 = ' display: none' ?>
	
	<? endif; ?>
	
	
	<span class="redtext error_pay_pt<?=$class1?>">Для оплаты не заполнен адрес доставки</span>
	
	<div style="<?=$class2?>" class="hide_block_pay">
	
	
	
    <?php $this->widget('PayTrailWidget', array('order' => $order)); ?>
	
	
	
	</div>
	

	
    <div class="clearBoth"></div>

    <div style="margin: 15px 0;">
        <?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
    </div>
    </div>
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

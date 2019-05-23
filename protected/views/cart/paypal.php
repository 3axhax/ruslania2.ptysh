<hr />

<?

$addrGet = CommonHelper::FormatAddress2($order['DeliveryAddress']);

//var_dump( $addrGet );

$hide_btn_next = 0;

if ($addrGet['streetaddress'] == '' OR $addrGet['postindex'] == '' OR $addrGet['city'] == '') { $hide_btn_next = 1; }

?>

<input type="hidden" value="<?=$hide_btn_next?>" name="hide_btn_next" />

<style>

	#pay_systems .selp { width: 100%; text-align: center }

</style>

<div class="container cartorder">
    <h1><?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?></h1>
	
	<div class="row">
	
	<div class="span8">
		
		<?php $this->renderPartial('/client/_one_order_my2', array('order' => $order)); ?>
	
	</div>
	
	<div class="span6">

    <div>
       
    
    
    <div class="popup0 popup<?=$p['id']?>" style="background-color: rgba(0,0,0,0.3); position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 99999; opacity: 0.3; display: none;" onclick="$('.popup0').hide();"></div>
    <div style="background-color: rgb(255, 255, 255); position: absolute; padding: 1%; width: 88%; z-index: 99999; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px; left: 5%; top: 5%; display: none; z-index: 999991;" class="popup0 popup1">
        
        <div style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="$('.popup0').hide();">X</div>
        
        <style> .popup0 div { width: auto !important; } </style>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . '/pictures/templates-static/paypal_'.Yii::app()->language.'.html.php'); ?>
    
    </div>
	
    <?=$ui->item('CARTNEW_PAYPAL_LABEL')?>
    
    
	<br />
	<br />
    <?php $this->widget('PayPalPayment', array('order' => $order, 'hide_btn_next' => $hide_btn_next, 'magaz'=>$order['delivery_type_id'])); ?>
   
    
    <div><?//=sprintf($ui->item('CARTNEW_ORDER_PAY_OTHER_LABEL'), 'dtype8')?></div>
    
    <div style="margin: 15px 0;">
        <?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
    </div>
	
	
	<div style="height: 20px;"></div>
    
	
	
	
						
	</div>					
	</div>					
    <div style="height: 20px;"></div>
	

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
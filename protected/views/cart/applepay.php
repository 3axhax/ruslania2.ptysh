<style>
  #apple-pay-button {
    display: none;
    background-color: black;
    background-image: url('/images/apple_pay_logo_black.png');
    background-size: auto 44px;
    background-origin: content-box;
    background-position: center;
    background-repeat: no-repeat;
    width: 320px;
    height: 44px;
    margin: 0 auto;
    margin-top: 20px;
    padding: 10px 0;
    border-radius: 10px;
  }
  
  #pay_systems .selp { width: 100%; text-align: center }
  
</style>
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
		
		<?php $this->renderPartial('/client/_one_order_my2', array('order' => $order)); ?>
	
	</div>
	
	<div class="span6">


       <div>
        Вы выбрали: <?= Yii::app()->ui->item('HEADER_APPLEPAY') ?><br /><br />
		
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
	
	
	
		<div id="apple-pay-text"><b>Ваше устройство не поддерживает ApplePay</b></div>
  
		<button id="apple-pay-button"></button>
	
	
	
	</div>
	
	
		
		
		


    <div style="margin: 15px 0;">
        Если у Вас остались вопросы по оформленному заказу или способам оплаты, звоните по номеру <a href="tel:+35892727070">+358 9 2727070</a> или WhatsApp (текст или звонок) +358 503889439 по будням с 9 до 18 ч., по субботам с 10 до 16 ч (по финскому времени GMT +2, летом GMT +3).
    </div>
  
</div>


    <div style="height: 20px;"></div>


<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

  Stripe.setPublishableKey('pk_test_B8MwXuaz10DDZVcF6QJQTki0');

  Stripe.applePay.checkAvailability(function(available) {
	if (available) {
      document.getElementById('apple-pay-button').style.display = 'block';
      document.getElementById('apple-pay-text').style.display = 'none';
    }
  });

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
</div>
</div>
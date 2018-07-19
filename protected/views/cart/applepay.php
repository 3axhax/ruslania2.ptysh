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
</style>
<hr />
<div class="container cartorder">
  <h1><?= Yii::app()->ui->item('HEADER_APPLEPAY') ?></h1>
  <div><?= Yii::app()->ui->item('DESC_APPLEPAY') ?></div>

  <div id="apple-pay-text">Ваше устройство не поддерживает ApplePay</div>
  <button id="apple-pay-button"></button>

  <div>Или выберите <a style="cursor: pointer;" onclick="openPaySystems('dtype5'); return false;">другой способ оплаты</a></div>
  <div id="pay_systems" class="row spay" style="display: none;">
    <?php $this->renderPartial('/site/pay_systems', array()); ?>
  </div>
</div>

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
    $('#pay_systems').show();
  }
  function check_cart_sel(cont,cont2,inputId) {
    document.location.href = '<?= Yii::app()->createUrl('cart/orderPay') ?>?id=<?= $number_zakaz ?>&ptype=' + document.getElementById(inputId).value;
  }

</script>

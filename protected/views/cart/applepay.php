<hr />
<div class="container cartorder">
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script>

Stripe.setPublishableKey('pk_test_B8MwXuaz10DDZVcF6QJQTki0');

Stripe.applePay.checkAvailability(function(available) {
  if (available) {
    document.getElementById('apple-pay-button').style.display = 'block';
    document.getElementById('apple-pay-text').style.display = 'none';
  }
});

</script>

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
<div id="apple-pay-text">Ваше устройство не поддерживает ApplePay</div>
<button id="apple-pay-button"></button>

</div>
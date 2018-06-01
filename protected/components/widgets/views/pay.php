<script type="text/javascript" src="//payment.verkkomaksut.fi/js/payment-widget-v1.0.min.js"></script>
<form action="https://payment.verkkomaksut.fi/" method="post" id="payment">
    <input name="MERCHANT_ID" type="hidden" value="13466">
    <input name="AMOUNT" type="hidden" value="<?=$this->order['full_price']; ?>">
    <input name="ORDER_NUMBER" type="hidden" value="<?=$this->order['id']; ?>">
    <input name="REFERENCE_NUMBER" type="hidden" value="<?=$refNumber; ?>">
    <input name="ORDER_DESCRIPTION" type="hidden" value="<?=$desc; ?>">
    <input name="CURRENCY" type="hidden" value="EUR">
    <input name="RETURN_ADDRESS" type="hidden" value="<?=$successUrl; ?>">
    <input name="CANCEL_ADDRESS" type="hidden" value="<?=$cancelUrl; ?>">
    <input name="PENDING_ADDRESS" type="hidden" value="">
    <input name="NOTIFY_ADDRESS" type="hidden" value="<?=$notifyUrl; ?>">
    <input name="TYPE" type="hidden" value="S1">
    <input name="CULTURE" type="hidden" value="<?=$culture; ?>">
    <input name="MODE" type="hidden" value="1">
    <input name="VISIBLE_METHODS" type="hidden" value="">
    <input name="GROUP" type="hidden" value="">
    <input name="AUTHCODE" type="hidden" value="<?=$hash; ?>">
    <input type="image" class="sort" name="sv_button" src="https://ssl.verkkomaksut.fi/logo/payhere_fin.jpg" >
</form>
<script type="text/javascript">
    SV.widget.initWithForm('payment', { width: 800});
</script>
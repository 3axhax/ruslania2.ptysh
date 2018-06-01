<form method="post" action="<?=PayPalPayment::URL; ?>">
<INPUT TYPE="hidden" NAME="cmd" VALUE="_ext-enter">
<INPUT TYPE="hidden" NAME="redirect_cmd" VALUE="_xclick">
<INPUT TYPE="hidden" NAME="business" VALUE="<?=PayPalPayment::BUSINESS; ?>">
<INPUT TYPE="hidden" NAME="item_name" VALUE="<?=$this->GetDescription(false); ?>">
<INPUT TYPE="hidden" NAME="item_number" VALUE="<?=$this->order['id']; ?>">
<INPUT TYPE="hidden" NAME="quantity" VALUE="1">
<INPUT TYPE="hidden" NAME="amount" VALUE="<?=$this->order['full_price']; ?>">
<INPUT TYPE="hidden" NAME="invoice" VALUE="<?=$this->order['invoice_refnum']; ?>">
<INPUT TYPE="hidden" NAME="no_shipping" VALUE="1">
<INPUT TYPE="hidden" NAME="no_note" VALUE="0">
<INPUT TYPE="hidden" name="currency_code" value="<?=Currency::ToStr($this->order['currency_id']); ?>">
<input type="hidden" name="return" value="<?=$this->GetAcceptUrl(); ?>">
<input type="hidden" name="cancel_return" value="<?=$this->GetCancelUrl(); ?>">
<input type="hidden" name="rm" value="2">
<INPUT TYPE="image" SRC="/pic1/paypal.jpg" ALT="Make payments with PayPal - it\'s fast, free and secure!"  data-ptid="<?=Payment::PAY_PAL; ?>">
</form>

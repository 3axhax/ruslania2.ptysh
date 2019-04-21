<? $ui = new RuslaniaUI; ?>
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

<? if ($this->hide_btn_next == '1') : ?>

<a href="javascript:;" class="order_start disabled paypalbtn" style="background-color: #5bb75b;" data-ptid="<?=Payment::PAY_PAL; ?>" onclick="<?=(($this->hide_btn_next == '1') ? "$('.error_text_btn').removeClass('hide'); setTimeout(function() { $('.error_text_btn').addClass('hide'); }, 3000); return false;" : "$('form').submit()")?>"><i class="fa fa-paypal"></i> <?=$ui->item('CARTNEW_FINAL_BTN_PAYPAL')?></a>

<span class="error_text_btn paypalbtn_error redtext hide" style="margin-left: 20px; font-size: 14px;">
					
	Не все данные заполнены
					
</span>

<? else :  ?>

<a href="javascript:;" class="order_start paypalbtn" style="background-color: #5bb75b;  margin-top: -65px;" data-ptid="<?=Payment::PAY_PAL; ?>" onclick="<?=(($this->hide_btn_next) ? "$('.error_text_btn').removeClass('hide'); setTimeout(function() { $('.error_text_btn').addClass('hide'); }, 3000); return false;" : "$('form').submit()")?>"><i class="fa fa-paypal"></i> <?=$ui->item('CARTNEW_FINAL_BTN_PAYPAL')?></a>

<span class="error_text_btn redtext hide" style="margin-left: 20px; font-size: 14px;">
					
	Не все данные заполнены
					
</span>

<? endif; ?>

<!--<INPUT TYPE="image" SRC="/pic1/paypal.jpg" ALT="Make payments with PayPal - it\'s fast, free and secure!"  data-ptid="<?=Payment::PAY_PAL; ?>">-->
</form>

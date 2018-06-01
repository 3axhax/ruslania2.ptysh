<form method="post" action="<?=LuottokuntaPayment::URL; ?>">
<input name="Merchant_Number" type="hidden" value="<?=LuottokuntaPayment::MerchantNumber; ?>">
<input name="Card_Details_Transmit" type="hidden" value="0">
<input name="Language" type="hidden" value="<?=$lang; ?>">
<input name="Device_Category" type="hidden" value="1">
<input name="Order_ID" type="hidden" value="<?=$this->order['id']; ?>">
<input name="Amount" type="hidden" value="<?=$fullPrice; ?>">
<input name="Currency_Code" type="hidden" value="978">
<input name="Order_Description" type="hidden" value="<?=$this->GetDescription(true); ?>">
<input name="Success_Url" type="hidden" value="<?=$this->GetAcceptUrl(); ?>">
<input name="Failure_Url" type="hidden" value="<?=$this->GetCancelUrl(); ?>">
<input name="Cancel_Url" type="hidden" value="<?=$this->GetCancelUrl(); ?>">
<input name="Transaction_Type" type="hidden" value="1">
<input name="Authentication_Mac" type="hidden" value="<?=$checkSum; ?>">
<!--<input type="image" src="/pic1/visa_mastercard_amex_logo.gif">-->
    <input type="image" src="/pic1/ccard.jpg" title="Credit cards"  data-ptid="<?=Payment::Luottokunta; ?>">
</form>

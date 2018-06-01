<form method="post" action="<?=NordeaPayment::URL; ?>">
    <input name="SOLOPMT_VERSION" type="hidden" value="0003">
    <input name="SOLOPMT_STAMP" type="hidden" value="<?=$stamp; ?>">
    <input name="SOLOPMT_RCV_ID" type="hidden" value="<?=NordeaPayment::RCV_ID; ?>">
    <input name="SOLOPMT_RCV_ACCOUNT" type="hidden" value="<?=NordeaPayment::RCV_ACCOUNT; ?>">
    <input name="SOLOPMT_LANGUAGE" type="hidden" value="'.$lng.'">
    <input name="SOLOPMT_AMOUNT" type="hidden" value="<?=$orderPrice; ?>">
    <input name="SOLOPMT_REF" type="hidden" value="<?=$this->order['invoice_refnum']; ?>">
    <input name="SOLOPMT_DATE" type="hidden" value="EXPRESS">
    <input name="SOLOPMT_MSG" type="hidden" value="<?=$this->GetDescription(true); ?>">
    <input name="SOLOPMT_RETURN" type="hidden" value="<?=$this->GetAcceptUrl(); ?>">
    <input name="SOLOPMT_CANCEL" type="hidden" value="<?=$this->GetCancelUrl(); ?>">
    <input name="SOLOPMT_REJECT" type="hidden" value="<?=$this->GetCancelUrl(); ?>">
    <input name="SOLOPMT_MAC" type="hidden" value="<?=$checkSum; ?>">
    <input name="SOLOPMT_CONFIRM" type="hidden" value="YES">
    <input name="SOLOPMT_KEYVERS" type="hidden" value="<?=NordeaPayment::RCV_MAC_VERSION; ?>">
    <input name="SOLOPMT_CUR" type="hidden" value="EUR">
    <input type="image" src="/pic1/nordea.jpg"  data-ptid="<?=Payment::Nordea; ?>">
</form>

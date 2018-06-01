<FORM METHOD="POST"ACTION="<?=DanskePayment::URL; ?>">
    <INPUT NAME="KNRO" TYPE="HIDDEN" VALUE="<?=DanskePayment::PROVIDER_ID; ?>">
    <INPUT NAME="SUMMA" TYPE="HIDDEN" VALUE="<?=$orderPrice; ?>">
    <INPUT NAME="VIITE" TYPE="HIDDEN" VALUE="<?=$this->order['invoice_refnum']; ?>">
    <INPUT NAME="VALUUTTA" TYPE="HIDDEN" VALUE="EUR">
    <INPUT NAME="VERSIO" TYPE="HIDDEN" VALUE="4">
    <INPUT NAME="ERAPAIVA" TYPE="HIDDEN" VALUE="<?=date('d.m.Y'); ?>">
    <INPUT NAME="OKURL" TYPE="HIDDEN" VALUE="<?=$this->GetAcceptUrl(); ?>">
    <INPUT NAME="VIRHEURL" TYPE="HIDDEN" VALUE="<?=$this->GetCancelUrl(); ?>">
    <INPUT NAME="TARKISTE" TYPE="HIDDEN"
           VALUE="<?=$checkSum256; ?>">
    <INPUT NAME="ALG" TYPE="HIDDEN" VALUE="03">
    <input type="image" src="/pic1/danske.png" data-ptid="<?=Payment::Danske; ?>">
</FORM>


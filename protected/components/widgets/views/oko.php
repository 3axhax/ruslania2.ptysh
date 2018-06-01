<form method="post" action="<?= OKOPayment::URL; ?>">
    <input name="action_id" type="hidden" value="701">
    <input name="VERSIO" type="hidden" value="1">
    <input name="MAKSUTUNNUS" type="hidden" value="<?=$orderNumber; ?>">
    <input name="MYYJA" type="hidden" value="<?= OKOPayment::MYYJA; ?>">
    <input name="SUMMA" type="hidden" value="<?= $orderPrice; ?>">
    <input name="VIITE" type="hidden" value="<?= $orderRefNum; ?>">
    <input name="VIESTI" type="hidden" value="<?= $desc; ?>">
    <input name="TARKISTE-VERSIO" type="hidden" value="<?= OKOPayment::TARKISTE_VERSIO; ?>">
    <input name="TARKISTE" type="hidden" value="<?=$checkSum; ?>">
    <input name="PALUU-LINKKI" type="hidden" value="<?= $acceptUrl; ?>">
    <input name="PERUUTUS-LINKKI" type="hidden" value="<?= $cancelUrl; ?>">
    <input name="VAHVISTUS" type="hidden" value="Y">
    <input name="VALUUTTALAJI" type="hidden" value="EUR">
    <input type="image" src="/pic1/oko_logo.jpg"  data-ptid="<?=Payment::OKO; ?>">
</form>
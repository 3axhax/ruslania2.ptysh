<form action="https://payment.paytrail.com/" method="post" id="pt<?=$formName; ?>">
    <input name="MERCHANT_ID" type="hidden" value="<?=$provider->GetMerchantID($env); ?>">
    <input name="AMOUNT" type="hidden" value="<?=$provider->amount; ?>">
    <input name="ORDER_NUMBER" type="hidden" value="<?=$provider->orderNumber; ?>">
    <input name="REFERENCE_NUMBER" type="hidden" value="">
    <input name="ORDER_DESCRIPTION" type="hidden" value="<?=$provider->description; ?>">
    <input name="CURRENCY" type="hidden" value="EUR">
    <input name="RETURN_ADDRESS" type="hidden" value="<?=$provider->successUrl; ?>">
    <input name="CANCEL_ADDRESS" type="hidden" value="<?=$provider->cancelUrl; ?>">
    <input name="PENDING_ADDRESS" type="hidden" value="">
    <input name="NOTIFY_ADDRESS" type="hidden" value="<?=$provider->notifyUrl; ?>">
    <input name="TYPE" type="hidden" value="<?=$provider->type; ?>">
    <input name="CULTURE" type="hidden" value="<?=$provider->culture; ?>">
    <input name="PRESELECTED_METHOD" type="hidden" value="">
    <input name="MODE" type="hidden" value="1">
    <input name="VISIBLE_METHODS" type="hidden" value="">
    <input name="GROUP" type="hidden" value="">
    <input name="AUTHCODE" type="hidden" value="<?=$provider->GetHash($env); ?>">
    <input type="submit" value="PAYYY" class="btn btn-success">
</form>

<?php

//CClientScript::POS_BEGIN
Yii::app()->clientScript->registerScriptFile('//payment.paytrail.com/js/payment-widget-v1.0.min.js');

$js = <<<EEE
SV.widget.initWithForm('pt$formName', {charset:'UTF-8'});
EEE;

Yii::app()->clientScript->registerScript('paytrailJS', $js);



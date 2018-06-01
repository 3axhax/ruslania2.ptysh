<?php

class PayTrailWidget extends CWidget
{
    public $order;
    public $env = PayTrail::ENV_TEST;

    public function run()
    {
        $provider = new PayTrail();
        $provider->orderNumber = $this->order['id'];
        $provider->amount = Currency::ConvertToEUR($this->order['full_price'], $this->order['currency_id']);
        $provider->currency = 'EUR';
        $provider->successUrl = Yii::app()->createAbsoluteUrl('/payment/accept', array('oid' => $this->order['id'], 'tid' => Payment::Paytrail));
        $provider->cancelUrl = Yii::app()->createAbsoluteUrl('/payment/cancel', array('oid' => $this->order['id'], 'tid' => Payment::Paytrail));
        $provider->notifyUrl = Yii::app()->createAbsoluteUrl('/payment/notify', array('oid' => $this->order['id'], 'tid' => Payment::Paytrail));

        $this->render('paytrail', array('provider' => $provider,
            'formName' => uniqid(),
            'env' => $this->env,
        ));
    }
}
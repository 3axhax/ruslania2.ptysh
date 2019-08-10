<?php

class PayTrailWidget extends CWidget
{
    public $order;
    public $env = /*PayTrail::ENV_TEST;//*/PayTrail::ENV_PROD;
    public $acceptUrl = 'payment/accept',
        $cancelUrl = 'payment/cancel',
        $notifyUrl = 'payment/notify';

    var $tpl = 'paytrail';

    function __construct($owner=null) {
        parent::__construct($owner);
        if ((int)Yii::app()->user->id === 77925) $this->env = PayTrail::ENV_TEST;
    }

    public function run()
    {
        $provider = new PayTrail();
        $provider->orderNumber = $this->order['id'];
        $provider->amount = Currency::ConvertToEUR($this->order['full_price'], $this->order['currency_id']);
        $provider->currency = 'EUR';
        $provider->successUrl = Yii::app()->createAbsoluteUrl($this->acceptUrl, array('oid' => $this->order['id'], 'tid' => Payment::Paytrail));
        $provider->cancelUrl = Yii::app()->createAbsoluteUrl($this->cancelUrl, array('oid' => $this->order['id'], 'tid' => Payment::Paytrail));
        $provider->notifyUrl = Yii::app()->createAbsoluteUrl($this->notifyUrl, array('oid' => $this->order['id'], 'tid' => Payment::Paytrail));
        
        $langInt = $provider->ptype[Yii::app()->language];
        
        if (!$langInt) { $langInt = 'en_US'; }
        
        $provider->culture = $langInt;

        $this->render($this->tpl, array('provider' => $provider,
            'formName' => uniqid(),
            'env' => $this->env,
        ));
    }
}
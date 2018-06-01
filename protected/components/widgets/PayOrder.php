<?php

// http://docs.paytrail.com/files/payment-api-en.pdf
class PayOrder extends CWidget
{
    public $order;
    public function run()
    {
        $successUrl = Yii::app()->createAbsoluteUrl('payment/process', array('mode' => 'success'));
        $cancelUrl = Yii::app()->createAbsoluteUrl('payment/process', array('mode' => 'cancel'));
        $notifyUrl = Yii::app()->createAbsoluteUrl('payment/process', array('mode' => 'notify'));
        $desc = 'Ruslania order '.$this->order['id'];
        $refNumber = '';

        $culture = 'en_US';
        if(Yii::app()->language == 'fi') $culture = 'fi_FI';
        if(Yii::app()->language == 'se') $culture = 'sv_SE';

        $parts = array();
        $parts[] = Yii::app()->params['MerchantAuthHash']; // merchant hash
        $parts[] = Yii::app()->params['MerchantID']; // merchant id
        $parts[] = $this->order['full_price'];
        $parts[] = $this->order['id']; // order_id
        $parts[] = $refNumber;
        $parts[] = $desc;
        $parts[] = 'EUR';
        $parts[] = $successUrl;
        $parts[] = $cancelUrl;
        $parts[] = ''; // pending address
        $parts[] = $notifyUrl;
        $parts[] = 'S1'; //type
        $parts[] = $culture;
        $parts[] = ''; //Preselected payment method
        $parts[] = 1; //Service mode
        $parts[] = ''; //Visible paymement method
        $parts[] = ''; //Group code

        $imp = implode('|', $parts);
        $hash = strtoupper(md5($imp));

        $this->render('pay', array('hash' => $hash, 'culture' => $culture,
                                   'successUrl' => $successUrl,
                                   'desc' => $desc,
                                   'refNumber' => $refNumber,
                                   'cancelUrl' => $cancelUrl,
                                   'notifyUrl' => $notifyUrl,
                             ));
    }
}
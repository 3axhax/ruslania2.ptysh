<?php

class OrderForm extends CFormModel
{
    public $BillingAddressID;
    public $DeliveryAddressID;
    public $DeliveryTypeID;
    public $Notes;
    public $DeliveryMode;
    public $CurrencyID;
    public $Mandate;
    public $payment;
    public $SmartpostAddress;
    private $sid;

    public function __construct($sid)
    {
        $this->sid = $sid;
    }

    public static function GenMandate()
    {
        return md5(uniqid(time(), true).'SOMESALT#1#5');
    }

    public function rules()
    {
        return array(
            array('Notes, DeliveryAddressID, BillingAddressID, CurrencyID, Mandate, SmartpostAddress', 'safe'),
            array('DeliveryMode', 'checkDM'),
            array('DeliveryTypeID', 'checkDeliveryType'),
            array('CurrencyID', 'checkCurrency'),
            array('Mandate', 'checkMandate'),
        );
    }

    public function checkMandate($attribute, $params)
    {
        $value = trim($this->$attribute);
        if(strlen($value) != 32)
            $this->Mandate = self::GenMandate();
    }

    public function checkCurrency($attribute, $params)
    {
        $value = $this->$attribute;
        $list = Currency::GetList();
        if(!in_array($value, $list))
            $this->addError($attribute, 'WrongCurrencyCode');
    }

    public function checkDM($attribute, $params)
    {
        $uid = Yii::app()->user->id;
        $value = $this->DeliveryMode;
        if($value == 1) // выкуп в магазине
        {
            $this->DeliveryAddressID = 0;
            $this->BillingAddressID = 0;
            return;
        }

        // доставка на адрес
        $a = new Address();

        if(!$a->IsMyAddress($uid, $this->DeliveryAddressID))
            $this->addError('DeliveryAddressID', 'WrongDeliveryAddress');

        if(!$a->IsMyAddress($uid, $this->BillingAddressID))
            $this->addError('BillingAddressID', 'WrongBillingAddress');

    }

    public function checkDeliveryType($attribute, $params)
    {
        if($this->DeliveryMode == 1) return; // выкуп в магазине
        $uid = Yii::app()->user->id;
        $value = $this->DeliveryTypeID;
        $p = new PostCalculator();

        $did = $this->DeliveryAddressID;
        $list = $p->GetRates($did, $uid, $this->sid);
        $avail = false;
        foreach($list as $rate)
        {
            if($rate['id'] == $value) $avail = true;
        }

        if(!$avail)
            $this->addError('DeliveryTypeID', 'WrongDeliveryType');
    }
}
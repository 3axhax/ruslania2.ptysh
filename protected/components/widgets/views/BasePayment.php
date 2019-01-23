<?php

abstract class BasePayment extends CWidget
{
    public $order;
    public abstract function GetPaymentType();
    public abstract function CheckPayment($oid, $params, $order);

    public function GetAcceptUrl()
    {
        return Yii::app()->createAbsoluteUrl('payment/accept', array('oid' => $this->order['id'],
                                                                           'tid' => $this->GetPaymentType()));
    }

    public function GetCancelUrl()
    {
        return Yii::app()->createAbsoluteUrl('payment/cancel', array('oid' => $this->order['id'],
                                                                     'tid' => $this->GetPaymentType()));
    }

    public function GetAmount($convertToEur=true)
    {
        $orderPrice = $this->order['full_price'];
        if($convertToEur && $this->order['currency_id'] != Currency::EUR)
        {
            $orderPrice = Currency::ConvertToEUR($orderPrice, $this->order['currency_id']);
            $name = 'EUR';
        }
        else
        {
            $name = Currency::ToStr($this->order['currency_id']);
        }

        return array('Currency' => $name, 'Amount' => $orderPrice);
    }

    public function GetDescription($convertToEur=true)
    {
        $data = $this->GetAmount($convertToEur);
        return "Ruslania Books Oy internet shop order No.".$this->order['id'].", ".$data['Amount']." ".$data['Currency'];
    }
}
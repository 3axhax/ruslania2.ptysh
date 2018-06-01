<?php
//http://ruslania/payment/accept/oid/1033496/tid/3?LKMAC=8787e54a8b1c1e902dbbfcd49bde2315bb13d36d1c0560814d489492b6d3e617

class LuottokuntaPayment extends BasePayment
{
    const URL = 'https://dmp2.luottokunta.fi/dmp/html_payments';
    const MerchantNumber = '8462525';
    const MAC = 'QKyyB6GZ4z';

    public function run()
    {
        $lang = 'EN';
        if(Yii::app()->language == 'fi') $lang = 'FI';
        $fullPrice = Currency::ConvertToEUR($this->order['full_price'], $this->order['currency_id']);
        $fullPrice =$fullPrice * 100;

        $checkSum = self::MerchantNumber.'&'
            .$this->order['id'].'&'
            .$fullPrice.'&'
            .'978&' // Currency_Code
            .'1&' // Transaction_Type
            .self::MAC;

        $checkSum = strtoupper(hash('sha256', $checkSum));


        $this->render('luottokunta', array('lang' => $lang,
                                           'fullPrice' => $fullPrice,
                                           'checkSum' => $checkSum
                                          ));
    }

    public function GetPaymentType()
    {
        return Payment::Luottokunta;
    }

    public function CheckPayment($oid, $params, $order)
    {
        $checkSum = strtoupper(@$params['LKMAC']);

        if(strlen($checkSum) != 64) return false;

        $fullPrice = Currency::ConvertToEUR($order['full_price'], $order['currency_id']);

        $msg = self::MAC.'&' // MAC
            .'1&' // Transaction_Type
            .'978&' // Currency_Code
            .(int) (100 * $fullPrice).'&' // Amount
            .$order['id'].'&'
            .self::MerchantNumber;

        $verifyCode = strtoupper(hash('sha256', $msg));

        return $verifyCode === $checkSum;
    }
}
<?php

// http://ruslania/payment/accept/oid/1033495/tid/4?SOLOPMT_RETURN_VERSION=0003&SOLOPMT_RETURN_STAMP=1375782002086939000&SOLOPMT_RETURN_REF=10334957&SOLOPMT_RETURN_PAID=06082588INWB1467&SOLOPMT_RETURN_MAC=76A99B855077271212B73B5826B93B35

class NordeaPayment extends BasePayment
{
    const RCV_ID = '26093195';
    const RCV_MAC = '5pwkU7eS3cda8mS5vQDKsUqNwZmuVM6s'; // "LEHTI" wtf?
    const RCV_MAC_VERSION = '0001';
    const RCV_ACCOUNT = '12393000604228';
    //const URL = 'https://solo3.nordea.fi/cgi-bin/SOLOPM01';
    const URL = 'https://epmt.nordea.fi/cgi-bin/SOLOPM01';

    public function run()
    {
        list($usec, $sec) = explode(" ", microtime());
        $orderStamp = $sec . str_replace(".", "", $usec);

        $orderPrice = Currency::ConvertToEUR($this->order['full_price'], $this->order['currency_id']);
        $orderPrice = number_format($orderPrice, 2, '.', '');

        $orderMacChecksum = "0003" . "&" . // 'SOLOPMT_VERSION'      AN 4
            $orderStamp . "&" . // 'SOLOPMT_STAMP'        N 20
            self::RCV_ID . "&" . // 'SOLOPMT_RCV_ID'       AN 15
            $orderPrice . "&" . // 'SOLOPMT_AMOUNT'       AN 19
            $this->order['invoice_refnum'] . "&" . // 'SOLOPMT_REF'          AN 20
            "EXPRESS" . "&" . // 'SOLOPMT_DATE'         AN 10
            "EUR" . "&" . // 'SOLOPMT_CUR'          A 3
            self::RCV_MAC . "&"; // 'Service Provider's MAC'

        $checkSum = strtoupper(md5($orderMacChecksum));


        $this->render('nordea', array('stamp' => $orderStamp,
                                      'checkSum' => $checkSum,
                                      'orderPrice' => $orderPrice));
    }

    public function GetPaymentType()
    {
        return Payment::Nordea;
    }

    public function CheckPayment($oid, $params, $order)
    {
        $checkNum = @$params['SOLOPMT_RETURN_MAC'];
        if(strlen($checkNum) !== 32) return false;

        $verifyCode = @$params['SOLOPMT_RETURN_VERSION'].'&'
                     .@$params['SOLOPMT_RETURN_STAMP'].'&'
                     .@$params['SOLOPMT_RETURN_REF'].'&'
                     .@$params['SOLOPMT_RETURN_PAID'].'&'
                     .self::RCV_MAC.'&';

        $verifyCode = strtoupper(md5($verifyCode));

        return $verifyCode === $checkNum;
    }
}
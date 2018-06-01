<?php

class OKOPayment extends BasePayment
{
    const MYYJA = 'RUSLANIABOOKS';
    const TARKISTE_VERSIO = '1';
    const TARKISTE = 'JW6AHZMQ8TBLCUCEKK2V5LRBSPHMAU63JVEHSYD2';
    const URL = 'https://kultaraha.osuuspankki.fi:443/cgi-bin/krcgi';

    public function run()
    {
        $orderPrice = $this->order['full_price'];
        $currency = $this->order['currency_id'];

        // convert to eur
        $orderPrice = Currency::ConvertToEUR($orderPrice, $currency);
        $orderPrice = number_format($orderPrice, 2, '.', '');

        $orderDescription = $this->GetDescription(true);

        $orderChecksum =
            "1".                                    // 'VERSIO'
            $this->order['id'].                           // 'MAKSUTUNNUS'
            self::MYYJA.                      // 'MYYJA'
            $orderPrice.                            // 'SUMMA'
            $this->order['invoice_refnum'].                // 'VIITE'
            "EUR".                                  // 'VALUUTTALAJI'
            self::TARKISTE_VERSIO.            // 'TARKISTE_VERSIO'
            self::TARKISTE;

        $orderChecksum = strtoupper(md5($orderChecksum));

        $this->render('oko', array(
                                   'orderPrice' => $orderPrice,
                                   'orderNumber' => $this->order['id'],
                                   'orderRefNum' => $this->order['invoice_refnum'],
                                   'desc' => $orderDescription,
                                   'checkSum' => $orderChecksum,
                                   'acceptUrl' => $this->GetAcceptUrl(),
                                   'cancelUrl' => $this->GetCancelUrl(),
                             ));
    }

    public function GetPaymentType()
    {
        return Payment::OKO;
    }

    public function CheckPayment($oid, $params, $order)
    {
        if(!isset($params['TARKISTE'])) return false;
        if(strlen($params['TARKISTE']) !== 32) return false;

        $verifyCode = @$params['VERSIO']
                     .@$params['MAKSUTUNNUS']
                     .@$params['VIITE']
                     .@$params['ARKISTOINTITUNNUS']
                     .@$params['TARKISTE-VERSIO']
                     .self::TARKISTE;

        $verifyCode = strtoupper(md5($verifyCode));
        return $verifyCode === $params['TARKISTE'] && $oid === $params['MAKSUTUNNUS'];
    }
}
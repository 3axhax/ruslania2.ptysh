<?php

//http://ruslania/payment/accept/oid/1033493/tid/2?KNRO=006452788800&VALUUTTA=EUR&VIITE=10334931&SUMMA=5,00&VERSIO=2&STATUS=0&TARKISTE=295CE9A69A243E24BF44D5D12BBB4421&MTAPA=1
//http://ruslania2.com/payment/accept/oid/7003804/tid/2?KNRO=006452788800&VALUUTTA=EUR&VIITE=70038044&SUMMA=5,00&VERSIO=4&STATUS=0&TARKISTE=4D3B292193A2C5CB4D9474D558B3817381800F022CDD316486DF076EA30EBAE0&MTAPA=1&ERAPAIVA=17.12.2013
//http://www.danskebank.fi/PDF/fi/Yritysasiakkaat/Verkkopalvelut/VerkkomaksupalveluPalveluntarjoajanOhjekirjaFI.pdf
class DanskePayment extends BasePayment
{
    const URL = 'https://verkkopankki.danskebank.fi/SP/vemaha/VemahaApp';
    //const PROVIDER_MAC = 'hDCSFk597RaGWMtmNyfGgZ7zG4gmsxg7RXnA2XLPZKs7p4gDZE5mKTyHrKqmmh4A'; // old
//    const PROVIDER_MAC = 'rKMpMK6tLRXy94kr2tJ2Qp2wCaHyp3p6cTPQL7E69KMM5W5Q7FDbUSzxtJaT97aL'; // old 13.11.2015
    const PROVIDER_MAC = 'DFGKsc5Hz6rwtVykRvyntt6MUGuCgX4QHFw7a5nZkA8DHhQgCB8rnJuD8DVmjRaW';
    const PROVIDER_ID = '006452788800';

    public function run()
    {
        $orderPrice = Currency::ConvertToEUR($this->order['full_price'], $this->order['currency_id']);
        $orderPrice = number_format($orderPrice, 2, '.', '');

        $checkSum = md5
        (
            self::PROVIDER_MAC.
            $orderPrice.
            $this->order['invoice_refnum'].
            self::PROVIDER_ID.
            '2'.
            'EUR'.
            $this->GetAcceptUrl().
            $this->GetCancelUrl()
        );

//        VERIFICATION CODE = SHA256(MAC+’&’+SUM+’&’+REFERENCE NUMBER+’&’+ID+’&’+
//    VERSION+’&’+CURRENCY+’&’+RETURN ADDRESS+’&’+CANCELLATION ADDRESS+’&’+
//    DUE DATE+’&’)

        $checkSum256 = hash('sha256',
                      self::PROVIDER_MAC
                      .'&'.$orderPrice
                      .'&'.$this->order['invoice_refnum']
                      .'&'.self::PROVIDER_ID
                      .'&'.'4'
                      .'&'.'EUR'
                      .'&'.$this->GetAcceptUrl()
                      .'&'.$this->GetCancelUrl()
                      .'&'.date('d.m.Y')
                      .'&');

        $this->render('danske', array('checkSum' => $checkSum,
                                      'checkSum256' => $checkSum256,
                                      'orderPrice' => $orderPrice));
    }

    public function GetPaymentType()
    {
        return Payment::Danske;
    }

    public function CheckPayment($oid, $params, $order)
    {
        $orn = intVal(@$params['VIITE']);
        if(empty($orn)) return false;

        $checkNum = @$params['TARKISTE'];
        if(strlen($checkNum) != 64) return false;

//        VERIFICATION CODE=SHA256(MAC+’&’+REFERENCE NUMBER +’&’+SUM+’&’+ STATUS+ ’&’+↵
//ID+ ’&’+VERSION+’&’+CURRENCY+’&’+DUE DATE+’&’)

        $verifyCode = strtoupper(hash('sha256',
            self::PROVIDER_MAC
           .'&'.@$params['VIITE']
           .'&'.@$params['SUMMA']
           .'&'.@$params['STATUS']
           .'&'.self::PROVIDER_ID
           .'&'.@$params['VERSIO']
           .'&'.@$params['VALUUTTA']
           .'&'.@$params['ERAPAIVA']
           .'&'
        ));


//        $verifyCode = self::PROVIDER_MAC
//                    .@$params['VIITE']
//                    .@$params['SUMMA']
//                    .@$params['STATUS']
//                    .self::PROVIDER_ID
//                    .@$params['VERSIO']
//                    .@$params['VALUUTTA'];
//        $verifyCode = strtoupper(md5($verifyCode));

        return $verifyCode === $checkNum;
    }
}
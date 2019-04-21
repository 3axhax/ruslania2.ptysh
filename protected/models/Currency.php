<?php

class Currency
{
    const EUR = 1;
    const USD = 2;
    const GBP = 3;
    private static $cache = array();

    public static function GetList()
    {
        return array(self::EUR, self::USD, self::GBP);
    }

    public static function GetRates()
    {
        if(!empty(self::$cache)) return self::$cache;

        $key = 'CurrencyRates';
        $data = Yii::app()->dbCache->get($key);
        if($data === false)
        {
            $sql = 'SELECT * FROM currencies';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $data = array();
            foreach($rows as $row)
            {
                $data[$row['id']] = $row['eur_rate'];
            }
            Yii::app()->dbCache->set($key, $data, Yii::app()->params['DbCacheTime']);
        }

        self::$cache = $data;
        return $data;
    }

    public static function ToSign($currency=null)
    {
        if(empty($currency)) $currency = Yii::app()->currency;
        switch($currency)
        {
            case self::EUR : return '€';
            case self::USD : return '$';
            case self::GBP : return '£';
        }
    }

    public static function ToStr($currency)
    {
        switch($currency)
        {
            case self::EUR : return 'EUR';
            case self::USD : return 'USD';
            case self::GBP : return 'GBP';
        }
    }

    public static function ConvertToEUR($orderPrice, $currency)
    {
        if($currency == Currency::EUR || empty($currency)) return $orderPrice;
        $orderPrice = floatval($orderPrice);
        $rates = self::GetRates();
        $rate = $rates[$currency];
        $newPrice = $orderPrice / $rate;
        $newPrice = round($newPrice, 1);
        return $newPrice;
    }

    /**
     * @param $price
     * @param $priceCurrency int текущая валюта
     * @param $toCurrency int новая валюта
     * @return float
     */
    static function convertToCurrency($price, $priceCurrency, $toCurrency) {
        if ($priceCurrency == $toCurrency) return $price;
        if (($toCurrency ==  Currency::EUR) || empty($toCurrency)) return self::ConvertToEUR($price, $priceCurrency);

        $rates = self::GetRates();
        $rate = $rates[$priceCurrency];
        $toRate = $rates[$toCurrency];
        return round(($price / $rate) * $toRate, 1);
    }
}
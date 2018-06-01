<?php

class PayTrail
{
    const ENV_PROD = 1;
    const ENV_TEST = 2;

    private $environments = array(
        self::ENV_TEST => array(
            'MerchantID' => '13466',
            'MerchantSecret' => '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ',
        ),
        self::ENV_PROD => array(
            'MerchantID' => '34135',
            'MerchantSecret' => 'r2Rz29cVKt8Ljsd63bBrZX4qUDXXLq',
        ),
    );

    public $env;
    public $amount;
    public $orderNumber;
    public $referenceNumber;
    public $notifyUrl;
    public $successUrl;
    public $cancelUrl;
    public $pendingUrl;
    public $description;
    public $currency = 'EUR';
    public $type = 'S1';
    public $culture = 'fi_FI';
    public $paymentUniqID = '';

    public function GetSecret($env)
    {
        return $this->environments[$env]['MerchantSecret'];
    }

    public function GetMerchantID($env)
    {
        return $this->environments[$env]['MerchantID'];
    }

    public function GetHash($env)
    {
        // http://docs.paytrail.com/en/ch05s03.html

        $ret = array();
        $ret[] = $this->GetSecret($env);
        $ret[] = $this->GetMerchantID($env);
        $ret[] = $this->amount;
        $ret[] = $this->orderNumber;
        $ret[] = $this->referenceNumber;
        $ret[] = $this->description;
        $ret[] = $this->currency;
        $ret[] = $this->successUrl;
        $ret[] = $this->cancelUrl;
        $ret[] = $this->pendingUrl;
        $ret[] = $this->notifyUrl;
        $ret[] = $this->type;
        $ret[] = $this->culture;
        $ret[] = ''; // preselected method
        $ret[] = 1; // mode
        $ret[] = ''; // visible methods
        $ret[] = ''; // groups

        $line = implode('|', $ret);

        return strtoupper(md5($line));

    }

    public function CheckPayment($oid, $data, $order, $env)
    {
        // http://docs.paytrail.com/fi/ch05s04.html
        if (!is_array($data)) return false;

        $fields = array('ORDER_NUMBER', 'TIMESTAMP', 'PAID', 'METHOD', 'RETURN_AUTHCODE');
        $ret = array();
        foreach ($fields as $field)
        {
            if (!array_key_exists($field, $data)) return false;
            if ($field != 'RETURN_AUTHCODE')
                $ret[] = $data[$field];
        }

        $env = $env == 'test' ? PayTrail::ENV_TEST : PayTrail::ENV_PROD;
        $ret[] = $this->GetSecret($env);

        $line = implode('|', $ret);

        $checkSum = strtoupper(md5($line));

        $this->paymentUniqID = $data['PAID'];
        $this->orderNumber = $data['ORDER_NUMBER'];

        return $checkSum === $data['RETURN_AUTHCODE'];
    }

    public function GetPaymentUniqID()
    {
        return $this->paymentUniqID;
    }

    public function GetOrderNumber()
    {
        return $this->orderNumber;
    }
}
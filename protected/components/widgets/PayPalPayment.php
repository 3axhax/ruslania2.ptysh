<?php

class PayPalPayment extends BasePayment
{
    const URL = 'https://www.paypal.com/cgi-bin/webscr';
    const AUTH = 'NOmQpS3HRnmk-smm8oOmQHR7HpulUk4bFWrNY-f0rqZ6PWNL0faARwh07aC';
    const BUSINESS = 'paypal@ruslania.com';

    public function GetPaymentType()
    {
        return Payment::PAY_PAL;
    }

    public function run()
    {
        $this->render('paypal');
    }

/*



     */



    public function CheckPayment($oid, $params, $order)
    {
        $request =
            'cmd=_notify-synch'.
            '&tx='.@$params['tx'].
            '&at='.self::AUTH;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST,          1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,        $request);
        curl_setopt($ch, CURLOPT_URL,           'https://www.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,    2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);

        $data = curl_exec($ch);

        if (curl_errno($ch) > 0)
        {
            curl_close($ch);
            return FALSE;
        }
        else
        {
            $lines = explode("\n", $data);
            $arr = array();

            if (strcmp ($lines[0], "SUCCESS") == 0)
            {
                for ($i=1; $i < sizeof($lines);$i++)
                {
                    $line = trim($lines[$i]);
                    if(empty($line)) continue;
                    list($key,$val) = explode("=", $line);
                    $arr[urldecode($key)] = urldecode($val);
                }

                if ( $arr['receiver_email'] != self::BUSINESS ||
                    $arr['payment_status'] != 'Completed' )
                {
                    curl_close($ch);
                    return FALSE;
                }

                $orderID = (int) $arr['item_number'];
                if($orderID != $oid)
                {
                    curl_close($ch);
                    return false;
                }
            }
            else if (strcmp ($lines[0], "FAIL") == 0)
            {
                curl_close($ch);
                return FALSE;
            }
        }
        curl_close($ch);
        return TRUE;
    }
}
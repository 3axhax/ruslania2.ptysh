<?php

class Payment
{

    const INVOICE = 7; //Инвойс / Счет (Только для клиентов Финляндии и организаций из стран ЕС)
    const PREPAYMENT_RUSLANIA_FINLAND = 13; // Предоплата на банковский счет Руслании в Финляндии
    const PREPAYMENT_GERMANY = 22; // Предоплата на банковский счет Руслании в Германии
    const PREPAYMENT_ENGLAND = 17; // Оплата на банковский счет в Великобритании
    const PREPAYMENT_RUSSIA = 14; // Оплата на банковский счет в России
    const PREPAYMENT_UKRAINE = 19; // Оплата на банковский счет на Украине
    const OKO = 6; // Оплата через финский интернет-банк OKO
    const Luottokunta = 3; // Оплата кредитной или банковской карточкой Visa, MasterCard Debit/Credit или American Express (только в евро!)
    const PAY_PAL = 8; // PayPal
    const Nordea = 4;// Nordea
    const Danske = 2; // Danske (ent. SAMPO)
    const Moneygram = 24; // Moneygram
    const Paytrail = 25; // PayTrail
    const Alipay = 26; // Alipay


    // self:: PREPAYMENT_GERMANY

    public static function GetOffilePaymentList()
    {
        $offline = array(self::INVOICE, self::PREPAYMENT_RUSLANIA_FINLAND,
            self::PREPAYMENT_RUSSIA,self::Alipay,
//            self::PREPAYMENT_UKRAINE,
//        self::Moneygram
        );

        $ret = array();
        foreach ($offline as $o)
            $ret[] = array('ID' => $o, 'Name' => Yii::app()->ui->item('MSG_PAYMENT_TYPE_' . $o),
                           'Desc' => Yii::app()->ui->item('MSG_PAYMENT_TYPE_COMMENT_' . $o));


        return $ret;
    }

    public static function ConvertFromVerkkomaksut($method)
    {
        switch ($method)
        {
            case 1 :
                return 4; // nordea
            case 2 :
                return 6; // osuuspankki
            case 3 :
                return -5;
        }

//3//Sampo Pankki
//4//Tapiola
//5//Ålandsbanken
//6//Handelsbanken
//7//Säästöpankit, paikallisosuuspankit, Aktia, Nooa
//8//Luottokunta
//9//Paypal
//10//S-Pankki
//11//Klarna, Laskulla
//12//Klarna, Osamaksulla
//13//Collector (poistuu käytöstä marraskuussa 2012, uusi Collector-maksutapa 19)
//18//Joustoraha
//19//Collector
    }

    public static function GetPaymentList()
    {
        return array(
            array('id' => 1, 'name' => 'Инвойс'),
            array('id' => 2, 'name' => 'Оплата через финский интернет-банк Sampo'),
            array('id' => 3, 'name' => 'Оплата кредитной или банковской карточкой Visa, MasterCard Debit/Credit или American Express (только в евро!)'),
            array('id' => 4, 'name' => 'Оплата через финский интернет-банк Nordea'),
            array('id' => 5, 'name' => 'Оплата кредитной или банковской карточкой Visa, MasterCard Debit/Credit или American Express'),
            array('id' => 6, 'name' => 'Оплата через финский интернет-банк OKO'),
            array('id' => 7, 'name' => 'Инвойс / Счет (Только для клиентов Финляндии и организаций из стран ЕС)'),
            array('id' => 8, 'name' => 'Paypal'),
            array('id' => 10, 'name' => 'Pay by check ( only in USD )'),
            array('id' => 11, 'name' => 'Pay by check ( only in USD )'),
            array('id' => 12, 'name' => 'Paypal'),
            array('id' => 13, 'name' => 'Предоплата на банковский счет Руслании в Финляндии'),
            array('id' => 14, 'name' => 'Оплата на банковский счет в России'),
            array('id' => 15, 'name' => 'Предоплата на банковский счет Руслании в Финляндии'),
            array('id' => 16, 'name' => 'Оплата на банковский счет в России'),
            array('id' => 17, 'name' => 'Оплата на банковский счет в Великобритании'),
            array('id' => 18, 'name' => 'Оплата на банковский счет в Великобритании'),
//            array('id' => 19, 'name' => 'Оплата на банковский счет на Украине'),
//            array('id' => 20, 'name' => 'Оплата на банковский счет на Украине'),
            array('id' => 21, 'name' => 'Предоплата на банковский счет Руслании в Германии'),
            array('id' => 22, 'name' => 'Предоплата на банковский счет Руслании в Германии'),
            array('id' => 23, 'name' => 'Google Wallet'),
            //array('id' => 24, 'name' => 'Moneygram'),
            array('id' => 25, 'name' => 'PayTrail'),
        );
    }

    public static function CheckPayment($oid, $tid, $params, $order) {
        if (OrderState::IsPaid($order['States'])) return true;

        $tid = intVal($tid);


        $classes = array(
            self::OKO => 'OKOPayment',
            self::Danske => 'DanskePayment',
            self::PAY_PAL => 'PayPalPayment',
            self::Nordea => 'NordeaPayment',
            self::Luottokunta => 'LuottokuntaPayment',
            self::Paytrail => 'PayTrail',
        );

        if(!array_key_exists($tid, $classes)) return false;
        $cName = $classes[$tid];

        $cls = new $cName;
        $result = $cls->CheckPayment($oid, $params, $order, Yii::app()->params['PAYMENT_ENVIRONMENT']);

        if(!$result)
        {
            $msg = CommonHelper::Log('Payment fail '.$oid.' - '.$tid);
            self::_mailOrder($order, Yii::app()->ui->item('ORDER_HELP'), 'order_pay_fail', array('oid'=>$oid, 'tid'=>$tid, 'params'=>$params));
            return false;
        }

        self::_mailOrder($order, Yii::app()->ui->item('ORDER_HAS_BEEN_PAID'), 'order_pay_success', array('oid'=>$oid, 'tid'=>$tid, 'params'=>$params));
        return true;
    }

    private static function _mailOrder($order, $subject, $tpl, $forlog = array()) {
        $user = User::model()->findByPk($order['uid']);
        $message = new YiiMailMessage($subject);
        $message->view = $tpl;
        $message->setBody(array(
            'user' => $user->attributes,
            'order' => $order,
        ), 'text/html');
        $message->addTo($user['login']);
        $message->from = 'noreply@ruslania.com';
        $mailResult = @Yii::app()->mail->send($message);
        file_put_contents(Yii::getPathOfAlias('webroot') . '/test/mail.log', implode("\t", array(
                date('d.m.Y H:i:s'),
                $user['login'],
                serialize($mailResult),
                $message->view,
                serialize($message->from),
                serialize($forlog),
            )
        ) . "\n", FILE_APPEND);
    }


}


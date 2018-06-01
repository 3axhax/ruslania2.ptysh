<?php

class CommonHelper
{
    public static function CommonHeader($message)
    {
        $msg = 'IP: ' . @$_SERVER['REMOTE_ADDR'] . "\n"
            . 'Q: ' . @$_SERVER['QUERY_STRING'] . "\n"
            . 'R: ' . @$_SERVER['REQUEST_URI'] . "\n"
            . 'REFERER: ' . @$_SERVER['HTTP_REFERER'] . "\n"
            . 'UA: '.@$_SERVER['HTTP_USER_AGENT']."\n"
            . 'Message: ' . $message . "\n"
            . 'USER: '.Yii::app()->user->id."\n"
            . 'GET = '.print_r($_GET, true)."\n"
            . 'POST = '.print_r($_POST, true)."\n";

        return $msg;
    }

    public static function MyLog($msg)
    {
        if($_SERVER['REMOTE_ADDR'] == '83.145.211.92')
        {
            Yii::log(print_r($msg, true), CLogger::LEVEL_ERROR, 'myerrors');
        }
    }

    public static function FormatLog($exception, $message)
    {
        $msg = self::CommonHeader($message)
            . 'Exception: ' . $exception->getMessage() . "\n"
            . 'Stack: ' . $exception->getTraceAsString() . "\n------------------------------------------------\n";
        return $msg;
    }


    public static function LogException($ex, $msg, $category = 'myerrors')
    {
        $msg = self::FormatLog($ex, $msg);
        Yii::log($msg, 'error', $category);
    }

    public static function Log($msg, $category = 'myerrors')
    {
        $msg = self::CommonHeader($msg);
        Yii::log($msg, 'error', $category);
        return $msg;
    }

    // TODO: Organization
    public static function FormatAddress($address)
    {
        $ui = Yii::app()->ui;
        if(empty($address)) return $ui->item('NO_DATA');

        if($address['type'] == Address::ORGANIZATION) $org = $address['business_title'].', ';
        else $org = '';

        $name = $address['receiver_name'];
        if(empty($name))
        {
            $name = $address['receiver_first_name'];
            if(!empty($address['receiver_middle_name'])) $name .= ' '.$address['receiver_middle_name'];
            if(!empty($address['receiver_last_name'])) $name .= ' '.$address['receiver_last_name'];
        }

        $ret = $org.$name.', '
             .$address['streetaddress'].', '
             .$address['postindex'].' '.$address['city'].', '
             .$address['country_name'];
        return $ret;
    }

    public static function FormatDeliveryType($dti)
    {
        $ret = Yii::app()->ui->item("MSG_DELIVERY_TYPE_".$dti);
        return $ret;
    }

    public static function FormatPaymentType($pti)
    {
        $ret = Yii::app()->ui->item("MSG_PAYMENT_TYPE_".$pti);
        return $ret;
    }

    public static function EntityName($int)
    {
        switch($int)
        {
            case 99 : $key = 'person'; break;
            case 98 : $key = 'category'; break;
            case 97 : $key = 'series'; break;
            case 96 : $key = 'actor'; break;
            case 95 : $key = 'director'; break;
            case 94: $key = 'publisher'; break;
            case 93 : $key = 'publisherauthor'; break;
            case Entity::BOOKS : $key = 'books'; break;
        }

        return $key;
    }


}
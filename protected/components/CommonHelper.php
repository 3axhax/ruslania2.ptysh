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
    public static function FormatAddress($address, $showVerkkolaskuosoite = false)
    {
        $ui = Yii::app()->ui;
        if(empty($address)) return $ui->item('NO_DATA');

        if($address['type'] == Address::ORGANIZATION) $org = empty($address['business_title'])?'':$address['business_title'];
        else $org = '';

        $name = empty($address['receiver_name'])?'':$address['receiver_name'];
        if(empty($name)) {
            $name = empty($address['receiver_first_name'])?'':$address['receiver_first_name'];
            if(!empty($address['receiver_middle_name'])) $name .= ' '.$address['receiver_middle_name'];
            if(!empty($address['receiver_last_name'])) $name .= ' '.$address['receiver_last_name'];
        }
        $arr_labels = array();
		if ($org) $arr_labels[] = $org;
		if ($name) $arr_labels[] = $name;
        if ($showVerkkolaskuosoite&&(!empty($address['verkkolaskuosoite'])||!empty($address['operaattoritunnus']))) {
            if (!empty($address['verkkolaskuosoite'])) $arr_labels[] = 'verkkolaskuosoite: ' . $address['verkkolaskuosoite'];
            if (!empty($address['operaattoritunnus'])) $arr_labels[] = 'operaattoritunnus: ' . $address['operaattoritunnus'];
        }
        else {
            if (!empty($address['streetaddress'])) $arr_labels[] = $address['streetaddress'];
            if (!empty($address['postindex'])) $arr_labels[] = $address['postindex'];
            if (!empty($address['city'])) $arr_labels[] = $address['city'];
            if (!empty($address['country_name'])) {
                if (!empty($address['statesNameShort'])) $arr_labels[] = $address['statesNameShort'];
                elseif (!empty($address['state_id'])&&!empty($address['country'])) {
                    $countryStates = Country::model()->GetStatesList($address['country']);
                    foreach ($countryStates as $countryState) {
                        if ($countryState['id'] == $address['state_id']) {
                            $arr_labels[] = $countryState['title_short'];
                            break;
                        }
                    }
                }
                $arr_labels[] = $address['country_name'];
            }
        }

        // $ret = $org.$name.', '
             // .$address['streetaddress'].', '
             // .$address['postindex'].' '.$address['city'].', '
             // .$address['country_name'];
			 
			 
		$ret = implode(', ', $arr_labels);	 
			 
        return $ret;
    }
	
	public static function FormatAddress2($address)
    {
		
		
        $ui = Yii::app()->ui;
		
		$ret_array = array();
		
        if(empty($address)) return $ui->item('NO_DATA');

        if($address['type'] == Address::ORGANIZATION) $ret_array['org'] = $address['business_title'].', ';
			
			
			
       
            $ret_array['first_name'] = $address['receiver_first_name'];
            if(!empty($address['receiver_middle_name'])) $ret_array['middle_name'] = $address['receiver_middle_name'];
            if(!empty($address['receiver_last_name'])) $ret_array['last_name'] = $address['receiver_last_name'];
			
		
	
		$ret_array['streetaddress'] = $address['streetaddress'];
		$ret_array['postindex'] = $address['postindex'];
		$ret_array['city'] = $address['city'];
		$ret_array['country_name'] = $address['country_name'];
		
		$ret_array['contact_phone'] = $address['contact_phone'];
		$ret_array['contact_email'] = $address['contact_email'];
		$ret_array['type'] = $address['type'];
		$ret_array['business_title'] = $address['business_title'];
		$ret_array['business_number1'] = $address['business_number1'];
		

        return $ret_array;
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
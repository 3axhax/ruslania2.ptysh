<?php

class RequestState extends CMyActiveRecord
{
    const SavedInSystem = 1;
    const ProcessedByShop = 2;
    const Ready = 3;
    const Cancelled = 4;
    const ClientInformed = 5;
    const SearchContinues = 7;
    const RequestProcessed = 8;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users_requests_states';
    }

    public static function GetTitle($state, $lang='ru')
    {
        return Yii::app()->ui->item('REQUEST_MSG_STATE_'.$state);
    }

    public static function IsClosed($states)
    {
        if(!is_array($states)) throw new CException('IsClosed got non array');
        $closed = array(RequestState::RequestProcessed,
                        RequestState::Cancelled,
                       );
        foreach($states as $state)
        {
            if(is_array($state) && isset($state['state'])) $state = $state['state'];
            if(in_array($state, $closed)) return true;
        }

        return false;
    }
    public static function GetFirstState($states)
    {
        if(isset($states[0])) return self::AppendStateInfo($states[0]);
        throw new CException('Wrong state data');
    }

    public static function GetLastState($states)
    {
        $size = count($states);
        if(isset($states[$size-1]))
            return self::AppendStateInfo($states[$size-1]);
        throw new CException('Wrong state data');
    }

    public static function GetLastMailState($states)
    {
        $lastMail = null;
        foreach($states as $state)
        {
            if(isset($state['mail_date']) && !empty($state['mail_date'])) $lastMail = $state;
        }
        return self::AppendStateInfo($lastMail);
    }

    public static function AppendStateInfo($state)
    {
        if($state == null)
        {
            $state['state_string'] = 'нет';
            $state['date_string'] = 'нет';
            $state['mail_string'] = 'никогда';
            return $state;
        }
        $state['state_string'] = self::GetTitle($state['state']);
        $date = new DateTime($state['timestamp']);
        $state['date_string'] = $date->format('d') . ' ' . Yii::app()->ui->item('A_NEW_M' . (int)$date->format('m')) . ' ' . $date->format('Y H:i:s');
//        $state['date_string'] = Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $state['timestamp']);

        if (empty($state['mail_date'])) $state['mail_string'] = 'никогда';
        else {
            $date = new DateTime($state['mail_date']);
            $state['mail_string'] = $date->format('d') . ' ' . Yii::app()->ui->item('A_NEW_M' . (int)$date->format('m')) . ' ' . $date->format('Y H:i:s');
        }
//        $state['mail_string'] = $state['mail_date'] == null
//            ? 'никогда'
//            : Yii::app()->dateFormatter->format('dd MMMM yyyy HH:mm:ss', $state['mail_date']);
        return $state;
    }

    public static function HasState($needle, $haystack)
    {
        foreach($haystack as $stack) if($stack['state'] == $needle) return true;
        return false;
    }

}
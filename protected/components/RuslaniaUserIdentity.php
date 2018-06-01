<?php

class RuslaniaUserIdentity extends CUserIdentity
{
    private $_id;

    public function authenticate()
    {
        $record = User::model()->findByAttributes(array('login' => $this->username, 'is_closed' => 0));
        if ($record === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else if ($record->pwd !== $this->password)
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else
        {
            $this->_id = $record->id;
            //$this->setState('language', Language::ConvertToString($record->mail_language));
            //$this->setState('currency', $record->currency);
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getErrorMessage()
    {
        switch($this->errorCode)
        {
            case self::ERROR_PASSWORD_INVALID :
            case self::ERROR_USERNAME_INVALID : $key = 'REGISTER_ERROR_LOGIN_NOT_EXISTS'; break;
            default: return '';
        }

        $message = Yii::app()->ui->item($key);
        return $message;
    }
}
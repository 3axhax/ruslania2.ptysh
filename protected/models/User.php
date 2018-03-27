<?php

class User extends CActiveRecord
{
    public $pwd2;
    public $nothing;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return array(
            array('login, first_name, last_name', 'checkLatin'),
            array('login, pwd', 'required', 'on' => 'login'),
            array('login, pwd, pwd2, first_name, last_name', 'required', 'on' => 'register'),
            array('login', 'email', 'on' => 'register'),
            array('login', 'unique', 'on' => 'register'),
            array('pwd', 'compare', 'compareAttribute' => 'pwd2', 'on' => 'register'),

            array('pwd, pwd2, title_name, nothing, '
                 .'mail_books_news, mail_musicsheets_news, mail_music_news, '
                 .'mail_audio_news, mail_video_news, mail_maps_news, mail_soft_news, '
                 .'mail_language, middle_name', 'safe', 'on' => 'update'),
            array('pwd', 'compare', 'compareAttribute' => 'pwd2', 'on' => 'update'),
            array('login', 'email', 'on' => 'update'),
            array('login', 'unique', 'on' => 'update'),
            array('first_name, last_name', 'required', 'on' => 'update'),

            array('login', 'required', 'on' => 'forgot'),
            array('login', 'email', 'on' => 'forgot'),

        );
    }

    public function checkLatin($attribute, $params)
    {
        $value = trim($this->$attribute);
        if(empty($value)) return;

        // http://en.wikipedia.org/wiki/Cyrillic_script_in_Unicode
//Cyrillic: U+0400–U+04FF, 256 characters
//Cyrillic Supplement: U+0500–U+052F, 48 characters
//Cyrillic Extended-A: U+2DE0–U+2DFF, 32 characters
//Cyrillic Extended-B: U+A640–U+A69F, 96 characters
//Phonetic Extensions: U+1D2B, U+1D78, 2 Cyrillic characters
//        $patterns = array('|[\x{0400}-\x{04ff}]|ui',
//                          '|[\x{0500}-\x{052f}]|ui',
//                          '|[\x{a640}-\x{a69f}]|ui',
//                          '|[\x{1d2b}-\x{1d78}]|ui',
//                         );
//        foreach($patterns as $pattern)
//            if(preg_match($pattern, $value))
//            {
//                $this->addError($attribute, Yii::app()->ui->item('ONLY_LATIN'));
//                return;
//            }

        //http://en.wikipedia.org/wiki/List_of_Unicode_characters
        $pattern = '|[^\x{0020}-\x{007e}\x{00a1}-\x{024f}]|ui';
        if(preg_match($pattern, $value))
            $this->addError($attribute, Yii::app()->ui->item('ONLY_LATIN'));
    }

    public function RegisterNew($langID, $currencyID)
    {
	    $sql = 'INSERT INTO users (login, pwd, first_name, last_name, mail_language, mail_audio_news, mail_books_news, '
        . 'mail_maps_news, mail_music_news, mail_musicsheets_news, mail_soft_news, mail_video_news, currency) VALUES '
        . '(:login, :pwd, :fName, :lName, :lang, 1, 1, 1, 1, 1, 1, 1, :currency)';
        $ret = Yii::app()->db->createCommand($sql)->execute(array(
            ':login' => $this->login,
            ':pwd' => $this->pwd,
            ':fName' => $this->first_name,
            ':lName' => $this->last_name,
            ':lang' => $langID,
            ':currency' => $currencyID));

        return $ret;
    }


    public function GetAddresses($uid)
    {
        $sql = 'SELECT uas.*, ua.*, cl.title_en AS country_name, cl.is_europe, cl.code '
               .'FROM users_addresses AS uas JOIN user_address AS ua ON uas.address_id=ua.id '
               .'JOIN country_list AS cl ON ua.country=cl.id '
               .'WHERE uas.uid=:uid ORDER BY if_default DESC';
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':uid' => $uid));
        $ret = array();
        foreach($rows as $row)
        {
            $withVat = Address::UseVAT($row);

            $row['AddressFormatted'] = CommonHelper::FormatAddress($row);
            $row['WithVAT'] = $withVat;
            $ret[] = $row;
        }
        return $ret;
    }
}
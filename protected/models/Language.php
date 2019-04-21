<?php

class Language
{
    const ENGLISH = 1;
    const RUSSIAN = 2;
    const TRANSLIT = 3;
    const FINNISH = 4;
    const GERMAN = 5;
    const FRENCH = 6;
    const ESPANIOL = 7;
    const SWEDISH = 8;

    public static $list = null;

    static $languages = array(1 => 'en',
                              2 => 'ru',
                              3 => 'rut',
                              4 => 'fi',
                              5 => 'de',
                              6 => 'fr',
                              7 => 'es',
                              8 => 'se');

    public static function ConvertToString($value)
    {
        if(isset(self::$languages[$value])) return self::$languages[$value];
        throw new CException('NoLanguage ' .$value);
    }

    public static function ConvertToInt($value)
    {
        return array_search($value, self::$languages);
    }

    public static function GetTitleByID($langID)
    {
        $langs = self::GetItemsLanguageList();
        if(array_key_exists($langID, $langs)) return ProductHelper::GetTitle($langs[$langID]);
        return 'Lang_'.$langID;
    }

    public static function GetTitleByID_country($langID)
    {
        $langs = self::GetItemsLanguageList();
        if(array_key_exists($langID, $langs)) {
            if (!empty($langs[$langID]['country'])) {
                $country = unserialize($langs[$langID]['country']);
                if (!empty($country[Yii::app()->getLanguage()])) return $country[Yii::app()->getLanguage()];
            }
            return ProductHelper::GetTitle($langs[$langID]);
        }
        return 'Lang_'.$langID;
    }

    public static function GetTitleByID_predl($langID)
    {
        $langs = self::GetItemsLanguageList();
        if(array_key_exists($langID, $langs)) {
            if (!empty($langs[$langID]['predl'])) {
                $predl = unserialize($langs[$langID]['predl']);
                if (!empty($predl[Yii::app()->getLanguage()])) return $predl[Yii::app()->getLanguage()];
            }
            return ProductHelper::GetTitle($langs[$langID]);
        }
        return 'Lang_'.$langID;
    }

    public static function GetItemsLanguageList()
    {
        if(!empty(self::$list)) return self::$list;

        $data = Yii::app()->dbCache->get('ItemsLanguageList');
        if($data === false)
        {
            $sql = 'SELECT * FROM languages';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $data = array();
            foreach($rows as $row) $data[$row['id']] = $row;
            Yii::app()->dbCache->set('ItemsLanguageList', $data, Yii::app()->params['DbCacheTime']);
        }

        self::$list = $data;
        return $data;
    }
}
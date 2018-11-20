<?php

class RuslaniaUI extends CApplicationComponent
{
    private static $_cache = array();
    static $_count = 0;

    public function item($key, $params=array())
    {
//        Yii::beginProfile('UI_'.$key);

        $file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].Yii::app()->language.'/uiconst.class.php';
        if(isset(self::$_cache[$file])) $data =self::$_cache[$file];
        else
        {
            Yii::beginProfile('require');
            $data = require_once($file);
            self::$_cache[$file] = $data;
            Yii::endProfile('require');
        }
        $ret = '<span style="color:red; font-weight:bold">NOKEY {'.$key.'}</span>';

        if(array_key_exists($key, $data))
        {
            if(empty($params)) $ret = $data[$key];
            else $ret = sprintf($data[$key], $params);
        }

//        Yii::endProfile('UI_'.$key);
        //Yii::beginProfile('CounterUI = '.(self::$_count++));

        return $ret;
    }
}
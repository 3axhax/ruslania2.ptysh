<?php

class MyHTML extends CHtml
{
    public static function csrf($mode = 'html')
    {
        $token = Yii::app()->request->csrfTokenName;
        $val = Yii::app()->request->csrfToken;

        switch ($mode)
        {
            case 'html' :
                return $token . "=" . $val;
            case 'ajax' :
                return "'$token': '$val'";
        }
    }
}
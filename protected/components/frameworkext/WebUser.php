<?php

class WebUser extends CWebUser
{
    private static $model = null;

    public function GetPersonalDiscount()
    {
        $uid = Yii::app()->user->id;
        if(empty($uid)) return 0;

        $model = $this->GetModel();

        if(empty($model)) return 0;
        return $model['discount'];
    }

    public function GetModel()
    {
        $uid = Yii::app()->user->id;
        if(empty(self::$model))
        {
            $sql = 'SELECT * FROM users WHERE id=:uid LIMIT 1';
            self::$model = Yii::app()->db->createCommand($sql)->queryRow(true, array(':uid' => $uid));
        }
        return self::$model;
    }
}
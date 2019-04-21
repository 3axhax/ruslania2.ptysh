<?php

class WebUser extends CWebUser
{
    private static $model = null;

    public function GetPersonalDiscount()
    {
        $uid = $this->id;
        if(empty($uid)) return 0;

        $model = $this->GetModel();

        if(empty($model)) return 0;
        return $model['discount'];
    }

    public function GetModel()
    {
        $uid = $this->id;
        if(self::$model === null) {
            $sql = 'SELECT * FROM users WHERE id=:uid LIMIT 1';
            self::$model = Yii::app()->db->createCommand($sql)->queryRow(true, array(':uid' => $uid));
        }
        return self::$model;
    }

    function init() {
        parent::init();
        if ($this->id&&!$this->GetModel()) {
            $this->logout();
            header("Refresh: 0");
        }
    }
}
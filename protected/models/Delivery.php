<?php

class Delivery
{
    const TYPE_ECONOMY = 3;
    const TYPE_PRIORITY = 2;
    const TYPE_EXPRESS = 1;
    const TYPE_FREE = 4;

    public static function ToString($idx) {
        switch($idx) {
            case self::TYPE_ECONOMY : return Yii::app()->ui->item('MSG_DELIVERY_TYPE_3');
            case self::TYPE_EXPRESS : return Yii::app()->ui->item('MSG_DELIVERY_TYPE_1');
            case self::TYPE_PRIORITY: return Yii::app()->ui->item('MSG_DELIVERY_TYPE_2');
            case self::TYPE_FREE : return Yii::app()->ui->item('MSG_DELIVERY_TYPE_4');
        }
        return Yii::app()->ui->item('MSG_DELIVERY_TYPE_0');
    }
}
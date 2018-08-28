<?php

class Country extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'country_list';
    }

    public static function GetCountryList()
    {
        $sql = 'SELECT * FROM country_list ORDER BY title_en';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

    public static function GetStatesList($id = 225)
    {
        $sql = 'SELECT * FROM address_states_list WHERE country_id = '.$id.' ORDER BY title_long';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

    public static function GetCountryById($id)
    {
        $sql = 'SELECT * FROM country_list WHERE id=:id';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $id));
        return $row;
    }
}
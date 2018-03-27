<?php

class Picture
{
    const SMALL = 1;
    const BIG = 2;

    public static function Get($item, $type)
    {
        if(empty($item['image'])) return '/pic1/nophoto.gif';
        $ret = '/pictures/'.(($type == self::BIG) ? 'big' : 'small').'/'.$item['image'];

        return Yii::app()->params['PicDomain'].$ret;
    }
}
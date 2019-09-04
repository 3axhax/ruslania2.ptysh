<?php

class Picture
{
    const SMALL = 1;
    const BIG = 2;

    public static function Get($item, $type)
    {
        /*if(empty($item['image'])) */return '/pic1/nophoto.gif';
        $ret = '/pictures/'.(($type == self::BIG) ? 'big' : 'small').'/'.$item['image'];
//        $ret = '/pictures/small/'.$item['image'];
        return Yii::app()->params['PicDomain'].$ret;
    }

    static function srcLoad() {
        return '/new_img/flower.gif';// return '/new_img/source.gif';
    }
    static function srcNoPhoto() {
        return '/pic1/nophoto.gif';
    }

}
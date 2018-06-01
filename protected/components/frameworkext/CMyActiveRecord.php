<?php

class CMyActiveRecord extends CActiveRecord
{
    protected function isEmpty($value,$trim=false)
    {
        return $value===null || $value===array() || $value==='' || $trim && is_scalar($value) && trim($value)==='';
    }

}
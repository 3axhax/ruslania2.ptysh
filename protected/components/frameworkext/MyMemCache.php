<?php

class MyMemCache extends CMemCache
{
    protected function setValue($key,$value,$expire)
    {
        try
        {
            if($expire>0)
                $expire+=time();
            else
                $expire=0;

            return $this->useMemcached
                ? @$this->_cache->set($key,$value,$expire)
                : @$this->_cache->set($key,$value,0,$expire);
        }
        catch(Exception $ex)
        {
            Yii::log(print_r($ex, true), CLogger::LEVEL_ERROR, 'myerrors');
            return false;
        }
    }
}
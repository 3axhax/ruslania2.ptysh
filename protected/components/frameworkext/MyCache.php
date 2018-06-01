<?php

class MyCache extends CFileCache
{
    public $folder = 'cache';
    public function init()
    {
        parent::init();
        $this->cachePath=Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.$this->folder;
        if(!is_dir($this->cachePath)) mkdir($this->cachePath,0777,true);
    }
}
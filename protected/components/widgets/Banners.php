<?php

class Banners extends MyWidget
{
    public $entity;
    public function run()
    {
        $list = array();
        $b = new Banner;
        $list = $b->GetAllBanners();
        if($this->entity == 'index') $this->entity = 1;
        $lang = strtoupper(Yii::app()->language);
        if(isset($list[$this->entity][$lang]))
            $list = $list[$this->entity][$lang];
        else
            $list = array();

        $this->render('banners', array('list' => $list));;
    }
}
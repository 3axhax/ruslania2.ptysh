<?php

class MyWidget extends CWidget
{
    public function render($view,$data=null,$return=false)
    {
        $data['ui'] = Yii::app()->ui;
        return parent::render($view, $data, $return);
    }

}
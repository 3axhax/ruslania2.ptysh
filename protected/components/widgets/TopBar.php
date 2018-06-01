<?php

class TopBar extends CWidget
{
    public $breadcrumbs;
    public $message;
    public $mode = 'yah'; // you are here
    public $yah = '';
    public $entity;

    public function run()
    {
        if($this->mode == 'index')
        {
            $this->yah = '<b>'.Yii::app()->ui->item('MSG_PERSONAL_INDEX_GREETING').'</b><br/>'
                         .Yii::app()->ui->item('MSG_TODAY_WE_RECOMMEND');
            $this->message = Yii::app()->ui->item('MSG_MAIN_WELCOME_INTERNATIONAL_ORDERS');
        }
        else
        {
           $this->yah = ''
           .$this->yah = $this->widget('zii.widgets.CBreadcrumbs', array('links' => $this->breadcrumbs,
                                                                         'encodeLabel' => false,
                                                                         'tagName' => 'div'), true);
            if(!empty($this->entity))
            {
                $key = 'TEXT_BANNER_'.strtoupper(Entity::GetUrlKey($this->entity));
                $this->message = Yii::app()->ui->item($key);
            }
        }

        $rusDay = null; //DiscountManager::GetRuslaniaDaysInfo();

        $this->render('top_bar', array('ui' => Yii::app()->ui, 'rusDay' => $rusDay));
    }
}
<?php

class Banners extends MyWidget {
    public $entity;
    protected $_params = array();//здесь массив начальных значений

    function __set($name, $value) {
        if ($value !== null) $this->_params[$name] = $value;
    }

    public function run() {
        $ctrl = $this->getController()->id;
        $action = $this->getController()->action->id;
        if (($ctrl == 'entity')&&($action == 'list')) {
            $this->_viewList();
            return;
        }

        if (($ctrl == 'product')&&($action == 'view')) {
            $this->_viewDetail();
            return;
        }

        //TODO:: далее слишком много не нужных данных, когда нибудь переделать
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

    protected function _viewList() {
        $location = 'topInList';//когда будет готова база будет понятно какой сделать location
        if (!empty($this->_params['location'])) $location = $this->_params['location'];

        $this->render('banners_list', array('href' => '/', 'img'=>'http://ruslania2.ptysh.ru/pictures/banners/moomintroll.gif', 'title'=>''));
    }

    protected function _viewDetail() {
        $this->render('banners_detail', array('href' => '/', 'img'=>'http://ruslania2.ptysh.ru/pictures/banners/moomintroll.gif', 'title'=>''));
    }

}
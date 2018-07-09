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
        $type = 'image';
        if (!empty($this->_params['type'])) $type = $this->_params['type'];

        switch ($type) {
            case 'image':
                $this->render('banners_detail', array('href' => '/', 'img'=>'http://ruslania2.ptysh.ru/pictures/banners/moomintroll.gif', 'title'=>''));
                break;
            case 'slider':
                $items = [
                    ['entity'=>10, 'id'=>1305213],
                    ['entity'=>10, 'id'=>1311683],
                    ['entity'=>15, 'id'=>154655],
                    ['entity'=>60, 'id'=>837],
                    ['entity'=>40, 'id'=>919],
                ];
//		        require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/AllProducts.php';
//                $banners = new IteratorsAllProducts($items);
                $banners = $this->_getProducts($items);
                $this->render('banners_detail_slider', array('items' => $banners));
                break;
        }
    }

    protected function _getLables($items) {

    }

    protected function _getProducts($items) {
        $entityIds = array();
        $order = '';

        foreach ($items as $item) {
            if (!Entity::IsValid($item['entity'])) continue;

            if (!isset($entityIds[$item['entity']])) $entityIds[$item['entity']] = array();
            $entityIds[$item['entity']][] = $item['id'];
            $order .= ', "' . $item['entity'] . '_' . $item['id'] . '"';
        }
        $sql = array();
        $fields = array(
            'id'=>'id',
            'title'=>'title_ru',
            'image'=>'image',
            'vat'=>'vat',
            'discount'=>'discount',
            'unitweight_skip'=>'unitweight_skip',
            'brutto'=>'brutto',
            'sub_fin_year'=>'0 sub_fin_year',
            'sub_world_year'=>'0 sub_world_year',
            'code'=>'code',
            'subcode'=>'subcode',
            'series_id'=>'series_id',
            'publisher_id'=>'publisher_id',
            'year'=>'year',
        );
        foreach ($entityIds as $entity=>$ids) {
            $fields['entity'] = $entity . ' entity';
            if ($entity == Entity::PERIODIC) {
                if (isset($fields['sub_fin_year'])) $fields['sub_fin_year'] = 'sub_fin_year';
                if (isset($fields['sub_world_year'])) $fields['sub_world_year'] = 'sub_world_year';
            }
            if (isset($fields['year'])&&!Entity::checkEntityParam($entity, 'years')) $fields['year'] = '0 year';
            if (isset($fields['series_id'])&&!Entity::checkEntityParam($entity, 'series')) $fields['series_id'] = '0 series_id';
            if (isset($fields['publisher_id'])&&!Entity::checkEntityParam($entity, 'publisher')) $fields['publisher_id'] = '0 publisher_id';
            $sql[] = 'select ' . implode(',', $fields) . ' from ' . Entity::GetEntitiesList()[$entity]['site_table'] . ' where (id in (' . implode(',', $ids) . '))';
        }
        if (empty($sql)) return array();

        $sql = implode(' union ', $sql) . ' ';
        $sql .= 'order by field(concat(entity, "_", id)' . $order . ')';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

}
<?php

class Banners extends MyWidget {
    public $entity;
    protected $_params = array();//здесь массив начальных значений
    static private $_listBanners = null;

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
        $langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
        $lang = strtolower(Yii::app()->language);
        if (!in_array($lang, $langs)) $lang = 'en';
        if (self::$_listBanners === null) {
            $page = 1;
            if (!empty($this->_params['page'])) $page = $this->_params['page'];
           /* if ($page > 1) {
                $sql = ''.
                    'select count(*) '.
                    'from banners_entity t '.
                        'join all_banners tAB on (tAB.id = t.banner_id) '.
                    'where (t.entity_id = ' . (int) $this->_params['entity'] . ') '.
                        'and (t.img_' . $lang . ' = 1) '.
                '';
            }*/
            $sql = ''.
                'select t.id, tAB.id bannerId, tAB.url, tAB.path_entity, tAB.path_route, tAB.path_id '.
                'from banners_entity t '.
                    'join all_banners tAB on (tAB.id = t.banner_id) and (tAB.img_' . $lang . ' = 1)'.
                'where (t.entity_id = ' . (int) $this->entity . ') '.
                'order by t.position '.
            '';
            $banners = Yii::app()->db->createCommand($sql)->queryAll();
            self::$_listBanners = array();
            if (!empty($banners)) {
                if (count($banners) == 1) {
                    self::$_listBanners = array(0=>$banners[0], 1=>$banners[0]);
                }
                else {
                    $startBanner = $page%count($banners) + 1;
                    for ($i=0;$i<2;$i++) {
                        self::$_listBanners[$i] = $banners[($startBanner+$i)%count($banners)];
                    }
                }
            }
        }
        if (!empty(self::$_listBanners)) {
            $location = 'topInList';//когда будет готова база будет понятно какой сделать location
            if (!empty($this->_params['location'])) $location = $this->_params['location'];
            switch ($location) {
                case 'topInList':
                    $href = $this->_getBannerHref(self::$_listBanners[0]);
                    $this->render('banners_list', array('href' => $href, 'img'=>$this->_getBannerFilePath(self::$_listBanners[0]['bannerId'], $lang), 'title'=>''));
                    break;
                case 'centerInList':
                    $href = $this->_getBannerHref(self::$_listBanners[1]);
                    $this->render('banners_list', array('href' => $href, 'img'=>$this->_getBannerFilePath(self::$_listBanners[1]['bannerId'], $lang), 'title'=>''));
                    break;
            }

        }
    }

    private function _getBannerHref($banner) {
        if (!empty($banner['path_route'])) {
            $params = array( );
            if (!empty($banner['path_entity'])){
                $params['entity'] = $banner['path_entity'];
                if (!empty($banner['path_id'])) {
                    $idName = HrefTitles::get()->getIdName($params['entity'], $banner['path_route']);
                    if (!empty($idName)) $params[$idName] = $banner['path_id'];
                }
            }
            $href = Yii::app()->createUrl($banner['path_route'], $params);
        }
        else {
            $href = $banner['url'];
        }
        return $href;
    }

    private function _getBannerFilePath($id, $lang) {
        return 'http://ruslania2.ptysh.ru/pictures/banners/' . $id . '_banner_' . $lang . '.jpg';
    }


    protected function _viewDetail() {
        $type = 'image';
        if (!empty($this->_params['type'])) $type = $this->_params['type'];

        switch ($type) {
            case 'image':
                $langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
                $lang = strtolower(Yii::app()->language);
                if (!in_array($lang, $langs)) $lang = 'en';
                $sql = ''.
                    'select t.id, tAB.id bannerId, tAB.url, tAB.path_entity, tAB.path_route, tAB.path_id '.
                    'from banners_entity t '.
                        'join all_banners tAB on (tAB.id = t.banner_id) and (tAB.img_' . $lang . ' = 1) '.
                    'where (t.entity_id = ' . (int) $this->entity . ') '.
                    'order by rand() '.
                    'limit 1 '.
                '';
                $banner = Yii::app()->db->createCommand($sql)->queryRow();
                $href = $this->_getBannerHref($banner);
                $this->render('banners_detail', array('href' => $href, 'img'=>$this->_getBannerFilePath($banner['bannerId'], $lang), 'title'=>''));
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

	/** 10 - это entity, получить ид книг
	 * @param $counts int количество в результате
	 * @return array
	 */
    private function _get10Ids($counts) {
	    /**
	    Книги : по приоритету: только последние 2 года, например сейчас 2017-2018, та же подборка, если ничего нет,
	     * тогда та же серия,
	     * тот же автор или то же издательство,
	     */
		$ids = array();
		return $ids;
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
            HrefTitles::get()->getByIds($entity, 'product/view', $ids);
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
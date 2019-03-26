<?php

class Banners extends MyWidget {
    public $entity;
    protected $_params = array();//здесь массив начальных значений
    static private $_listBanners = null;
    static private $_mainBanners = null;

    function __set($name, $value) {
        if ($value !== null) $this->_params[$name] = $value;
    }

    public function run() {
        $ctrl = $this->getController()->id;
        $action = $this->getController()->action->id;
        if (($ctrl == 'entity')/*&&($action == 'list')*/) {
            $this->_viewList();
            return;
        }

        if (($ctrl == 'product')&&($action == 'view')) {
            $this->_viewDetail();
            return;
        }

        if (!empty($this->_params['type'])&& ($ctrl == 'site')&&($action == 'index')) {
            //type - big и small
            $this->_viewMain();
            return;
        }

//        //TODO:: далее слишком много не нужных данных, когда нибудь переделать
//        $b = new Banner;
//        $list = $b->GetAllBanners();
//        if($this->entity == 'index') $this->entity = 1;
//        $lang = strtoupper(Yii::app()->language);
//        if(isset($list[$this->entity][$lang]))
//            $list = $list[$this->entity][$lang];
//        else
//            $list = array();
//
//        $this->render('banners', array('list' => $list));;
    }

    private function _getListBanners($lang) {
        if (self::$_listBanners === null) {
            $page = 1;
            if (!empty($this->_params['page'])) $page = $this->_params['page'];
            $sql = ''.
                'select t.id, tAB.id bannerId, tAB.url, tAB.path_entity, tAB.path_route, tAB.path_id, tAB.path_params '.
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
        return self::$_listBanners;
    }

    private function _getMainBanners($lang) {
        if (self::$_mainBanners === null) {
            $sql = ''.
                'select t.location, tAB.id bannerId, tAB.url, tAB.path_entity, tAB.path_route, tAB.path_id, tAB.path_params '.
                'from banners_main t '.
                    'join all_banners tAB on (tAB.id = t.banner_id) and (tAB.img_' . $lang . ' = 1)'.
                'order by t.position, t.id desc '.
            '';
            $banners = Yii::app()->db->createCommand($sql)->queryAll();
            self::$_mainBanners = array();
            foreach ($banners as $banner) {
                if (empty(self::$_mainBanners[$banner['location']])) {
                    $location = $banner['location'];
                    unset($banner['location']);
                    self::$_mainBanners[$location] = $banner;
                }
            }
        }
        return self::$_mainBanners;
    }

    /** есть или нет большой баннер на главной (location = 3)
     * @return bool
     */
    static function checkBigBanner() {
        $langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
        $lang = strtolower(Yii::app()->language);
        if (!in_array($lang, $langs)) $lang = 'en';
        $banners = self::_getMainBanners($lang);
        return isset($banners[3]);
    }

    protected function _viewMain() {
        $langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
        $lang = strtolower(Yii::app()->language);
        if (!in_array($lang, $langs)) $lang = 'en';
        $banners = $this->_getMainBanners($lang);
        switch ($this->_params['type']) {
            case 'big':
                if (!empty($banners[3])) {
                    $href = $this->_getBannerHref($banners[3]);
                    $this->render('banners_main_big', array('href' => $href, 'img'=>$this->_getBannerFilePath($banners[3]['bannerId'], $lang), 'title'=>''));
                }
                break;
            case 'small':
                $offerDay = DiscountManager::getOfferDay();

                if (!empty($offerDay)) {
                    $entity = $offerDay['entity_id'];
                    $extraTxt = $offerDay['extra_txt'];
                    $p = new Product();
                    $offerDay = $p->GetProduct($entity, $offerDay['item_id']);
                    $offerDay['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $offerDay);
                    $offerDay['priceData']['unit'] = '';
                    if ($entity == Entity::PERIODIC) {
                        $issues = Periodic::getCountIssues($offerDay['issues_year']);
                        if (!empty($issues['show3Months'])) {
                            $offerDay['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                            $offerDay['priceData'][DiscountManager::BRUTTO] = $offerDay['priceData'][DiscountManager::BRUTTO_FIN]/4;
                            $offerDay['priceData'][DiscountManager::WITH_VAT] = $offerDay['priceData'][DiscountManager::WITH_VAT_FIN]/4;
                            $offerDay['priceData'][DiscountManager::WITHOUT_VAT] = $offerDay['priceData'][DiscountManager::WITHOUT_VAT_FIN]/4;
                        }
                        elseif (!empty($issues['show6Months'])) {
                            $offerDay['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                            $offerDay['priceData'][DiscountManager::BRUTTO] = $offerDay['priceData'][DiscountManager::BRUTTO_FIN]/2;
                            $offerDay['priceData'][DiscountManager::WITH_VAT] = $offerDay['priceData'][DiscountManager::WITH_VAT_FIN]/2;
                            $offerDay['priceData'][DiscountManager::WITHOUT_VAT] = $offerDay['priceData'][DiscountManager::WITHOUT_VAT_FIN]/2;
                        }
                        else {
                            $offerDay['priceData'][DiscountManager::BRUTTO] = $offerDay['priceData'][DiscountManager::BRUTTO_FIN];
                            $offerDay['priceData'][DiscountManager::WITH_VAT] = $offerDay['priceData'][DiscountManager::WITH_VAT_FIN];
                            $offerDay['priceData'][DiscountManager::WITHOUT_VAT] = $offerDay['priceData'][DiscountManager::WITHOUT_VAT_FIN];
                            $offerDay['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                        }
                    }
                    if (empty($extraTxt[Yii::app()->language])) $offerDay['extraTxt'] = '';
                    else $offerDay['extraTxt'] = $extraTxt[Yii::app()->language];
                }

                $leftBanner = $rightBanner = array();
                if (!empty($banners[1])) {
                    $leftBanner = array(
                        'href' => $this->_getBannerHref($banners[1]),
                        'img'=>$this->_getBannerFilePath($banners[1]['bannerId'], $lang),
                        'title'=>'',
                    );
                }
                if (!empty($banners[2])) {
                    $rightBanner = array(
                        'href' => $this->_getBannerHref($banners[2]),
                        'img'=>$this->_getBannerFilePath($banners[2]['bannerId'], $lang),
                        'title'=>'',
                    );
                }
                elseif (!empty($leftBanner)&&!empty($offerDay)) {
                    $rightBanner = $leftBanner;
                    $leftBanner = array();
                }
                if (!empty($offerDay)||!empty($rightBanner)||!empty($leftBanner)) {
                    $this->render('banners_main_small', array('offerDay' => $offerDay, 'leftBanner'=>$leftBanner, 'rightBanner'=>$rightBanner));
                }
                break;
        }
    }

    protected function _viewList() {
        $langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
        $lang = strtolower(Yii::app()->language);
        if (!in_array($lang, $langs)) $lang = 'en';
        $listBanners = $this->_getListBanners($lang);
        if (!empty($listBanners)) {
            $location = 'topInList';//когда будет готова база будет понятно какой сделать location
            if (!empty($this->_params['location'])) $location = $this->_params['location'];
            switch ($location) {
                case 'topInList':
                    $href = $this->_getBannerHref($listBanners[0]);
                    $this->render('banners_list', array('href' => $href, 'img'=>$this->_getBannerFilePath($listBanners[0]['bannerId'], $lang), 'title'=>''));
                    break;
                case 'centerInList':
                    $href = $this->_getBannerHref($listBanners[1]);
                    $this->render('banners_list', array('href' => $href, 'img'=>$this->_getBannerFilePath($listBanners[1]['bannerId'], $lang), 'title'=>''));
                    break;
            }

        }
    }

    private function _getBannerHref($banner) {
        if (!empty($banner['path_route'])) {
            $params = array();
            if (!empty($banner['path_entity'])){
                $params['entity'] = $banner['path_entity'];
                if (!empty($banner['path_id'])) {
                    $idName = HrefTitles::get()->getIdName($params['entity'], $banner['path_route']);
                    if (!empty($idName)) $params[$idName] = $banner['path_id'];
                }
            }
            elseif (!empty($banner['path_id'])) {
                $idName = HrefTitles::get()->getIdName(0, $banner['path_route']);
                if (!empty($idName)) $params[$idName] = $banner['path_id'];
            }
            elseif (!empty($banner['path_params'])) {
                $params = unserialize($banner['path_params']);
            }
            $href = Yii::app()->createUrl($banner['path_route'], $params);
        }
        else {
            $href = $banner['url'];
        }
        return $href;
    }

    private function _getBannerFilePath($id, $lang) {
        return 'https://beta.ruslania.com/pictures/banners/' . $id . '_banner_' . $lang . '.jpg';
    }

    protected function _viewDetail() {
        $type = 'image';
        if (!empty($this->_params['type'])) $type = $this->_params['type'];
		
		//var_dump();
		
        switch ($type) {
            case 'image':
                $langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
                $lang = strtolower(Yii::app()->language);
                if (!in_array($lang, $langs)) $lang = 'en';
                $sql = ''.
                    'select t.id, tAB.id bannerId, tAB.url, tAB.path_entity, tAB.path_route, tAB.path_id, tAB.path_params '.
                    'from banners_entity t '.
                        'join all_banners tAB on (tAB.id = t.banner_id) and (tAB.img_' . $lang . ' = 1) '.
                    'where (t.entity_id = ' . (int) $this->entity . ') '.
                    'order by rand() '.
                    'limit 1 '.
                '';
                $banner = Yii::app()->db->createCommand($sql)->queryRow();
                if (!empty($banner)) {
                    $href = $this->_getBannerHref($banner);
                    $this->render('banners_detail', array('href' => $href, 'img'=>$this->_getBannerFilePath($banner['bannerId'], $lang), 'title'=>''));
                }
                break;
            case 'slider':
                $items = array();
                switch ((int)$this->_params['item']['entity']) {
                    case 10:
                        foreach ($this->_get10Ids(10) as $id) $items[] = array('entity'=>10, 'id'=>$id);
                        break;
                    case 15:
                        foreach ($this->_get15Ids(10) as $id) $items[] = array('entity'=>15, 'id'=>$id);
                        break;
                    case 22:
                        foreach ($this->_get22Ids(10) as $id) $items[] = array('entity'=>22, 'id'=>$id);
                        break;
                    case 30:
                        foreach ($this->_get30Ids(10) as $id) $items[] = array('entity'=>30, 'id'=>$id);
                        break;
                    case 50:
                        foreach ($this->_get50Ids(10) as $id) $items[] = array('entity'=>50, 'id'=>$id);
                        break;
                    case 40:
                        foreach ($this->_get40Ids(10) as $id) $items[] = array('entity'=>40, 'id'=>$id);
                        break;
                    case 60:
                        foreach ($this->_get60Ids(10) as $id) $items[] = array('entity'=>60, 'id'=>$id);
                        break;
                    case 24:
                        foreach ($this->_get24Ids(10) as $id) $items[] = array('entity'=>24, 'id'=>$id);
                        break;
                }
                $banners = $this->_getProducts($items);
                foreach ($banners as $i=>$item) {

                    $item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
                    $item['priceData']['unit'] = '';
                    if ($item['entity'] == Entity::PERIODIC) {
                        $issues = Periodic::getCountIssues($item['issues_year']);
                        if (!empty($issues['show3Months'])) {
                            $item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                            $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO_FIN]/4;
                            $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT_FIN]/4;
                            $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT_FIN]/4;
                        }
                        elseif (!empty($issues['show6Months'])) {
                            $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                            $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO_FIN]/2;
                            $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT_FIN]/2;
                            $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT_FIN]/2;
                        }
                        else {
                            $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO_FIN];
                            $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT_FIN];
                            $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT_FIN];
                            $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                        }
                    }
                    $banners[$i] = $item;
                }
                $this->render('banners_detail_slider', array('items' => $banners, 'sid'=>$this->_params['sid'], 'uid'=>$this->_params['uid']));
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
        $titleLang = Yii::app()->getLanguage();
        if (!in_array($titleLang, HrefTitles::get()->getLangs(10, 'product/view'))) $titleLang = 'en';
        $fields = array(
            'id'=>'id',
            'title'=>'title_' . $titleLang,
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
                unset($fields['unitweight_skip'], $fields['brutto']);
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


    /** 10 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get10Ids($counts) {
        /**
        Книги : по приоритету: только последние 2 года, например сейчас 2017-2018, та же подборка, если ничего нет,
         * тогда та же серия, тот же автор или то же издательство,
         * сортировка в случайном порядке
         */

        $ids = array();
        if (!empty($this->_params['item']['Offers'])) {
            //в подборке
            $offerIds = array();
            foreach ($this->_params['item']['Offers'] as $offer) $offerIds[] = $offer['id'];
            if (!empty($offerIds)) {
                $sql = ''.
                    'select t.id ' .
                    'from `books_catalog` as t '.
                        'join (select item_id id from offer_items where (offer_id in (' . implode(',',$offerIds) . '))) tOf using (id) '.
                    'where (t.id <> ' . (int) $this->_params['item']['id'] . ') '.
                        'and (t.year between ' . (date('Y')-1) . ' and ' . date('Y') . ') '.
                        'and (t.avail_for_order = 1) '.
                    'order by rand() '.
                    'limit ' . $counts . ' '.
                '';
                $ids = Yii::app()->db->createCommand($sql)->queryColumn();
                $counts = $counts - count($ids);
            }
        }
        if ($counts > 0) {
            $exclude = $ids;
            $exclude[] = (int) $this->_params['item']['id'];
            $beforeIds = array(
                'authors' => array(),
                'serie' => array(),
                'publisher' => array(),
            );
            if (!empty($this->_params['item']['Authors'])) {
                $authors = array();
                foreach ($this->_params['item']['Authors'] as $author) $authors[] = $author['id'];
                if (!empty($authors)) {
                    $sql = ''.
                        'select t.id ' .
                        'from `books_catalog` as t '.
                            'join books_authors tA on (tA.book_id = t.id) and (tA.author_id in (' . implode(',',$authors) . ')) '.
                        'where (t.avail_for_order = 1) '.
                            'and (t.id not in (' . implode(',', $exclude) . ')) '.
                        'group by t.id '.
                        'order by rand() '.
                        'limit ' . $counts . ' '.
                    '';
                    $beforeIds['authors'] = Yii::app()->db->createCommand($sql)->queryColumn();
                    $exclude = array_merge($exclude, $beforeIds['authors']);
                }
            }
            if (!empty($this->_params['item']['series_id'])) {
                $sql = ''.
                    'select t.id ' .
                    'from `books_catalog` as t '.
                    'where (t.avail_for_order = 1) '.
                        'and (t.series_id = ' . (int) $this->_params['item']['series_id'] . ')'.
                        'and (t.id not in (' . implode(',', $exclude) . ')) '.
                    'order by rand() '.
                    'limit ' . $counts . ' '.
                '';
                $beforeIds['serie'] = Yii::app()->db->createCommand($sql)->queryColumn();
                $exclude = array_merge($exclude, $beforeIds['serie']);
            }
            if (!empty($this->_params['item']['publisher_id'])) {
                $sql = ''.
                    'select t.id ' .
                    'from `books_catalog` as t '.
                    'where (t.avail_for_order = 1) '.
                        'and (t.publisher_id = ' . (int) $this->_params['item']['publisher_id'] . ')'.
                        'and (t.id not in (' . implode(',', $exclude) . ')) '.
                    'order by rand() '.
                    'limit ' . $counts . ' '.
                '';
                $beforeIds['publisher'] = Yii::app()->db->createCommand($sql)->queryColumn();
            }
            $beforeIds = array_merge($beforeIds['authors'], $beforeIds['serie'], $beforeIds['publisher']);
            shuffle($beforeIds);
            $ids = array_merge($ids, array_slice($beforeIds, 0, $counts));
        }
        return $ids;
    }

    /** 15 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get15Ids($counts) {
        /**
        Ноты : тот же автор, если ничего нет, тогда та же категория
         * сортировка в случайном порядке
         */
        $ids = array();
        $exclude = $ids;
        $exclude[] = (int) $this->_params['item']['id'];
        if (!empty($this->_params['item']['Authors'])) {
            $authors = array();
            foreach ($this->_params['item']['Authors'] as $author) $authors[] = $author['id'];
            if (!empty($authors)) {
                $sql = ''.
                    'select t.id ' .
                    'from `musicsheets_catalog` as t '.
                        'join musicsheets_authors tA on (tA.musicsheet_id = t.id) and (tA.author_id in (' . implode(',',$authors) . ')) '.
                    'where (t.avail_for_order = 1) '.
                        'and (t.id not in (' . implode(',', $exclude) . ')) '.
                    'group by t.id '.
                    'order by rand() '.
                    'limit ' . $counts . ' '.
                '';
                $ids = Yii::app()->db->createCommand($sql)->queryColumn();
                $counts = $counts - count($ids);
                $exclude = array_merge($exclude, $ids);
            }
        }
        if ($counts > 0) {
            $sql = ''.
                'select t.id ' .
                'from `musicsheets_catalog` as t '.
                'where (t.avail_for_order = 1) '.
                    'and ((t.code in (' . (int) $this->_params['item']['code'] . ', ' . (int) $this->_params['item']['subcode'] . ')) or (t.subcode in (' . (int) $this->_params['item']['code'] . ', ' . (int) $this->_params['item']['subcode'] . ')))'.
                    'and (t.id not in (' . implode(',', $exclude) . ')) '.
                'order by rand() '.
                'limit ' . $counts . ' '.
            '';
            $ids = array_merge($ids, Yii::app()->db->createCommand($sql)->queryColumn());
        }
        return $ids;
    }

    /** 22 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get22Ids($counts) {
        /**
        Музыка:по приоритету: та же подборка,
         * тот же исполнитель,
         * та же категория,
         * тот же формат
         * сортировка в случайном порядке
         *
         * задание https://dfaktor.bitrix24.ru/company/personal/user/836/tasks/task/view/6440/?MID=23520&IFRAME=Y&IFRAME_TYPE=SIDE_SLIDER#com23520
         * Музыка:по приоритету: тот же исполнитель, та же категория, та же подборка, тот же формат
         */

        $ids = array();
        $exclude = $ids;
        $exclude[] = (int) $this->_params['item']['id'];
        if ($counts > 0) {
            //исполнитель
            if (!empty($this->_params['item']['Performers'])) {
                $performers = array();
                foreach ($this->_params['item']['Performers'] as $author) $performers[] = $author['id'];
                if (!empty($performers)) {
                    $sql = ''.
                        'select t.id ' .
                        'from `music_catalog` as t '.
                            'join music_performers tA on (tA.music_id = t.id) and (tA.person_id in (' . implode(',',$performers) . ')) '.
                        'where (t.avail_for_order = 1) '.
                            'and (t.id not in (' . implode(',', $exclude) . ')) '.
                        'group by t.id '.
                        'order by rand() '.
                        'limit ' . $counts . ' '.
                    '';
                    $result = Yii::app()->db->createCommand($sql)->queryColumn();
                    $ids = array_merge($ids, $result);
                    $exclude = array_merge($exclude, $result);
                    $counts = $counts - count($result);
                }
            }
        }
        if ($counts > 0) {
            //категория
            $sql = ''.
                'select t.id ' .
                'from `music_catalog` as t '.
                'where (t.avail_for_order = 1) '.
                    'and ((t.code in (' . (int) $this->_params['item']['code'] . ', ' . (int) $this->_params['item']['subcode'] . ')) or (t.subcode in (' . (int) $this->_params['item']['code'] . ', ' . (int) $this->_params['item']['subcode'] . ')))'.
                    'and (t.id not in (' . implode(',', $exclude) . ')) '.
                'order by rand() '.
                'limit ' . $counts . ' '.
            '';
            $result = Yii::app()->db->createCommand($sql)->queryColumn();
            $ids = array_merge($ids, $result);
            $exclude = array_merge($exclude, $result);
            $counts = $counts - count($result);
        }
        if (($counts > 0)&&!empty($this->_params['item']['Offers'])) {
            //в подборке
            $offerIds = array();
            foreach ($this->_params['item']['Offers'] as $offer) $offerIds[] = $offer['id'];
            if (!empty($offerIds)) {
                $sql = ''.
                    'select t.id ' .
                    'from `music_catalog` as t '.
                        'join (select item_id id from offer_items where (offer_id in (' . implode(',',$offerIds) . '))) tOf using (id) '.
                    'where (t.id not in (' . implode(',', $exclude) . '))'.
                        'and (t.avail_for_order = 1) '.
                    'group by t.id '.
                    'order by rand() '.
                    'limit ' . $counts . ' '.
                '';
                $result = Yii::app()->db->createCommand($sql)->queryColumn();
                $ids = array_merge($ids, $result);
                $exclude = array_merge($exclude, $result);
                $counts = $counts - count($result);
            }
        }
        if (($counts > 0)&&!empty($this->_params['item']['media_id'])) {
            //формат
            $sql = ''.
                'select t.id ' .
                'from `music_catalog` as t '.
                'where (t.avail_for_order = 1) '.
                    'and (t.media_id = ' . (int) $this->_params['item']['media_id'] . ')'.
                'and (t.id not in (' . implode(',', $exclude) . ')) '.
                'order by rand() '.
                'limit ' . $counts . ' '.
            '';
            $result = Yii::app()->db->createCommand($sql)->queryColumn();
            $ids = array_merge($ids, $result);
        }
        return $ids;
    }

    /** 30 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get30Ids($counts) {
        /**
        Подписка: та же категория, тот же язык
         * сортировка в случайном порядке
         */
        $sql = 'select language_id from _support_languages_periodics where (id = ' . (int)$this->_params['item']['id'] . ') limit 1';
        $langId = (int) Yii::app()->db->createCommand($sql)->queryScalar();
        $condition = array(
            'category_id'=>'(category_id in (' . (int)$this->_params['item']['code'] . ', ' . (int)$this->_params['item']['subcode'] . '))',
        );
        if ($langId > 0) $condition['lang'] = '(t.language_id = ' . $langId . ')';
        $sql = ''.
            'select t.id ' .
            'from `_support_languages_periodics` as t '.
            'where ' . implode(' and ', $condition) . ' '.
            'order by rand() '.
            'limit ' . $counts . ' '.
        '';
        $ids = Yii::app()->db->createCommand($sql)->queryColumn();
        return $ids;
    }

    /** 50 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get50Ids($counts) {
        /**
        Сувениры: та же тематика
         * сортировка в случайном порядке
         */
        $sql = 'select language_id from _support_languages_printed where (id = ' . (int)$this->_params['item']['id'] . ')';
        $langIds = Yii::app()->db->createCommand($sql)->queryColumn();
        if (empty($langIds)) {
            $condition = array(
                'category_id'=>'(category_id in (' . (int)$this->_params['item']['code'] . ', ' . (int)$this->_params['item']['subcode'] . '))',
            );
        }
        else {
            $condition = array(
                'lang'=>'(t.language_id in (' . implode(',', $langIds) . '))',
            );
        }
        $sql = ''.
            'select t.id ' .
            'from `_support_languages_printed` as t '.
            'where ' . implode(' and ', $condition) . ' '.
            'order by rand() '.
            'limit ' . $counts . ' '.
        '';
        $ids = Yii::app()->db->createCommand($sql)->queryColumn();
        return $ids;
    }

    /** 40 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get40Ids($counts) {
        /**
        Видео:по приоритету: того же режиссера,
         * того же актера,
         * той же категории
         * сортировка в случайном порядке
         */
        $ids = array();
        $exclude = $ids;
        $exclude[] = (int) $this->_params['item']['id'];
        if ($counts > 0) {
            //режисер
            if (!empty($this->_params['item']['Directors'])) {
                $directors = array();
                foreach ($this->_params['item']['Directors'] as $author) $directors[] = $author['id'];
                if (!empty($directors)) {
                    $sql = ''.
                        'select t.id ' .
                        'from `video_catalog` as t '.
                            'join video_directors tA on (tA.video_id = t.id) and (tA.person_id in (' . implode(',',$directors) . ')) '.
                        'where (t.avail_for_order = 1) '.
                            'and (t.id not in (' . implode(',', $exclude) . ')) '.
                        'group by t.id '.
                        'order by rand() '.
                        'limit ' . $counts . ' '.
                    '';
                    $result = Yii::app()->db->createCommand($sql)->queryColumn();
                    $ids = array_merge($ids, $result);
                    $exclude = array_merge($exclude, $result);
                    $counts = $counts - count($result);
                }
            }
        }
        if ($counts > 0) {
            //актер
            if (!empty($this->_params['item']['Actors'])) {
                $actors = array();
                foreach ($this->_params['item']['Actors'] as $author) $actors[] = $author['id'];
                if (!empty($directors)) {
                    $sql = ''.
                        'select t.id ' .
                        'from `video_catalog` as t '.
                            'join video_actors tA on (tA.video_id = t.id) and (tA.person_id in (' . implode(',',$actors) . ')) '.
                        'where (t.avail_for_order = 1) '.
                            'and (t.id not in (' . implode(',', $exclude) . ')) '.
                        'group by t.id '.
                        'order by rand() '.
                        'limit ' . $counts . ' '.
                    '';
                    $result = Yii::app()->db->createCommand($sql)->queryColumn();
                    $ids = array_merge($ids, $result);
                    $exclude = array_merge($exclude, $result);
                    $counts = $counts - count($result);
                }
            }
        }
        if ($counts > 0) {
            //категория
            $sql = ''.
                'select t.id ' .
                'from `video_catalog` as t '.
                'where (t.avail_for_order = 1) '.
                    'and ((t.code in (' . (int) $this->_params['item']['code'] . ', ' . (int) $this->_params['item']['subcode'] . ')) or (t.subcode in (' . (int) $this->_params['item']['code'] . ', ' . (int) $this->_params['item']['subcode'] . ')))'.
                    'and (t.id not in (' . implode(',', $exclude) . ')) '.
                'order by rand() '.
                'limit ' . $counts . ' '.
                '';
            $result = Yii::app()->db->createCommand($sql)->queryColumn();
            $ids = array_merge($ids, $result);
        }
        return $ids;
    }

    /** 60 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get60Ids($counts) {
        /**
        Карты: того же издателя
         * сортировка в случайном порядке
         */
        $ids = array();
        if (!empty($this->_params['item']['publisher_id'])) {
            $exclude = $ids;
            $exclude[] = (int) $this->_params['item']['id'];
            $sql = ''.
                'select t.id ' .
                'from `maps_catalog` as t '.
                'where (t.avail_for_order = 1) '.
                    'and (t.publisher_id = ' . (int) $this->_params['item']['publisher_id'] . ')'.
                    'and (t.id not in (' . implode(',', $exclude) . ')) '.
                'order by rand() '.
                'limit ' . $counts . ' '.
            '';
            $ids = Yii::app()->db->createCommand($sql)->queryColumn();
        }
        return $ids;
    }

    /** 24 - это entity, получить ид книг
     * @param $counts int количество в результате
     * @return array
     */
    private function _get24Ids($counts) {
        /**
        Мультимедиа: по приоритету: та же подборка,
         * тот же формат,
         * та же категория
         * сортировка в случайном порядке
         */
        $ids = array();
        $exclude = array();
        $exclude[] = (int) $this->_params['item']['id'];
        if (!empty($this->_params['item']['Offers'])) {
            //в подборке
            $offerIds = array();
            foreach ($this->_params['item']['Offers'] as $offer) $offerIds[] = $offer['id'];
            if (!empty($offerIds)) {
                $sql = ''.
                    'select t.id ' .
                    'from `soft_catalog` as t '.
                        'join (select item_id id from offer_items where (offer_id in (' . implode(',',$offerIds) . '))) tOf using (id) '.
                    'where (t.id <> ' . (int) $this->_params['item']['id'] . ') '.
                        'and (t.avail_for_order = 1) '.
                    'order by rand() '.
                    'limit ' . $counts . ' '.
                '';
                $result = Yii::app()->db->createCommand($sql)->queryColumn();
                $ids = array_merge($ids, $result);
                $exclude = array_merge($exclude, $result);
            }
        }
        if (($counts > 0)&&!empty($this->_params['item']['media_id'])) {
            //формат
            $sql = ''.
                'select t.id ' .
                'from `soft_catalog` as t '.
                'where (t.avail_for_order = 1) '.
                    'and (t.media_id = ' . (int) $this->_params['item']['media_id'] . ')'.
                    'and (t.id not in (' . implode(',', $exclude) . ')) '.
                'order by rand() '.
                'limit ' . $counts . ' '.
            '';
            $result = Yii::app()->db->createCommand($sql)->queryColumn();
            $ids = array_merge($ids, $result);
        }
        return $ids;
    }
}
<?php

class EntityController extends MyController {

    public function actionFilter() {
        $searcher = SearchHelper::Create();

        if (!empty($_GET['author'])) {
            $author = $_GET['author'];
            if (is_array($author))
                $searcher->SetFilter('author', $author);
            else
                $searcher->SetFilter('author', array(intVal($author)));
        }
        if (!empty($_GET['publisher_id'])) {
            $publisher = $_GET['publisher_id'];
            if (is_array($publisher))
                $searcher->SetFilter('publisher_id', $publisher);
            else
                $searcher->SetFilter('publisher_id', array(intVal($publisher)));
        }

        $searcher->SetLimits(0, 50);
        $res = $searcher->query('', 'products');

        $items = array();
        $paginatorInfo = new CPagination(0);

        if ($res['total_found'] > 0) {
            $paginatorInfo = new CPagination($res['total_found']);
            $this->_maxPages = ceil($res['total_found']/$paginatorInfo::DEFAULT_PAGE_SIZE);
            $matches = $res['matches'];
            $ids = array();
            foreach ($matches as $match) {
                $attrs = $match['attrs'];
                $ids[$attrs['entity']][] = $attrs['real_id'];
            }

            $p = new Product();
            foreach ($ids as $entity => $ids2) {
                $list = $p->GetProductsV2($entity, $ids2);
                foreach ($list as $idx => $item)
                    $list[$idx]['is_product'] = true;
                if (!empty($list))
                    $items = array_merge($items, $list);
            }
        }

        $this->breadcrumbs[] = 'Filter';
        $this->render('/site/search', array('products' => $items, 'paginatorInfo' => $paginatorInfo));
    }

    private function AppendCartInfo($items, $entity, $uid, $sid) {
        $c = new Cart;
        $cart = $c->GetCart($uid, $sid);
        foreach ($items as $idx => $item) {
            foreach ($cart as $cartItem) {
                if ($cartItem['entity'] == $entity && $cartItem['id'] == $item['id']) {
                    $items[$idx]['AlreadyInCart'] = $cartItem['quantity'];
                }
            }
        }
        return $items;
    }

    public function actionList($entity, $cid = 0, $sort = 0, $avail = true) {

		$entity = Entity::ParseFromString($entity);
        if ($entity === false) $entity = Entity::BOOKS;
        $filters = FilterHelper::getEnableFilters($entity, $cid);

        $category = new Category();

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['lang'] = Yii::app()->getRequest()->getParam('lang');
        if (empty($dataForPath['lang'])) unset($dataForPath['lang']);

        $langTitles = array();
        if (!empty($cid)) {
            $dataForPath['cid'] = $cid;
            $cat = $category->GetByIds($entity, array($cid));
            if (empty($cat)) throw new CHttpException(404);

            $cat = array_shift($cat);
            $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($cat));
            foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
                if ($_lang !== 'rut') {
                    if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                    else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($cat, 'title', 0, $_lang));
                }
            }

        }
        $this->_checkUrl($dataForPath, $langTitles);
        $lang = Yii::app()->getRequest()->getParam('lang');

       /* if (isset($_GET['sel']) && $_GET['lang'] != '') {
			$lang = $_GET['lang'];
			if (!Product::is_lang($_GET['lang'], $cid,$entity)) {
				$lang = '';
			}

		} elseif (isset(Yii::app()->getRequest()->cookies['langsel']->value)) {
			
			$lang = Yii::app()->getRequest()->cookies['langsel']->value;
			
			if (!Product::is_lang(Yii::app()->getRequest()->cookies['langsel']->value, $cid,$entity)) {
				$lang = '';
			}
			
		}*/



        //получаем языки категории
        $langs = $category->getFilterLangs($entity, $cid);

        $catList = $category->GetCategoryList($entity, $cid);

        $path = $category->GetCategoryPath($entity, $cid);

        $title = Entity::GetTitle($entity);
        if ($cid == 0)
            array_push($this->breadcrumbs, $title);
        else
            $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));

        $cnt = count($path);
        $ids = array();
        $selectedCategory = null;

        for ($i = 0; $i < $cnt; $i++) {
            $p = $path[$i];
            $pTitle = ProductHelper::GetTitle($p);
            if ($i == $cnt - 1) {
                array_push($this->breadcrumbs, $pTitle);
                $selectedCategory = $p;
            } else {
                $this->breadcrumbs[$pTitle] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $p['id'], 'title'=>ProductHelper::ToAscii($pTitle)));
            }
            $ids[] = $p['id'];
        }
        // Получить статик-файл инфы категории
        $categoryInfo = null;
        if (!empty($selectedCategory) && !empty($selectedCategory['description_file_' . Yii::app()->language])) {
            $file = $selectedCategory['description_file_' . Yii::app()->language];
            $path = Yii::getPathOfAlias('webroot') . '/templates-html/' . Entity::GetUrlKey($entity) . '-categories/' . $file;
            if (file_exists($path))
                $categoryInfo = file_get_contents($path);
        }
		$title_cat = '';
		if ($cid) {
			$title_cat = ProductHelper::GetTitle($selectedCategory);
		}

        if (isset($lang) && $lang != '') {
            FilterHelper::setOneFiltersData($entity, $cid,'lang_sel', $lang);
        }
		$data = FilterHelper::getFiltersData($entity, $cid);
        if (isset($data) && !empty($data)) {

            $cat = new Category();
            $totalItems = $cat->count_filter($entity, $cid, $data);
            $paginatorInfo = new CPagination($totalItems);
            $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
            $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);


			$items = $cat->result_filter($data, $lang, $paginatorInfo->currentPage);

			$filter_data = $data;
		}
		else {
            $avail = $this->GetAvail($avail);
            $totalItems = $category->GetTotalItems($entity, $cid, $avail);
            if ($cid > 0 && empty($path) && $totalItems == 0)
                throw new CHttpException(404);
            $sort = SortOptions::GetDefaultSort($sort);
            $paginatorInfo = new CPagination($totalItems);
            $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
            $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
            $items = $category->GetItems($entity, $cid, $paginatorInfo, $sort, Yii::app()->language, $avail, $lang);
        }

        $paginatorInfo->itemCount = $totalItems;

        // Добавляем к товарам инфу сколько уже содержится в корзине
        $items = $this->AppendCartInfo($items, $entity, $this->uid, $this->sid);

        if (isset(Yii::app()->session['last_e'])
            && (Yii::app()->session['last_e'] != '')
            && (Yii::app()->session['last_e'] != 'filter_e' . $entity . '_c_' . $cid)) {
            Yii::app()->session[Yii::app()->session['last_e']] = '';
            $fd = FiltersData::instance();
            $fd->deleteFiltersData();
        }

        Yii::app()->session['last_e'] = 'filter_e' . $entity . '_c_' . $cid;

        if ($entity == 10) {
            switch (Yii::app()->language) {
                case 'ru' : //$this->pageTitle = 'Интернет магазин русских книг Руслания в Финляндии с доставкой по всему миру';
                break;
                case 'en': 
                //$this->pageTitle = 'Ruslania.com bookstore in Finland - buy Russian books online in '.geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
                break;
            }
        }
        $this->render('list', array('categoryList' => $catList,
            'entity' => $entity, 'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'cid'=>$cid, 'filter_data' => $filter_data,
            'info' => $categoryInfo, 'filters' => $filters, 'langs'=>$langs,
            'title_cat'=>$title_cat, 'cat_id'=>$selectedCategory, 'total'=>$totalItems));
    }

    public function actionCategoryList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $c = new Category();
        $tree = $c->GetCategoriesTree($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('LIST_SOFT_CATTREE');

        $this->render('category_list', array('tree' => $tree, 'entity' => $entity));
    }

    public function actionPublisherList($entity, $char = null) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false) $entity = Entity::BOOKS;

        $a = new Publisher();

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        if ($char !== null) $dataForPath['char'] = $char;
        $this->_checkUrl($dataForPath);

        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en') $lang = 'en';

        $abc = $a->GetABC($lang, $entity);

//        if (empty($char) && !empty($abc)) $char = $abc[array_rand($abc)]['first_' . $lang];

        $list_count = 0;
        if (!empty($_GET['qa'])) {
            $lists = $a->GetPublishersBySearch($char, $lang, $entity);
            $list = $lists['rows'];
            $list_count = $lists['count'];
        }
        elseif (!empty($char)) {
            list($list, $list_count) = $a->GetPublishersByFirstChar($char, $lang, $entity);
//			$list_count = count($a->GetAuthorsByFirstCharCount($char, $lang, $entity)); //TODO:: так делать нельзя или CALC_FOUND_ROWS или count(*), но не так
        }
        else {
            list($list, $list_count) = $a->getPublisherList($entity, Yii::app()->language);;
        }

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('PROPERTYLIST_FOR_PUBLISHERS');

        $paginatorInfo = false;
        if ($list_count > count($list)) {
            $paginatorInfo = new CPagination($list_count);
            $paginatorInfo->setPageSize($a->getPerToPage());
            $paginatorInfo->route = 'publisherlist';
        }

        $this->render('authors_list', array(
            'entity' => $entity,
            'abc' => $abc,
            'paginatorInfo' => $paginatorInfo,
            'list' => $list,
            'idName' => 'pid',
            'lang' => $lang,
            'url' => 'entity/bypublisher',
            'liveAction'=>'publishers',
            'route'=>'entity/publisherlist',
        ));




/*        if ($entity == Entity::SHEETMUSIC) {
            $list = $p->GetAll($entity, $lang);
        } else {
            $abc = $p->GetABC($lang, $entity);
            if (empty($char) && !empty($abc))
                $char = $abc[array_rand($abc)]['first_' . $lang];
            $list = $p->GetPublishersByFirstChar($char, $lang, $entity);
        }


        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('PROPERTYLIST_FOR_PUBLISHERS');

        $this->render('publisher_list', array('entity' => $entity, 'abc' => $abc, 'list' => $list, 'lang' => $lang));*/
    }

    public function actionSeriesList($entity) {

        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $s = new Series;
        $list = $s->GetList($entity, Yii::app()->language);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_BOOKS_SERIES_PROPERTYLIST');

        $this->render('series_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionBySeries($entity, $sid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $s = new Series;
        $list = $s->GetByIds($entity, array($sid));

        if (empty($list))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['sid'] = $sid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($list[0]));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($list[0], 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $totalItems = $s->GetTotalItems($entity, $sid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $s->GetItems($entity, $sid, $paginatorInfo, $sort, Yii::app()->language, $avail);
        $items = $this->AppendCartInfo($items, $entity, $this->uid, $this->sid);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_BOOKS_SERIES_PROPERTYLIST')] = Yii::app()->createUrl('entity/serieslist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('SERIES_IS'), ProductHelper::GetTitle($list[0]));

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'seria', $sid);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array('entity' => $entity,
            'paginatorInfo' => $paginatorInfo,
            'items' => $items,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }

    public function actionByMedia($entity, $mid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $m = new Media;
        $media = $m->GetMedia($entity, $mid);
        if (empty($media))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['mid'] = $mid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($media));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($media, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $totalItems = $m->GetTotalItems($entity, $mid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($m->GetItems($entity, $mid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('Media')] = Yii::app()->createUrl('entity/medialist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_MEDIA_IS'), $media['title']);

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'binding_id', $mid);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array('entity' => $entity, 'paginatorInfo' => $paginatorInfo,
            'items' => $items,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }

    public function actionByPublisher($entity, $pid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $p = new Publisher();
        $publisher = $p->GetByID($entity, $pid);
        if (empty($publisher))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['pid'] = $pid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($publisher));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($publisher, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $totalItems = $p->GetTotalItems($entity, $pid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($p->GetItems($entity, $pid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('PROPERTYLIST_FOR_PUBLISHERS')] = Yii::app()->createUrl('entity/publisherlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('PUBLISHED_BY'), ProductHelper::GetTitle($publisher));

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'publisher', $pid);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array('entity' => $entity,
            'paginatorInfo' => $paginatorInfo,
            'items' => $items,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }

    public function actionAuthorList($entity, $char = null) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $a = new CommonAuthor();

        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en') $lang = 'en';

        $abc = $a->GetABC($lang, $entity);

//        if (empty($char) && !empty($abc)) $char = $abc[array_rand($abc)]['first_' . $lang];

		$list_count = 0;
        if (!empty($_GET['qa'])) {
            $lists = $a->GetAuthorsBySearch($char, $lang, $entity);
			$list = $lists['rows'];
			$list_count = $lists['count'];

		}
        elseif (!empty($char)) {
			list($list, $list_count) = $a->GetAuthorsByFirstChar($char, $lang, $entity);
//			$list_count = count($a->GetAuthorsByFirstCharCount($char, $lang, $entity)); //TODO:: так делать нельзя или CALC_FOUND_ROWS или count(*), но не так
		}
        else {
//            $list = array();
            $char = 'А';
            list($list, $list_count) = $a->GetAuthorsByFirstChar($char, $lang, $entity);
        }
		
        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('PROPERTYLIST_FOR_AUTHORS');

        $paginatorInfo = false;
        if ($list_count > count($list)) {
            $paginatorInfo = new CPagination($list_count);
            $paginatorInfo->setPageSize($a->getPerToPage());
            $paginatorInfo->route = 'AuthorList';
        }

        $this->render('authors_list', array('entity' => $entity, 'paginatorInfo' => $paginatorInfo, 'abc' => $abc, 'list' => $list, 'lang' => $lang,'chasdr'=>$char));
    }

    public function actionByAuthor($entity, $aid, $sort = null, $avail = true) {

        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $a = new CommonAuthor();
        $author = $a->GetById($aid);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['aid'] = $aid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($author));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($author, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $totalItems = $a->GetTotalItems($entity, $aid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $a->GetItems($entity, $aid, $paginatorInfo, $sort, Yii::app()->language, $avail);
        $items = $this->AppendCartInfo($items, $entity, $this->uid, $this->sid);

        // Получить статик-файл инфы категории
        $authorInfo = null;
        if (!empty($author) && !empty($author['description_file_' . Yii::app()->language])) {
            $file = $author['description_file_' . Yii::app()->language];
            /*$path = Yii::getPathOfAlias('webroot') . '/templates-html/' . Entity::GetUrlKey($entity) . '-authors/' . $file;
            if (file_exists($path))
                $authorInfo = file_get_contents($path);*/
        }

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('PROPERTYLIST_FOR_AUTHORS')] = Yii::app()->createUrl('entity/authorlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_WRITTEN_BY'), ProductHelper::GetTitle($author));

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'author', $aid);
        $filter_data = FilterHelper::getFiltersData($entity);

		$this->render('list', array('entity' => $entity,
            'paginatorInfo' => $paginatorInfo,
            'items' => $items,
            'presentation' => $file,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }

    public function actionPerformerList($entity, $char = null) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $p = new Performer;
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en') $lang = 'ru';

        if (!empty($_GET['qa'])) {
            list($list, $list_count) = $p->getPerformersBySearch($entity);
        }
        else {
            list($list, $list_count) = $p->GetPerformerList($entity, Yii::app()->language);
        }

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_AUDIO_AZ_PROPERTYLIST_PERFORMERS');

        $paginatorInfo = false;
        if ($list_count > count($list)) {
            $paginatorInfo = new CPagination($list_count);
            $paginatorInfo->setPageSize(10);
            $paginatorInfo->route = 'performerlist';
        }

        $this->render('authors_list', array(
            'entity' => $entity,
            'abc' => array(),
            'paginatorInfo' => $paginatorInfo,
            'list' => $list,
            'idName' => 'pid',
            'lang' => $lang,
            'url' => 'entity/byperformer',
            'liveAction'=>'performers'
        ));
    }

    public function actionActorList($entity) {

        $entity = Entity::ParseFromString($entity);
        if ($entity === false) $entity = Entity::VIDEO;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $p = new VideoActor();
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en') $lang = 'ru';

        if (!empty($_GET['qa'])) {
            list($list, $list_count) = $p->getActorsBySearch($entity);
        }
        else {
            list($list, $list_count) = $p->GetActorList($entity, Yii::app()->language);
        }

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_ACTORS');

        $paginatorInfo = false;
        if ($list_count > count($list)) {
            $paginatorInfo = new CPagination($list_count);
            $paginatorInfo->setPageSize($p->getPerToPage());
            $paginatorInfo->route = 'ActorList';
        }

        $this->render('authors_list', array(
            'entity' => $entity,
            'abc' => array(),
            'paginatorInfo' => $paginatorInfo,
            'list' => $list,
            'idName' => 'aid',
            'lang' => $lang,
            'url' => 'entity/byactor',
            'liveAction'=>'actors'
        ));
    }

    public function actionBindingsList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false) $entity = Entity::BOOKS;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $list = (new Binding())->getAll($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('Binding');

        $this->render('bindings_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionAudiostreamsList($entity) {
        $entity = Entity::ParseFromString($entity);
        if (!$this->_checkTagByEntity('audiostreams', $entity))
            throw new CHttpException(404);

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));


        $list = (new VideoAudioStream)->getAll($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('AUDIO_STREAMS');

        $this->render('audiostreams_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionSubtitlesList($entity) {
        $entity = Entity::ParseFromString($entity);
        if (!$this->_checkTagByEntity('subtitles', $entity))
            throw new CHttpException(404);

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $list = (new VideoSubtitle)->getAll($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('Credits');

        $this->render('subtitles_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionMediaList($entity) {
        $entity = Entity::ParseFromString($entity);
        if (!$this->_checkTagByEntity('media', $entity))
            throw new CHttpException(404);

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $list = (new Media())->getAll($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('Media');

        $this->render('media_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionTypesList($entity) {
        $entity = Entity::ParseFromString($entity);
        if (!$this->_checkTagByEntity('magazinetype', $entity))
            throw new CHttpException(404);

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $list = (new TypeRetriever)->getAll($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_NEW_TYPE_IZD');

        $this->render('types_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionDirectorList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false) $entity = Entity::VIDEO;

        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $p = new VideoDirector();
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en') $lang = 'ru';

        if (!empty($_GET['qa'])) {
            list($list, $list_count) = $p->getDirectorsBySearch($entity);
        }
        else {
            list($list, $list_count) = $p->GetDirectorList($entity, Yii::app()->language);
        }

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_DIRECTORS');

        $paginatorInfo = false;
        if ($list_count > count($list)) {
            $paginatorInfo = new CPagination($list_count);
            $paginatorInfo->setPageSize($p->getPerToPage());
            $paginatorInfo->route = 'directorlist';
        }

        $this->render('authors_list', array(
            'entity' => $entity,
            'abc' => array(),
            'paginatorInfo' => $paginatorInfo,
            'list' => $list,
            'idName' => 'did',
            'lang' => $lang,
            'url' => 'entity/bydirector',
            'liveAction'=>'directors'
        ));


/*        $list = $p->GetDirectorList($entity, $lang);
        $abc = array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_DIRECTORS');

        $this->render('authors_list', array('entity' => $entity, 'abc' => $abc,
            'list' => $list,
            'idName' => 'did',
            'lang' => $lang,
            'url' => 'entity/bydirector'));*/
    }

    public function actionYearsList($entity) {

        $entity = Entity::ParseFromString($entity);
        if ($entity === false) $entity = Entity::BOOKS;


       $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $list = (new YearRetriever)->getAll($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_NEW_FILTER_YEAR');

        $this->render('years_list', array('list' => $list, 'entity' => $entity));
    }

    public function actionByPerformer($entity, $pid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $p = new Performer;
        $performer = $p->GetById($entity, $pid);

        if (empty($performer))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['pid'] = $pid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($performer));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($performer, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $totalItems = $p->GetTotalItems($entity, $pid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($p->GetItems($entity, $pid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        // Получить статик-файл инфы
        $performerInfo = null;
        if (!empty($performer) && !empty($performer['description_file_' . Yii::app()->language])) {
            $file = $performer['description_file_' . Yii::app()->language];
            $path = Yii::getPathOfAlias('webroot') . '/pictures/templates-html/' . Entity::GetUrlKey($entity) . '-performers/' . $file;
            if (file_exists($path))
                $performerInfo = file_get_contents($path);
        }

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_AUDIO_AZ_PROPERTYLIST_PERFORMERS')] = Yii::app()->createUrl('entity/performerlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('READ_BY'), ProductHelper::GetTitle($performer));

        $filters = FilterHelper::getEnableFilters($entity);

        $this->render('list', array('entity' => $entity, 'paginatorInfo' => $paginatorInfo,
            'items' => $items, 'authorInfo' => $performerInfo,
            'filters' => $filters,
            ));
    }

    public function actionByDirector($entity, $did, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity != Entity::VIDEO)
            throw new CHttpException(404);

        $vd = new VideoDirector();
        $director = CommonAuthor::model()->findByPk($did);

        if (empty($director))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['did'] = $did;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($director->attributes));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($director->attributes, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_DIRECTORS')] = Yii::app()->createUrl('entity/directorlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('DIRECTOR_IS'), ProductHelper::GetTitle($director->attributes));

        $totalItems = $vd->GetTotalItems($entity, $did, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($vd->GetItems($entity, $did, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            ));
    }

    public function actionByActor($entity, $aid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);

        if (!Entity::checkEntityParam($entity, 'actors')) throw new CHttpException(404);

        $va = new VideoActor();
        //$actor = $va->GetById($aid);
        $actor = CommonAuthor::model()->findByPk($aid);
//        $this->widget('Debug', array($actor, CommonAuthor::model()->findByPk($aid)));

        if (empty($actor)) throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['aid'] = $aid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($actor->attributes));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($actor->attributes, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_ACTORS')] = Yii::app()->createUrl('entity/actorlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_ACTOR_IS'), ProductHelper::GetTitle($actor->attributes));

        $totalItems = $va->GetTotalItems($entity, $aid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($va->GetItems($entity, $aid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            ));
    }

    public function actionBySubtitle($entity, $sid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity != Entity::VIDEO)
            throw new CHttpException(404);

        $vs = new VideoSubtitle();
        $subtitle = VideoSubtitle::model()->findByPk($sid);

        if (empty($subtitle))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['sid'] = $sid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle->attributes));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle->attributes, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('Credits')] = Yii::app()->createUrl('entity/subtitleslist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_CREDITS_IS'), ProductHelper::GetTitle($subtitle->attributes));

        $totalItems = $vs->GetTotalItems($entity, $sid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($vs->GetItems($entity, $sid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'subtitlesVideo', $sid);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }

    public function actionByBinding($entity, $bid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $binding = new Binding;
        $bData = $binding->GetBinding($entity, $bid);
        if (empty($bData))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['bid'] = $bid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($bData));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($bData, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('Binding')] = Yii::app()->createUrl('entity/bindingslist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('Binding') . ': ' . ProductHelper::GetTitle($bData);

        $totalItems = $binding->GetTotalItems($entity, $bid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($binding->GetItems($entity, $bid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'binding_id', $bid);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array(
            'entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            'filter_data' => $filter_data,
        ));
    }

    public function actionByYear($entity, $year, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        if ((int) $year > 0) $dataForPath['year'] = $year;
        $this->_checkUrl($dataForPath);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_NEW_FILTER_YEAR')] = Yii::app()->createUrl('entity/yearslist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('IN_YEAR'), $year);

        $yr = new YearRetriever;

        $totalItems = $yr->GetTotalItems($entity, $year, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($yr->GetItems($entity, $year, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'ymin', $year);
        FilterHelper::setOneFiltersData($entity, 0, 'ymax', $year);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }
	
	public function actionByType($entity, $type, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;
		
        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['type'] = $type;
        $this->_checkUrl($dataForPath);

        $title = Entity::GetTitle($entity);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_NEW_TYPE_IZD')] = Yii::app()->createUrl('entity/typeslist', array('entity' => Entity::GetUrlKey($entity)));
        
        $key = Entity::GetUrlKey($entity);
        
        $db = $key . '_bindings';
        
        if ($entity == Entity::PERIODIC) { $db = 'pereodics_types'; } 
        
        $sql = 'SELECT * FROM '.$db.' WHERE id='.$type;
        $row = Yii::app()->db->createCommand($sql)->queryAll();
        
        
        
        $title = ProductHelper::GetTitle($row[0]);
        
        
        
       $this->breadcrumbs[] = $title;

        $yr = new TypeRetriever;

        $totalItems = $yr->GetTotalItems($entity, $type, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($yr->GetItems($entity, $type, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            ));
    }
	
	public function actionByYearRelease($entity, $year, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        if ((int) $year > 0) $dataForPath['year'] = $year;
        $this->_checkUrl($dataForPath);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('IN_YEAR'), $year);

        $yr = new YearRetriever;

        $totalItems = $yr->GetTotalItems2($entity, $year, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($yr->GetItems2($entity, $year, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            ));
    }

    public function actionByAudioStream($entity, $sid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity != Entity::VIDEO)
            throw new CHttpException(404);

        $s = new VideoAudioStream();
        $stream = VideoAudioStream::model()->findByPk($sid);
        if (empty($stream))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['sid'] = $sid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($stream->attributes));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($stream->attributes, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $title = Entity::GetTitle($entity);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('AUDIO_STREAMS')] = Yii::app()->createUrl('entity/audiostreamslist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('AUDIO_STREAMS') . ': ' . ProductHelper::GetTitle($stream->attributes);

        $totalItems = $s->GetTotalItems($entity, $sid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($s->GetItems($entity, $sid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $filters = FilterHelper::getEnableFilters($entity);
        FilterHelper::deleteEntityFilter($entity);
        FilterHelper::setOneFiltersData($entity, 0, 'langVideo', $sid);
        $filter_data = FilterHelper::getFiltersData($entity);

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            'filter_data' => $filter_data,
            ));
    }

    public function actionByMagazineType($entity, $tid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity != Entity::PERIODIC)
            throw new CHttpException(404);

        $mt = new MagazineType;
        $type = MagazineType::model()->findByPk($tid);
        if (empty($type))
            throw new CHttpException(404);

        $dataForPath = array('entity' => Entity::GetUrlKey($entity));
        $dataForPath['tid'] = $tid;
        $dataForPath['title'] = ProductHelper::ToAscii(ProductHelper::GetTitle($type->attributes));

        $langTitles = array();
        foreach (Yii::app()->params['ValidLanguages'] as $_lang) {
            if ($_lang !== 'rut') {
                if ($_lang === Yii::app()->language) $langTitles[$_lang] = $dataForPath['title'];
                else $langTitles[$_lang] = ProductHelper::ToAscii(ProductHelper::GetTitle($type->attributes, 'title', 0, $_lang));
            }
        }
        $this->_checkUrl($dataForPath, $langTitles);

        $title = Entity::GetTitle($entity, Yii::app()->language);

        $totalItems = $mt->GetTotalItems($entity, $tid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $this->_maxPages = ceil($totalItems/Yii::app()->params['ItemsPerPage']);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($mt->GetItems($entity, $tid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_PERIODTYPE_IS'), ProductHelper::GetTitle($type->attributes));

        $filters = FilterHelper::getEnableFilters($entity);

        $this->render('list', array('entity' => Entity::PERIODIC,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'filters' => $filters,
            ));
    }

    public function actionGetAuthorData()
    {
        $category = new Category();
        $author = $category->getFilterAuthor($_GET['entity'], $_GET['cid'],0,'',$_GET['lang']);
        print_r(json_encode($author));
        return true;
    }

    public function actionGetIzdaData()
    {
        $category = new Category();
        $izda = $category->getFilterPublisher($_GET['entity'], $_GET['cid'],0,'',$_GET['lang']);
        print_r(json_encode($izda));
        return true;
    }
    public function actionGetSeriesData()
    {
        $category = new Category();
        $series = $category->getFilterSeries($_GET['entity'], $_GET['cid'],0,'',$_GET['lang']);
        print_r(json_encode($series));
        return true;
    }

    public function actionGift()
    {
        $entity = Entity::PERIODIC;
        $this->_checkUrl(array('entity' => Entity::GetUrlKey($entity)));

        $o = new Offer();
        $group = $o->GetItems(Offer::INDEX_PAGE, $entity);
        $this->render('gift', array('entity' => $entity, 'group' => current($group)['items']));
    }

    private function _checkTagByEntity($tag, $entity) {
   		$entitys = Entity::GetEntitiesList();
   		if (!empty($entitys[$entity])) return in_array($tag, $entitys[$entity]['with']);
   		return false;
   	}

    /** функция сравнивает адрес страниц (которая должна быть и с которой реально зашли)
     * если совпадают, то возвращаю false
     * иначе редирект или 404
     * @param array $data параметры для формирования пути
     */
    private function _checkUrl($data, $langTitles = array()) {
   		$path = getenv('REQUEST_URI');
   		$ind = mb_strpos($path, "?", null, 'utf-8');
   		$query = '';
   		if ($ind !== false) {
   			$query = mb_substr($path, $ind, null, 'utf-8');
            $query = preg_replace("/\blang=\d+\b/ui", '', $query);
            $query = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $query);
            if ($query === '?') $query = '';

            $path = mb_substr($path, 0, $ind, 'utf-8');
   		}
        $typePage = $this->action->id;
        $this->_canonicalPath = Yii::app()->createUrl('entity/' . $typePage, $data);
        if ((mb_strpos($this->_canonicalPath, '?') !== false)&&!empty($query)) $query = '&' . mb_substr($query, 1, null, 'utf-8');

        foreach (Yii::app()->params['ValidLanguages'] as $lang) {
            if ($lang !== 'rut') {
                if ($lang === Yii::app()->language) $this->_otherLangPaths[$lang] = $this->_canonicalPath;
                else {
                    $_data = $data;
                    if (isset($data['title'])&&isset($langTitles[$lang])) $_data['title'] = $langTitles[$lang];
                    $_data['__langForUrl'] = $lang;
                    $this->_otherLangPaths[$lang] = Yii::app()->createUrl('entity/' . $typePage, $_data);
                }
            }
        }

        $canonicalPath = $this->_canonicalPath;
        $ind = mb_strpos($canonicalPath, "?", null, 'utf-8');
        if ($ind !== false) {
            $canonicalPath = mb_substr($canonicalPath, 0, $ind, 'utf-8');
        }

        if ($canonicalPath === $path) {
            //редирект с page=1
            $countPage1 = 0;
            $query = preg_replace("/\bpage=1\b/ui", '', $query, -1, $countPage1);
            if ($countPage1 > 0) {
                $query = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $query);
                if ($query === '?') $query = '';
                $this->redirect($this->_canonicalPath . $query, true, 301);
            }
            //редирект с page=1
            return;
        }

        $this->_redirectOldPages($path, $this->_canonicalPath, $query, $data);
        throw new CHttpException(404);

   	}


}

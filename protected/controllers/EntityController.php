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
        if ($entity === false)
            $entity = Entity::BOOKS;
		
		
		
		if ($_GET['lang'] != '') {
			$lang = $_GET['lang'];
			if (!Product::is_lang($_GET['lang'], $cid,$entity)) {
				$lang = '';
			}

		} elseif (Yii::app()->getRequest()->cookies['langsel']->value) {
			
			$lang = Yii::app()->getRequest()->cookies['langsel']->value;
			
			if (!Product::is_lang(Yii::app()->getRequest()->cookies['langsel']->value, $cid,$entity)) {
				$lang = '';
			}
			
		}



		$avail = $this->GetAvail($avail);
        

        $category = new Category();

		$maxminyear = $category->getFilterSlider($entity, $cid);
        $pubs = $category->getFilterPublisher($entity, $cid,1,$lang);
        $series = $category->getFilterSeries($entity, $cid,1,$lang);
        $authors = $category->getFilterAuthor($entity, $cid,1,$lang);
		//получаем языки категории
		$langs = $category->getFilterLangs($entity, $cid);
		
		$langVideo = array();
		
		if ($entity == 40) {
			
			$langVideo = $category->getFilterLangsVideo($entity, $cid);
			
		}

        $langSubtitles = array();

        if ($entity == 40) {

            $langSubtitles = $category->getSubtitlesVideo($entity, $cid);

        }

        $formatVideo = array();

        if ($entity == 40) {

            $formatVideo = $category->getFilterFormatVideo($entity, $cid);

        }
		
        $bg = $category->getFilterBinding($entity, $cid);

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
            } else
                $this->breadcrumbs[$pTitle] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $p['id']));
            $ids[] = $p['id'];
        }



        $totalItems = $category->GetTotalItems($entity, $cid, $avail);

        if ($cid > 0 && empty($path) && $totalItems == 0)
            throw new CHttpException(404);

        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

		$items = $category->GetItems($entity, $cid, $paginatorInfo, $sort, Yii::app()->language, $avail, $lang);

        // Добавляем к товарам инфу сколько уже содержится в корзине
       $items = $this->AppendCartInfo($items, $entity, $this->uid, $this->sid);
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

		$filter_data = array(
		'author'=>'',
		'binding_id'=>array(),
		'year_min'=>'',
		'year_max'=>'',
		'min_cost'=>'',
		'max_cost'=>''
		);

        if (Yii::app()->session['filter_e' . $entity . '_c_' . $cid] != '') {

            $data = unserialize(Yii::app()->session['filter_e' . $entity . '_c_' . $cid]); //получаем строку из сессии


			$cat = new Category();

			$items = $cat->result_filter($data, $lang);

			$data['binding_id'] = $data['binding'];
			$data['year_min'] = $ymin;
			$data['year_max'] = $ymax;
			$data['min_cost'] = $cmin;
			$data['max_cost'] = $cmax;

			$totalItems = Category::count_filter($entity, $cid, $data);
			$paginatorInfo = new CPagination($totalItems);
			$paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
			 $filter_data = $data;
		}

        if (isset(Yii::app()->session['last_e'])
            && (Yii::app()->session['last_e'] != '')
            && (Yii::app()->session['last_e'] != $entity))
            Yii::app()->session['filter_e' . Yii::app()->session['last_e'] . '_c_' . $cid] = '';

        Yii::app()->session['last_e'] = $entity;

        $filter_data['avail'] = 1;

		$this->render('list', array('categoryList' => $catList,
            'entity' => $entity, 'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'cid'=>$cid, 'filter_data' => $filter_data,
            'info' => $categoryInfo, 'filter_year' => $maxminyear,
            'bgs' => $bg, 'pubs' => $pubs, 'series'=>$series, 'authors'=>$authors, 'langs'=>$langs,
            'langVideo'=>$langVideo, 'langSubtitles' => $langSubtitles, 'formatVideo' => $formatVideo,
            'title_cat'=>$title_cat, 'cat_id'=>$selectedCategory, 'total'=>$totalItems));
    }

    public function actionCategoryList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $c = new Category();
        $tree = $c->GetCategoriesTree($entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('LIST_SOFT_CATTREE');

        $this->render('category_list', array('tree' => $tree, 'entity' => $entity));
    }

    public function actionPublisherList($entity, $char = null) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $p = new Publisher();
        $abc = array();
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en')
            $lang = 'en';

        if ($entity == Entity::SHEETMUSIC) {
            $list = $p->GetAll($entity, $lang);
        } else {
            $abc = $p->GetABC($lang, $entity);
            if (empty($char) && !empty($abc))
                $char = $abc[array_rand($abc)]['first_' . $lang];
            $list = $p->GetPublishersByFirstChar($char, $lang, $entity);
        }


        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('PROPERTYLIST_FOR_PUBLISHERS');

        $this->render('publisher_list', array('entity' => $entity, 'abc' => $abc, 'list' => $list, 'lang' => $lang));
    }

    public function actionSeriesList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;
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

        $totalItems = $s->GetTotalItems($entity, $sid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $s->GetItems($entity, $sid, $paginatorInfo, $sort, Yii::app()->language, $avail);
        $items = $this->AppendCartInfo($items, $entity, $this->uid, $this->sid);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_BOOKS_SERIES_PROPERTYLIST')] = Yii::app()->createUrl('entity/serieslist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('SERIES_IS'), ProductHelper::GetTitle($list[0]));

        $this->render('list', array('entity' => $entity,
            'paginatorInfo' => $paginatorInfo,
            'items' => $items));
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

        $totalItems = $m->GetTotalItems($entity, $mid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($m->GetItems($entity, $mid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_MEDIA_IS'), $media['title']);

        $this->render('list', array('entity' => $entity, 'paginatorInfo' => $paginatorInfo,
            'items' => $items));
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

        $totalItems = $p->GetTotalItems($entity, $pid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($p->GetItems($entity, $pid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('PROPERTYLIST_FOR_PUBLISHERS')] = Yii::app()->createUrl('entity/publisherlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('PUBLISHED_BY'), ProductHelper::GetTitle($publisher));

        $this->render('list', array('entity' => $entity,
            'paginatorInfo' => $paginatorInfo,
            'items' => $items));
    }

    public function actionAuthorList($entity, $char = null) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $a = new CommonAuthor();

        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en')
            $lang = 'en';
        $abc = $a->GetABC($lang, $entity);

        if (empty($char) && !empty($abc))
        $char = $abc[array_rand($abc)]['first_' . $lang];

		$list_count = 10;

		if ($_GET['qa']) {

			

			$list = $lists['rows'];
			$list_count = $lists['count'];

		} else {

			$list = $a->GetAuthorsByFirstChar($char, $lang, $entity);
			$list_count = count($a->GetAuthorsByFirstCharCount($char, $lang, $entity));

		}
		
		$lists = $a->GetAuthorsBySearch($char, $lang, $entity);

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('PROPERTYLIST_FOR_AUTHORS');



			//$paginatorInfo = new CPagination($list_count);
			//$paginatorInfo->setPageSize(150);
			//$paginatorInfo->route = 'AuthorList';

        $this->render('authors_list', array('entity' => $entity, 'abc' => $abc, 'list' => $list, 'lang' => $lang,'chasdr'=>$char));
    }

    public function actionByAuthor($entity, $aid, $sort = null, $avail = true) {

        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $a = new CommonAuthor();
        $author = $a->GetById($aid);

        $totalItems = $a->GetTotalItems($entity, $aid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
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

		$this->render('list', array('entity' => $entity,
            'paginatorInfo' => $paginatorInfo,
            'items' => $items,
            'presentation' => $file));
    }

    public function actionPerformerList($entity, $char = null) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $p = new Performer;
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en')
            $lang = 'ru';
        $list = $p->GetPerformerList($entity, $lang, $char);
        $abc = array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_AUDIO_AZ_PROPERTYLIST_PERFORMERS');

        $this->render('authors_list', array('entity' => $entity, 'abc' => $abc,
            'list' => $list,
            'idName' => 'pid',
            'lang' => $lang,
            'url' => 'entity/byperformer'));
    }

    public function actionActorList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $p = new VideoActor();
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en')
            $lang = 'ru';
        $list = $p->GetActorList($entity, $lang);
        $abc = array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_ACTORS');

        $this->render('authors_list', array('entity' => $entity, 'abc' => $abc,
            'list' => $list,
            'idName' => 'aid',
            'lang' => $lang,
            'url' => 'entity/byactor'));
    }

    public function actionDirectorList($entity) {
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $p = new VideoDirector();
        $lang = Yii::app()->language;
        if ($lang != 'ru' && $lang != 'en')
            $lang = 'ru';
        $list = $p->GetDirectorList($entity, $lang);
        $abc = array();

        $this->breadcrumbs[Entity::GetTitle($entity)] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_DIRECTORS');

        $this->render('authors_list', array('entity' => $entity, 'abc' => $abc,
            'list' => $list,
            'idName' => 'did',
            'lang' => $lang,
            'url' => 'entity/bydirector'));
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

        $totalItems = $p->GetTotalItems($entity, $pid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
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

        $this->render('list', array('entity' => $entity, 'paginatorInfo' => $paginatorInfo,
            'items' => $items, 'authorInfo' => $performerInfo));
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

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_DIRECTORS')] = Yii::app()->createUrl('entity/directorlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('DIRECTOR_IS'), ProductHelper::GetTitle($director->attributes));

        $totalItems = $vd->GetTotalItems($entity, $did, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($vd->GetItems($entity, $did, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
    }

    public function actionByActor($entity, $aid, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity != Entity::VIDEO)
            throw new CHttpException(404);

        $va = new VideoActor();
        $actor = CommonAuthor::model()->findByPk($aid);

        if (empty($actor))
            throw new CHttpException(404);

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_ACTORS')] = Yii::app()->createUrl('entity/actorlist', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_ACTOR_IS'), ProductHelper::GetTitle($actor->attributes));

        $totalItems = $va->GetTotalItems($entity, $aid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($va->GetItems($entity, $aid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
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

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_CREDITS_IS'), ProductHelper::GetTitle($subtitle->attributes));

        $totalItems = $vs->GetTotalItems($entity, $sid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($vs->GetItems($entity, $sid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
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

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('Binding') . ': ' . ProductHelper::GetTitle($bData);

        $totalItems = $binding->GetTotalItems($entity, $bid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($binding->GetItems($entity, $bid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
    }

    public function actionByYear($entity, $year, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('IN_YEAR'), $year);

        $yr = new YearRetriever;

        $totalItems = $yr->GetTotalItems($entity, $year, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($yr->GetItems($entity, $year, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
    }
	
	public function actionByType($entity, $type, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;
		
        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = ProductHelper::GetTitle(ProductHelper::GetTypes($entity, $type));

        $yr = new TypeRetriever;

        $totalItems = $yr->GetTotalItems($entity, $type, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($yr->GetItems($entity, $type, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
    }
	
	public function actionByYearRelease($entity, $year, $sort = null, $avail = true) {
        $avail = $this->GetAvail($avail);
        $entity = Entity::ParseFromString($entity);
        if ($entity === false)
            $entity = Entity::BOOKS;

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('IN_YEAR'), $year);

        $yr = new YearRetriever;

        $totalItems = $yr->GetTotalItems2($entity, $year, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($yr->GetItems2($entity, $year, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => $entity,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
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

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = Yii::app()->ui->item('AUDIO_STREAMS') . ': ' . ProductHelper::GetTitle($stream->attributes);

        $totalItems = $s->GetTotalItems($entity, $sid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($s->GetItems($entity, $sid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->render('list', array('entity' => Entity::VIDEO,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
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

        $title = Entity::GetTitle($entity, Yii::app()->language);

        $totalItems = $mt->GetTotalItems($entity, $tid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $sort = SortOptions::GetDefaultSort($sort);

        $items = $totalItems > 0 ? $this->AppendCartInfo($mt->GetItems($entity, $tid, $paginatorInfo, $sort, Yii::app()->language, $avail), $entity, $this->uid, $this->sid) : array();

        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('YM_FILTER_PERIODTYPE_IS'), ProductHelper::GetTitle($type->attributes));

        $this->render('list', array('entity' => Entity::PERIODIC,
            'items' => $items,
            'paginatorInfo' => $paginatorInfo));
    }

    public function actionGetAuthorData()
    {
        $category = new Category();
        $author = $category->getFilterAuthor($_GET['entity'], $_GET['cid'],0,$_GET['lang']);
        print_r(json_encode($author));
        return true;
    }

    public function actionGetIzdaData()
    {
        $category = new Category();
        $izda = $category->getFilterPublisher($_GET['entity'], $_GET['cid'],0,$_GET['lang']);
        print_r(json_encode($izda));
        return true;
    }
    public function actionGetSeriesData()
    {
        $category = new Category();
        $series = $category->getFilterSeries($_GET['entity'], $_GET['cid'],0,$_GET['lang']);
        print_r(json_encode($series));
        return true;
    }

    public function actionGift()
    {
        $entity = Entity::PERIODIC;
        $o = new Offer();
        $group = $o->GetItems(Offer::INDEX_PAGE, $entity);
        $this->render('gift', array('entity' => $entity, 'group' => current($group)['items']));
    }

}

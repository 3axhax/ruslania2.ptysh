<?php

class SiteController extends MyController {

    private $searchQuery = '';
    private $searchResults = 0;
    private $searchFilters = array();

    public function accessRules() {
        return array(array('allow',
                'actions' => array('update', 'error', 'index', 'categorylistjson', 'static',
                    'redirect', 'test', 'sale', 'landingpage', 'mload', 'loaditemsauthors', 'loaditemsizda', 'loaditemsseria',
                    'login', 'forgot', 'register', 'logout', 'search', 'advsearch', 'gtfilter', 'ggfilter'/*, 'ourstore'*/, 'addcomments', 'loadhistorysubs'),
                'users' => array('*')),
            array('allow', 'actions' => array('AddAddress', 'EditAddress', 'GetDeliveryTypes', 'loaditemsauthors', 'loaditemsizda', 'loaditemsseria',
                    'MyAddresses', 'Me', 'gtfilter', 'ggfilter', 'addcomments', 'loadhistorysubs'),
                'users' => array('@')),
            array('deny',
                'users' => array('*')));
    }

    public function actionSale() {
        $this->breadcrumbs[] = Yii::app()->ui->item('MENU_SALE');
        $this->render('sale');
    }

    public function actionLandingpage() {
        Yii::app()->language = 'fi';
        $this->breadcrumbs[] = 'Landingpage';
        $this->render('landingpage');
    }

    public function actionMload() {
        $url = explode('/', str_replace('/site/mload/', '', $_SERVER['REQUEST_URI']));

        //var_dump( $url[0] );
        $entity = Entity::ParseFromString($url[0]);
        $cid = $url[2];
        $sort = SortOptions::GetDefaultSort($sort);
        $avail = true;

        $category = new Category();

        $totalItems = $category->GetTotalItems($entity, $cid, $avail);
        $paginatorInfo = new CPagination($totalItems);
        $paginatorInfo->setPageSize(10);
        $items = $category->GetItems($entity, $cid, $paginatorInfo, $sort, Yii::app()->language, $avail);
		
		
		
        //var_dump($items);

        $this->renderPartial('listmenu', array('categoryList' => $catList,
            'entity' => $entity, 'items' => $items,
            'paginatorInfo' => $paginatorInfo,
            'info' => $categoryInfo));
    }

    public function actionRedirect($entity, $id) {
        $entities = Entity::GetEntitiesList();
        if (array_key_exists($entity, $entities)) {
            $url = Yii::app()->createUrl('product/view', array('entity' => Entity::GetUrlKey($entity), 'id' => $id,
                'title' => 'redirect'));
            header('Location: ' . $url, true, 302);
            exit;
        }

        throw new CHttpException(404);
    }

    public static function GetCombinations($arrays) {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public function actionIndex() {
        $o = new Offer();
        $groups = $o->GetItems(Offer::INDEX_PAGE);
        $count = 1;
        $this->render('index', array('groups' => $groups, 'cart' => $count,));
    }

    public function actionStatic($page) {
        $file = Yii::getPathOfAlias('webroot') . '/pictures/templates-static/' . $page . '_' . Yii::app()->language . '.html.php';
        if ($page == 'sitemap') $file = (new Sitemap)->builder();
        if (!file_exists($file)) $file = Yii::getPathOfAlias('webroot') . '/pictures/templates-static/' . $page . '_en.html.php';
        if (!file_exists($file)) $file = Yii::getPathOfAlias('webroot') . '/pictures/templates-static/' . $page . '_ru.html.php';

        if (!file_exists($file)) {
            throw new CHttpException(404);
        }
        $data = file_get_contents($file);

        $titles = array(
            'conditions' => 'MSG_CONDITIONS_OF_USE',
            'conditions_order' => 'YM_CONTEXT_CONDITIONS_ORDER_ALL',
            'conditions_subscription' => 'YM_CONTEXT_CONDITIONS_ORDER_PRD',
            'contact' => 'YM_CONTEXT_CONTACTUS',
            'legal_notice' => 'YM_CONTEXT_LEGAL_NOTICE',
            'faq' => 'A_FAQ',
            'aboutus' => 'A_ABOUTUS',
            'partners' => 'A_PARTNERS',
            'links' => 'A_LINKS',
			'ourstore' => 'A_STORE',
            'csr' => 'A_CSR',
            'offers_partners' => 'YM_CONTEXT_OFFERS_PARTNERS',
            'thawte' => 'MSG_YAHLIST_INFO_THAWTE',
            'safety' => 'MSG_YAHLIST_INFO_PAYMENTS_ARE_SECURE',
            'zone_info' => 'Zone',
            'paypal' => 'MSG_WHAT_IS_PAYPAL',
            'sitemap' => 'A_SITEMAP',
        );

        $this->breadcrumbs[] = Yii::app()->ui->item($titles[$page]);
        $this->render('static', array('data' => $data, 'entity' => 'static'));
    }

    public function actionAddAddress() {
        $address = new Address('new');
        if (Yii::app()->user->isGuest)
            throw new CHttpException(403, 'Access Denied');
        if (Yii::app()->request->isPostRequest) {
            $ret = KnockoutForm::PerformValidation($address, 'add-address');
            if (!KnockoutForm::HasErrors($ret)) {
                $id = $address->InsertNew(Yii::app()->user->id, false);
                if (empty($id))
                    $this->ResponseJsonError('Fail');
                $this->ResponseJson(array('hasError' => false, 'message' => 'Address inserted', 'id' => $id));
            }
            $this->ResponseJson($ret);
        }
    }

    public function actionEditAddress() {
        $uid = Yii::app()->user->id;
        $address = new Address('edit');
        if (Yii::app()->user->isGuest)
            throw new CHttpException(403, 'Access Denied');
        if (Yii::app()->request->isPostRequest) {
            $ret = KnockoutForm::PerformValidation($address, 'add-address');

            if (!KnockoutForm::HasErrors($ret)) {
                $oldID = $address->id;

                if (!$address->IsMyAddress($uid, $oldID))
                    $this->ResponseJsonError('Not my address');

                $address->id = null; // При редактировании добавляем новый адрес
                // что бы история доставок была правильной
                // а так что бы это выглядело как редактирование
                // удалим у пользователя его старый адрес и таблицы соответствий
                $id = $address->InsertNew($uid, false);
                if (empty($id))
                    $this->ResponseJsonError('Fail');

                $address->DeleteAddress($uid, $oldID);
                // Если у человека были подписки, то послать емейл в отдел подписок о смене адреса
                $address->NotifyIfAddressChanged($uid, $oldID, $address->attributes);
 
                $this->ResponseJsonOk('Address inserted');
            }
            $this->ResponseJson($ret);
        }
    }

    public function actionGetDeliveryTypes($aid) {
        $uid = Yii::app()->user->id;
        $a = new Address();
        if ($a->IsMyAddress($uid, $aid)) {
            $p = new PostCalculator();
            $list = $p->GetRates($aid, $uid, $this->sid);
            $this->ResponseJson(array('DeliveryTypes' => $list));
        }
        $this->ResponseJson($aid);
    }

    public function actionMyAddresses() {
        if (Yii::app()->user->isGuest)
            throw new CHttpException(403);
        $uid = Yii::app()->user->id;
        $user = new User;
        $addresses = $user->GetAddresses($uid);
        $this->ResponseJson(array('Addresses' => $addresses));
    }

    public function actionLogin() {
        if (Yii::app()->request->isPostRequest) {
            $user = new User('login');
            $ret = KnockoutForm::PerformValidation($user, 'user-login');
            if (!KnockoutForm::HasErrors($ret)) {
                $identity = new RuslaniaUserIdentity($user->login, $user->pwd);
                if ($identity->authenticate()) {
                    Yii::app()->user->login($identity, Yii::app()->params['LoginDuration']);
                    $cart = new Cart();
                    $cart->UpdateCartToUid($this->sid, $identity->getId());
                    $this->ResponseJsonOk('Welcome!');
                }

                $ret['HasValidationErrors'] = true;
                $ret['error']['login'][] = $identity->getErrorMessage();
            }
            $this->ResponseJson($ret);
        }

        $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_PERSONAL_LOGIN');

        $this->render('login');
    }

    public function actionRegister() {
        $user = new User('register');
        if (Yii::app()->request->isPostRequest) {
            $ret = KnockoutForm::PerformValidation($user, 'user-register');
            if (!KnockoutForm::HasErrors($ret)) {
                $langID = Language::ConvertToInt(Yii::app()->language);
                $ret = $user->RegisterNew($langID, Yii::app()->currency);
                if ($ret) {
                    $identity = new RuslaniaUserIdentity($user->login, $user->pwd);
                    $identity->authenticate();
                    Yii::app()->user->login($identity, Yii::app()->params['LoginDuration']);
                    $cart = new Cart();
                    $cart->UpdateCartToUid($this->sid, $identity->getId());
                }
                $ret = array('hasError' => !$ret);
            }
            $this->ResponseJson($ret);
        }

        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
        $this->render('register', array('model' => $user));
        ;
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect('/');
    }

    public function afterAction($action) {
        parent::afterAction($action);

        if ($action->id == 'search') {
            SearchHelper::LogSearch(Yii::app()->user->id, $this->searchQuery, $this->searchFilters, $this->searchResults);
        }
        return true;
    }

    public function actionSearch($q = '', $e = 0, $page = 0, $avail = 1) {
        $avail = $this->GetAvail($avail);
        $page = intVal($page);
        $page = $page - 1;
        if ($page < 0)
            $page = 0;
        $e = abs(intVal($e));

        $origSearch = trim($q);
        $this->searchQuery = $origSearch;
        $products = array();

        $this->searchFilters = array('e' => $e, 'page' => $page);

        Yii::app()->session['SearchData'] = array('q' => $origSearch, 'time' => time(), 'e' => $e);

        if (empty($origSearch)) {
            if (Yii::app()->request->isAjaxRequest)
                $this->ResponseJson(array());

            // постраничный результат

            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
            $this->render('search', array('result' => array(),
                'q' => $q, 'products' => array(),
                'paginatorInfo' => new CPagination(0)));
            return;
        }

        $search = SearchHelper::Create();

        $result = array();

        $pp = Yii::app()->params['ItemsPerPage'];
        // Ищем товар
        $resArray = array();
        // Вдруг это складской номер
        if (ProductHelper::IsShelfId($origSearch)) {
            $search->SetFilter('stock_id', array($origSearch));
            $resArray = $search->query('', 'products');
        } else if (ProductHelper::IsEan($origSearch)) {
            $search->SetFilter('eancode', array($origSearch));
            $resArray = $search->query('', 'products');
        } else if (ProductHelper::IsIsbn($origSearch)) {
            $matches = array();
            if (preg_match_all('|\d+|', $origSearch, $matches)) {
                $isbn = implode('', $matches[0]);
                $search->SetFilter('isbnnum', array($isbn));
                $resArray = $search->query('', 'products');
            }
        } else {
            $products = array();
            $searchFilters = array();
            if (!empty($e))
                $searchFilters['entity'] = $e;

            $publishersResult = SearchHelper::SearchInPublishers($q, $searchFilters);
            $authorsResult = SearchHelper::SearchInPersons($q, $searchFilters);

            $categoriesResult = SearchHelper::SearchInCategories($q, $searchFilters);
            $seriesResult = array(); //$this->SearchInSeries($search, $q, $e);
			
			//var_dump($authorsResult);
			
            $authorsIds = array();
            foreach ($authorsResult as $author)
                $authorsIds[] = $author['orig_data']['id'];
				$publishersIds = array();
            foreach ($publishersResult as $publisher)
                $publishersIds[] = $publisher['orig_data']['id'];
            $categoriesIds = array();
            foreach ($categoriesResult as $cat)
                $categoriesIds[] = $cat['orig_data']['id'];
            $seriesIds = array();

            $expando = array();
            $arr = array();
            if (!empty($publishersIds)) {
                $expando['publisher_id'] = $publishersIds;
                array_push($arr, 'publisher_id');
            }
            if (!empty($authorsIds)) {
                $expando['author'] = $authorsIds;
                array_push($arr, 'author');
            }
            if (!empty($categoriesIds)) {
                $expando['category'] = $categoriesIds;
                array_push($arr, 'category');
            }

//            echo '<pre>';
//            var_dump($expando);

            $len = count($arr);
            $list = array();

            for ($i = 1; $i < (1 << $len); $i++) {
                $c = array();
                for ($j = 0; $j < $len; $j++)
                    if ($i & (1 << $j))
                        $c[] = $arr[$j];

                if (count($c) >= 2)
                    $list[] = $c;
            }

            $list = array_reverse($list);
            $filters = array();
            foreach ($list as $data) {
                $search->ResetFilters();
                foreach ($data as $filter) {
//                    echo '<li>'.$filter.' - '.print_r($expando[$filter], true);
                    $search->SetFilter($filter, $expando[$filter]);
                }

                $res = $search->query('', 'products');
                $tmpFilter = array();
                $alreadyCategory = array();
                $alreadyAuthors = array();
                $alreadyPublishers = array();

                if ($res['total_found'] > 0) {
                    foreach ($res['matches'] as $match) {
                        $attrs = $match['attrs'];
                        $categories = $attrs['category'];
                        $authors = $attrs['author'];
                        $publisher = array_key_exists('publisher_id', $attrs) ? $attrs['publisher_id'] : false;
                        if (!empty($publisher) && !in_array($publisher, $alreadyPublishers)) {
                            $tmpFilter['publisher_id'][] = $publisher;
                            $alreadyPublishers[] = $publisher;
                        }

                        foreach ($categories as $cat) {
                            if (array_key_exists('category', $expando) && in_array($cat, $expando['category']) && !in_array($cat, $alreadyCategory)
                            ) {
                                $tmpFilter['category'][] = $cat;
                                $alreadyCategory[] = $cat;
                            }
                        }

                        foreach ($authors as $a) {
                            if (array_key_exists('author', $expando) && in_array($a, $expando['author']) && !in_array($a, $alreadyAuthors)
                            ) {
                                $tmpFilter['author'][] = $a;
                                $alreadyAuthors[] = $a;
                            }
                        }
                    }
                    $filters[implode(' ', $data)] = $tmpFilter;
                }
            }

            $filterResult = array();
            foreach ($filters as $filter) {
                $fParams = $this->GetCombinations($filter);
                foreach ($fParams as $param) {
                    $url = Yii::app()->createUrl('entity/filter', $param);
                    $keys = array_keys($param);
                    $titles = array();
                    foreach ($keys as $key) {
                        if ($key == 'author') {
                            foreach ($authorsResult as $a) {
                                if ($a['orig_data']['id'] == $param[$key]) {
                                    $t = substr(Yii::app()->ui->item('YM_FILTER_WRITTEN_BY'), 0, 5);
                                    $titles[] = $t . ': <b>' . ProductHelper::GetTitle($a['orig_data']) . '</b>';
                                    break;
                                }
                            }
                        } else if ($key == 'publisher_id') {
                            foreach ($publishersResult as $p) {
                                if ($p['orig_data']['id'] == $param[$key]) {
                                    $titles[] = Yii::app()->ui->item('Published by') . ': <b>' . ProductHelper::GetTitle($p['orig_data']) . '</b>';
                                    break;
                                }
                            }
                        } else if ($key == 'category') {
                            foreach ($categoriesResult as $c) {
                                if ($c['orig_data']['id'] == $param[$key]) {
                                    $titles[] = Yii::app()->ui->item('Related categories') . ': <b>' . ProductHelper::GetTitle($c['orig_data']) . '</b>';
                                    break;
                                }
                            }
                        }
                    }

                    $filterResult[] = array('title' => implode('; ', $titles),
                        'url' => $url, 'is_product' => false);
                }
            }

            $search->ResetFilters();
            if (!empty($e) && true)
                $search->SetFilter('entity', array($e));
            if ($avail)
                $searchFilters['avail'] = 1;
            //$searchFilters['avail'] = $avail ? 1 : 0;
            //$search->SetSortMode(SPH_SORT_ATTR_DESC, "avail");

            $totalFound = 0;
            $realProducts = SearchHelper::SearchInProducts($q, $searchFilters, $page, $pp, $totalFound);
            $products = array_merge($products, $realProducts);
			
			//var_dump($products);
			
			$tF = 0;
            $prodCrossAuthors = SearchHelper::SearchCrossProdAuthors($q, $searchFilters, $authorsResult, $page, $pp, $tF);
            $products = array_merge($prodCrossAuthors['Items'], $products);
			
            $totalFound += $tF;
			
			//$k = array();
			$s = 0;
			
			$products2 = array();
			
			foreach($products as $e=>$ids) {
				
				$k = array();
				
				$ids = (array)$ids;
				
				
				if (count($ids)) {
				
				foreach ($ids as $id) {
					
					if (!in_array($id, $k)) {
						$k[] = $id;
						$s++;
					}
					
				}
				
				}
				
				$products2[$e] = $k;
				
			}
			
			$products = SearchHelper::ProcessProducts2($products2);
			
			
			
			//var_dump($products);
			
			//сортировка товаров
			
			$arr_order = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] > 5 AND $arr['avail_for_order'] != '0') {
				
				return true;
				
			}
			
		});
		
		$arr_order2 = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] < 5 AND $arr['avail_for_order'] != '0') {
				
				return true;
				
			}
			
		});
		
		$arr_not_order = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] == 0 AND $arr['avail_for_order'] != '0') {
				
				return true;
				
			}
			
		});
		
		$arr_not_avail = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] == 0 AND $arr['avail_for_order'] == '0') {
				
				return true;
				
			}
			
		});
		
		$products = array_merge($arr_order, $arr_order2, $arr_not_order, $arr_not_avail);
         
		
		 
		/* разбиваем на страницы */
		$page_count = Yii::app()->params['ItemsPerPage'];
		
		$curpage = (int) $_GET['page'];
		
		if (!$curpage) $curpage = 1;
		
		$min = ($curpage-1) * $page_count;
		
		if ($min == 0) { $min = 1; }
		
		$max = $min+$page_count;
		
		//var_dump($page_count);
		
		$i = 0;
			
			$products2 = array();
			
			foreach($products as $e=>$ids) {
				
								
					$i++;
			
					if ($i<$min OR $max<=$i) continue;
					
					$products2[(string)$e] = $ids;
				
				
				
			}
			
			
			$products = $products2;
			
			
			 if (count($filterResult) > 3)
                $filterResult = array_splice($filterResult, 0, 3);
            $result = array_merge($result, $filterResult);
            if (count($authorsResult) > 3)
                $authorsResult = array_splice($authorsResult, 0, 3);
            $result = array_merge($result, $authorsResult);
            if (count($categoriesResult) > 3)
                $categoriesResult = array_splice($categoriesResult, 0, 3);
            $result = array_merge($result, $categoriesResult);

            if (count($publishersResult) > 3)
                $publishersResult = array_splice($publishersResult, 0, 3);
            $result = array_merge($result, $publishersResult);
            $result = array_merge($result, $seriesResult);
			
			
        }

        if (!empty($resArray)) {
            $t = SearchHelper::ProcessProducts($resArray);
			$s = 0;
			
			$products2 = array();
			
			foreach($t as $e=>$ids) {
				
				$k = array();
				
				foreach ($ids as $id) {
					
					if (!in_array($id, $k)) {
						$k[] = $id;
						$s++;
					}
					
				}
				
				$products2[$e] = $k;
				
			}
			
			$products = SearchHelper::ProcessProducts2($products2);
			
			
			
			//var_dump($products);
			
			//сортировка товаров
			
			$arr_order = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] > 5 AND $arr['avail_for_order'] != '0') {
				
				return true;
				
			}
			
		});
		
		$arr_order2 = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] < 5 AND $arr['avail_for_order'] != '0') {
				
				return true;
				
			}
			
		});
		
		$arr_not_order = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] == 0 AND $arr['avail_for_order'] != '0') {
				
				return true;
				
			}
			
		});
		
		$arr_not_avail = array_filter($products, function ($arr) {
			
			if ($arr['in_shop'] == 0 AND $arr['avail_for_order'] == '0') {
				
				return true;
				
			}
			
		});
		
		$products = array_merge($arr_order, $arr_order2, $arr_not_order, $arr_not_avail);
         
		
		 
		/* разбиваем на страницы */
		$page_count = Yii::app()->params['ItemsPerPage'];
		
		$curpage = (int) $_GET['page'];
		
		if (!$curpage) $curpage = 1;
		
		$min = ($curpage-1) * $page_count;
		
		if ($min == 0) { $min = 1; }
		
		$max = $min+$page_count;
		
		//var_dump($page_count);
		
		$i = 0;
			
			$products2 = array();
			
			foreach($products as $e=>$ids) {
				
								
					$i++;
			
					if ($i<$min OR $max<=$i) continue;
					
					$products2[(string)$e] = $ids;
				
				
				
			}
			
			
			$products = $products2;
			
			
            //$products = array_merge($products, $t);
            $totalFound = count($products);
        }

        

       
        $totalFound = $s;

        if (Yii::app()->request->isAjaxRequest) {
            
			$products = array_values($products);
			
			foreach ($result as $idx => $data)
                unset($result[$idx]['orig_data']);
            $arr = array_merge($result, $products);
			
			$this->searchResults = count($arr);
			
			$ents = Entity::GetEntitiesList();
			
			foreach($arr as $k => $goods) {
				
				$curCount = (int) $r[0]['Counts']['enityes'][$ents[$goods['entity']]['site_id']][1];
				
				$r[0]['Counts']['enityes'][$ents[$goods['entity']]['site_id']] = array($q,$curCount+1, 'в разделе '. Entity::GetTitle($goods['entity']), '/site/search?q='.$q.'&e='.$goods['entity'].'&avail='.$avail);
				
			}
			
			$r[] = $arr;
			
			
			
			$this->ResponseJson($r);
        }

        $paginatorInfo = new CPagination($totalFound);
        $paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);
        $this->searchResults = $totalFound;		
		
		
		
		// постраничный результат
        $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
        $this->render('search', array('q' => $q, 'items' => $result,
            'products' => $products,
            'paginatorInfo' => $paginatorInfo));
    }

    public function SearchInSeries($search, $q, $e) {
        $result = array();
        if (!empty($e))
            $search->SetFilter('entity', array($e));
        $aRes = $search->query($q, 'series');
        if (isset($aRes['total_found']) && count($aRes['matches']) > 0) {
            $s = new Series();
            $ids = array();
            foreach ($aRes['matches'] as $id => $data)
                $ids[$data['attrs']['entity']][] = $data['attrs']['id'];

            foreach ($ids as $entity => $itemIDs) {
                $key = strtoupper(Entity::GetUrlKey($entity));
                $list = $s->GetByIds($entity, $itemIDs);
                foreach ($list as $item) {
                    $item['entity'] = $entity;
                    $row = array();
                    $row['entity'] = 97;
                    $row['is_product'] = false;
                    $row['item_entity'] = $entity;
                    $row['entity_name'] = CommonHelper::EntityName(97);
                    $row['url'] = Series::Url($item);
                    $row['picture_url'] = '';
                    $row['title'] = sprintf(Yii::app()->ui->item('FOUND_' . $key . '_SERIES'), ProductHelper::GetTitle($item));
                    $row['description'] = '';
                    $row['id'] = $item['id'];
                    $result[] = $row;
                }
            }
        }
        return $result;
    }

    public function actionAdvSearch($e = 0, $cid = 0, $title = '', $author = '', $perf = '', $publisher = '', $l = '', $only = false, $year = '', $page = 0) {
        $page = intVal($page);
        if ($page < 1)
            $page = 1;
        $e = abs(intVal($e));

        $data = SearchHelper::AdvancedSearch($e, $cid, $title, $author, $perf, $publisher, $only, $l, $year, Yii::app()->params['ItemsPerPage'], $page, $_GET['binding_id'.$e]);
        $this->breadcrumbs[] = Yii::app()->ui->item('Advanced search');
        $this->render('adv_search', array('items' => $data['Items'], 'paginatorInfo' => $data['Paginator']));
    }

    public function actionForgot() {
        $this->breadcrumbs[] = Yii::app()->ui->item('A_TITLE_REMIND_PASS');

        $model = new User('forgot');
        $this->PerformAjaxValidation($model, 'remind-form');
        $user = null;

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['User'];
            if ($model->validate()) {
                $user = User::model()->findByAttributes(array('login' => $model->login));
                if (empty($user)) {
                    $this->render('forgot', array('model' => $model, 'user' => $user, 'notFound' => true));
                    return;
                }

                $message = new YiiMailMessage('Ruslania.com password');
                $message->view = 'forgot';
                $message->setBody($user->attributes, 'text/html');
                $message->addTo($user->login);
                $message->from = 'ruslania@ruslania.com';
                Yii::app()->mail->send($message);
            }
        }

        $this->render('forgot', array('model' => $model, 'user' => $user));
    }

    private function formatTree($tree, $depth, &$ret) {
        if (!is_null($tree) && count($tree) > 0) {
            foreach ($tree as $node) {
                $name = ProductHelper::GetTitle($node['payload']);
                $name = str_repeat('----', $depth) . ' ' . $name;
                $ret[] = array('ID' => $node['payload']['id'], 'Name' => $name);
                $this->formatTree($node['children'], $depth + 1, $ret);
            }
        }
    }

    public function actioncategorylistjson($e) {
        $entity = (Entity::IsValid($e)) ? $e : Entity::BOOKS;

        $c = new Category();
        $tree = $c->GetCategoriesTree($entity);
        $ret = array();
        $this->formatTree($tree, 0, $ret);
        $this->ResponseJson($ret);
    }

    function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if ($error['code'] != 404) {
                $error['REQUEST_URI'] = @$_SERVER['REQUEST_URI'];
                $error['UID'] = Yii::app()->user->id;
                $error['IP'] = Yii::app()->request->getUserHostAddress();
                $error['UA'] = @$_SERVER['HTTP_USER_AGENT'];
                $error['POST'] = $_POST;
                $error['GET'] = $_GET;
                unset($error['traces']);
                unset($error['trace']);
                Yii::log(print_r($error, true), CLogger::LEVEL_ERROR, 'myerrors');
            }
            $this->breadcrumbs[] = $error['code'];
            $this->render('error', $error);
        }
    }

    public function actionLoadItemsAuthors($page = 1, $entity = 10, $cid = 0) {

        $category = new Category();

        $authors = $category->getFilterAuthor($entity, $cid, $page);

        foreach ($authors as $rows => $binfo) {
            $row = CommonAuthor::GetById($binfo['author_id']);
            if (!$row['id'] OR $row['id'] == '0')
                continue;
            $name_publ = $row['title_' . Yii::app()->language];
            if (!trim($name_publ))
                continue;

            echo '<div class="item" rel="' . $row['id'] . '" onclick="select_item($(this), \'author\')">' . $name_publ . '</div>';
        }
    }

    public function actionLoadItemsIzda($page = 1, $entity = 10, $cid = 0) {

        $category = new Category();

        $authors = $category->getFilterPublisher($entity, $cid, $page);

        foreach ($authors as $rows => $binfo) {
            $row = Publisher::GetByID($entity, $binfo['publisher_id']);
            if (!$row['id'] OR $row['id'] == '0')
                continue;
            $name_publ = $row['title_' . Yii::app()->language];
            if (!trim($name_publ))
                continue;

            echo '<div class="item" rel="' . $row['id'] . '" onclick="select_item($(this), \'izda\')">' . $name_publ . '</div>';
        }
    }

    public function actionLoadItemsSeria($page = 1, $entity = 10, $cid = 0) {

        $category = new Category();

        $authors = $category->getFilterSeries($entity, $cid, $page);

        foreach ($authors as $rows => $binfo) {
            $row = Series::GetByIds($entity, array($binfo['series_id']));
            if (!$row[0]['id'] OR $row[0]['id'] == '0')
                continue;
            $name_publ = $row[0]['title_' . Yii::app()->language];
            if (!trim($name_publ))
                continue;

            echo '<div class="item" rel="' . $row[0]['id'] . '" onclick="select_item($(this), \'seria\')">' . $name_publ . '</div>';
        }
    }

    function actionGTfilter() { //узнаем сколько выбрано товаров при фильтре
        if (Yii::app()->request->isPostRequest) {
            $category = new Category();

			echo $category->count_filter($_POST['entity_val'], $_POST['cid_val'], $_POST);
        }
    }

    public function getStateKeyPrefix() {
        if ($this->_keyPrefix !== null)
            return $this->_keyPrefix;
        else
            return $this->_keyPrefix = md5('Yii.' . get_class($this) . '.' . Yii::app()->getId());
    }

    function actionGGfilter($entity = 0, $cid = 0, $author = '0', $avail = '0', $ymin = '0', $ymax = '0',
                            $izda = '0', $seria = '0', $cmin = '0', $cmax = '0', $binding = '0', $langsel = '',
                            $langVideo = '0', $formatVideo = '0', $subtitlesVideo = '0') {

        /* Строка урл: /site/ggfilter/entity/10/cid/0/author/4758/avail/1/ymin/2008/ymax/2018/izda/18956/seria/1290/cmin/1000/cmax/9000/ */

        $_GET['name_search'] = $_POST['search_name'];
        $_GET['sort'] = (($_POST['sort']) ? $_POST['sort'] : 3);
        $_GET['binding'] = $_POST['binding_id'];
        $_GET['langVideo'] = $_POST['langVideo'];
        $_GET['formatVideo'] = $_POST['formatVideo'];
        $_GET['subtitlesVideo'] = $_POST['subtitlesVideo'];
        $_GET['langsel'] = $_POST['langsel'];

        //записываем фильтр в сессию каждой категории
        if (Yii::app()->session['filter_e' . $entity . '_c_' . $cid] != serialize($_GET)) {
            Yii::app()->session['filter_e' . $entity . '_c_' . $cid] = serialize($_GET);
        }

        $data = unserialize(Yii::app()->session['filter_e' . $entity . '_c_' . $cid]); //получаем строку из сессии
        //var_dump($data);

        $cat = new Category();
        $items = $cat->result_filter($data);

        //$data['binding_id'] = (array) unserialize($data['binding']);
        $data['binding_id'] = $data['binding'];
        $data['year_min'] = $ymin;
        $data['year_max'] = $ymax;
        $data['min_cost'] = $cmin;
        $data['max_cost'] = $cmax;

        $totalItems = Category::count_filter($entity, $cid, $data);
        $paginator = new CPagination($totalItems);
        $paginator->setPageSize(Yii::app()->params['ItemsPerPage']);

        $path = $cat->GetCategoryPath($entity, $cid);
        $selectedCategory = array_pop($path);

        $this->renderPartial('list_ajax', array(
            'entity' => $entity, 'items' => $items,
            'paginatorInfo' => $paginator,
            'title_cat' => ProductHelper::GetTitle($selectedCategory),
            'cid' => $cid
        ));
    }
	
	function actionAddComments() {
		if (Yii::app()->request->isPostRequest) {
			
			if (!trim(strip_tags($_POST['comment_text']))) {
				return '';
			}
			
			$text = trim(strip_tags($_POST['comment_text']));
			
			$text = str_replace("\n", '<br />', $text);
			
			$comm = new Comments;
			$comm->date_publ = date('Y-m-d');
			$comm->text = $text;
			$comm->product_id = $_POST['id'];
			$comm->product_entity = $_POST['entity'];
			$comm->user_id = Yii::app()->user->id;
			$comm->moder = 0;
			
			$comm->save(false);
			
			$comments = $comm->get_list($_POST['entity'], $_POST['id']);
			
			echo '1';
		
		}
	}
	
	function actionLoadHistorySubs() {
		if (Yii::app()->request->isPostRequest) {
				
				$sql = 'SELECT * FROM `subscriptions_sentlog` WHERE econet_id='.$_POST['uid'].' AND periodic_id='.$_POST['sid'].' ORDER BY sent_date DESC';
				
				$subs_id = $_POST['subsid'];
				
				$rowc = Yii::app()->db->createCommand($sql)->queryAll();
								
				if (!$rowc) {
					echo Yii::app()->ui->item('A_NEW_SUBS_NOTFOUND');
				} else {
								
					
						
						$month = array(
							
							'',
							Yii::app()->ui->item("A_NEW_SUBS_MONTH1"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH2"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH3"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH4"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH5"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH6"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH7"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH8"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH9"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH10"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH11"),
							Yii::app()->ui->item("A_NEW_SUBS_MONTH12")
					
						);
						
					echo '
					
					<table>
			<thead>
				<tr>
					<th width="150" nowrap>'.Yii::app()->ui->item('A_NEW_SUBS_TD1_1').'</th>
					<th>'.Yii::app()->ui->item('A_NEW_SUBS_TD2_1').'</th>
					<th>'.Yii::app()->ui->item('A_NEW_SUBS_TD3_1').'</th>
				</tr>
			</thead>
			<tbody>';
				
				foreach($rowc as $k=>$row) : 
				
				echo '<tr>
					<td>'.date('d '.$month[date('n',strtotime($row['sent_date']))].' Y', strtotime($row['sent_date'])). '<br />'.date('H:i:s', strtotime($row['sent_date'])).'</td>
					<td>'.$subs_id.'</td>
					<td style="text-align: center;">'.$row['number'].' / '.$row['year_of'].'</td>
				</tr>';
				
				endforeach;
				
			echo '</tbody>
		</table>
					
					
					';
				}
		}
	}
}

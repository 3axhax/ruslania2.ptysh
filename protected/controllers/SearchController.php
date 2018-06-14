<?php
/*Created by Кирилл (09.06.2018 23:37)*/
class SearchController extends MyController {
	//количество в результате поиска
	private $_counts = null;

	private $searchQuery = '';
	private $searchResults = 0;
	private $searchFilters = array();

	public function actionGeneral($q = '', $e = 0, $page = 0, $avail = 1) {
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
//var_dump($origSearch);
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
		$abstract = array();

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
		$this->_maxPages = ceil($totalFound/Yii::app()->params['ItemsPerPage']);
		$this->searchResults = $totalFound;



		print_r($result);
		echo '<br><br>';
		print_r($products);
		// постраничный результат
		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
//		$this->render('search', array('q' => $q, 'items' => $result,
//			'products' => $products,
//			'paginatorInfo' => $paginatorInfo));
	}

	/*	function actionGeneral() {
			$q = trim((string) Yii::app()->getRequest()->getParam('q'));
			if (empty($q)) {
				$this->_viewEmpty($q);
				return;
			}

			$avail = $this->GetAvail(1);
			$page = $this->_getNumPage();
		}*/

	function getEntitys() {

	}

	function getDidYouMean() {

	}

	function getList() {

	}



	public function afterAction($action) {
		parent::afterAction($action);
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		$e = (int) Yii::app()->getRequest()->getParam('e');
		SearchHelper::LogSearch(Yii::app()->user->id, $q, array('e' => $e, 'page' => $this->_getNumPage()), (int)$this->_counts);
	}


	private function _getNumPage() {
		return min(100000, max((int) Yii::app()->getRequest()->getParam('page'), 1));
	}

	private function _viewEmpty($q) {
		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
		$this->render(
			'search',
			array(
				'result' => array(),
				'q' => $q,
				'products' => array(),
				'paginatorInfo' => new CPagination(0),
			)
		);
	}

}
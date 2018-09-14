<?php
/*Created by Кирилл (09.06.2018 23:37)*/
class SearchController extends MyController {
	//количество в результате поиска
	private $_counts = null;
	protected $_exactMatch = false;

	/**
	 * @var DGSphinxSearch
	 */
	private $_search;

	function actionGeneral() {
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		if (empty($q)) {
			$this->_viewEmpty($q);
			return;
		}

		$list = array();
		$isCode = false;
		if ($code = $this->isCode($q)) {
			$list = $this->getByCode($code, $q);
			if (!empty($list)) $isCode = true;
		}

		$page = $this->_getNumPage();
		$abstractInfo = array();
		$didYouMean = array();
		if (!$isCode) {
			/*$list = $this->getListExactMatch($q, $page, Yii::app()->params['ItemsPerPage']);
			if (empty($list)) */$list = $this->getList($q, $page, Yii::app()->params['ItemsPerPage']);
//			$list = $this->getList($q, $page, Yii::app()->params['ItemsPerPage']);
			$list = $this->inDescription($list, $q);
			$abstractInfo = $this->getEntitys($q);
			$didYouMean = $this->getDidYouMean($q);
		}

		if (empty($abstractInfo)) $paginatorInfo = new CPagination(count($list));
		else {
			$e = (int) Yii::app()->getRequest()->getParam('e');
			if (Entity::IsValid($e)) {
				if (empty($abstractInfo[$e])) $paginatorInfo = new CPagination(count($list));
				else $paginatorInfo = new CPagination($abstractInfo[$e]);
			}
			else $paginatorInfo = new CPagination(array_sum($abstractInfo));
		}
		$paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);

		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
		$this->render('list', array(
			'q' => $q,
			'items' => $didYouMean,
			'products' => $list,
			'abstractInfo'=>$abstractInfo,
			'paginatorInfo' => $paginatorInfo));
	}

	function isCode($q) {
		$code = array();
		if (ProductHelper::IsShelfId($q)) $code[] = 'stock_id';
		if (ProductHelper::IsEan($q)) $code[] = 'eancode';
		if (ProductHelper::IsIsbn($q)) $code[] = 'isbnnum';
		if (!preg_match("/[^a-z0-9-]/i", $q)&&preg_match("/\d/i", $q)) {
			$code[] = 'catalogue';
		}
		return $code;
	}

	function getByCode($code, $q) {
		foreach ($code as $codeName) {
			switch ($codeName) {
				case 'catalogue':
					$sql = ''.
//						'(select (220000000 + id) AS `id`, id `real_id`,22 AS `entity`,`in_stock` ,(case when ((`in_shop` > 0) and (`in_shop` < 6)) then (10 - `in_shop`) when (`in_shop` > 5) then 4 when (`econet_skip` > 0) then 3 else 0 end) AS `in_shop`,`avail_for_order` AS `avail`,`econet_skip`,`publisher_id`,`isbn`,`title_ru`,`title_en`,`title_rut`,`title_fi`,`stock_id`,`eancode`,`description_ru`,`description_en`,`description_fi`,`description_rut`,`year`,`media_id` AS `bindingid`, 1 as is_product from `music_catalog` where (catalogue = :q)) '.
//						'union all (select (300000000 + `id`) AS `id`,`id` AS `real_id`,30 AS `entity`,`in_stock`,(case when ((`in_shop` > 0) and (`in_shop` < 6)) then (10 - `in_shop`) when (`in_shop` > 5) then 4 else `in_shop` end) AS `in_shop`,`avail_for_order` AS `avail`,1 AS `econet_skip`,NULL AS `publisher_id`,NULL AS `isbn`,`title_ru`,`title_en`,`title_rut`,`title_fi`,`stock_id`,`eancode`,`description_ru`,`description_en`,`description_fi`,`description_rut`,0 AS `year`,0 AS `bindingid`, 1 as is_product from `pereodics_catalog` where (issn = :q) or (`index` = :q)) '.
						'(select `id`, 22 AS `entity` from `music_catalog` where (catalogue = :q)) '.
						'union all (select `id`,30 AS `entity` from `pereodics_catalog` where (issn = :q) or (`index` = :q)) '.
					'';
					$items = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q'=>$q));
					if (!empty($items)) {
						$product = array();
						foreach ($items as $item) {
							if (empty($product['e' . $item['entity']])) $product['e' . $item['entity']] = array();
							$product['e' . $item['entity']][] = $item['id'];
						}
						return SearchHelper::ProcessProducts2($product, false);
//						return $items;
					}
					break;
				default:
					$q = preg_replace("/\D/iu", '', $q);
					$this->_search->resetCriteria();
					$this->_search->SetFilter($codeName, array($q));
					$find = $this->_search->query('', 'products');
					if (!empty($find)&&!empty($find['matches'])) {
						$product = SearchHelper::ProcessProducts($find);
						return SearchHelper::ProcessProducts2($product, false);
					}
					break;
			}
		}
		$sql = ''.
			'select `id`, 10 AS `entity` '.
			'from `books_catalog` '.
			'where (isbn2 like :q) '.
				'or (isbn3 like :q) '.
				'or (isbn4 like :q) '.
				'or (isbn_wrong like :qPr) '.
		'';
		$items = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q'=>$q, ':qPr'=>'%' . $q . '%'));
		if (!empty($items)) {
			$product = array();
			foreach ($items as $item) {
				if (empty($product['e' . $item['entity']])) $product['e' . $item['entity']] = array();
				$product['e' . $item['entity']][] = $item['id'];
			}
			return SearchHelper::ProcessProducts2($product, false);
//						return $items;
		}
		return array();

	}

	function getEntitys($query) {
		$this->_search->resetCriteria();
		$filters = array();
		$avail = $this->GetAvail(1);
		if ($avail) $filters['avail'] = 1;

		if (!empty($filters)) {
			foreach ($filters as $name => $value) {
				$this->_search->SetFilter($name, array($value));
			}
		}

		$groupby = array(
			'field'=>'entity',
			'mode'=>SPH_GROUPBY_ATTR,
			'order'=>'@group desc',
		);
		if ($this->_exactMatch) {
			$q = '@(title_ru,title_rut,title_en,title_fi) "' . $this->_search->EscapeString($query) . '"';
//			$this->_search->SetMatchMode(SPH_MATCH_ALL);
			$res = $this->_search->groupby($groupby)->query($q, 'products_no_morphy');
		}
		else {
			$q = '@* ' . $this->_search->EscapeString($query);
			$res = $this->_search->groupby($groupby)->query($q, 'products');
		}

		if (empty($res['matches'])) return array();

		$result = array();
		foreach (Entity::GetEntitiesList() as $entity=>$set) $result[$entity] = false;

		foreach ($res['matches'] as $data) {
			//audio не показываем
			if (!empty($data['attrs']['@count'])&&($data['attrs']['@groupby'] != 20)) {
			    $result[$data['attrs']['@groupby']] = $data['attrs']['@count'];
		    }
		}
		return array_filter($result);
	}

	function getDidYouMean($q) {
		$authors = $this->_getAuthors($q);
		$publishers = $this->_getPublishers($q);
		$categories = $this->_getCategories($q);
		return array_merge($authors, $categories, $publishers);
	}

	function getListExactMatch($query, $page, $pp) {
		$this->_search->resetCriteria();
		$this->_search->SetLimits(($page-1)*$pp, $pp);

		$filters = array();
		$avail = $this->GetAvail(1);
		if ($avail) $filters['avail'] = 1;
		$e = (int) Yii::app()->getRequest()->getParam('e');
		if (Entity::IsValid($e)) $filters['entity'] = $e;

		if (!empty($filters)) {
			foreach ($filters as $name => $value) {
				$this->_search->SetFilter($name, array($value));
			}
		}

		$q = '@(title_ru,title_rut,title_en,title_fi) "' . $this->_search->EscapeString($query) . '"';

//		$this->_search->SetMatchMode(SPH_MATCH_ALL);
//		$this->_search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC, in_shop DESC");
		$this->_search->SetSortMode(SPH_SORT_EXTENDED, "in_shop DESC");


		$find = $this->_search->query($q, 'products_no_morphy');
//		Debug::staticRun(array($q, $find));
		if (empty($find)) return array();
		$this->_exactMatch = true;

		$product = SearchHelper::ProcessProducts($find);
		$prepareData =  SearchHelper::ProcessProducts2($product, false);
		$result = array();
		foreach ($find['matches'] as $id => $data) {
			$attr = $data['attrs'];
			$key = $attr['entity'] . '-' . $attr['real_id'];
			if (!empty($prepareData[$key])) $result[$key] = $prepareData[$key];
		}

		return $result;
	}

	function getList($query, $page, $pp) {
		$this->_search->resetCriteria();
		$this->_search->SetLimits(($page-1)*$pp, $pp);

		$filters = array();
		$avail = $this->GetAvail(1);
		if ($avail) $filters['avail'] = 1;
		$e = (int) Yii::app()->getRequest()->getParam('e');
		if (Entity::IsValid($e)) $filters['entity'] = $e;

		if (!empty($filters)) {
			foreach ($filters as $name => $value) {
				$this->_search->SetFilter($name, array($value));
			}
		}

		$q = '@* ' . $this->_search->EscapeString($query);

		$this->_search->SetFieldWeights(array(
			'title_ru'=>1000,
			'title_rut'=>1000,
			'title_en'=>1000,
			'title_fi'=>1000,
			'title_eco'=>1000,
			'description_ru'=>100,
			'description_rut'=>100,
			'description_en'=>100,
			'description_fi'=>100,
			'description_de'=>100,
			'description_fr'=>100,
			'description_es'=>100,
			'description_se'=>100,
		));
		$this->_search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC, dictionary_position ASC, spec_position ASC, entity_position ASC, time_position ASC");
//		$this->_search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC");
		$this->_search->setRankingMode(SPH_RANK_EXPR, 'sum((4*lcs+2*(min_hit_pos==1)+exact_hit*100)*user_weight)*1000+bm25');
//		$this->_search->SetRankingMode(SPH_RANK_EXPR,'sum(lcs*user_weight+exact_hit)*1000+bm25');


		$find = $this->_search->query($q, 'products');
		if (empty($find)) return array();

		$product = SearchHelper::ProcessProducts($find);
		$prepareData =  SearchHelper::ProcessProducts2($product, false);
		$result = array();
		foreach ($find['matches'] as $id => $data) {
			$attr = $data['attrs'];
			$key = $attr['entity'] . '-' . $attr['real_id'];
			if (!empty($prepareData[$key])) $result[$key] = $prepareData[$key];
		}

		return $result;
	}

	/** функция проверяет найденное в title_. Если не нашло, то в результирующий массив добавляет inDescription
	 * @param $list
	 * @param $query
	 * @param int $countChars
	 * @return mixed
	 */
	function inDescription($list, $query, $countChars = 100) {
		foreach ($list as $k=>$item) {
			$isTitle = false;

			foreach (Yii::app()->params['ValidLanguages'] as $lang) {
				if (empty($item['title_' . $lang])) continue;

				if (mb_strpos($item['title_' . $lang], $query, null, 'utf-8') !== false) {
					$isTitle = true;
					break;
				}
			}
			if ($isTitle) continue;

			foreach (Yii::app()->params['ValidLanguages'] as $lang) {
				if (empty($item['description_' . $lang])) continue;

				if (($pos = mb_strpos($item['description_' . $lang], $query, null, 'utf-8')) !== false) {
					$list[$k]['inDescription'] = '';
					$posStart = 0;
					if ($pos >= ceil($countChars/2)) {
						$list[$k]['inDescription'] .= '... ';
						$posStart = mb_strpos($item['description_' . $lang], ' ', $pos - ceil($countChars/2), 'utf-8');
					}
					$posEnd = mb_strpos($item['description_' . $lang], ' ', $pos + mb_strlen($query, 'utf-8'), 'utf-8');
					$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], $posStart, ($pos-$posStart), 'utf-8');
					$list[$k]['inDescription'] .= '<span class="title__bold">' . $query . '</span>';
					$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], ($pos+mb_strlen($query, 'utf-8')), ($posEnd - ($pos+mb_strlen($word, 'utf-8'))), 'utf-8');

					if ($posEnd < mb_strlen($item['description_' . $lang])) $list[$k]['inDescription'] .= ' ...';
					break;
				}
			}

		}
		return $list;

		/*		горе от ума
		foreach ($list as $k=>$item) {
					$isTitle = false;
					$words = array_filter(preg_split("/\W+/ui", $query), function($word){ return mb_strlen($word, 'utf-8') > 2; });
					if (empty($words)) continue;

					foreach ($words as $word) {
						foreach (Yii::app()->params['ValidLanguages'] as $lang) {
							if (empty($item['title_' . $lang])) continue;

							if (mb_strpos($item['title_' . $lang], $word, null, 'utf-8') !== false) {
								$isTitle = true;
								break 2;
							}
						 }
					}
					if ($isTitle) continue;

					foreach ($words as $word) {
						foreach (Yii::app()->params['ValidLanguages'] as $lang) {
							if (empty($item['description_' . $lang])) continue;

							if (($pos = mb_strpos($item['description_' . $lang], $word, null, 'utf-8')) !== false) {
								$list[$k]['inDescription'] = '';
								$posStart = 0;
								if ($pos >= ceil($countChars/2)) {
									$list[$k]['inDescription'] .= '... ';
									$posStart = mb_strpos($item['description_' . $lang], ' ', $pos - ceil($countChars/2), 'utf-8');
								}
								$posEnd = mb_strpos($item['description_' . $lang], ' ', $pos + mb_strlen($word, 'utf-8'), 'utf-8');
								$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], $posStart, ($pos-$posStart), 'utf-8');
								$list[$k]['inDescription'] .= '<span class="title__bold">' . $word . '</span>';
								$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], ($pos+mb_strlen($word, 'utf-8')), ($posEnd - ($pos+mb_strlen($word, 'utf-8'))), 'utf-8');

								if ($posEnd < mb_strlen($item['description_' . $lang])) $list[$k]['inDescription'] .= ' ...';
								break 2;
							}
						}
					}

				}
		*/
	}

	private function _queryIndex($query, $index, $limit) {
		$result = array();
		$query = trim($query);
		$queryWords = preg_split("/\W+/ui", $query);
		$queryWords = array_filter($queryWords);
		if (count($queryWords) == 1) {
			//если одно слово, то ищем то что начинается
			$oneWordQuery = $queryWords[0];
			$oneWordQuery = preg_replace("/\d/ui", '', $oneWordQuery);
			if (!empty($oneWordQuery)) {
				$oneWordQuery = '^' . $oneWordQuery . '*';
				$result = $this->_querySimple($oneWordQuery, 'authors', 0);
			}
		}

		//ищем по всей фразе
		$resultAllWords = $this->_querySimple(implode(' ', $queryWords), $index, $limit);
		if (!empty($resultAllWords)) return $result + $resultAllWords;

		//по отдельным словам
		$pre = SearchHelper::BuildKeywords($query, $index);
//		var_dump($pre);
		$resultWord = array();
		foreach ($pre['Queries'] as $query) {
			if (empty($query)) continue;

			$resultWord = $this->_querySimple($query, $index, $limit);
			if (!empty($resultWord)) break;
		}
		return $result + $resultWord;
	}

	private function _querySimple($query, $index, $limit) {
		$this->_search->resetCriteria();
		if ($limit > 0) $this->_search->SetLimits(0, $limit);
		$res = $this->_search->query($query, $index);

		$result = array();
		if ($res['total_found'] > 0) {
			foreach ($res['matches'] as $key => $match) {
				$d = array('key' => $key);
				$attrs = $match['attrs'];
				foreach ($attrs as $name => $value) {
					$d[$name] = $value;
				}
				$result[$key] = $d;
			}
		}
		return $result;
	}

	protected function _getAuthors($query) {
		$result = $this->_queryIndex($query, 'authors', 0);

		if (empty($result)) return array();

		$limit = 3;
		$ids = array();
		$findAuthor = false;
		foreach ($result as $id=>$item) {
			if ($item['is_10_author'] > 0) {
				$ids[$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>10);
				$findAuthor = true;
			}
			elseif ($item['is_22_author'] > 0) {
				$ids[$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>22);
				$findAuthor = true;
			}
			elseif ($item['is_24_author'] > 0) {
				$ids[$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>24);
				$findAuthor = true;
			}
			elseif ($item['is_40_actor'] > 0) $ids[$id] = array('role_id'=>Person::ROLE_ACTOR, 'entity'=>40);
			elseif ($item['is_40_director'] > 0) $ids[$id] = array('role_id'=>Person::ROLE_DIRECTOR, 'entity'=>40);
			elseif ($item['is_22_performer'] > 0) $ids[$id] = array('role_id'=>Person::ROLE_PERFORMER, 'entity'=>22);
			if (count($ids) >= $limit) break;
		}
		if (empty($findAuthor)&&(count($ids) < $limit)) {
			$ids10 = $this->_isAuthors(10, array_keys($result));
			$ids22 = $this->_isAuthors(22, array_keys($result));
			$ids24 = $this->_isAuthors(24, array_keys($result));
			foreach ($result as $id=>$item) {
				if (in_array($id, $ids10)) $ids[$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>10, 'itemsAvail'=>$item['is_10_author']);
				elseif (in_array($id, $ids22)) $ids[$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>22, 'itemsAvail'=>$item['is_22_author']);
				elseif (in_array($id, $ids24)) $ids[$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>24, 'itemsAvail'=>$item['is_24_author']);
				if (count($ids) >= $limit) break;
			}
		}
		if (empty($ids)) return array();

		$roles = array();
		foreach($ids as $id=>$r) {
			$roles[$r['role_id']][$id] = array('real_id'=>$id, 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$id]['itemsAvail'] = $r['itemsAvail'];
		}
		$ids = array_keys($ids);
		$result = SearchHelper::ProcessPersons($roles, $ids, array(), $this->GetAvail(1));
		return $result;
	}

	protected function _isAuthors($entity, $ids) {
		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItemsAuthors = $entityParam['author_table'];
		$sql = 'select author_id from ' . $tableItemsAuthors . ' where (author_id in (' . implode(',',$ids) . ')) group by author_id';
		return Yii::app()->db->createCommand($sql)->queryColumn();
	}

	protected function _getPublishers($query) {
		$result = $this->_queryIndex($query, 'publishers', 0);
		if (empty($result)) return array();

		$limit = 3;
		$ids = array();
		foreach (Entity::GetEntitiesList() as $entity=>$set) {
			if (Entity::checkEntityParam($entity, 'publisher')) {
				$sql = ''.
					'select publisher_id '.
					'from ' . $set['site_table'] . ' '.
					'where (publisher_id in (' . implode(',',array_keys($result)) . ')) '.
					'group by publisher_id '.
					'order by field(publisher_id, ' . implode(',',array_keys($result)) . ') '.
					'limit ' . ($limit - count($ids)) .
				';';
				foreach (Yii::app()->db->createCommand($sql)->queryColumn() as $publisher) {
					$ids[$publisher] = $entity;
				}
			}
			if (count($ids) >= $limit) break;
		}
		if (empty($ids)) return array();

		$idList = implode(', ', array_keys($ids));

		$sql = ''.
			'SELECT * '.
			'FROM all_publishers AS p '.
			'WHERE p.id IN (' . $idList . ') '.
			'ORDER BY FIELD(id, ' . $idList . ') '.
		'';

		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$ret = array();

		foreach ($rows as $row) {
			$row['entity'] = $ids[$row['id']];
			$item = array();
			$itemTitle = ProductHelper::GetTitle($row);
			$title = Entity::GetTitle($row['entity']) . '; ' . sprintf(Yii::app()->ui->item('PUBLISHED_BY'), '<b>' . $itemTitle . '</b>');

			$urlParams = array(
				'entity' => Entity::GetUrlKey($row['entity']),
				'title' => ProductHelper::ToAscii($itemTitle),
				'pid' => $row['id']
			);
			if (!$this->GetAvail(1)) $urlParams['avail'] = 0;
			$item['url'] = Yii::app()->createUrl('entity/bypublisher', $urlParams);
			$item['title'] = $title;
			$item['orig_data'] = $row;
			$ret[] = $item;
		}

		return $ret;
	}

	protected function _getCategories($query) {
		$result = $this->_queryIndex($query, 'categories', 0);
		if (empty($result)) return array();

		$where = array();
		foreach($result as $cat) {
			//audio не показываем
			if (empty($cat['entity'])||empty($cat['real_id'])||($cat['entity'] == 20)) continue;
			
			if (empty($where[$cat['entity']])) $where[$cat['entity']] = array();
			$where[$cat['entity']][] = '((entity='.intVal($cat['entity']).') AND (real_id='.intVal($cat['real_id']).'))';
		}
		if(empty($where)) return array();

		$i = 0;
		$condition = array();
		do {
			foreach ($where as $e=>$cond) {
				$condition[] = array_shift($cond);
				if (empty($cond)) unset($where[$e]);
				$i++;
			}
		} while (($i < 3)&&!empty($where));

		if(empty($condition)) return array();



		$sql = 'SELECT * FROM all_categories WHERE '.implode(' OR ', $condition);
		$rows = Yii::app()->db->createCommand($sql)->queryAll();

		$ret = array();
		foreach ($rows as $item) {
			$itemTitle = ProductHelper::GetTitle($item);
			$row = array();

			$urlParams = array(
				'cid' => $item['real_id'],
				'title' => ProductHelper::ToAscii($itemTitle),
				'entity' => Entity::GetUrlKey($item['entity'])
			);
			if (!$this->GetAvail(1)) $urlParams['avail'] = 0;
			$row['url'] = Yii::app()->createUrl('entity/list', $urlParams);
			$row['title'] = Entity::GetTitle($item['entity']) . ' - ' . Yii::app()->ui->item('Related categories') . ': <b>' . $itemTitle . '</b>';
			$row['is_product'] = false;
			$row['orig_data'] = $item;
			$ret[] = $row;
		}
		return $ret;
	}


	public function afterAction($action) {
		parent::afterAction($action);
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		$e = (int) Yii::app()->getRequest()->getParam('e');
		SearchHelper::LogSearch(Yii::app()->user->id, $q, array('e' => $e, 'page' => $this->_getNumPage()), (int)$this->_counts);
	}

	function beforeAction($action) {
		$result = parent::beforeAction($action);
		if ($result) $this->_search = SearchHelper::Create();
		return $result;
	}



	private function _getNumPage() {
		return min(100000, max((int) Yii::app()->getRequest()->getParam('page'), 1));
	}

	private function _viewEmpty($q) {
		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
		$this->render(
			'not_found',
			array(
				'result' => array(),
				'q' => $q,
				'products' => array(),
				'paginatorInfo' => new CPagination(0),
			)
		);
	}

}
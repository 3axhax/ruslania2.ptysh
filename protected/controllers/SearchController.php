<?php
/*Created by Кирилл (09.06.2018 23:37)*/
class SearchController extends MyController {
	//количество в результате поиска
	private $_counts = null;

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
			$abstractInfo = $this->getEntitys($q);
			$didYouMean = $this->getDidYouMean($q);
			$list = $this->getList($q, $page, Yii::app()->params['ItemsPerPage']);
			$list = $this->inDescription($list, $q);
		}

		if (empty($abstractInfo)) $paginatorInfo = new CPagination(count($list));
		else $paginatorInfo = new CPagination(array_sum($abstractInfo));
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
		if (!preg_match("/[^a-z0-9-]/i", $q)) {
			$code[] = 'catalogue';
		}
		return $code;
	}

	function getByCode($code, $q) {
		foreach ($code as $codeName) {
			switch ($codeName) {
				case 'catalogue':
					$sql = 'select * from music_catalog where (catalogue = :q) limit 1';
					$item = Yii::app()->db->createCommand($sql)->queryRow(true, array(':q'=>$q));
					if (!empty($item)) {
						$item['is_product'] = true;
						$item['entity'] = 22;
						return array('22-' . $item['id']=>$item);
					}
					break;
				default:
					$q = preg_replace("/\D/iu", '', $q);
					$this->_search->resetCriteria();
					$this->_search->SetFilter($codeName, array($q));
					$find = $this->_search->query('', 'products');
					if (!empty($find)) {
						$product = SearchHelper::ProcessProducts($find);
						return SearchHelper::ProcessProducts2($product, false);
					}
					break;
			}
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

		$q = '@* ' . $this->_search->EscapeString($query);
		$groupby = array(
			'field'=>'entity',
			'mode'=>SPH_GROUPBY_ATTR,
			'order'=>'@group desc',
		);
		$res = $this->_search->groupby($groupby)->query($q, 'products');
		if (empty($res['matches'])) return array();

		$result = array();
		foreach (Entity::GetEntitiesList() as $entity=>$set) $result[$entity] = false;

		foreach ($res['matches'] as $data) {
		    if (!empty($data['attrs']['@count'])) {
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

//		$this->_search->SetSortMode(SPH_SORT_ATTR_DESC, "in_shop");
//		$this->_search->SetSortMode(SPH_SORT_RELEVANCE);
//		$this->_search->SetSortMode(SPH_MATCH_EXTENDED2);
		$this->_search->SetFieldWeights(array(
			'title_ru'=>10000000,
			'title_rut'=>10000000,
			'title_en'=>10000000,
			'title_fi'=>10000000,
			'title_eco'=>10000000,
			'description_ru'=>1000000,
			'description_rut'=>1000000,
			'description_en'=>1000000,
			'description_fi'=>1000000,
			'description_de'=>1000000,
			'description_fr'=>1000000,
			'description_es'=>1000000,
			'description_se'=>1000000,
		));
//		$this->_search->setRankingMode(SPH_RANK_EXPR, 'sum((4*lcs+2*(min_hit_pos==1)+exact_hit*100)*user_weight)*1000+bm25');
		$this->_search->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC, in_shop DESC");


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
		if (!preg_match("/[^\w ,.]/ui", $query)) {
			$result = $this->_querySimple($query, $index, $limit);
			if (!empty($result)) return $result;
		}

		$pre = SearchHelper::BuildKeywords($query, $index);
		$result = array();
		foreach ($pre['Queries'] as $query) {
			if (empty($query)) continue;

			$result = $this->_querySimple($query, $index, $limit);
			if (!empty($result)) break;
		}
		return $result;
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
		$result = array();
		$query = trim($query);
		if (!mb_strpos($query, ' ', null, 'utf-8')) {
			$oneWordQuery = preg_replace("/\W/", '', $query);
			$oneWordQuery = preg_replace("/\d/", '', $oneWordQuery);
			if (!empty($oneWordQuery)) {
				$oneWordQuery = '^' . $oneWordQuery . '*';
				$result = $this->_querySimple($oneWordQuery, 'authors', 0);
			}

		}
		$result = array_merge($result, $this->_queryIndex($query, 'authors', 0));

		if (empty($result)) return array();

		$limit = 3;
		$authorsWithItems = array();
		foreach (Entity::GetEntitiesList() as $entity=>$set) {
			if (Entity::checkEntityParam($entity, 'authors')) {
				$ids = array();
				foreach ($result as $id=>$item) {
					if ($entity == $item['entity']) $ids[$item['real_id']] = $id;
				}
				if (!empty($ids)) {
					$sql = ''.
						'select author_id '.
						'from ' . $set['author_table'] . ' '.
						'where (author_id in (' . implode(',',array_keys($ids)) . ')) '.
						'group by author_id '.
						'order by field(author_id, ' . implode(',',array_keys($ids)) . ') '.
						'limit ' . ($limit - count($authorsWithItems)) .
					';';
					foreach (Yii::app()->db->createCommand($sql)->queryColumn() as $author) {
						$authorsWithItems[$ids[$author]] = $result[$ids[$author]];
					}
				}
			}
			if (count($authorsWithItems) >= $limit) break;
		}
		if (empty($authorsWithItems)) return array();

		$roles = array();
		$ids = array();
		foreach($authorsWithItems as $r) {
			$roles[$r['role_id']][$r['real_id']] = $r;
			$ids[] = $r['real_id'];
		}

		$ids = array_unique($ids);
		if (empty($ids)) return array();

		$result = SearchHelper::ProcessPersons($roles, $ids, array(), $this->GetAvail(1));
		return $result;
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
			$where[] = '((entity='.intVal($cat['entity']).') AND (real_id='.intVal($cat['real_id']).'))';
		}

		if(empty($where)) return array();

		$sql = 'SELECT * FROM all_categories WHERE '.implode(' OR ', $where);
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
			'search',
			array(
				'result' => array(),
				'q' => $q,
				'products' => array(),
				'paginatorInfo' => new CPagination(0),
			)
		);
	}

	private function _filterManyInShop($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop > 5) && ($avail > 0);
	}

	private function _filterFewInShop($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop <= 5) && ($avail > 0);
	}

	private function _filterUnderOrder($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop === 0) && ($avail > 0);
	}

	private function _filterNotAvailable($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop === 0) && ($avail === 0);
	}

}
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

		$page = $this->_getNumPage();

		$this->getEntitys($q);
		$this->getDidYouMean($q);
		$this->getList($q, $page, Yii::app()->params['ItemsPerPage']);

		$abstractInfo = $this->getEntitys($q);

		$paginatorInfo = new CPagination(array_sum($abstractInfo));
		$paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);

		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
		$this->render('list', array('q' => $q, 'items' => $this->getDidYouMean($q),
			'products' => $this->getList($q, $page, Yii::app()->params['ItemsPerPage']),
			'abstractInfo'=>$abstractInfo,
			'paginatorInfo' => $paginatorInfo));
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

		$q = '@(title_ru,title_rut,title_fi,title_en) ' . $this->_search->EscapeString($query);
		$groupby = array(
			'field'=>'entity',
			'mode'=>SPH_GROUPBY_ATTR,
			'order'=>'@group desc',
		);
		$res = $this->_search->groupby($groupby)->query($q, 'products');
		if (empty($res['matches'])) return array();

		$result = array();
		foreach ($res['matches'] as $data) {
		    if (!empty($data['attrs']['@count'])) {
			    $result[$data['attrs']['@groupby']] = $data['attrs']['@count'];
		    }
		}
		return $result;
	}

	function getDidYouMean($query) {
		$authors = $this->_getAuthors($q);
		$publishers = $this->_getPublishers($q);
		return array_merge($authors, $publishers);
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

		$q = '@(title_ru,title_rut,title_fi,title_en)  ' . $this->_search->EscapeString($query);

		$this->_search->SetSortMode(SPH_SORT_ATTR_DESC, "in_shop");
		$res = $this->_search->query($q, 'products');
		$result = SearchHelper::ProcessProducts($res);
		return $result;
	}

	private function _queryIndex($query, $index, $limit) {
		$pre = SearchHelper::BuildKeywords($query, $index);
		$result = array();
		foreach ($pre['Queries'] as $query) {
			if (empty($query)) continue;

			$this->_search->resetCriteria();
			$this->_search->SetLimits(0, 3);
			if (!empty($filters)) {
				foreach ($filters as $name => $value) {
					$this->_search->SetFilter($name, array($value));
				}
			}
			$res = $this->search->query($query, $index);

			if ($res['total_found'] > 0) {
				foreach ($res['matches'] as $key => $match) {
					$d = array('key' => $key);
					$attrs = $match['attrs'];
					foreach ($attrs as $name => $value) {
						$d[$name] = $value;
					}
					$result[$key] = $d;
				}
				break; // если нашли по какому-то запросу, то ниже уже не идем
			}
		}
		return $result;
	}

	protected function _getAuthors($query) {
		$result = $this->_queryIndex($query, 'authors', 3);
		if (empty($result)) return array();

		$roles = array();
		$ids = array();
		foreach($result as $r) {
			$roles[$r['aentity']][$r['real_id']] = $r;
			$ids[] = $r['real_id'];
		}

		$ids = array_unique($ids);
		if (empty($ids)) return array();

		$result = SearchHelper::ProcessPersons($roles, $ids);
		return $result;
	}

	protected function _getPublishers($query) {
		$result = $this->_queryIndex($query, 'publishers', 3);
		if (empty($result)) return array();

		$ids = array_keys($result);
		$idList = implode(', ', $ids);

		$sql = ''.
			'SELECT * '.
			'FROM all_publishers AS p '.
				'JOIN all_publishers_entity AS pe ON (p.id=pe.publisher) '.
			'WHERE id IN (' . $idList . ') '.
			'ORDER BY FIELD(id, ' . $idList . ') '.
		'';

		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$ret = array();

		foreach ($rows as $row) {
			$item = array();
			$itemTitle = ProductHelper::GetTitle($row);
			$title = Entity::GetTitle($row['entity']) . '; ' . sprintf(Yii::app()->ui->item('PUBLISHED_BY'), '<b>' . $itemTitle . '</b>');

			$item['is_product'] = false;
			$item['url'] = Yii::app()->createUrl('entity/bypublisher',
				array('entity' => Entity::GetUrlKey($row['entity']),
					'title' => ProductHelper::ToAscii($itemTitle),
					'pid' => $row['id']
				));
			$item['title'] = $title;
			$item['orig_data'] = $row;
			$ret[] = $item;
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
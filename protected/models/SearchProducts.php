<?php
/*Created by Кирилл (18.02.2019 20:22)*/

class SearchProducts {
	private $_maxMatches = 100000;//это количество сфинкс перебирает в индексах
	private $_avail, $_eid = 0;
	private $_ranker = 'sph04';

	/**
	 * @var DGSphinxSearch
	 */
	private $_search;

	function __construct($avail, $eid = 0) {
		$this->_avail = $avail;
		if (Entity::IsValid($eid)) $this->_eid = $eid;
		$this->_search = SearchHelper::Create();
	}

	function isCode($q) {
		$code = array();
		if (ProductHelper::IsShelfId($q)) $code[] = 'stock_id';
		if (ProductHelper::IsEan($q)) $code[] = 'eancode';
		if (ProductHelper::IsIsbn($q)) $code[] = 'isbnnum';
		if ((mb_strlen($q, 'utf-8') > 5)&&!preg_match("/[^a-z0-9-]/i", $q)&&preg_match("/\d/i", $q)) {
			$code[] = 'catalogue';
		}
		return $code;
	}

	function getByPath($q) {
		$request = new MyRefererRequest();
		$request->setFreePath($q);
		//$request->getParams();//здесь $entity (текстовый), id и другие параметры из адреса referer
		$refererRoute = Yii::app()->getUrlManager()->parseUrl($request);
		$params = $request->getParams();
		if (empty($params)) array();
		$entity = 0;
		if (!empty($params['entity'])) $entity = Entity::ParseFromString($params['entity']);
		if (empty($entity)) return array();
		if (empty($params['id'])) return array();
		return SearchHelper::ProcessProducts2(array('e' . $entity=>array($params['id'])), false);
	}


	function getByCode($code, $q) {
		foreach ($code as $codeName) {
			switch ($codeName) {
				case 'catalogue':
					$sql = ''.
						'(select `id`, 22 AS `entity` from `music_catalog` where (catalogue = :q)) '.
						'union all '.
						'(select `id`,30 AS `entity` from `pereodics_catalog` where (issn = :q) or (`index` = :q)) '.
					'';
					$items = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q'=>$q));
					if (!empty($items)) {
						$product = array();
						foreach ($items as $item) {
							if (empty($product['e' . $item['entity']])) $product['e' . $item['entity']] = array();
							$product['e' . $item['entity']][] = $item['id'];
						}
						return SearchHelper::ProcessProducts2($product, false);
					}
					break;
				default:
					$qCode = preg_replace("/\D/iu", '', $q);
					$sql = ''.
						'select id, entity, real_id '.
						'from all_items_with_morphy ' .
						'where (' . $codeName . ' = ' . $qCode . ') '.
						'order by position asc, time_position asc '.
						'option ranker=none '.
					'';
					Debug::staticRun(array($sql));
					$items = SphinxQL::getDriver()->multiSelect($sql);
					if (!empty($items)) {
						$product = array();
						foreach ($items as $item) {
							if (empty($product['e' . $item['entity']])) $product['e' . $item['entity']] = array();
							$product['e' . $item['entity']][] = $item['real_id'];
						}
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
		}
		return array();

	}

	function getList($q, $page, $pp, $eid = 0) {
		$firstUnion = true;
		$math = explode('|', $this->getMath($q));

//		if (count($math) > 1) {
			$sql = ''.
				'select t.entity, t.real_id '.
				'from (';
			foreach (array('_se_avail_items_without_morphy', '_se_product_authors', '_se_avail_items_with_morphy') as $seTable) {
				foreach ($math as $m) {
					if ($firstUnion) $firstUnion = false;
					else $sql .= 'union ';
					$spxCond = array($m);
					$spxCond['ranker'] = 'ranker=' . $this->_ranker;
					$spxCond['limit'] = 'limit=100000';
					$spxCond['maxmatches'] = 'maxmatches=100000';
					$sql .= '(SELECT entity, real_id '.
						'FROM `' . $seTable . '` '.
						'WHERE (query=' . SphinxQL::getDriver()->mest(implode(';', $spxCond)) . ') '.
						'order by position, time_position) '.
					'';
					if ($seTable === '_se_avail_items_without_morphy') break;
				}
			}
			$sql .= ') t '.
				'limit ' . ($page-1)*$pp . ', ' . $pp . ' '.
			'';
			$find = Yii::app()->db->createCommand($sql)->queryAll();;
/*		}
		else {
			$condition = $join = [];
			$condition['morphy_name'] = 'match(' . SphinxQL::getDriver()->mest($this->getMath($q)) . ')';
			if (!empty($eid)) $condition['entity'] = '(entity = ' . (int) $eid . ')';
			$sql = ''.
				'select id, entity, real_id, position '.
				'from ' . implode(',',$this->_getTablesForList()) . ' ' .
				'where ' . implode(' and ', $condition) . ' '.
				'order by position asc, time_position asc '.
				'limit ' . ($page-1)*$pp . ', ' . $pp . ' '.
				'option ranker=' . $this->_ranker . ', max_matches=' . $this->_maxMatches . ' '.
			'';
			$find = SphinxQL::getDriver()->multiSelect($sql);
		}*/
		Debug::staticRun(array($sql, $find));
		if (empty($find)) return array();

		$product = array();;
		foreach ($find as $data) $product['e'.$data['entity']][] = $data['real_id'];
		$prepareData =  SearchHelper::ProcessProducts2($product, false);
		$result = array();
		foreach ($find as $data) {
			$key = $data['entity'] . '-' . $data['real_id'];
			if (!empty($prepareData[$key])) {
				$prepareData[$key]['position'] = $data['position']; //надо, чтобы потом определить из описания или нет
				$result[$key] = $prepareData[$key];
			}
		}
		return $result;
	}

	function getIds($q, $page, $pp, $eid) {
		if (empty($eid)) return array();

		$condition = $join = [];
		$condition['morphy_name'] = 'match(' . SphinxQL::getDriver()->mest($this->getMath($q)) . ')';
		$condition['entity'] = '(entity = ' . (int) $eid . ')';
		$sql = ''.
			'select real_id '.
			'from ' . implode(',',$this->_getTablesForList()) . ' ' .
			'where ' . implode(' and ', $condition) . ' '.
			'order by position asc, time_position asc '.
			'limit ' . ($page-1)*$pp . ', ' . $pp . ' '.
			'option ranker=' . $this->_ranker . ', max_matches=' . $this->_maxMatches . ' '.
		'';
		return SphinxQL::getDriver()->queryCol($sql);
	}

	function getDidYouMean($q) {
		$authors = $this->_getAuthors($q);
		foreach ($authors as $i=>$item) $authors[$i]['didYouMeanType'] = 'authors';
		$publishers = $this->_getPublishers($q);
		foreach ($publishers as $i=>$item) $publishers[$i]['didYouMeanType'] = 'publishers';
		$categories = $this->_getCategories($q);
		foreach ($categories as $i=>$item) $categories[$i]['didYouMeanType'] = 'categories';
		$series = $this->_getSeries($q);
		foreach ($series as $i=>$item) $series[$i]['didYouMeanType'] = 'series';
		return array_merge($authors, $categories, $publishers, $series);
	}

	function getEntitys($query) {
/*		$sql = ''.
			'select entity, count(distinct real_id) counts './/', GROUP_CONCAT(real_id) '.
			'from ' . implode(',',$this->_getTablesForList()) . ' ' .
			'where (match(' . SphinxQL::getDriver()->mest($this->getMath($query)) . ')) '.
			'group by entity '.
//			'order by position asc '.
			'option ranker=' . $this->_ranker . ', max_matches=' . $this->_maxMatches . ' '.
		'';
		$find = SphinxQL::getDriver()->multiSelect($sql);*/

		$firstUnion = true;
		$math = explode('|', $this->getMath($query));

		$sql = ''.
			'select t.entity, count(*) counts '.
			'from (';
		foreach (array('_se_avail_items_without_morphy', '_se_product_authors', '_se_avail_items_with_morphy') as $seTable) {
			foreach ($math as $m) {
				if ($firstUnion) $firstUnion = false;
				else $sql .= 'union ';
				$spxCond = array($m);
				$spxCond['ranker'] = 'ranker=sph04';
				$spxCond['limit'] = 'limit=100000';
				$spxCond['maxmatches'] = 'maxmatches=100000';
				$sql .= 'SELECT entity, real_id '.
					'FROM `' . $seTable . '` '.
					'WHERE (query=' . SphinxQL::getDriver()->mest(implode(';', $spxCond)) . ') '.
				'';
			}
		}
		$sql .= ') t '.
			'group by t.entity '.
		'';
/*		$spxCond = array($this->getMath($query));
		$spxCond['ranker'] = 'ranker=sph04';
		$spxCond['limit'] = 'limit=100000';
		$spxCond['maxmatches'] = 'maxmatches=100000';
		$sql = ''.
			'select t.entity, count(*) counts '.
			'from ('.
				'SELECT entity, real_id '.
				'FROM `_se_avail_items_without_morphy` '.
				'WHERE (query=' . SphinxQL::getDriver()->mest(implode(';', $spxCond)) . ') '.
				'union '.
				'SELECT entity, real_id '.
				'FROM `_se_product_authors` '.
				'WHERE (query=' . SphinxQL::getDriver()->mest(implode(';', $spxCond)) . ') '.
				'union '.
				'SELECT entity, real_id '.
				'FROM `_se_avail_items_with_morphy`'.
				'WHERE (query=' . SphinxQL::getDriver()->mest(implode(';', $spxCond)) . ') '.
			') t '.
			'group by t.entity '.
		'';*/
		$find = Yii::app()->db->createCommand($sql)->queryAll();;
		Debug::staticRun(array($sql, $find));

		$result = array();
		foreach (Entity::GetEntitiesList() as $entity=>$set) $result[$entity] = false;

		Debug::staticRun(array($sql, $find));
		foreach ($find as $data) {
			//audio не показываем
			if (!empty($data['entity'])&&($data['entity'] != 20)) {
				$result[$data['entity']] = $data['counts'];
			}
		}
		return array_filter($result);
	}


	function getListByDidYouMean($didYouMean) {
		$peoples = array(
			'authors'=>array(
				10=>array(),
				22=>array(),
				24=>array(),
			),
			'directors'=>array(
				40=>array(),
			),
			'actors'=>array(
				40=>array(),
			),
			'performers'=>array(
				22=>array(),
			),
		);
		$countAll = 0;
		foreach ($didYouMean as $i=>$item) {
			if ($item['didYouMeanType'] == 'authors') {
				if (!empty($item['orig_data']['is_10_author'])) {
					$sql = 'select t.id from books_catalog t join books_authors tA on (tA.book_id = t.id) and (tA.author_id = ' . (int) $item['real_id'] . ') where (t.avail_for_order = 1) limit 10';
					$peoples['authors'][10][$item['real_id']] = Yii::app()->db->createCommand($sql)->queryColumn();
					$countAll++;
				}
				if (!empty($item['orig_data']['is_22_author'])) {
					$sql = 'select t.id from music_catalog t join music_authors tA on (tA.music_id = t.id) and (tA.author_id = ' . (int) $item['real_id'] . ') where (t.avail_for_order = 1) limit 10';
					$peoples['authors'][22][$item['real_id']] = Yii::app()->db->createCommand($sql)->queryColumn();
					$countAll++;
				}
				if (!empty($item['orig_data']['is_24_author'])) {
					$sql = 'select t.id from soft_catalog t join soft_authors tA on (tA.soft_id = t.id) and (tA.author_id = ' . (int) $item['real_id'] . ') where (t.avail_for_order = 1) limit 10';
					$peoples['authors'][24][$item['real_id']] = Yii::app()->db->createCommand($sql)->queryColumn();
					$countAll++;
				}
				if (!empty($item['orig_data']['is_40_actor'])) {
					$sql = 'select t.id from video_catalog t join video_actors tA on (tA.video_id = t.id) and (tA.person_id = ' . (int) $item['real_id'] . ') where (t.avail_for_order = 1) limit 10';
					$peoples['actors'][40][$item['real_id']] = Yii::app()->db->createCommand($sql)->queryColumn();
					$countAll++;
				}
				if (!empty($item['orig_data']['is_40_director'])) {
					$sql = 'select t.id from video_catalog t join video_directors tA on (tA.video_id = t.id) and (tA.person_id = ' . (int) $item['real_id'] . ') where (t.avail_for_order = 1) limit 10';
					$peoples['directors'][40][$item['real_id']] = Yii::app()->db->createCommand($sql)->queryColumn();
					$countAll++;
				}
				if (!empty($item['orig_data']['is_22_performer'])) {
					$sql = 'select t.id from music_catalog t join music_performers tA on (tA.music_id = t.id) and (tA.person_id = ' . (int) $item['real_id'] . ') where (t.avail_for_order = 1) limit 10';
					$peoples['performers'][22][$item['real_id']] = Yii::app()->db->createCommand($sql)->queryColumn();
					$countAll++;
				}
			}
		}
		if (empty($countAll)) return array();

		$count = max(1, floor(10/$countAll));
		$ids = array(
			'e10' => array(),
			'e22' => array(),
			'e24' => array(),
			'e40' => array(),
		);
		foreach ($peoples['authors'][10] as $aId=>$items) {
			shuffle($items);
			$ids['e10'] = array_merge($ids['e10'], array_slice($items, 0, $count));
		}
		foreach ($peoples['authors'][22] as $aId=>$items) {
			shuffle($items);
			$ids['e22'] = array_merge($ids['e22'], array_slice($items, 0, $count));
		}
		foreach ($peoples['authors'][24] as $aId=>$items) {
			shuffle($items);
			$ids['e24'] = array_merge($ids['e24'], array_slice($items, 0, $count));
		}
		foreach ($peoples['actors'][40] as $aId=>$items) {
			shuffle($items);
			$ids['e40'] = array_merge($ids['e40'], array_slice($items, 0, $count));
		}
		foreach ($peoples['directors'][40] as $aId=>$items) {
			shuffle($items);
			$ids['e40'] = array_merge($ids['e40'], array_slice($items, 0, $count));
		}
		foreach ($peoples['performers'][22] as $aId=>$items) {
			shuffle($items);
			$ids['e22'] = array_merge($ids['e22'], array_slice($items, 0, $count));
		}
		if (empty($ids['e10'])) unset($ids['e10']);
		if (empty($ids['e22'])) unset($ids['e22']);
		if (empty($ids['e24'])) unset($ids['e24']);
		if (empty($ids['e40'])) unset($ids['e40']);
		return SearchHelper::ProcessProducts2($ids, false);
//		if (!empty($result)) $result = arr_sl
	}

	/** функция проверяет найденное в title_. Если не нашло, то в результирующий массив добавляет inDescription
	 * @param $list
	 * @param $query
	 * @param int $countChars
	 * @return mixed
	 */
	function inDescription($list, $query, $countChars = 100) {
		$fields = array('description_ru', 'description_rut', 'description_en', 'description_fi', );
		foreach ($list as $k=>$item) {
			if (in_array(mb_substr($item['position'], 0, 1, 'utf-8'), array(1, 3))) continue;//найдено по названию

			$text = '';
			foreach ($fields as $field) $text .= $item[$field] . ' ';
			$text = trim($text);
			if (empty($text)) continue;

			$list[$k]['inDescription'] = SphinxQL::getDriver()->snippet($text, $query);
		}
		return $list;
	}
	/** порядок в массиве важен для правильного результата
	 * @return array
	 */
	private function _getTablesForList() {
		if ($this->_avail) {
			return array(
				'avail_items_with_morphy',
				'product_authors',
				'avail_items_without_morphy',
			);
		}
		return array(
			'all_items_with_morphy',
			'product_authors',
			'all_items_without_morphy',
		);
	}

	private function _getTablesForEntity() {
		if ($this->_avail) {
			return array(
				'product_authors',
				'avail_items_without_morphy',
			);
		}
		return array(
			'product_authors',
			'all_items_without_morphy',
		);
	}

	private function _getAuthors($query) {
		$result = $this->_queryIndex($query, 'authors', 0);

		if (empty($result)) return array();

		$limit = 5;
		$peoples = array(
			'authors'=>array(
				10=>array(),
				22=>array(),
				24=>array(),
			),
			'actors'=>array(
				40=>array(),
			),
			'directors'=>array(
				40=>array(),
			),
			'performers'=>array(
				22=>array(),
			),
		);
		foreach ($result as $id=>$item) {
			if ((count($peoples['authors'][10]) < $limit)&&($item['is_10_author'] > 0)) {
				$peoples['authors'][10][$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>10, 'id'=>$id);
			}
			if ((count($peoples['authors'][22]) < $limit)&&($item['is_22_author'] > 0)) {
				$peoples['authors'][22][$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>22, 'id'=>$id);
			}
			if ((count($peoples['authors'][24]) < $limit)&&($item['is_24_author'] > 0)) {
				$peoples['authors'][24][$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>24, 'id'=>$id);
			}
			if ((count($peoples['actors'][40]) < $limit)&&($item['is_40_actor'] > 0)) {
				$peoples['actors'][40][$id] = array('role_id'=>Person::ROLE_ACTOR, 'entity'=>40, 'id'=>$id);
			}
			if ((count($peoples['directors'][40]) < $limit)&&($item['is_40_director'] > 0)) {
				$peoples['directors'][40][$id] = array('role_id'=>Person::ROLE_DIRECTOR, 'entity'=>40, 'id'=>$id);
			}
			if ((count($peoples['performers'][22]) < $limit)&&($item['is_22_performer'] > 0)) {
				$peoples['performers'][22][$id] = array('role_id'=>Person::ROLE_PERFORMER, 'entity'=>22, 'id'=>$id);
			}
			if ((count($peoples['authors'][10]) >= $limit)&&
				(count($peoples['authors'][22]) >= $limit)&&
				(count($peoples['authors'][24]) >= $limit)&&
				(count($peoples['actors'][40]) >= $limit)&&
				(count($peoples['directors'][40]) >= $limit)&&
				(count($peoples['performers'][22]) >= $limit)
			) break;
		}

//		if (count($peoples['authors'][10]) < $limit) {
//			$ids10 = $this->_isAuthors(10, array_keys($result));
//			foreach ($result as $id=>$item) {
//				if (!isset($peoples['authors'][10][$id])&&in_array($id, $ids10)) {
//					$peoples['authors'][10][$id] = array('role_id'=>Person::ROLE_AUTHOR, 'entity'=>10, 'itemsAvail'=>$item['is_10_author'], 'id'=>$id);
//				}
//				if (count($peoples['authors'][10]) >= $limit) break;
//			}
//		}

		if (empty($peoples['authors'][10])&&
			empty($peoples['authors'][22])&&
			empty($peoples['authors'][24])&&
			empty($peoples['actors'][40])&&
			empty($peoples['directors'][40])&&
			empty($peoples['performers'][22])
		)  return array();

		$roles = array();
		$authorIds = array();

		foreach($peoples['authors'][10] as $r) {
			$authorIds[$r['id']] = 1;
			$roles[$r['role_id']][$r['id']] = array('real_id'=>$r['id'], 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$r['id']]['itemsAvail'] = $r['itemsAvail'];
		}
		foreach($peoples['authors'][22] as $r) {
			$authorIds[$r['id']] = 1;
			$roles[$r['role_id']][$r['id']] = array('real_id'=>$r['id'], 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$r['id']]['itemsAvail'] = $r['itemsAvail'];
		}
		foreach($peoples['authors'][24] as $r) {
			$authorIds[$r['id']] = 1;
			$roles[$r['role_id']][$r['id']] = array('real_id'=>$r['id'], 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$r['id']]['itemsAvail'] = $r['itemsAvail'];
		}
		foreach($peoples['actors'][40] as $r) {
			$authorIds[$r['id']] = 1;
			$roles[$r['role_id']][$r['id']] = array('real_id'=>$r['id'], 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$r['id']]['itemsAvail'] = $r['itemsAvail'];
		}
		foreach($peoples['directors'][40] as $r) {
			$authorIds[$r['id']] = 1;
			$roles[$r['role_id']][$r['id']] = array('real_id'=>$r['id'], 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$r['id']]['itemsAvail'] = $r['itemsAvail'];
		}
		foreach($peoples['performers'][22] as $r) {
			$authorIds[$r['id']] = 1;
			$roles[$r['role_id']][$r['id']] = array('real_id'=>$r['id'], 'entity'=>$r['entity']);
			if (isset($r['itemsAvail'])) $roles[$r['role_id']][$r['id']]['itemsAvail'] = $r['itemsAvail'];
		}
		$result = SearchHelper::ProcessPersons($roles, array_keys($authorIds), array(), $this->_avail);
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
			if (!$this->_avail) $urlParams['avail'] = 0;
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
		$max = count($where);
		do {
			foreach ($where as $e=>$cond) {
				$condition[] = array_shift($cond);
				if (empty($cond)) unset($where[$e]);
				else $where[$e] = $cond;
				$i++;
			}
		} while (($i < max(3, $max))&&!empty($where));

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
			if (!$this->_avail) $urlParams['avail'] = 0;
			$row['url'] = Yii::app()->createUrl('entity/list', $urlParams);
			$row['title'] = Entity::GetTitle($item['entity']) . ' - ' . Yii::app()->ui->item('Related categories') . ': <b>' . $itemTitle . '</b>';
			$row['is_product'] = false;
			$row['orig_data'] = $item;
			$ret[] = $row;
		}
		return $ret;
	}

	protected function _getSeries($query) {
		$result = $this->_queryIndex($query, 'series', 0);
		if (empty($result)) return array();

		$where = array();
		foreach($result as $serie) {
			//audio не показываем
			if (empty($serie['entity'])||empty($serie['real_id'])||($serie['entity'] == 20)) continue;

			if (empty($where[$serie['entity']])) $where[$serie['entity']] = array();
			$where[$serie['entity']][] = '((entity='.intVal($serie['entity']).') AND (id='.intVal($serie['real_id']).'))';
		}
		if(empty($where)) return array();

		$i = 0;
		$condition = array();
		$max = count($where);
		do {
			foreach ($where as $e=>$cond) {
				$condition[] = array_shift($cond);
				if (empty($cond)) unset($where[$e]);
				else $where[$e] = $cond;
				$i++;
			}
		} while (($i < max(3, $max))&&!empty($where));

		if(empty($condition)) return array();



		$sql = 'SELECT * FROM all_series WHERE '.implode(' OR ', $condition);
		$rows = Yii::app()->db->createCommand($sql)->queryAll();

		$ret = array();
		foreach ($rows as $item) {
			$itemTitle = ProductHelper::GetTitle($item);
			$row = array();

			$urlParams = array(
				'sid' => $item['id'],
				'title' => ProductHelper::ToAscii($itemTitle),
				'entity' => Entity::GetUrlKey($item['entity'])
			);
			if (!$this->_avail) $urlParams['avail'] = 0;
			$row['url'] = Yii::app()->createUrl('entity/byseries', $urlParams);
			$row['title'] = Yii::app()->ui->item('FOUND_' . mb_strtoupper(Entity::GetUrlKey($item['entity'])) . '_SERIES', $itemTitle);
			$row['is_product'] = false;
			$row['orig_data'] = $item;
			$ret[] = $row;
		}
		return $ret;
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
				$result = $this->_querySimple($oneWordQuery, $index, 0);
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

	function getWords($query) {
		$query = trim($query);
		$queryWords = preg_split("/\W+/ui", $query);
		$queryWords = array_filter($queryWords);

		//по отдельным словам
		$pre = SearchHelper::BuildKeywords($query, 'forSnippet');
		return $pre;
		Debug::staticRun(array($pre));
//		$resultWord = array();
//		foreach ($pre['Queries'] as $query) {
//			if (empty($query)) continue;
//
//			$resultWord = $this->_querySimple($query, $index, $limit);
//			if (!empty($resultWord)) break;
//		}
//		return $result + $resultWord;
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

	function getMath($q) {
		$q = mb_strtolower($q, 'utf-8');
		$query = preg_split("/\W/ui", $q);
		$query = array_filter($query);
		$math = implode(' ', $query);
		$countWords = count($query);
		$i = 0;
		if ($countWords > 3) {
			$phrases = array('(' . implode(' ', $query) . ')');
			while ($i++ < $countWords) {
				$word = array_pop($query);
				$phrases[] = '(' . implode(' ', $query) . ')';
				array_unshift($query, $word);
			}
			$math = implode('|', $phrases);
		}
		return $math;
	}

}
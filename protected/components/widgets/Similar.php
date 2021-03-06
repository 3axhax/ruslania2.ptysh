<?php
/*Created by Кирилл (27.07.2018 21:10)*/

class Similar extends CWidget {
	private $_paramsHeight = 0;
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array(
		'item' => array(),
		'entity' => 0,
	);
	private $_urlRule;

	function init() {

	}

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		$products = $this->_getProducts();
		if (empty($products)) return;

		$this->render('similar', array('items'=>$products, 'eid'=>$this->_params['entity'], 'paramsHeight'=>$this->_paramsHeight));
	}

	private function _getProducts() {
		$items = array();
		foreach($this->_getSimilarIds() as $entity=>$ids) $items[$entity] = $ids;
		foreach($this->_getCatalogIds() as $entity=>$ids) {
			if (empty($items[$entity])) $items[$entity] = array();
			foreach ($ids as $id) {
				$items[$entity][] = $id;
			}
			array_unique($items[$entity]);
		}
		$products = array();
		$p = new Product();
		foreach($items as $entity=>$ids) {
			if (!empty($ids)) {
				foreach ($p->GetProductsV2($entity, $ids) as $item) {
					$paramsHeight = 0;
					if (!empty($item['Authors'])) $paramsHeight += 20;//высота одного параметра
					if (!empty($item['isbn'])&&!in_array($item['entity'], array(Entity::SHEETMUSIC/*, Entity::MUSIC*/))) $paramsHeight += 20;
					if (!empty($item['eancode'])&&in_array($item['entity'], array(Entity::SHEETMUSIC/*, Entity::MUSIC*/))) $paramsHeight += 20;
					if ($item['year']) $paramsHeight += 20;
					if ($item['binding_id']) $paramsHeight += 20;
					if ($paramsHeight > $this->_paramsHeight) $this->_paramsHeight = $paramsHeight;

					$item['status'] = $p->GetStatusProduct($entity, $item['id']);
					$item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
					$item['priceData']['unit'] = '';
					if ($entity == Entity::PERIODIC) {
						$issues = Periodic::getCountIssues($item['issues_year']);
						if (!empty($issues['show3Months'])) {
							$item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
							$item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
							$item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
							$item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
						}
						elseif (!empty($issues['show6Months'])) {
							$item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
							$item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
							$item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
							$item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
						}
						else {
							$item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
						}
					}
					$products[] = $item;
				}
			}
		}
		return $products;
	}

	private function _getSimilarIds() {
		$sql = ''.
			'select t.similar_id, t.similar_entity '.
			'from `similar_items` t '.
				'join ' . Entity::GetEntitiesList()[$this->_params['entity']]['site_table'] . ' tG on (tG.id = t.`item_id`) and (tG.avail_for_order = 1) '.
			'where (t.`item_id` = ' . $this->_params['item']['id'] . ') '.
				'and (t.`item_entity` = ' . $this->_params['entity'] . ') '.
			'order by t.similar_id desc '.
			'limit 10 '.
		'';
		$items = array();
		foreach (Yii::app()->db->createCommand($sql)->queryAll() as $item) {
			if (!isset($items[$item['similar_entity']])) $items[$item['similar_entity']] = array();
			$items[$item['similar_entity']][] = $item['similar_id'];
		}
		return $items;
	}

	private function _getCatalogIds() {
		$refererRoute = '';
		$referer = Yii::app()->getRequest()->getUrlReferrer();
		if (!empty($referer)) {
			$request = new MyRefererRequest();
			$request->setFreePath($referer);
			$refererRoute = Yii::app()->getUrlManager()->parseUrl($request);
		}

		$sqlParams = array(':eid'=>$this->_params['entity'], ':id'=>$this->_params['item']['id'], 'route'=>$refererRoute);
		$sql = 'select ids, date_add from _similar_items where (eid = :eid) and (id = :id) and (route = :route) limit 1';
		$row = Yii::app()->db->createCommand($sql)->queryRow(true, $sqlParams);
		if (!empty($row)) {
			$ids = explode(',',$row['ids']);
		}
		else {
			$ids = null;
			$limit = 10;
			if (!empty($refererRoute)) {
				$sql = $this->_sql($refererRoute, $limit);
				if (!empty($sql)) {
					$ids = Yii::app()->db->createCommand($sql)->queryColumn(array(':titleName'=>ProductHelper::GetTitle($this->_params['item'])));
				}
			}
			if ($ids === null) {
				$ids = array();
				$sql = $this->_sqlByAuthors(10);
				if (!empty($sql)) {
					$ids = Yii::app()->db->createCommand($sql)->queryColumn(array(':titleName'=>ProductHelper::GetTitle($this->_params['item'])));
				}
			}

			if (count($ids) < $limit) {
				$sql = $this->_sqlByCategory($limit-count($ids));
				$ids = array_merge($ids, Yii::app()->db->createCommand($sql)->queryColumn(array(':titleName'=>ProductHelper::GetTitle($this->_params['item']))));
			}
			$sql = 'insert ignore into _similar_items set eid = :eid, id = :id, route = :route, ids = :ids, date_add = :date';
			$sqlParams[':ids'] = implode(',',$ids);
			$sqlParams[':date'] = time();
			Yii::app()->db->createCommand($sql)->query($sqlParams);
		}
		if (empty($ids)) return array();
		return array($this->_params['entity']=>$ids);
	}


	private function _sql($refererRoute, $limit) {
		$sql = '';
		switch ($refererRoute) {
			case 'entity/categorylist':case 'entity/list': $sql = $this->_sqlByCategory($limit); break;
			case 'entity/serieslist':case 'entity/byseries': $sql = $this->_sqlBySeries($limit); break;
			case 'entity/publisherlist':case 'entity/bypublisher': $sql = $this->_sqlByPublisher($limit); break;
			case 'entity/authorlist':case 'entity/byauthor': $sql = $this->_sqlByAuthors($limit); break;
			case 'entity/performerlist':case 'entity/byperformer': $sql = $this->_sqlByPerformers($limit); break;
			case 'entity/actorlist':case 'entity/byactor': $sql = $this->_sqlByActors($limit); break;
			case 'entity/directorlist':case 'entity/bydirector': $sql = $this->_sqlByDirectors($limit); break;
			case 'entity/bindingslist':case 'entity/bybinding': $sql = $this->_sqlByBinding($limit); break;
			case 'entity/audiostreamslist':case 'entity/byaudiostream': $sql = $this->_sqlByAudiostream($limit); break;
			case 'entity/subtitleslist':case 'entity/bysubtitle': $sql = $this->_sqlBySubtitles($limit); break;
			case 'entity/medialist':case 'entity/bymedia': $sql = $this->_sqlByMedia($limit); break;
			case 'entity/typeslist':case 'entity/bymagazinetype': $sql = $this->_sqlByType($limit); break;
		}
		return $sql;
	}

	private function _sqlByCategory($limit) {
		$entity = $this->_params['entity'];
		$entityParam = Entity::GetEntitiesList()[$entity];

		$cid = (int) $this->_params['item']['Category']['id'];
		$cidSub = (int) $this->_params['item']['SubCategory']['id'];
		$catIds = array();
		if (!empty($cid)) $catIds[] = $cid;
		if (!empty($cidSub)) $catIds[] = $cidSub;

		$jCond = '';
		if (!empty($catIds)) $jCond = 'where ((code in (' . implode(',', $catIds) . ')) or (subcode in (' . implode(',', $catIds) . '))) ';

		$order = $this->_getOrders($entity);
		if (!empty($order['year'])) unset($order['add_date']);
		$sql = ''.
			'select t.id, t.image '.
			'from ' . $entityParam['site_table'] . ' t '.
				'join (select id, avail_for_order from books_catalog ' . $jCond . ' having (id <> ' . (int) $this->_params['item']['id'] . ') and (avail_for_order > 0) order by positionTimeHL limit 200) tCat using (id) '.
			'where (t.image <> "") '.
			'order by t.positionTimeHL '.
			'limit ' . $limit . ''.
		'';
/*
		$condition = array('avail'=>'(t.avail_for_order = 1)', );
		if (!empty($catIds)) $condition['category'] = '((t.code in (' . implode(',', $catIds) . ')) or (t.subcode in (' . implode(',', $catIds) . ')))';

		$having = array('id'=>'(id <> ' . (int) $this->_params['item']['id'] . ')', 'image'=>'(image <> "")', );

		$order = $this->_getOrders($entity);
		if (!empty($order['year'])) unset($order['add_date']);
		$sql = ''.
			'select t.id, t.image '.
			'from ' . $entityParam['site_table'] . ' t '.
			'where ' . implode(' and ', $condition) . ' '.
			'having ' . implode(' and ', $having) . ' '.
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';*/
		return $sql;
	}

	private function _sqlByAuthors($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'authors')) return '';
		if (empty($this->_params['item']['Authors'])) return '';
		$authorIds = array();
		foreach ($this->_params['item']['Authors'] as $author) {
			$authorIds[] = $author['id'];
		}

		$entityParam = Entity::GetEntitiesList()[$entity];

		$order = $this->_getOrders($entity);
		array_unshift($order, 'if (t.title_ru LIKE :titleName, 1, 0) desc');

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
				'join ' . $entityParam['author_table'] . ' tA on (tA.' . $entityParam['author_entity_field'] . ' = t.id) '.
					'and (tA.author_id in (' . implode(',', $authorIds) . ')) '.
					'and (tA.' . $entityParam['author_entity_field'] . ' <> ' . (int) $this->_params['item']['id'] . ') '.
			'having (avail_for_order = 1) and (image <> "") ' .
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
			'';
		return $sql;
	}

	private function _sqlBySeries($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'series')) return '';
		if (empty($this->_params['item']['series_id'])) return '';

		$entityParam = Entity::GetEntitiesList()[$entity];
		$order = $this->_getOrders($entity);

		$sql = ''.
			'select t.id, t.image, t.avail_for_order ' .
			'from ' . $entityParam['site_table'] . ' t '.
			'where (t.series_id = ' . $this->_params['item']['series_id'] . ') '.
			'having (t.avail_for_order = 1) and (id <> ' . (int) $this->_params['item']['id'] . ') and (image <> "") '.
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByPublisher($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'publisher')) return '';
		if (empty($this->_params['item']['publisher_id'])) return '';

		$entityParam = Entity::GetEntitiesList()[$entity];
		$order = $this->_getOrders($entity);

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
			'where(t.publisher_id = ' . $this->_params['item']['publisher_id'] . ') '.
			'having (id <> ' . (int) $this->_params['item']['id'] . ') and (avail_for_order = 1) and (image <> "")'.
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByPerformers($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'performers')) return '';
		if (empty($this->_params['item']['Performers'])) return '';
		$performerIds = array();
		foreach ($this->_params['item']['Performers'] as $performer) {
			$performerIds[] = $performer['id'];
		}

		$entityParam = Entity::GetEntitiesList()[$entity];

		$order = $this->_getOrders($entity);
		array_unshift($order, 'if (t.title_ru LIKE :titleName, 1, 0) desc');

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
				'join ' . $entityParam['performer_table'] . ' tA on (tA.' . $entityParam['performer_field'] . ' = t.id) '.
					'and (tA.person_id in (' . implode(',', $performerIds) . ')) '.
					'and (tA.' . $entityParam['performer_field'] . ' <> ' . (int) $this->_params['item']['id'] . ') '.
			'having (avail_for_order = 1) and (image <> "") ' .
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByActors($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'actors')) return '';
		if (empty($this->_params['item']['Actors'])) return '';
		$actorIds = array();
		foreach ($this->_params['item']['Actors'] as $actor) {
			$actorIds[] = $actor['id'];
		}

		$entityParam = Entity::GetEntitiesList()[$entity];

		$order = $this->_getOrders($entity);
		array_unshift($order, 'if (t.title_ru LIKE :titleName, 1, 0) desc');

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
				'join video_actors tA on (tA.video_id = t.id) '.
					'and (tA.person_id in (' . implode(',', $actorIds) . ')) '.
					'and (tA.video_id <> ' . (int) $this->_params['item']['id'] . ') '.
			'having (avail_for_order = 1) and (image <> "") ' .
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByDirectors($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'directors')) return '';
		if (empty($this->_params['item']['Directors'])) return '';
		$actorIds = array();
		foreach ($this->_params['item']['Directors'] as $actor) {
			$actorIds[] = $actor['id'];
		}

		$entityParam = Entity::GetEntitiesList()[$entity];

		$order = $this->_getOrders($entity);
		array_unshift($order, 'if (t.title_ru LIKE :titleName, 1, 0) desc');

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
				'join video_directors tA on (tA.video_id = t.id) '.
					'and (tA.person_id in (' . implode(',', $actorIds) . ')) '.
					'and (tA.video_id <> ' . (int) $this->_params['item']['id'] . ') '.
			'having (avail_for_order = 1) and (image <> "") ' .
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByBinding($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'binding')) return '';
		if (empty($this->_params['item']['binding_id'])) return '';

		$entityParam = Entity::GetEntitiesList()[$entity];
		$order = $this->_getOrders($entity);

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
			'where (t.binding_id = ' . $this->_params['item']['binding_id'] . ') '.
			'having (id <> ' . (int) $this->_params['item']['id'] . ') and (avail_for_order = 1) and (image <> "") '.
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByAudiostream($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'audiostreams')) return '';

		if (empty($this->_params['item']['AudioStreams'])) return '';
		$ids = array();
		foreach ($this->_params['item']['AudioStreams'] as $row) {
			$ids[] = $row['id'];
		}

		$entityParam = Entity::GetEntitiesList()[$entity];

		$order = $this->_getOrders($entity);
		array_unshift($order, 'if (t.title_ru LIKE :titleName, 1, 0) desc');

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
				'join video_audiostreams tA on (tA.video_id = t.id) '.
					'and (tA.stream_id in (' . implode(',', $ids) . ')) '.
					'and (tA.video_id <> ' . (int) $this->_params['item']['id'] . ') '.
			'having (avail_for_order = 1) and (image <> "") ' .
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlBySubtitles($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'subtitles')) return '';

		if (empty($this->_params['item']['Subtitles'])) return '';
		$ids = array();
		foreach ($this->_params['item']['Subtitles'] as $row) {
			$ids[] = $row['id'];
		}

		$entityParam = Entity::GetEntitiesList()[$entity];

		$order = $this->_getOrders($entity);
		array_unshift($order, 'if (t.title_ru LIKE :titleName, 1, 0) desc');

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
				'join video_credits tA on (tA.video_id = t.id) '.
					'and (tA.credits_id in (' . implode(',', $ids) . ')) '.
					'and (tA.video_id <> ' . (int) $this->_params['item']['id'] . ') '.
			'having (avail_for_order = 1) and (image <> "") ' .
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByMedia($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'media')) return '';
		if (empty($this->_params['item']['media_id'])) return '';

		$entityParam = Entity::GetEntitiesList()[$entity];
		$order = $this->_getOrders($entity);

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
			'where (t.media_id = ' . $this->_params['item']['media_id'] . ') '.
			'having (id <> ' . (int) $this->_params['item']['id'] . ') and (avail_for_order = 1) and (image <> "") '.
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _sqlByType($limit) {
		$entity = $this->_params['entity'];
		if (!Entity::checkEntityParam($entity, 'magazinetype')) return '';
		if (empty($this->_params['item']['type'])) return '';

		$entityParam = Entity::GetEntitiesList()[$entity];
		$order = $this->_getOrders($entity);

		$sql = ''.
			'select t.id, t.avail_for_order, t.image ' .
			'from ' . $entityParam['site_table'] . ' t '.
			'where (t.type = ' . $this->_params['item']['type'] . ') '.
			'having (id <> ' . (int) $this->_params['item']['id'] . ') and (avail_for_order = 1) and (image <> "") '.
			'order by ' . implode(', ', $order) . ' '.
			'limit ' . $limit . ' '.
		'';
		return $sql;
	}

	private function _getOrders($entity) {
		return array('t.positionTimeHL');
		$order = array('year'=>'t.year desc', 'add_date'=>'t.add_date desc');
		if (!Entity::checkEntityParam($entity, 'years')) unset($order['year']);
		return $order;
	}

}
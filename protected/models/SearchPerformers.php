<?php
/*Created by Кирилл (19.06.2018 21:48)*/

class SearchPerformers {
	static private $_self = null;
	private $_siteLang = 'ru';

	/**
	 * @return SearchPerformers
	 */
	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

	private function __construct() {
		if (isset(Yii::app()->language)) {
			$this->_siteLang = Yii::app()->language;
			if (!in_array($this->_siteLang, array('ru', 'en'))) $this->_siteLang = 'en'; //не на всех языках
		}
	}

	function getSiteLang() { return $this->_siteLang; }

	function getAll($entity, $limit, &$counts) {
		if (!Entity::checkEntityParam($entity, 'performers')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tableItemsPerformers = $entityParam['performer_table'];
		$tablePerformers = $entityParam['performer_table_list'];
		$fieldIdItem = $entityParam['performer_field'];

		$sql = ''.
			'select sql_calc_found_rows t.id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tablePerformers . ' t '.
				'join ' . $tableItemsPerformers . ' tIA on (tIA.performer_id = t.id) '.
				'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'group by t.id '.
			'order by t.title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
			'';
		$items = Yii::app()->db->createCommand($sql)->queryAll();
		$sql = 'select found_rows();';
		$counts = Yii::app()->db->createCommand($sql)->queryScalar();
		return $items;
	}

	function getPerformers($entity, $q, $limit = 20) {
		if (!Entity::checkEntityParam($entity, 'performers')) return array();
		if ($q == '') return array();

		$items = array();
		//сначала ищу если начанается
		if (mb_strlen($q, 'utf-8') == 1) $items = $this->getBegin($entity, $q, array(), $limit);
		else $items = $this->getLike($entity, $q, array(), $limit, true);
		//сначала ищу если начанается

		//потом добавляю тем, что содержит
		if ($limit > count($items)) {
			$ids = array();
			foreach ($items as $item) $ids[] = $item['id'];

			$items = array_merge($items, $this->getLike($entity, $q, $ids, $limit - count($ids)));
		}
		//потом добавляю тем, что содержит
		return $items;
	}

	function getBegin($entity, $q, $excludes = array(), $limit = '', &$count = false) {
		return $this->getLike($entity, $q, $excludes, $limit, true, $count);
	}

	function getLike($entity, $q, $excludes = array(), $limit = '', $isBegin = false, &$count = false) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'performers')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tableItemsPerformers = $entityParam['performer_table'];
		$tablePerformers = $entityParam['performer_table_list'];
		$fieldIdItem = $entityParam['performer_field'];

		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tablePerformers . ' t '.
				'join ' . $tableItemsPerformers . ' tIA on (tIA.performer_id = t.id) '.
				'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'where (t.title_' . $this->_siteLang . ' like :q) '.
				(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.id '.
			'order by t.title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$qStr = $q . '%';
		if (!$isBegin) $qStr = '%' . $qStr;
		$items = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $qStr));
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return $items;
	}

}
<?php
/*Created by Кирилл (20.06.2018 19:17)*/

class SearchDirectors {
	static private $_self = null;
	private $_siteLang = 'ru';

	/**
	 * @return SearchDirectors
	 */
	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

	private function __construct() {
		if (isset(Yii::app()->language)) {
			$this->_siteLang = Yii::app()->language;
			if (!in_array($this->_siteLang, array('ru', 'en', 'rut', 'fi'))) $this->_siteLang = 'en'; //не на всех языках
		}
	}

	function getSiteLang() { return $this->_siteLang; }

	function getAll($entity, $limit, &$counts) {
		$sql = ''.
			'select sql_calc_found_rows t.real_id id, t.title_' . $this->_siteLang . ' '.
			//'from video_directorslist t '.
			'from all_authorslist t '.
			'join video_directors tIA on (tIA.person_id = t.real_id) '.
			'join video_catalog tI on (tI.id = tIA.video_id) and (tI.avail_for_order = 1) '.
//			'where (t.entity = ' . (int) $entity . ') '.
			'group by t.real_id '.
			'order by t.title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
			'';
		$items = Yii::app()->db->createCommand($sql)->queryAll();
		$sql = 'select found_rows();';
		$counts = Yii::app()->db->createCommand($sql)->queryScalar();
		return $items;
	}

	function getDirectors($entity, $q, $limit = 20) {
		if (!Entity::checkEntityParam($entity, 'directors')) return array();
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
		if (!Entity::checkEntityParam($entity, 'directors')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tableItemsAuctors = 'video_directors';
		$tableAuctors = 'all_authorslist';//'video_directorslist';
		$fieldIdItem = 'video_id';

		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.real_id id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tableAuctors . ' t '.
			'join ' . $tableItemsAuctors . ' tIA on (tIA.person_id = t.real_id) '.
			'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'where (t.title_' . $this->_siteLang . ' like :q) '.
//			'and (t.entity = ' . (int) $entity . ') '.
			(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.real_id '.
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
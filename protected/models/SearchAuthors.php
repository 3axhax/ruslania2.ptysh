<?php
/*Created by Кирилл (09.06.2018 23:42)*/

class SearchAuthors {
	static private $_self = null;
	private $_siteLang = 'ru';

	/**
	 * @return SearchAuthors
	 */
	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

	private function __construct() {
		if (isset(Yii::app()->language)) {
			$this->_siteLang = Yii::app()->language;
			if (!in_array($this->_siteLang, array('ru', 'en', 'fi', 'rut'))) $this->_siteLang = 'en'; //не на всех языках
		}
	}

	function getSiteLang() { return $this->_siteLang; }

	function getAuthors($entity, $q, $limit = 20) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$authors = array();
		//сначала ищу если начанается
		if (mb_strlen($q, 'utf-8') == 1) $authors = $this->getBegin($entity, $q, array(), $limit);
		else $authors = $this->getLike($entity, $q, array(), $limit, true);
		//сначала ищу если начанается

		//потом добавляю тем, что содержит
		if ($limit > count($authors)) {
			$ids = array();
			foreach ($authors as $author) $ids[] = $author['id'];

			$authors = array_merge($authors, $this->getLike($entity, $q, $ids, $limit - count($ids)));
		}
		//потом добавляю тем, что содержит
		return $authors;
	}

	function getBegin($entity, $q, $excludes = array(), $limit = '', &$count = false) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tableItemsAuthors = $entityParam['author_table'];
		$tableAuthors = 'all_authorslist';
		$fieldIdItem = $entityParam['author_entity_field'];

		$fieldFirst = 'first_' . $this->_siteLang;
		if (!in_array($this->_siteLang, array('ru', 'en'))) $fieldFirst = 'first_en'; //не на всех языках

		$sql = ''.
			'select ' . (($count)?'sql_calc_found_rows ':'') . 't.id, if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ' '.
			'from ' . $tableAuthors . ' t '.
				'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
				'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'where (t.' . $fieldFirst . ' = :q) '.
			(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.id '.
			'order by t.title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $q));
		if ($count) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return $authors;
	}

	function getLike($entity, $q, $excludes = array(), $limit = '', $isBegin = false, &$count = false) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tableItemsAuthors = $entityParam['author_table'];
		$tableAuthors = 'all_authorslist';
		$fieldIdItem = $entityParam['author_entity_field'];

		$sql = ''.
			'select ' . (($count)?'sql_calc_found_rows ':'') . 't.id, if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ' '.
			'from ' . $tableAuthors . ' t '.
				'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
				'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'where (t.title_' . $this->_siteLang . ' like :q) '.
			(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.id '.
			'order by t.title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$qStr = $q . '%';
		if (!$isBegin) $qStr = '%' . $qStr;
		$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $qStr));
		if ($count) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return $authors;
	}

}
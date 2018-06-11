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
		if (isset(Yii::app()->language)) $this->_siteLang = Yii::app()->language;
	}

	function getAuthors($entity, $q, $limit = 20) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$authors = array();
		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tableItemsAuthors = $entityParam['author_table'];
		$tableAuthors = 'all_authorslist';
		$fieldIdItem = $entityParam['author_entity_field'];
		//сначала ищу если начанается
		if (mb_strlen($q, 'utf-8') == 1) {
			$sql = ''.
				'select t.id, t.title_' . $this->_siteLang . ' title '.
				'from ' . $tableAuthors . ' t '.
					'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
					'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
				'where (t.first_ru = :q) '.
				'group by t.id '.
				'order by t.title_' . $this->_siteLang . ' '.
				'limit ' . $limit . ' '.
			'';
			$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $q));
		}
		else {
			$sql = ''.
				'select t.id, t.title_' . $this->_siteLang . ' title '.
				'from ' . $tableAuthors . ' t '.
					'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
					'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
				'where (t.title_' . $this->_siteLang . ' like :q) '.
				'group by t.id '.
				'order by t.title_' . $this->_siteLang . ' '.
				'limit ' . $limit . ' '.
			'';
			$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $q . '%'));
		}
		//сначала ищу если начанается

		//потом добавляю тем, что содержит
		if ($limit > count($authors)) {
			$ids = array();
			foreach ($authors as $author) $ids[] = $author['id'];

			$sql = ''.
				'select t.id, t.title_' . $this->_siteLang . ' title '.
				'from ' . $tableAuthors . ' t '.
					'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
					'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
				'where (t.title_' . $this->_siteLang . ' like :q) '.
				(empty($ids)?'':' and (t.id not in (' . implode(', ', $ids) . ')) ').
				'group by t.id '.
				'order by t.title_' . $this->_siteLang . ' '.
				'limit ' . ($limit - count($ids)) . ' '.
			'';
			$authors = array_merge($authors, Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => '%' . $q . '%')));
		}
		//потом добавляю тем, что содержит
		return $authors;
	}

}
<?php
/*Created by Кирилл (20.06.2018 20:32)*/

class SearchPublishers {
	static private $_self = null;
	private $_siteLang = 'ru';

	/**
	 * @return SearchPublishers
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

	function getPublishers($entity, $q, $limit = 20) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'publisher')) return array();

		//сначала ищу если начанается
		if (mb_strlen($q, 'utf-8') == 1) $publishers = $this->getBegin($entity, $q, array(), $limit);
		else $publishers = $this->getLike($entity, $q, array(), $limit, true);
		//сначала ищу если начанается

		//потом добавляю тем, что содержит
		if ($limit > count($publishers)) {
			$ids = array();
			foreach ($publishers as $publisher) $ids[] = $publisher['id'];

			$publishers = array_merge($publishers, $this->getLike($entity, $q, $ids, $limit - count($ids)));
		}
		//потом добавляю тем, что содержит
		return $publishers;
	}

	function getBegin($entity, $q, $excludes = array(), $limit = '', &$count = false) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'publisher')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tablePublishers = 'all_publishers';

		$fieldFirst = 'first_' . $this->_siteLang;
		if (!in_array($this->_siteLang, array('ru', 'en'))) $fieldFirst = 'first_en'; //не на всех языках

		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tablePublishers . ' t '.
				'join ' . $tableItems . ' tI on (tI.publisher_id = t.id) and (tI.avail_for_order = 1) '.
			'where (t.' . $fieldFirst . ' = :q) '.
			(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.id '.
			'order by title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
			'';
		$publishers = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $q));
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		$ids = array();
		foreach ($publishers as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/bypublisher', $ids);
		}
		return $publishers;
	}

	function getLike($entity, $q, $excludes = array(), $limit = '', $isBegin = false, &$count = false) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'publisher')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tablePublishers = 'all_publishers';

		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tablePublishers . ' t '.
				'join ' . $tableItems . ' tI on (tI.publisher_id = t.id) and (tI.avail_for_order = 1) '.
			'where (t.title_' . $this->_siteLang . ' like :q) '.
			(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.id '.
			'order by title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$qStr = $q . '%';
		if (!$isBegin) $qStr = '%' . $qStr;
		$publishers = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $qStr));
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		$ids = array();
		foreach ($publishers as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/bypublisher', $ids);
		}
		return $publishers;
	}

	function getAll($entity, $limit, &$counts) {
		if (!Entity::checkEntityParam($entity, 'publisher')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItems = $entityParam['site_table'];
		$tablePublishers = 'all_publishers';

		$sql = ''.
			'select sql_calc_found_rows t.id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tablePublishers . ' t '.
//			'join ' . $tableItems . ' tI on (tI.publisher_id = t.id) and (tI.avail_for_order = 1) '.
//			'group by t.id '.
				'join (select publisher_id from books_catalog where (avail_for_order = 1) group by publisher_id) tI on (tI.publisher_id = t.id) '.
			'order by t.title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$items = Yii::app()->db->createCommand($sql)->queryAll();
		$ids = array();
		foreach ($items as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/bypublisher', $ids);
		}
		$sql = 'select found_rows();';
		$counts = Yii::app()->db->createCommand($sql)->queryScalar();
		return $items;
	}

    static function  getPublishersForFilters($entity, $q, $cid = 0, $limit = 20) {
        if (!Entity::checkEntityParam($entity, 'publisher')) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];

        $whereLike = 'LOWER(title_ru) LIKE LOWER(:q) OR LOWER(title_en) LIKE LOWER(:q)';
        if ($cid > 0) {
            $sql = 'SELECT tc.publisher_id, ap.title_ru, ap.title_en 
                    FROM (SELECT id, title_ru, title_en FROM all_publishers 
                    WHERE ('.$whereLike.')) as ap 
                    LEFT JOIN ' . $tbl . ' as tc ON (ap.id = tc.publisher_id)
                    WHERE tc.avail_for_order=1 AND (tc.`code`=:code OR tc.`subcode`=:code)
                    GROUP BY tc.publisher_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid, ':q' => '%'.$q.'%'));
        } else {
            $sql = 'SELECT tc.publisher_id, ap.title_ru, ap.title_en 
                    FROM (SELECT id, title_ru, title_en FROM all_publishers 
                    WHERE ('.$whereLike.')) as ap 
                    LEFT JOIN ' . $tbl . ' as tc ON (ap.id = tc.publisher_id)
                    WHERE tc.avail_for_order=1
                    GROUP BY tc.publisher_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => '%'.$q.'%'));
        }
        $publishers = [];
        $i = 0;
        foreach ($rows as $row) {
            if (mb_stripos($row['title_ru'], $q) !== false) {
                $publishers[$i]['id'] = $row['publisher_id'];
                $publishers[$i]['title'] = $row['title_ru'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_en'], $q) !== false) {
                $publishers[$i]['id'] = $row['publisher_id'];
                $publishers[$i]['title'] = $row['title_en'];
                $i++;
                continue;
            }
        }
        return $publishers;
    }

}
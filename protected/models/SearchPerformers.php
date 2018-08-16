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
		$ids = array();
		foreach ($items as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/byperformer', $ids);
		}
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
		$ids = array();
		foreach ($items as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/byperformer', $ids);
		}
		return $items;
	}

    function  getPerformersForFilters($entity, $q, $cid = 0, $limit = 20) {
        if (!Entity::checkEntityParam($entity, 'performers')) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_performers = $entities[$entity]['performer_table'];
        $tbl_performers_list = $entities[$entity]['performer_table_list'];

        $whereLike = 'LOWER(title_ru) LIKE LOWER(:q) OR LOWER(title_en) LIKE LOWER(:q)';
        if ($cid > 0) {
            $sql = 'SELECT tc.performer_id, ap.title_ru, ap.title_en 
                    FROM (SELECT id, title_ru, title_en FROM all_performers 
                    WHERE ('.$whereLike.')) as ap 
                    LEFT JOIN ' . $tbl . ' as tc ON (ap.id = tc.performer_id)
                    WHERE tc.avail_for_order=1 AND (tc.`code`=:code OR tc.`subcode`=:code)
                    GROUP BY tc.performer_id LIMIT 0,'.$limit;

            $sql = 'SELECT tpl.id, tpl.title_ru, tpl.title_en
                    FROM (SELECT id, title_ru, title_en FROM '.$tbl_performers_list.' 
                    WHERE ('.$whereLike.')) as tpl
                    LEFT JOIN '.$tbl_performers.' as tp ON (tpl.id = tp.performer_id) 
                    LEFT JOIN '.$tbl.' as t ON (tp.'.$entities[$entity]['entity'].'_id = t.id)
                    WHERE t.avail_for_order=1 AND (t.`code`=:code OR t.`subcode`=:code)
                    GROUP BY tp.performer_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid, ':q' => '%'.$q.'%'));
        } else {
            $sql = 'SELECT tc.performer_id, ap.title_ru, ap.title_en 
                    FROM (SELECT id, title_ru, title_en FROM all_performers 
                    WHERE ('.$whereLike.')) as ap 
                    LEFT JOIN ' . $tbl . ' as tc ON (ap.id = tc.performer_id)
                    WHERE tc.avail_for_order=1
                    GROUP BY tc.performer_id LIMIT 0,'.$limit;
            $sql = 'SELECT tpl.id, tpl.title_ru, tpl.title_en
                    FROM (SELECT id, title_ru, title_en FROM '.$tbl_performers_list.' 
                    WHERE ('.$whereLike.')) as tpl
                    LEFT JOIN '.$tbl_performers.' as tp ON (tpl.id = tp.performer_id) 
                    LEFT JOIN '.$tbl.' as t ON (tp.'.$entities[$entity]['entity'].'_id = t.id)
                    WHERE t.avail_for_order=1
                    GROUP BY tp.performer_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => '%'.$q.'%'));
        }
        $performers = [];
        $i = 0;
        foreach ($rows as $row) {
            if (mb_stripos($row['title_ru'], $q) !== false) {
                $performers[$i]['id'] = $row['id'];
                $performers[$i]['title'] = $row['title_ru'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_en'], $q) !== false) {
                $performers[$i]['id'] = $row['id'];
                $performers[$i]['title'] = $row['title_en'];
                $i++;
                continue;
            }
        }
        return $performers;
    }

}
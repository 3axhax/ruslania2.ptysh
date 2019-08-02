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
			'select sql_calc_found_rows t.id id, t.title_' . $this->_siteLang . ' '.
			//'from video_directorslist t '.
			'from all_authorslist t '.
			'join video_directors tIA on (tIA.person_id = t.id) '.
			'join video_catalog tI on (tI.id = tIA.video_id) and (tI.avail_for_order = 1) '.
//			'where (t.entity = ' . (int) $entity . ') '.
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
			HrefTitles::get()->getByIds($entity, 'entity/bydirector', $ids);
		}
		return $items;
	}

	function getDirectors($entity, $q, $limit = 20) {
		if (!Entity::checkEntityParam($entity, 'directors')) return array();
		if ($q == '') return array();

		$items = array();
		//сначала ищу если начанается
		if (mb_strlen($q, 'utf-8') == 1) $items = $this->getBegin($entity, $q, array(), $limit);
		else {
			$items = $this->useSphinx($entity, $q, array(), $limit, true);
			if (!empty($items)) return $items;
			$items = $this->getLike($entity, $q, array(), $limit, true);
		}
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
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id id, t.title_' . $this->_siteLang . ' '.
			'from ' . $tableAuctors . ' t '.
			'join ' . $tableItemsAuctors . ' tIA on (tIA.person_id = t.id) '.
			'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'where (t.title_' . $this->_siteLang . ' like :q) '.
//			'and (t.entity = ' . (int) $entity . ') '.
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
			HrefTitles::get()->getByIds($entity, 'entity/bydirector', $ids);
		}
		return $items;
	}

    function  getDirectorsForFilters($entity, $q, $cid = 0, $limit = 20) {
        if (!Entity::checkEntityParam($entity, 'directors')) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_director = $entities[$entity]['directors_table'];

        $whereLike = 'LOWER(title_ru) LIKE LOWER(:q) OR LOWER(title_rut) LIKE LOWER(:q) OR 
            LOWER(title_en) LIKE LOWER(:q) OR LOWER(title_fi) LIKE LOWER(:q)';

        if ($cid > 0) {
            $sql = 'SELECT vd.person_id as id, aa.title_ru, aa.title_rut, aa.title_en, aa.title_fi FROM ' . $tbl_director . ' as vd, 
            ' . $tbl . ' as vc,  (SELECT id, title_ru, title_rut, title_en, title_fi FROM all_authorslist WHERE
            ('.$whereLike.')) as aa where (vd.person_id = aa.id) and (vc.id = vd.video_id) and (vc.`code`=:code OR vc.`subcode`=:code) 
				GROUP BY vd.person_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid, ':q' => '%'.$q.'%'));
        } else {
            $sql = 'SELECT vd.person_id as id, aa.title_ru, aa.title_rut, aa.title_en, aa.title_fi FROM ' . $tbl_director . ' as vd, 
            ' . $tbl . ' as vc,  (SELECT id, title_ru, title_rut, title_en, title_fi FROM all_authorslist WHERE
            ('.$whereLike.')) as aa where (vd.person_id = aa.id) and (vc.id = vd.video_id) 
				GROUP BY vd.person_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll($sql, array(':q' => '%'.$q.'%'));
        }
        $directors = [];
        $i = 0;
        foreach ($rows as $row) {
            if (mb_stripos($row['title_ru'], $q) !== false) {
                $directors[$i]['id'] = $row['id'];
                $directors[$i]['title'] = $row['title_ru'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_rut'], $q) !== false) {
                $directors[$i]['id'] = $row['id'];
                $directors[$i]['title'] = $row['title_rut'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_en'], $q) !== false) {
                $directors[$i]['id'] = $row['id'];
                $directors[$i]['title'] = $row['title_en'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_fi'], $q) !== false) {
                $directors[$i]['id'] = $row['id'];
                $directors[$i]['title'] = $row['title_fi'];
                $i++;
                continue;
            }

        }
        return $directors;
    }

	function getFromMorphy($entity, $q, $limit = 20, $useAvail = true) {
		$condition = array($q, 'mode=boolean');
		if (!empty($useAvail)) $condition[] = '!filter=is_' . $entity . '_director,0';
		$condition['limit'] = 'limit=' . $limit;
		$condition['maxmatches'] = 'maxmatches=' . $limit;
		$sql = ''.
			'select id '.
			'from _se_authors '.
			'where (query=:condition)'.
			'';
		return Yii::app()->db->createCommand($sql)->queryColumn(array(':condition'=>implode(';', $condition)));
	}

	function useSphinx($entity, $q, $excludes = array(), $limit = '', $isBegin = false, &$count = false, $useAvail = 1) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'directors')) return array();

		$words = preg_split("/\W/ui", $q);
		$words = array_filter($words);
		if (empty($words)) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItemsAuthors = 'video_directors';
		$tableAuthors = 'all_authorslist';

		$filter = array();
		if ($useAvail) $filter['avail'] = '!filter=is_' . $entity . '_director,0';
		if (!empty($excludes)) $filter['id'] = '!filter=id,' . implode(',', $excludes) . '';

		$condition = array();
		$condition['morphy_name'] = '(^' . implode(' ', $words) . '*)|(' . implode(' ', $words) . ')';
		$condition['mode'] = 'mode=extended';
		$condition['ranker'] = 'ranker=expr:top((word_count + (lcs - 1)/5 + 1/min_hit_pos + (word_count > 1)/(min_gaps + 1) + exact_hit + exact_order))';
		if (!empty($filter)) $condition['filter'] = implode(';', $filter);
//		$condition['fieldweights'] = 'fieldweights=100' . implode(',',$fieldWeights);
		$condition['limit'] = 'limit=50000';
		$condition['maxmatches'] = 'maxmatches=50000';

		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, '.
			'if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ', '.
			'is_' . $entity . '_director availItems '.
			'from ' . $tableAuthors . ' t '.
			'join ( ' .
			'select t1.id ' .
			'from _se_authors t1 ' .
			'where (t1.query = ' . SphinxQL::getDriver()->mest(implode(';', $condition)) . ') '.
			') tMN using (id) ' .
			(!$useAvail?'join ' . $tableItemsAuthors . ' tA on (tA.person_id = t.id) ':'').
			(!$useAvail?'group by t.id ':'').
			(empty($limit)?'':'limit ' . $limit . ' ').
			'';
		$authors = Yii::app()->db->createCommand($sql)->queryAll();
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		$ids = array();
		foreach ($authors as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/bydirector', $ids);
		}
		return $authors;
	}
}
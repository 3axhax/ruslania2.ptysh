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

	function getAuthors($entity, $q, $limit = 20, $useAvail = true) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$authors = array();
		//сначала ищу если начинается
		$count = false;
		if (mb_strlen($q, 'utf-8') == 1) $authors = $this->getBegin($entity, $q, array(), $limit, $count, true);
		else {
			$count = false;
			$authors = $this->useSphinx($entity, $q, array(), $limit, true, $count, true);
			if (!empty($authors)) return $authors;

			$authors = $this->getLike($entity, $q, array(), $limit, true, $count, true);
			$count = false;
			if (empty($authors)) $authors = $this->getFromCompliances($entity, $q, array(), $limit, $count, true);
		}
		//сначала ищу если начанается

		//потом добавляю тем, что содержит
		if ($limit > count($authors)) {
			$ids = array();
			foreach ($authors as $author) $ids[] = $author['id'];

			$count = false;
			$authors = array_merge($authors, $this->getLike($entity, $q, $ids, $limit - count($ids), false, $count, true));
		}
		//потом добавляю тем, что содержит

		//если нужны с товарами, которые не продаются
		if (($limit > count($authors))&&!$useAvail) {
			$ids = array();
			foreach ($authors as $author) $ids[] = $author['id'];
			$count = false;
			if (mb_strlen($q, 'utf-8') == 1) $authors = array_merge($authors, $this->getBegin($entity, $q, $ids, $limit - count($ids), $count, $useAvail));
			else {
				$count = false;
				$authors = array_merge($authors, $this->getLike($entity, $q, $ids, $limit - count($ids), true, $count, $useAvail));
			}
			//сначала ищу если начанается

			//потом добавляю тем, что содержит
			if ($limit > count($authors)) {
				$ids = array();
				foreach ($authors as $author) $ids[] = $author['id'];

				$count = false;
				$authors = array_merge($authors, $this->getLike($entity, $q, $ids, $limit - count($ids), false, $count, $useAvail));
			}

		}

		return $authors;
	}

	function getBegin($entity, $q, $excludes = array(), $limit = '', &$count = false, $useAvail = true) {
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
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, '.
				'if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ', '.
				'is_' . $entity . '_author availItems '.
			'from ' . $tableAuthors . ' t '.
			(!$useAvail?'join ' . $tableItemsAuthors . ' tA on (tA.author_id = t.id) ':'').
			'where (ord(t.' . $fieldFirst . ') = ord(:q)) '.
				($useAvail?'and (is_' . $entity . '_author > 0) ':'').
				(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
			'group by t.id '.
			'order by ' . (!$useAvail?'is_' . $entity . '_author desc, ':'') . 'title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';

//		$sql = ''.
//			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ' '.
//			'from ' . $tableAuthors . ' t '.
//			'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
//			'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
//			'where (t.' . $fieldFirst . ' = :q) '.
//			(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
//			'group by t.id '.
//			'order by title_' . $this->_siteLang . ' '.
//			(empty($limit)?'':'limit ' . $limit . ' ').
//		'';

		$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $q));
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
//		$ids = array();
//		foreach ($authors as $item) $ids[] = $item['id'];
//		if (!empty($ids)) {
//			HrefTitles::get()->getByIds($entity, 'entity/byauthor', $ids);
//		}
		return $authors;
	}

	function getLike($entity, $q, $excludes = array(), $limit = '', $isBegin = false, &$count = false, $useAvail = true) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
//		$tableItems = $entityParam['site_table'];
		$tableItemsAuthors = $entityParam['author_table'];
		$tableAuthors = 'all_authorslist';
//		$fieldIdItem = $entityParam['author_entity_field'];

		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, '.
				'if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ', '.
				'is_' . $entity . '_author availItems '.
			'from ' . $tableAuthors . ' t '.
			(!$useAvail?'join ' . $tableItemsAuthors . ' tA on (tA.author_id = t.id) ':'').
//				'join ' . $tableItemsAuthors . ' tIA on (tIA.author_id = t.id) '.
//				'join ' . $tableItems . ' tI on (tI.id = tIA.' . $fieldIdItem . ') and (tI.avail_for_order = 1) '.
			'where (t.title_' . $this->_siteLang . ' like :q) '.
				($useAvail?'and (is_' . $entity . '_author > 0) ':'').
				(empty($excludes)?'':' and (t.id not in (' . implode(', ', $excludes) . ')) ').
//			'group by t.id '.
			(!$useAvail?'group by t.id ':'').
			'order by ' . (!$useAvail?'is_' . $entity . '_author desc, ':'') . 'title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$qStr = $q . '%';
		if (!$isBegin) $qStr = '%' . $qStr;
		$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $qStr));
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		$ids = array();
		foreach ($authors as $item) $ids[] = $item['id'];
		if (!empty($ids)) {
			HrefTitles::get()->getByIds($entity, 'entity/byauthor', $ids);
		}
		return $authors;
	}

	function getFromCompliances($entity, $q, $excludes = array(), $limit = '', &$count = false, $useAvail = true) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItemsAuthors = $entityParam['author_table'];
		$condition = array();
		if ($useAvail) $condition['avail'] = '(is_' . $entity . '_author > 0)';
		if (!empty($excludes)) $condition['excl'] = '(t.id not in (' . implode(', ', $excludes) . '))';
		$sql = ''.
			'select ' . (($count !== false)?'sql_calc_found_rows ':'') . 't.id, '.
				'if (t.repair_title_' . $this->_siteLang . ' <> "", t.repair_title_' . $this->_siteLang . ', t.title_' . $this->_siteLang . ') title_' . $this->_siteLang . ', '.
				'is_' . $entity . '_author availItems '.
			'from all_authorslist t '.
				'join ('.
					'select db_id id '.
					'from compliances '.
					'where (xml_value like :q) '.
						'and (type_id = 4) '.
					'group by db_id '.
				') tCompl using (id) '.
			(!$useAvail?'join ' . $tableItemsAuthors . ' tA on (tA.author_id = t.id) ':'').
			(empty($condition)?'':'where ' . implode(' and ', $condition) . ' ') .
			(!$useAvail?'group by t.id ':'').
			'order by ' . (!$useAvail?'is_' . $entity . '_author desc, ':'') . 'title_' . $this->_siteLang . ' '.
			(empty($limit)?'':'limit ' . $limit . ' ').
		'';
		$qStr = $q . '%';
		$authors = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => $qStr));
		if ($count !== false) {
			$sql = 'select found_rows();';
			$count = Yii::app()->db->createCommand($sql)->queryScalar();
		}
		$ids = array();
		foreach ($authors as $item) $ids[] = $item['id'];
		if (!empty($ids)) HrefTitles::get()->getByIds($entity, 'entity/byauthor', $ids);
		return $authors;
	}

	function  getAuthorsForFilters($entity, $q, $cid = 0, $limit = 20) {
        if (!Entity::checkEntityParam($entity, 'authors')) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];
        
        $whereLike = 'LOWER(title_ru) LIKE LOWER(:q) OR LOWER(title_rut) LIKE LOWER(:q) OR 
            LOWER(title_en) LIKE LOWER(:q) OR LOWER(title_fi) LIKE LOWER(:q)';

        if ($cid > 0) {
            $sql = 'SELECT ba.author_id, aa.title_ru, aa.title_rut, aa.title_en, aa.title_fi FROM ' . $tbl_author . ' as ba, 
            ' . $tbl . ' as bc,  (SELECT id, title_ru, title_rut, title_en, title_fi FROM all_authorslist WHERE
            ('.$whereLike.')) as aa where (ba.author_id = aa.id) and (bc.id = ba.' . $field . ') and (bc.`code`=:code OR bc.`subcode`=:code)
				GROUP BY ba.author_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid, ':q' => '%'.$q.'%'));
        } else {
            $sql = 'SELECT ba.author_id, aa.title_ru, aa.title_rut, aa.title_en, aa.title_fi FROM ' . $tbl_author . ' as ba, 
            ' . $tbl . ' as bc,  (SELECT id, title_ru, title_rut, title_en, title_fi FROM all_authorslist WHERE
            ('.$whereLike.')) as aa where (ba.author_id = aa.id) and (bc.id = ba.' . $field . ') 
				GROUP BY ba.author_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll($sql, array(':q' => '%'.$q.'%'));
        }
        $authors = [];
        $i = 0;
        foreach ($rows as $row) {
            if (mb_stripos($row['title_ru'], $q) !== false) {
                $authors[$i]['id'] = $row['author_id'];
                $authors[$i]['title'] = $row['title_ru'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_rut'], $q) !== false) {
                $authors[$i]['id'] = $row['author_id'];
                $authors[$i]['title'] = $row['title_rut'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_en'], $q) !== false) {
                $authors[$i]['id'] = $row['author_id'];
                $authors[$i]['title'] = $row['title_en'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_fi'], $q) !== false) {
                $authors[$i]['id'] = $row['author_id'];
                $authors[$i]['title'] = $row['title_fi'];
                $i++;
                continue;
            }

        }
        return $authors;
    }

	function getFromMorphy($entity, $q, $limit = 20, $useAvail = true) {
		$condition = array($q, 'mode=boolean');
		if (!empty($useAvail)) $condition[] = '!filter=is_' . $entity . '_author,0';
		$condition['limit'] = 'limit=' . $limit;
		$condition['maxmatches'] = 'maxmatches=' . $limit;
		$sql = ''.
			'select id '.
			'from _se_authors '.
			'where (query=:condition)'.
		'';
		return Yii::app()->db->createCommand($sql)->queryColumn(array(':condition'=>implode(';', $condition)));
	}

	function useSphinx($entity, $q, $excludes = array(), $limit = '', $isBegin = false, &$count = false, $useAvail = true) {
		if ($q == '') return array();
		if (!Entity::checkEntityParam($entity, 'authors')) return array();

		$words = preg_split("/\W/ui", $q);
		$words = array_filter($words);
		if (empty($words)) return array();

		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItemsAuthors = $entityParam['author_table'];
		$tableAuthors = 'all_authorslist';

		$filter = array();
		if ($useAvail) $filter['avail'] = '!filter=is_' . $entity . '_author,0';
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
				'is_' . $entity . '_author availItems '.
			'from ' . $tableAuthors . ' t '.
				'join ( ' .
					'select t1.id ' .
					'from _se_authors t1 ' .
					'where (t1.query = ' . SphinxQL::getDriver()->mest(implode(';', $condition)) . ') '.
				') tMN using (id) ' .
				(!$useAvail?'join ' . $tableItemsAuthors . ' tA on (tA.author_id = t.id) ':'').
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
			HrefTitles::get()->getByIds($entity, 'entity/byauthor', $ids);
		}
		return $authors;
	}

}
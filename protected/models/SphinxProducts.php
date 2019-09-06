<?php
/*Created by Кирилл (16.07.2019 21:10)*/
require_once dirname(__FILE__) . '/SearchProducts.php';
class SphinxProducts extends SearchProducts {

	function getSqlParam($searchWords, $realWords, $useRealWord = false, $eid, $useSe = false) {
		$wTtitle = 100; $wAuthors = 100; $wDescription = 80;
		$countWords = count($searchWords);
		if ($countWords >= $this->_exactMatchNumber) {
			$wDescription = ceil($wTtitle*($countWords - 1)/$countWords) + 1;
//			$wAuthors = ceil($wTtitle*($countWords - 1)/$countWords) + 2;
		}
		$tables = array('books_catalog', 'pereodics_catalog', 'printed_catalog', 'music_catalog', 'musicsheets_catalog', 'video_catalog', 'maps_catalog', 'soft_catalog');
		if (!empty($eid)) {
			$params = Entity::GetEntitiesList();
			if (isset($params[$eid])&&!empty($params[$eid]['entity'])) {
				if ($useSe) $tables = array('_se_' . $params[$eid]['entity'] . '_catalog');
				else $tables = array($params[$eid]['entity'] . '_catalog');
			}
		}
		$condition = array(
			'morphy_name'=>'',
			'avail'=>'',
			'weight'=>'',
		);
		$separator = ' ';
		if (($countWords > $this->_exactMatchNumber)&&!$this->isFromNumeric($searchWords)) {
			$separator = '|';
			if ($useSe) $condition['weight'] = '!filter=@weight,0';
			else $condition['weight'] = '(weight() > 0)';
		}
		if ($useSe) $condition['morphy_name'] = implode($separator, $searchWords);
		else $condition['morphy_name'] = 'match(' . SphinxQL::getDriver()->mest(implode($separator, $searchWords)) . ')';

		$order = array(
			'weight'=>'weight() desc',
			'avail'=>'avail desc',
			'position'=>'position asc',
			'time_position'=>'time_position asc',
		);
		if ($this->_avail) {
			if ($useSe) $condition['avail'] = 'filter=avail,1';
			else $condition['avail'] = '(avail = 1)';
			unset($order['avail']);
		}
		if ($useSe) {
			$fieldWeights = array(
				'title_ru,' . $wTtitle,
				'title_en,' . $wTtitle,
				'title_fi,' . $wTtitle,
				'title_rut,' . $wTtitle,
				'title_eco,' . $wTtitle,
				'title_original,' . $wTtitle,
				'description_ru,' . $wDescription,
				'description_en,' . $wDescription,
				'description_fi,' . $wDescription,
				'description_de,' . $wDescription,
				'description_fr,' . $wDescription,
				'description_es,' . $wDescription,
				'description_se,' . $wDescription,
				'description_rut,' . $wDescription,
				'authors,' . $wAuthors,
			);
			$option = array();
			$condition['mode'] = 'mode=extended';
			$condition['ranker'] = 'ranker=expr:top((word_count + (lcs - 1)/5 + 1/(min_hit_pos*3 + 1) + (word_count > 1)/(min_gaps + 1) + exact_hit + exact_order)*user_weight' . ((($countWords > $this->_exactMatchNumber)&&!$this->isFromNumeric($searchWords))?'*(word_count > ' . ($this->_exactMatchNumber-1) . ')':'') . ')';
			$condition['fieldweights'] = 'fieldweights=' . implode(',',$fieldWeights);
			$condition['limit'] = 'limit=50000';
			$condition['maxmatches'] = 'maxmatches=50000';
		}
		else {
			$fieldWeights = array(
				'title_ru=' . $wTtitle,
				'title_en=' . $wTtitle,
				'title_fi=' . $wTtitle,
				'title_rut=' . $wTtitle,
				'title_eco=' . $wTtitle,
				'title_original=' . $wTtitle,
				'description_ru=' . $wDescription,
				'description_en=' . $wDescription,
				'description_fi=' . $wDescription,
				'description_de=' . $wDescription,
				'description_fr=' . $wDescription,
				'description_es=' . $wDescription,
				'description_se=' . $wDescription,
				'description_rut=' . $wDescription,
				'authors=' . $wAuthors,
			);
			//'option ranker=expr(\'top((word_count + (lcs - 1)/5 + 1/(min_hit_pos*3 + 1) + (word_count > 1)/(min_gaps + 1) + exact_hit + exact_order)*user_weight)\'), field_weights=(' . implode(',',$field_weights) . '), max_matches=100000 './/*(word_count > 2)
	/*
	 * word_count - кол-во найденных слов
	 * lcs - максимальная длина слов по порядку
	 * min_hit_pos - позиция первого найденного слова
	 * min_gaps - минимальное расстояние между поисковыми словами
	 * exact_hit - точное соответствие (0/1)
	 * exact_order - найдены все слова в порядке поискового запроса
	 * bm25 - вес, который считает сфинкса для документа по поисковому запросу
	 */
			$option = array(
				'ranker'=>"ranker=expr('top((word_count + (lcs - 1)/5 + 1/(min_hit_pos*3 + 1) + (word_count > 1)/(min_gaps + 1) + exact_hit + exact_order)*user_weight" . ((($countWords > $this->_exactMatchNumber)&&!$this->isFromNumeric($searchWords))?'*(word_count > ' . ($this->_exactMatchNumber-1) . ')':'') . ")')",
				'field_weights'=>"field_weights=(" . implode(',',$fieldWeights) . ")",
				'max_matches'=>"max_matches=100000",
			);
		}
		return array($tables, array_filter($condition), $order, $option);
	}

	function getBooleanByCode($code, $q) {
		foreach ($code as $codeName) {
			switch ($codeName) {
				case 'catalogue':
					$sql = ''.
						'select entity, real_id '.
						'from music_catalog ' .
						'where (catalogue = ' . SphinxQL::getDriver()->mest($q) . ') '.
						'option ranker=none '.
					'';
					$find = SphinxQL::getDriver()->multiSelect($sql);

					$sql = ''.
						'select entity, real_id '.
						'from pereodics_catalog ' .
						'where (issn = ' . SphinxQL::getDriver()->mest($q) . ') '.
						'option ranker=none '.
					'';
					$find = array_merge($find, SphinxQL::getDriver()->multiSelect($sql));

					$sql = ''.
						'select entity, real_id '.
						'from pereodics_catalog ' .
						'where (index = ' . SphinxQL::getDriver()->mest($q) . ') '.
						'option ranker=none '.
					'';
					$find = array_merge($find, SphinxQL::getDriver()->multiSelect($sql));
					if (!empty($find)) return $this->_prepareProducts($find);
					break;
				case 'real_id':
					$num = explode('-', $q);
					if (count($num) == 2) {
						$sql = ''.
							'select entity, real_id '.
							'from books_catalog, pereodics_catalog, printed_catalog, music_catalog, musicsheets_catalog, video_catalog, maps_catalog, soft_catalog ' .
							'where (' . $codeName . ' = ' . $num[1] . ') and (entity = ' . $num[0] . ') '.
							'option ranker=none '.
						'';
						$find = SphinxQL::getDriver()->multiSelect($sql);
						if (!empty($find)) return $this->_prepareProducts($find);
					}
					break;
				case 'eancode': case 'isbnnum':
					$qCode = preg_replace("/\D/iu", '', $q);
					if (!empty($qCode)) {
						$sql = ''.
							'select entity, real_id '.
							'from books_catalog, pereodics_catalog, printed_catalog, music_catalog, musicsheets_catalog, video_catalog, maps_catalog, soft_catalog ' .
							'where (' . $codeName . ' = ' . $qCode . ') '.
							'option ranker=none '.
						'';
						$find = SphinxQL::getDriver()->multiSelect($sql);
//						if (!empty($find)) return $this->_prepareProducts($find); закоментировал, что бы искать еще в wrong_isbn
					}
					break 2;
				default:
					$qCode = preg_replace("/\D/iu", '', $q);
					if (!empty($qCode)) {
						$sql = ''.
							'select entity, real_id '.
							'from books_catalog, pereodics_catalog, printed_catalog, music_catalog, musicsheets_catalog, video_catalog, maps_catalog, soft_catalog ' .
							'where (' . $codeName . ' = ' . $qCode . ') '.
							'option ranker=none '.
						'';
						$find = SphinxQL::getDriver()->multiSelect($sql);
						if (!empty($find)) return $this->_prepareProducts($find);
					}
					break;
			}
		}
		if (empty($find)) $find = array();
		$sql = ''.
			'select entity, real_id '.
			'from wrong_isbn ' .
			'where match(' . SphinxQL::getDriver()->mest(str_replace(array('-', 'x'), array('',''), $q)) . ') '.
			'option ranker=none '.
		'';
		$find = array_merge($find, SphinxQL::getDriver()->multiSelect($sql));
		if (!empty($find)) return $this->_prepareProducts($find);
		return array();
	}

}
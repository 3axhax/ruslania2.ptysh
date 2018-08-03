<?php
/*Created by Кирилл (01.08.2018 18:57)*/

class Condition {

	private $_data = array(), $_entity = null, $_cid = null;
	private $_condition = array(), $_join = array();
	private $_onlySupportLanguage = false;
	private $_languageCondition = array();

	private $_isDiscount = array(
		'category'=>array(),
		'series'=>array(),
		'publisher'=>array(),
		'years'=>array(),
		'entity'=>0,
		'user'=>0,
		'ruslania'=>0,
	);

	static private $_self = array();

	final private function __construct($entity, $cid) {
		$this->_entity = (int) $entity;
		$this->_cid = (int) $cid;
		$this->_data = FilterHelper::getFiltersData($this->_entity, $this->_cid);
		$this->_fillConditionJoin();
	}

	function g($name, $default = null) { return isset($this->_data[$name])?$this->_data[$name]:$default; }
	function gav($arrKey, $name, $default = null) {
		$arr = $this->g($arrKey);
		if (!is_array($arr)) return $default;
		return isset($arr[$name])?$arr[$name]:$default;
	}

	function getCondition() { return $this->_condition; }
	function getJoin() { return $this->_join; }

	function onlySupportCondition($onlySupport = true) {
		if (!$onlySupport) return $this->_languageCondition;

		if ($onlySupport&&$this->_onlySupportLanguage) return $this->_languageCondition;

		return array();
	}

	/**
	 * @return Condition
	 */
	static function get($entity, $cid) {
		$key = 'e' . (int)$entity . 'c' . (int) $cid;
		if (!isset(self::$_self[$key])) self::$_self[$key] = new self($entity, $cid);
		return self::$_self[$key];
	}

	private function _fillConditionJoin() {
		$this->_avail();
		$this->_category();
		$this->_years();
		$this->_price();
		$this->_author();
		$this->_seria();
		$this->_publisher();
		$this->_binding();
		$this->_media();
		$this->_stream();
		$this->_subtitle();

		//Важно, что бы _lang() запускался последним.
		$this->_lang();
	}

	private function _category() {
		if ($this->_cid > 0) {
			$category = new Category();
			$allChildren = $category->GetChildren($this->_entity, $this->_cid);
			$allChildren[] = $this->_cid;
			$this->_condition['cid'] = '((t.code in (' . implode(', ', $allChildren) . ')) or (t.subcode in (' . implode(', ', $allChildren) . ')))';
		}
	}

	private function _avail() {
		$avail = $this->g('avail');
		if (($avail === 0)||($avail === '0')) return;

		$this->_condition['avail'] = '(t.avail_for_order = 1)';
	}

	private function _years() {
		if (!Entity::checkEntityParam($this->_entity, 'years')) return;

		$yMin = abs((int) $this->g('year_min'));
		$yMax = abs((int) $this->g('year_max'));
		if (($yMax > 0)&&($yMax < $yMin)) {
			$buf = $yMin;
			$yMin = $yMax;
			$yMax = $buf;
		}
		if (!empty($yMin)||!empty($yMax)) {
			if (empty($yMin)) $this->_condition['year'] = '(t.year <= ' . $yMax . ')';
			elseif (empty($yMax)) $this->_condition['year'] = '(t.year >= ' . $yMin . ')';
			elseif ($yMin == $yMax) $this->_condition['year'] = '(t.year = ' . $yMin . ')';
			else $this->_condition['year'] = '(t.year between ' . $yMin . ' and ' . $yMax . ')';
		}
	}

	private function _price() {
		//TODO:: цена для PERIODIC как то хитро сделална. Буду делать позжее.
		if ($this->_entity == Entity::PERIODIC) return;

		$bMin = abs((float) $this->g('cost_min'));
		$bMax = abs((float) $this->g('cost_max'));
		if (($bMax > 0)&&($bMax < $bMin)) {
			$buf = $bMin;
			$bMin = $bMax;
			$bMax = $buf;
		}
		//TODO:: добавить конвертацию валюты
		if (!empty($bMin)||!empty($bMax)) {
			$rates = Currency::GetRates();
			$rate = $rates[Yii::app()->currency];

			$this->_fillDiscounts();
			$allPercent = $this->_isDiscount['user'];
			if (!empty($this->_isDiscount['entity'])) $allPercent = max($this->_isDiscount['user'], $this->_isDiscount['entity'], $this->_isDiscount['ruslania']);
			$categoryPercent = $this->_getSqlBruttoCategory();
			$seriesPercent = $this->_getSqlBrutto('series', 'series_id');
			$publisherPercent = $this->_getSqlBrutto('publisher', 'publisher_id');
			$yearPercent = $this->_getSqlBrutto('year', 'year');

			$percents = array();
			if (!empty($allPercent)) $percents[] = $allPercent;
			if (!empty($categoryPercent)) $percents[] = $categoryPercent;
			if (!empty($seriesPercent)) $percents[] = $seriesPercent;
			if (!empty($publisherPercent)) $percents[] = $publisherPercent;
			if (!empty($yearPercent)) $percents[] = $yearPercent;

			$sqlPercent = '';
			//что то ломает придумывать алгоритм
			switch (count($percents)) {
				case 1: $sqlPercent = $percents[0]; break;
				case 2: $sqlPercent = 'if(' . $percents[0] . ' > ' . $percents[1] . ', ' . $percents[0] . ', ' . $percents[1] . ')'; break;
				case 3: $sqlPercent = 'if(' . $percents[0] . ' > ' . $percents[1] . ', if(' . $percents[0] . ' > ' . $percents[2] . ', ' . $percents[0] . ', ' . $percents[2] . '), if(' . $percents[1]  . ' > ' . $percents[2] . ', ' . $percents[1] . ', ' . $percents[2] . '))'; break;
				case 4: $sqlPercent = ''.
					'case '.
						'when (' . $percents[0] . ' >= ' . $percents[1] . ') and (' . $percents[0] . ' >= ' . $percents[2] . ') and (' . $percents[0] . ' >= ' . $percents[3] . ') then ' . $percents[0] . ' '.
						'when (' . $percents[1] . ' >= ' . $percents[0] . ') and (' . $percents[1] . ' >= ' . $percents[2] . ') and (' . $percents[1] . ' >= ' . $percents[3] . ') then ' . $percents[1] . ' '.
						'when (' . $percents[2] . ' >= ' . $percents[1] . ') and (' . $percents[2] . ' >= ' . $percents[0] . ') and (' . $percents[2] . ' >= ' . $percents[3] . ') then ' . $percents[2] . ' '.
					'else ' . $percents[3] . ' end '.
				'';
					break;
				case 5: $sqlPercent = ''.
					'case '.
						'when (' . $percents[0] . ' >= ' . $percents[1] . ') and (' . $percents[0] . ' >= ' . $percents[2] . ') and (' . $percents[0] . ' >= ' . $percents[3] . ') and (' . $percents[0] . ' >= ' . $percents[4] . ') then ' . $percents[0] . ' '.
						'when (' . $percents[1] . ' >= ' . $percents[0] . ') and (' . $percents[1] . ' >= ' . $percents[2] . ') and (' . $percents[1] . ' >= ' . $percents[3] . ') and (' . $percents[1] . ' >= ' . $percents[4] . ') then ' . $percents[1] . ' '.
						'when (' . $percents[2] . ' >= ' . $percents[1] . ') and (' . $percents[2] . ' >= ' . $percents[0] . ') and (' . $percents[2] . ' >= ' . $percents[3] . ') and (' . $percents[2] . ' >= ' . $percents[4] . ') then ' . $percents[2] . ' '.
						'when (' . $percents[3] . ' >= ' . $percents[1] . ') and (' . $percents[2] . ' >= ' . $percents[0] . ') and (' . $percents[2] . ' >= ' . $percents[3] . ') and (' . $percents[3] . ' >= ' . $percents[4] . ') then ' . $percents[2] . ' '.
					'else ' . $percents[4] . ' end '.
				'';
					break;
			}

			if (empty($sqlPercent)) $brutto = 'if((ifnull(t.discount, 0) > 0) and (t.brutto > ifnull(t.discount, 0)), t.discount, t.brutto)';
			else {
				$brutto = '(t.brutto - (' . $sqlPercent . ')*t.brutto/100)';
				$brutto = 'if((ifnull(t.discount, 0) > 0) and (' . $brutto . ' > t.discount), t.discount, ' . $brutto . ')';
			}


			if (empty($bMin)) $this->_condition['brutto'] = '(' . $brutto . ' <= ' . $bMax / $rate . ')';
			elseif (empty($bMax)) $this->_condition['brutto'] = '(' . $brutto . ' >= ' . $bMin / $rate . ')';
			elseif ($bMin == $bMax) $this->_condition['brutto'] = '(' . $brutto . ' = ' . $bMin / $rate . ')';
			else $this->_condition['brutto'] = '(' . $brutto . ' between ' . $bMin / $rate . ' and ' . $bMax / $rate . ')';
		}
	}

	private function _author() {
		if (Entity::checkEntityParam($this->_entity, 'authors')) {
			$aid = (int) $this->g('author');
			if ($aid > 0) {
				$entityParams = Entity::GetEntitiesList()[$this->_entity];
				$this->_join['tA'] = 'join ' . $entityParams['author_table'] . ' tA on (tA.' . $entityParams['author_entity_field'] . ' = t.id) and (tA.author_id = ' . $aid . ')';
			}
		}
	}

	private function _seria() {
		if (Entity::checkEntityParam($this->_entity, 'series')) {
			$sid = (int) $this->g('series');
			if ($sid > 0) $this->_condition['seria_id'] = '(t.series_id = ' . $sid . ')';
		}
	}

	private function _publisher() {
		if (Entity::checkEntityParam($this->_entity, 'publisher')) {
			$pid = (int) $this->g('publisher');
			if ($pid > 0) $this->_condition['publisher_id'] = '(t.publisher_id = ' . $pid . ')';
		}
	}

	private function _binding() {
		if (Entity::checkEntityParam($this->_entity, 'binding')||Entity::checkEntityParam($this->_entity, 'media')) {
			$bindings = $this->g('binding');
			if (is_array($bindings)) {
				foreach ($bindings as $i=>$binding) {
					$binding = (int) $binding;
					if ($binding <= 0) unset($bindings[$i]);
					else $bindings[$i] = $binding;
				}
				if (!empty($bindings)) {
					if (Entity::checkEntityParam($this->_entity, 'media'))  $this->_condition['binding_id'] = '(t.media_id in (' . implode(',', $bindings) . '))';
					else $this->_condition['binding_id'] = '(t.binding_id in (' . implode(',', $bindings) . '))';
				}
			}
		}
	}

	private function _media() {
		if (Entity::checkEntityParam($this->_entity, 'media')) {
			$pid = (int) $this->g('format_video');
			if ($pid > 0) $this->_condition['media_id'] = '(t.media_id = ' . $pid . ')';
		}
	}

	private function _stream() {
		if (Entity::checkEntityParam($this->_entity, 'audiostreams')) {
			$pid = (int) $this->g('lang_video');
			if ($pid > 0) $this->_join['tVAS'] = 'join video_audiostreams tVAS on (tVAS.video_id = t.id) and (tVAS.stream_id = ' . $pid . ')';
		}
	}

	private function _subtitle() {
		if (Entity::checkEntityParam($this->_entity, 'subtitles')) {
			$pid = (int) $this->g('subtitles_video');
			if ($pid > 0) $this->_join['tVC'] = 'join video_credits tVC on (tVAS.video_id = t.id) and (tVC.credits_id = ' . $pid . ')';
		}
	}

	private function _lang() {
		if (!Entity::checkEntityParam($this->_entity, 'languages')) return;

		$langsel = (int) $this->g('lang_sel');
		if ($langsel > 0) {
			if (empty($this->_condition['avail'])) {
				$this->_join['tL_all'] = 'join all_items_languages tL on (tL.item_id = t.id) and (tL.language_id = ' . $langsel . ')';
			}
			else {
				unset($this->_condition['avail']);
				if (empty($this->_join)&&(($countCond = count($this->_condition)) <= 3)) {
					if (!empty($this->_condition['cid'])) $countCond--;
					if (!empty($this->_condition['year'])) $countCond--;
					if (!empty($this->_condition['brutto'])) {
						if (mb_strpos($this->_condition['brutto'], 't.code', null, 'utf-8') === false) $countCond--;
					}
					if ($countCond === 0) $this->_onlySupportLanguage = true;
				}
				$this->_languageCondition = array('language_id'=>'(t.language_id = ' . $langsel . ')');
				$condition = array('id'=>'(tL.id = t.id)', 'language_id'=>'(tL.language_id = ' . $langsel . ')');
				if (!empty($this->_condition['cid'])) {
					$condCid = explode(' or ', $this->_condition['cid']);
					$condCid = array_shift($condCid);
					$condCid = explode(' in ', $condCid);
					$condCid = array_pop($condCid);
					$this->_languageCondition['cid'] = '(t.category_id in ' . $condCid;
					$condition['cid'] = '(tL.category_id in ' . $condCid;
					unset($this->_condition['cid']);
				}
				else {
					$this->_languageCondition['isSubcode'] = '(t.isSubcode = 0)';
					$condition['isSubcode'] = '(tL.isSubcode = 0)';
				}
				if (!empty($this->_condition['year'])) {
					$this->_languageCondition['year'] = str_replace('(t.', '(t.', $this->_condition['year']);
					$condition['year'] = str_replace('(t.', '(tL.', $this->_condition['year']);
					unset($this->_condition['year']);
				}
				if (!empty($this->_condition['brutto'])) {
					$this->_languageCondition['brutto'] = str_replace('(t.', '(t.', $this->_condition['brutto']);
					$condition['brutto'] = str_replace('(t.', '(tL.', $this->_condition['brutto']);
					unset($this->_condition['brutto']);
				}
				$this->_join['tL_support'] = 'join _support_languages_' . Entity::GetUrlKey($this->_entity) . ' tL on ' . implode(' and ', $condition);

			}
		}
	}

	private function _fillDiscounts() {
		$this->_isDiscount['user'] = Yii::app()->user->id?Yii::app()->user->GetPersonalDiscount():0;
		$maxDiscount = $this->_isDiscount['user'];
		$dateStart = $dateEnd = date('Y-m-d');
//		$dateEnd = '2017-06-01';

		$sql = 'select type_id, max(discount) discount '.
			'from discounts '.
			'where (ifnull(entity_id, 0) in (0, ' . $this->_entity . ')) '.
				'and (ifnull(discount, 0) > ' . $maxDiscount . ') '.
				'and (start_date <= "' . $dateStart . '") '.
				'and (end_date >= "' . $dateEnd . '") '.
			'group by type_id '.
		'';
		$discounts = Yii::app()->db->createCommand($sql)->queryAll();

		//надо 2 раза пробежаться по массиву скидок, чтобы сначала получить максимальную скидку на все товары
		$maxDiscount = $this->_fillDiscountAll($discounts, $maxDiscount);
		//а второй раз скидку на категорию (серию, тд) больше чем общая скидка
		foreach ($discounts as $discount) {
			$idTypeDiscount = (int) $discount['type_id'];
			switch ($idTypeDiscount) {
				case 2: $this->_fillDiscountCategory($idTypeDiscount, $dateStart, $dateEnd, $maxDiscount); break;
				case 3: $this->_fillDiscountParams('series', 'serie_id', $idTypeDiscount, $dateStart, $dateEnd, $maxDiscount); break;
				case 4: $this->_fillDiscountParams('publisher', 'publisher_id', $idTypeDiscount, $dateStart, $dateEnd, $maxDiscount); break;
				case 9: $this->_fillDiscountParams('years', 'year', $idTypeDiscount, $dateStart, $dateEnd, $maxDiscount); break;
			}
		}
	}

	private function _fillDiscountAll($discounts, $maxDiscount) {
		foreach ($discounts as $discount) {
			$idTypeDiscount = (int) $discount['type_id'];
			switch ($idTypeDiscount) {
				case 1:
					$this->_isDiscount['ruslania'] = (float) $discount['discount'];
					if ($this->_isDiscount['ruslania'] > $maxDiscount) {
						$maxDiscount = $this->_isDiscount['ruslania'];
						$this->_isDiscount['user'] = 0;
						$this->_isDiscount['entity'] = 0;
					}
					break;
				case 10:
					$this->_isDiscount['entity'] = (float) $discount['discount'];
					if ($this->_isDiscount['entity'] > $maxDiscount) {
						$maxDiscount = $this->_isDiscount['entity'];
						$this->_isDiscount['user'] = 0;
						$this->_isDiscount['ruslania'] = 0;
					}
					break;
			}
		}
		return $maxDiscount;
	}

	private function _fillDiscountParams($paramName, $paramId, $idTypeDiscount, $dateStart, $dateEnd, $maxDiscount) {
		if (!Entity::checkEntityParam($this->_entity, $paramName)) return;
		$sql = 'select ' . $paramId . ', discount '.
			'from discounts '.
			'where (entity_id = ' . $this->_entity . ') '.
				'and (type_id = ' . $idTypeDiscount . ') '.
				'and (start_date <= "' . $dateStart . '") '.
				'and (end_date >= "' . $dateEnd . '") '.
				'and (ifnull(discount, 0) > ' . $maxDiscount . ') '.
		'';
		foreach (Yii::app()->db->createCommand($sql)->queryAll() as $row) {
			if (empty($this->_isDiscount[$paramName][$row[$paramId]])) $this->_isDiscount[$paramName][$row[$paramId]] = 0;
			$this->_isDiscount[$paramName][$row[$paramId]] = max($this->_isDiscount[$paramName][$row[$paramId]], (float) $row['discount']);
		}
	}

	private function _fillDiscountCategory($idTypeDiscount, $dateStart, $dateEnd, $maxDiscount) {
		$sql = 'select category_id, discount '.
			'from discounts '.
			'where (entity_id = ' . $this->_entity . ') '.
				'and (type_id = ' . DiscountManager::TYPE_PART . ') '.
				'and (start_date <= "' . $dateStart . '") '.
				'and (end_date >= "' . $dateEnd . '") '.
				'and (ifnull(discount, 0) > ' . $maxDiscount . ') '.
		'';
		foreach (Yii::app()->db->createCommand($sql)->queryAll() as $row) {
			$row['discount'] = (float) $row['discount'];
			$c = new Category;
			$childIds = $c->GetChildren($this->_entity, $row['category_id']);
			array_push($childIds, $row['category_id']);
			if (empty($this->_cid)||in_array($this->_cid, $childIds)) {
				foreach ($childIds as $cid) {
					if (empty($this->_isDiscount['category'][$cid])) $this->_isDiscount['category'][$cid] = 0;
					$this->_isDiscount['category'][$cid] = max($this->_isDiscount['category'][$cid], $row['discount']);
				}
			}
		}
	}

	private function _getSqlBrutto($paramName, $paramId) {
		if (!Entity::checkEntityParam($this->_entity, $paramName)) return '';

		$percents = array();
		$result = array();
		foreach ($this->_isDiscount[$paramName] as $id=>$percent) {
			if (empty($percents[$percent])) $percents[$percent] = array();
			$percents[$percent][] = $id;
		}
		foreach ($percents as $percent=>$ids) {
			$result[] = 'if(t.' . $paramId . ' in (' . implode(',', $ids) . '), "' . $percent . '", ';
		}
		if (!empty($result)) {
			$result = implode($result) . '0' . str_repeat(')', count($result));
		}
		else $result = '';
		return $result;
	}

	private function _getSqlBruttoCategory() {
		$percents = array();
		$result = array();
		foreach ($this->_isDiscount['category'] as $id=>$percent) {
			if (empty($percents[$percent])) $percents[$percent] = array();
			$percents[$percent][] = $id;
		}
		foreach ($percents as $percent=>$ids) {
			$result[] = 'if((t.code in (' . implode(',', $ids) . ')) or (t.subcode in (' . implode(',', $ids) . ')), "' . $percent . '", ';
		}
		if (!empty($result)) {
			$result = implode($result) . '0' . str_repeat(')', count($result));
		}
		else $result = '';
		return $result;
	}

}
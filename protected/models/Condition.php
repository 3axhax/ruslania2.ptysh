<?php
/*Created by Кирилл (01.08.2018 18:57)*/

class Condition {

	private $_data = array(), $_entity = null, $_cid = null;
	private $_condition = array(), $_join = array();
	private $_onlySupportLanguage = false;
	private $_languageCondition = array();

	static private $_self = array();

	final private function __construct($entity, $cid) {
		$this->_entity = (int) $entity;
		$this->_cid = (int) $cid;
		$this->_data = FilterHelper::getFiltersData($this->_entity, $this->_cid);
		Debug::staticRun(array($this->_data));
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
		$bMin = abs((float) $this->g('cost_min'));
		$bMax = abs((float) $this->g('cost_max'));
		if (($bMax > 0)&&($bMax < $bMin)) {
			$buf = $bMin;
			$bMin = $bMax;
			$bMax = $buf;
		}
		//TODO:: добавить конвертацию валюты
		if (!empty($bMin)||!empty($bMax)) {
			if (empty($bMin)) $this->_condition['brutto'] = '(t.brutto <= ' . $bMax . ')';
			elseif (empty($bMax)) $this->_condition['brutto'] = '(t.brutto >= ' . $bMin . ')';
			elseif ($bMin == $bMax) $this->_condition['brutto'] = '(t.brutto = ' . $bMin . ')';
			else $this->_condition['brutto'] = '(t.brutto between ' . $bMin . ' and ' . $bMax . ')';
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
					if (!empty($this->_condition['brutto'])) $countCond--;
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

}
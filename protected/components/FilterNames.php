<?php
/*Created by Кирилл (29.01.2019 16:22)*/

class FilterNames {
	static private $_self = array();
	private $_eid, $_cid;
	private $_data = array();
	private $_params = array();

	private function __construct($entity, $cid) {
		$this->_eid = $entity;
		$this->_cid = $cid;
		$filter = FilterHelper::getFiltersData($this->_eid, $this->_cid);
		foreach($filter as $k=>$v) {
			switch ($k) {
				case 'entity': case 'cid': case 'sort': case 'year_min': case 'cost_min': case 'release_year_min': break;
				case 'avail': if (empty($v)) $this->_data[$k] = $v; break;
				case 'year_max':
					$this->_data['year'] = array();
					if (!empty($filter['year_min'])) $this->_data['year']['min'] = $filter['year_min'];
					if (!empty($v)) $this->_data['year']['max'] = $v;
					if (empty($this->_data['year'])) unset($this->_data['year']);
					break;
				case 'cost_max':
					$this->_data['cost'] = array();
					if (!empty($filter['cost_min'])) $this->_data['cost']['min'] = $filter['cost_min'];
					if (!empty($v)) $this->_data['cost']['max'] = $v;
					if (empty($this->_data['cost'])) unset($this->_data['cost']);
					break;
				case 'release_year_max':
					$this->_data['release_year'] = array();
					if (!empty($filter['release_year_min'])) $this->_data['release_year']['min'] = $filter['release_year_min'];
					if (!empty($v)) $this->_data['release_year']['max'] = $v;
					if (empty($this->_data['release_year'])) unset($this->_data['release_year']);
					break;
				default: if (!empty($v)) $this->_data[$k] = $v; break;
			}
		}
		Debug::staticRun(array($this->_data, $filter));
	}

	function getParams() {
		$params = array();
		foreach ($this->_data as $k=>$v) {
			if ($k == 'lang_sel') continue;
			$res = $this->$k;
			if (!empty($res)) $params[$k] = $res;
		}
		return $params;
	}

	function __get($name) {
		if (isset($this->_data[$name])) {
			if (!isset($this->_params[$name])) {
				$fname = '_get' . mb_strtoupper(mb_substr($name, 0, 1, 'utf-8')) . mb_substr($name, 1, null, 'utf-8');
				$this->_params[$name] = $this->$fname();
			}
			return $this->_params[$name];
		}
		return '';
	}

	private function _getCost() {
		if (empty($this->_data['cost'])) return '';
		return Yii::app()->ui->item('CART_COL_PRICE') . ': ' . implode('-',  $this->_data['cost']) . Currency::ToSign(Yii::app()->currency);
	}

	private function _getRelease_year() {
		if (empty($this->_data['release_year'])) return '';
		return Yii::app()->ui->item('A_NEW_YEAR_FILM') . ': ' . implode('-',  $this->_data['release_year']);
	}

	private function _getYear() {
		if (empty($this->_data['year'])) return '';
		return Yii::app()->ui->item('A_NEW_FILTER_YEAR') . ': ' . implode('-',  $this->_data['year']);
	}

	private function _getAvail() {
		if (!isset($this->_data['avail'])) return '';
		if (!empty($this->_data['avail'])) return '';
		return Yii::app()->ui->item('CART_COL_ITEM_AVAIBILITY') . ': ' . Yii::app()->ui->item('A_NEW_FILTER_ALL');
	}

	private function _getAuthor() {
		if (empty($this->_data['author'])) return '';
		$author = CommonAuthor::model()->GetById($this->_data['author']);
		return Yii::app()->ui->item('A_NEW_FILTER_AUTHOR') . ': ' . ProductHelper::GetTitle($author);
	}

	private function _getPublisher() {
		if (empty($this->_data['publisher'])) return '';

		$label = Yii::app()->ui->item('A_NEW_FILTER_PUBLISHER');
		switch ($this->_eid) {
			case Entity::MUSIC: $label = Yii::app()->ui->item('A_NEW_LABEL'); break;
			case Entity::MAPS: case Entity::SOFT: case Entity::PRINTED: $label = Yii::app()->ui->item('A_NEW_PRODUCER'); break;
		}

		$publisher = Publisher::model()->GetById($this->_eid, $this->_data['publisher']);
		Debug::staticRun(array($this->_data['publisher'], $publisher));
		return $label . ': ' . ProductHelper::GetTitle($publisher);
	}

	private function _getSeries() {
		if (empty($this->_data['series'])) return '';
		$label = Yii::app()->ui->item('A_NEW_FILTER_SERIES');
		$serie = Series::model()->findByAttributes(['id'=>$this->_data['series'], 'entity'=>$this->_eid]);
		return $label . ': ' . ProductHelper::GetTitle($serie->getAttributes());
	}

	private function _getBinding() {
		if (empty($this->_data['binding'])) return '';
		switch ($this->_eid) {
			case Entity::BOOKS:case Entity::SHEETMUSIC: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE1'); break;
			case Entity::MUSIC: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE3'); break;
			case Entity::PERIODIC: $label = Yii::app()->ui->item('A_NEW_TYPE_IZD'); break;
			default: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE2'); break;
		}
		$bindings = array();
		$binding = new Binding();
		foreach ($this->_data['binding'] as $bid) {
			$b = $binding->GetBinding($this->_eid, $bid);
			if (!empty($b)) $bindings[] = ProductHelper::GetTitle($b);
		}
		if (empty($bindings)) return '';
		return $label . ': ' . implode(', ', $bindings);
	}

	private function _getLang_sel() {
		if (empty($this->_data['lang_sel'])) return '';

		switch ($this->_eid) {
			case Entity::PRINTED: return Yii::app()->ui->item('A_NEW_FILTER_TITLE_THEME') . Language::GetTitleByID_country($this->_data['lang_sel']); break;
		}
		return Language::GetTitleByID_predl($this->_data['lang_sel']);
	}

	/**
	 * @return FilterNames
	 */
	static function get($entity, $cid) {
		$key = 'e' . (int)$entity . 'c' . (int) $cid;
		if (!isset(self::$_self[$key])) self::$_self[$key] = new self($entity, $cid);
		return self::$_self[$key];
	}

}
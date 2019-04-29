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
					if (isset($this->_data['year']['max'])&&isset($this->_data['year']['min'])&&($this->_data['year']['max'] == $this->_data['year']['min'])) unset($this->_data['year']['max']);
					if (empty($this->_data['year'])) unset($this->_data['year']);
					break;
				case 'cost_max':
					$this->_data['cost'] = array();
					if (!empty($filter['cost_min'])) $this->_data['cost']['min'] = $filter['cost_min'];
					if (!empty($v)) $this->_data['cost']['max'] = $v;
					if (isset($this->_data['cost']['max'])&&isset($this->_data['cost']['min'])&&($this->_data['cost']['max'] == $this->_data['cost']['min'])) unset($this->_data['cost']['max']);
					if (empty($this->_data['cost'])) unset($this->_data['cost']);
					break;
				case 'release_year_max':
					$this->_data['release_year'] = array();
					if (!empty($filter['release_year_min'])) $this->_data['release_year']['min'] = $filter['release_year_min'];
					if (!empty($v)) $this->_data['release_year']['max'] = $v;
					if (isset($this->_data['release_year']['max'])&&isset($this->_data['release_year']['min'])&&($this->_data['release_year']['max'] == $this->_data['release_year']['min'])) unset($this->_data['release_year']['max']);
					if (empty($this->_data['release_year'])) unset($this->_data['release_year']);
					break;
				default: if (!empty($v)) $this->_data[$k] = $v; break;
			}
		}
	}

	function getParams($route = '') {
		if ($route == 'entity/list') return array();

		$params = array();
		$routes = array(
			'entity/bypublisher'      =>  'publisher',
			'entity/byseries'         =>  'series',
			'entity/byauthor'         =>  'author',
			'entity/bybinding'        =>  'binding',
			'entity/byyear'           =>  'year',
			'entity/byyearrelease'    =>  'release_year',
			'entity/byperformer'      =>  'performer',
			'entity/bymedia'          =>  'format_video',
	//		'entity/bytype'
			'entity/byactor'          =>  'actors',
			'entity/bydirector'       =>  'directors',
			'entity/byaudiostream'    =>  'lang_video',
			'entity/bysubtitle'       =>  'subtitles_video',
	//		'entity/bystudio'
		);
		if (!empty($routes[$route])&&isset($this->_data[$routes[$route]])) {
			$res = $this->$routes[$route];
			if (!empty($res)) $params[$routes[$route]] = $res;
		}
		else {
			foreach ($this->_data as $k=>$v) {
				if ($k == 'lang_sel') continue;
				$res = $this->$k;
				if (!empty($res)) $params[$k] = $res;
			}
		}
		return $params;
	}

	function __get($name) {
		if (isset($this->_data[$name])) {
			if (!isset($this->_params[$name])) {
				$fname = '_get' . mb_strtoupper(mb_substr($name, 0, 1, 'utf-8')) . mb_substr($name, 1, null, 'utf-8');
				if (method_exists($this, $fname)) $this->_params[$name] = $this->$fname();
			}
			return $this->_params[$name];
		}
		return '';
	}

	private function _getCost() {
		if (empty($this->_data['cost'])) return '';
		return mb_strtolower(Yii::app()->ui->item('CART_COL_PRICE'), 'utf-8') . ': ' . implode('-',  $this->_data['cost']) . Currency::ToSign(Yii::app()->currency);
	}

	private function _getRelease_year() {
		if (empty($this->_data['release_year'])) return '';
//		return mb_strtolower(Yii::app()->ui->item('A_NEW_YEAR_FILM'), 'utf-8') . ': ' . implode('-',  $this->_data['release_year']);
		return Yii::app()->ui->item('IN_YEAR', implode('-',  $this->_data['release_year']));
	}

	private function _getYear() {
		if (empty($this->_data['year'])) return '';
//		return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_YEAR'), 'utf-8') . ': ' . implode('-',  $this->_data['year']);
		return Yii::app()->ui->item('IN_YEAR', implode('-',  $this->_data['year']));
	}

	private function _getAvail() {
		if ($this->_eid == Entity::PERIODIC) return '';
		if (!isset($this->_data['avail'])) return '';
		if (!empty($this->_data['avail'])) return '';
		return mb_strtolower(Yii::app()->ui->item('CART_COL_ITEM_AVAIBILITY'), 'utf-8') . ': ' . Yii::app()->ui->item('A_NEW_FILTER_ALL');
	}

	private function _getAuthor() {
		if (empty($this->_data['author'])) return '';
		$author = CommonAuthor::model()->GetById($this->_data['author']);
		return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_AUTHOR'), 'utf-8') . ': ' . ProductHelper::GetTitle($author);
	}

	private function _getPublisher() {
		if (empty($this->_data['publisher'])) return '';

		$label = Yii::app()->ui->item('A_NEW_FILTER_PUBLISHER');
		switch ($this->_eid) {
			case Entity::MUSIC: $label = Yii::app()->ui->item('A_NEW_LABEL'); break;
			case Entity::MAPS: case Entity::SOFT: case Entity::PRINTED: $label = Yii::app()->ui->item('A_NEW_PRODUCER'); break;
		}

		$publisher = Publisher::model()->GetById($this->_eid, $this->_data['publisher']);
		return mb_strtolower($label, 'utf-8') . ': ' . ProductHelper::GetTitle($publisher);
	}

	private function _getSeries() {
		if (empty($this->_data['series'])) return '';
		$label = Yii::app()->ui->item('A_NEW_FILTER_SERIES');
		$serie = Series::model()->findByAttributes(['id'=>$this->_data['series'], 'entity'=>$this->_eid]);
		return mb_strtolower($label, 'utf-8') . ': ' . ProductHelper::GetTitle($serie->getAttributes());
	}

	private function _getBinding() {
		if (empty($this->_data['binding'])) return '';
		switch ($this->_eid) {
			case Entity::BOOKS:case Entity::SHEETMUSIC: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE1'); $binding = new Binding(); break;
			case Entity::MUSIC: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE3'); $binding = MusicMedia::model(); break;
			case Entity::PERIODIC: $label = Yii::app()->ui->item('A_NEW_TYPE_IZD'); $binding = PereodicsTypes::model(); break;
			default: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE2'); $binding = new Binding(); break;
		}
		$bindings = array();
		foreach ($this->_data['binding'] as $bid) {
			$b = $binding->GetBinding($this->_eid, $bid);
			if (!empty($b)) $bindings[] = ProductHelper::GetTitle($b);
		}
		if (empty($bindings)) return '';
		return mb_strtolower($label, 'utf-8') . ': ' . implode(', ', $bindings);
	}

	private function _getLang_sel() {
		if (empty($this->_data['lang_sel'])) return '';

		switch ($this->_eid) {
			case Entity::PRINTED:
				return Yii::app()->ui->item('A_NEW_FILTER_TITLE_THEME') . Language::GetTitleByID_country($this->_data['lang_sel']);
				break;
		}
		if (in_array(Yii::app()->getLanguage(), array('en', 'de'))) return Language::GetTitleByID_predl($this->_data['lang_sel']);
		return mb_strtolower(Language::GetTitleByID_predl($this->_data['lang_sel']), 'utf-8');
	}

	private function _getPre_sale() {
		$this->_data['pre_sale'] = (int) $this->_data['pre_sale'];
		switch ($this->_data['pre_sale']) {
			case 1: return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_PRE_SALE_2'), 'utf-8'); break;
			case 2: return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_PRE_SALE_3'), 'utf-8'); break;
		}
		return '';
	}

	private function _getActors() {
		if (empty($this->_data['actors'])) return '';
		$actor = CommonAuthor::model()->GetById($this->_data['actors']);
		return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_ACTORS'), 'utf-8') . ': ' . ProductHelper::GetTitle($actor);
	}

	private function _getDirectors() {
		if (empty($this->_data['directors'])) return '';
		$item = CommonAuthor::model()->GetById($this->_data['directors']);
		return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_DIRECTORS'), 'utf-8') . ': ' . ProductHelper::GetTitle($item);
	}

	private function _getPerformer() {
		if (empty($this->_data['performer'])) return '';
		$item = CommonAuthor::model()->GetById($this->_data['performer']);
		return mb_strtolower(Yii::app()->ui->item('A_NEW_FILTER_PERFORMER'), 'utf-8') . ': ' . ProductHelper::GetTitle($item);
	}

	private function _getLang_video() {
//		lang_video
		$this->_data['lang_video'] = (int) $this->_data['lang_video'];
		if ($this->_data['lang_video'] <= 0) return '';
		$stream = VideoAudioStream::model()->findByPk($this->_data['lang_video']);
		$label = Yii::app()->ui->item('A_NEW_FILTER_LANG_VIDEO');
		return mb_strtolower($label, 'utf-8') . ': ' . ProductHelper::GetTitle($stream->getAttributes());
	}

	private function _getSubtitles_video() {
		$this->_data['subtitles_video'] = (int) $this->_data['subtitles_video'];
		if ($this->_data['subtitles_video'] <= 0) return '';

		$subtitle = VideoSubtitle::model()->findByPk($this->_data['subtitles_video']);
		$label = Yii::app()->ui->item('A_NEW_FILTER_LANG_SUBTITLES');
		return mb_strtolower($label, 'utf-8') . ': ' . ProductHelper::GetTitle($subtitle->getAttributes());
	}

	private function _getFormat_video() {
		$this->_data['format_video'] = (int) $this->_data['format_video'];
		if ($this->_data['format_video'] <= 0) return '';

		$item = Media::model()->GetMedia($this->_eid, $this->_data['format_video']);
		if (empty($item)) return '';

		$label = Yii::app()->ui->item('A_NEW_FILTER_FORMAT_VIDEO');
		return mb_strtolower($label, 'utf-8') . ': ' . ProductHelper::GetTitle($item);
	}

	private function _getCountry() {
		$this->_data['country'] = (int) $this->_data['country'];
		if ($this->_data['country'] <= 0) return '';

		$item = PeriodicCountry::model()->findByPk($this->_data['country']);
		if (empty($item)) return '';

		$label = Yii::app()->ui->item('A_NEW_FILTER_PERIODIC_COUNTRY');
		return mb_strtolower($label, 'utf-8') . ': ' . ProductHelper::GetTitle($item->getAttributes());
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
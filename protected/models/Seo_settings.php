<?php
/*Created by Кирилл (28.01.2019 20:54)*/

class Seo_settings {
	static private $_self = null;
	protected $_replace = array();
	protected $_route = '';
	protected $_settings = array('h1'=>'', 'title'=>'', 'description'=>'', 'keywords'=>'');

	protected function __construct($params = null) {
		$controller = Yii::app()->getController();
		if ($params === null) $this->_route = $controller->id . '/' . $controller->action->id;
		else $this->_route = $params['route'];
	}

	/**
	 * @return Seo_settings
	 */
	static function get() {
		if (self::$_self === null) {
			$controller = Yii::app()->getController();
			switch (mb_strtolower($controller->id)) {
				case 'entity':
					require_once dirname(__FILE__) . '/Seo/Entity.php';
					self::$_self = new ModelsSeoEntity();
					break;
				default: self::$_self = new self; break;
			}
			self::$_self->_fillReplace();
		}
		return self::$_self;
	}

	/** метод нужен для получения экземпляра класса без его сохранения
	 * @param $params array ['route'=>'entity/list', 'entity'=>10, 'id'=>0]
	 * @return ModelsSeoEntity|Seo_settings
	 */
	static function handler($params) {
		$route = explode('/', $params['route']);
		switch (mb_strtolower($route[0])) {
			case 'entity':
				require_once dirname(__FILE__) . '/Seo/Entity.php';
				return new ModelsSeoEntity($params);
				break;
			default: return new self; break;
		}
	}

	function getDefaultSettings($lang) { return array(); }

	function getH1() {
		return $this->_prepareText($this->_getH1());
	}

	function getTitle() {
		return $this->_prepareText($this->_getTitle());
	}

	function getDescription() {
		return $this->_prepareText($this->_getDescription());
	}

	function getKeywords() {
		return $this->_prepareText($this->_getKeywords());
	}

	protected function _prepareText($s){
		if (!empty($this->_replace)) $s = str_replace(array_keys($this->_replace), $this->_replace, $s);
		$s = preg_replace('/\{\w+\}/', '', $s);
		$s = preg_replace('/[:, ]+$/', '', $s);
		$s = preg_replace('/\s+/u', ' ', $s);
		return $s;
	}

	protected function _fillReplace() {
		$this->_replace['{geoip_country}'] = geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
		$this->_replace['{domain}'] = 'Rusliania.com';
		$this->_replace['{page_n}'] = '';
		if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) {
			$this->_replace['{page_n}'] = '&ndash; '. Yii::app()->ui->item('PAGES_N', $page);
		}
	}

	/**
	 * @return string заранее подготовленный текст, в котором будут замены
	 */
	protected function _getH1() { return $this->_settings['h1']; }
	protected function _getTitle() { return $this->_settings['title']; }
	protected function _getDescription() { return $this->_settings['description']; }
	protected function _getKeywords() { return $this->_settings['keywords']; }

}
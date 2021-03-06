<?php
/*Created by Кирилл (27.07.2018 23:22)*/

/** класс написан, чтоб можно было получить route любого пути (например получить referer)
 * Class MyRefererRequest
 */
class MyRefererRequest extends CHttpRequest {
	private $_path = null, $_uri = null;
	private $_params = array();//для параметров из адреса
	private $_language = null;//для языка сайта

	function setFreePath($path) {
		$this->_uri = $path;
		$this->_path = ltrim(parse_url($path, PHP_URL_PATH), '/');
		$domains = explode('/', ltrim($this->_path, '/'));
		$validLanguages = Yii::app()->params['ValidLanguages'];
		if (in_array($domains[0], $validLanguages)) $this->_language = $domains[0];
		else $this->_language = Yii::app()->getLanguage();
	}

	function getPathInfo() {
		if ($this->_path === null) $path = parent::getPathInfo();
		else $path = $this->_path;

		$language = $this->getLangFromUrl();//Yii::app()->language;
		if (!empty($language)) {
			$langLen = mb_strlen($language, 'utf-8');
			if ($path == $language) $path = '';
			elseif (mb_strpos($path, $language . '/', null, 'utf-8') === 0) $path = mb_substr($path, $langLen+1, null, 'utf-8');
		}
		return $path;
	}

	function getRequestUri() {
		if ($this->_uri !== null) return $this->_uri;
		return parent::getRequestUri();
	}

	function getParam ($name, $defaultValue = null) {
		if (isset($this->_params[$name])) return $this->_params[$name];

		return parent::getParam($name, $defaultValue);
	}

	function setParam($name, $value) {
		$this->_params[$name] = $value;
	}

	function getLangFromUrl() { return $this->_language; }

	function getParams() {
		return $this->_params;
	}

}
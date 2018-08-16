<?php
/*Created by Кирилл (15.08.2018 22:32)*/

class EntityUrlRule extends CBaseUrlRule {
	private $_entitys = array();
	public $urlSuffix = '/';

	function __construct() {
		$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].Yii::app()->language.'/urlTranslite.php';
		foreach (include $file as $entityStr=>$urlNames) {
			if (Entity::ParseFromString($entityStr)) {
				$this->_entitys[$entityStr] = $urlNames['level_1'];
			}
		}

	}

	function createUrl($manager,$route,$params,$ampersand) {
		if (!isset($_GET['ha'])) return false;

		if (defined('OLD_PAGES')) return false;

		$prefix = array();
		$language = Yii::app()->language;
		if (!empty($params['__langForUrl'])&&in_array($params['__langForUrl'], Yii::app()->params['ValidLanguages'])) {
			//что бы получить путь для другого языка
			$language = $params['__langForUrl'];
		}
		unset($params['__langForUrl']);

		if ($language === 'rut') $params['language'] = $language;
		else $prefix[] = $language;

		if (!empty($params['lang'])) {
			$langGoods = ProductLang::getShortLang();
			if (isset($langGoods[$params['lang']])) {
				if ($language !== 'rut') {
					$prefix[] = $langGoods[$params['lang']];
					unset($params['lang']);
				}
			}
		}

		$url = '';
		switch ($route) {
			case 'entity/list':
				if (!empty($params['entity'])) {
					if (empty($params['cid'])) $url = $this->_createRazd($params['entity']);
//					return ;
				}
				unset($params['cid'], $params['entity'], $params['title']);
				break;
		}
		if (!empty($url)) {
			$params['ha'] = 1;//TODO:: убрать, когда будет все проверено
			if (!empty($prefix)) $url = implode('/', $prefix) . '/' . $url;
			if (!empty($params)) $url .= '?' . http_build_query($params);
			return $url;
		}
		return false;
	}

	function parseUrl($manager,$request,$pathInfo,$rawPathInfo) {
		if (!isset($_GET['ha'])) return false;

		if (defined('OLD_PAGES')) return false;

		if ($this->urlSuffix !== null) $pathInfo = $manager->removeUrlSuffix($rawPathInfo, $this->urlSuffix);
		// URL suffix required, but not found in the requested URL
		if($manager->useStrictParsing && ($pathInfo === $rawPathInfo)) {
			(($urlSuffix = $this->urlSuffix) === null) ? $manager->urlSuffix : $this->urlSuffix;
			if(($urlSuffix != '') && ($urlSuffix !== '/')) return false;
		}
		$pathInfo = mb_strtolower(ltrim($pathInfo, '/'));
		$pathInfo = explode('/', $pathInfo);
		if (empty($pathInfo)) return false;

		$langGoods = ProductLang::getShortLang();
		if ($langId = array_search($pathInfo[0], $langGoods)) {
			array_shift($pathInfo);
			if (empty($pathInfo)) return false;
			$_REQUEST['lang'] = $_GET['lang'] = $langId;
		}
		unset($langGoods);

		$route = array('id'=>'entity', 'actionId'=>'list');
		$level = 0;
		do {
			switch ($level++) {
				case 0: if (($route['id'] = $this->_parseRazd(array_shift($pathInfo))) === false) return false; break;
				case 1: if (($route['actionId'] = $this->_parseSub(array_shift($pathInfo))) === false) return false; break;
				case 2: if ($this->_parseId(array_shift($pathInfo)) === false) return false; break;
				default; return false; break;
			}
		} while (!empty($pathInfo));
		return implode('/', $route);
	}

	private function _createRazd($entity) {
		if (is_numeric($entity)) $entity = Entity::GetUrlKey($entity);
		if (empty($entity)) return '';
		if (!isset($this->_entitys[$entity])) return '';
		return $this->_entitys[$entity] . '/';
	}

	private function _parseRazd($urlParam) {
		if ($entity = array_search($urlParam, $this->_entitys)) {
			$entityId = (int) Entity::ParseFromString($entity);
			if ($entityId > 0) {
				$_REQUEST['entity'] = $_GET['entity'] = $entityId;
				return 'entity';
			}
		}
		return false;
	}

	private function _parseSub($urlParam) {
		return 'list';
	}

	private function _parseId($urlParam) {
		return false;
	}

}
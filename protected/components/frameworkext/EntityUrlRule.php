<?php
/*Created by Кирилл (15.08.2018 22:32)*/

class EntityUrlRule extends CBaseUrlRule {
	private $_entitys = array(), $_level2 = array();
	static private $_routes = array(
		'product/view' => array('idName' => 'id','nameLevel2' => '',),
		'entity/categorylist' => array('idName' => '','nameLevel2' => 'categories',),
		'entity/list' => array('idName' => 'cid','nameLevel2' => 'categories',),
		'entity/publisherlist' => array('idName' => '','nameLevel2' => 'publishers',),
		'entity/bypublisher' => array('idName' => 'pid','nameLevel2' => 'publishers',),
		'entity/serieslist' => array('idName' => '','nameLevel2' => 'series',),
		'entity/byseries' => array('idName' => 'sid','nameLevel2' => 'series',),
	);
//case 'entity/bybinding': return 'bid'; break;
//case 'entity/byseries': return 'sid'; break;
//case 'entity/bymedia': return 'mid'; break;
//case 'entity/byaudiostream': return 'sid'; break;
//case 'entity/bysubtitle': return 'sid'; break;
////			case 'entity/bytype': return 'tid'; break;
//case 'entity/bymagazinetype': return 'tid'; break;
//case 'entity/byauthor': case 'entity/byactor': return 'aid'; break;
//case 'entity/bydirector': return 'did'; break;
//case 'entity/byperformer': return 'pid'; break;

	private $_routesLevel2 = array(), $_routesLevel3 = array();

	public $urlSuffix = '/';

	function __construct() {
		$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].Yii::app()->language.'/urlTranslite.php';
		if (file_exists($file)) {
			foreach (include $file as $entityStr=>$urlNames) {
				if ($entityId = Entity::ParseFromString($entityStr)) {
					$this->_entitys[$entityStr] = $urlNames['level_1'];
					$this->_level2[$entityStr] = $urlNames['level_2'];
				}
			}
			foreach (self::$_routes as $route=>$rParam) {
				if ($route != 'product/view') {
					if (empty($rParam['idName'])) $this->_routesLevel2[$rParam['nameLevel2']] = $route;
					else $this->_routesLevel3[$rParam['nameLevel2']] = $route;
				}
			}
		}
//		Debug::staticRun(array($this->_routesLevel2, $this->_routesLevel3));
	}

	function createUrl($manager, $route, $params, $ampersand) {
		if (!isset($_GET['ha'])) return false;
		if (defined('OLD_PAGES')) return false;
		if (empty(self::$_routes[$route])) return false;
		if (empty($params['entity'])) return false;

		if (is_numeric($params['entity'])) {
			$entityId = $params['entity'];
			$entityStr = Entity::GetUrlKey($params['entity']);
		}
		else {
			$entityStr = $params['entity'];
			$entityId = Entity::ParseFromString($params['entity']);
		}
		if (empty($entityId)||empty($entityStr)) return false;

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
		$title = (!empty($params['__useTitleParams'])&&!empty($params['title'])) ? $params['title'] : '';

		switch ($route) {
			case 'product/view':
				if (!empty($params[self::$_routes[$route]['idName']]))
					$url = $this->_createProduct($route, $entityStr, $entityId, $params[self::$_routes[$route]['idName']], $title, $language);
				break;
			case 'entity/list':
				if (!empty($params['entity'])) {
					if (empty($params['cid'])) $url = $this->_createRazd($entityStr);
					else $url = $this->_createLevel3($route, $entityStr, $entityId, 'categories', $params['cid'], $title, $language);
				}
				break;
			default:
				if (empty(self::$_routes[$route]['idName'])||empty($params[self::$_routes[$route]['idName']]))
					$url = $this->_createLevel2($entityStr, self::$_routes[$route]['nameLevel2']);
				else $url = $this->_createLevel3($route, $entityStr, $entityId, self::$_routes[$route]['nameLevel2'], $params[self::$_routes[$route]['idName']], $title, $language);
				break;
		}
		unset($params[self::$_routes[$route]['idName']], $params['entity'], $params['title'], $params['__useTitleParams']);
		if (!empty($url)) {
			$params['ha'] = 1;//TODO:: убрать, когда будет все проверено
			if (!empty($prefix)) $url = implode('/', $prefix) . '/' . $url;
			if (!empty($params)) $url .= '?' . http_build_query($params);
			return $url;
		}
		return false;
	}

	function parseUrl($manager, $request, $pathInfo, $rawPathInfo) {
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
		$urlParamPrev = '';
		do {
			$urlParam = array_shift($pathInfo);
			switch ($level++) {
				case 0: if (($route['id'] = $this->_parseLevel1($urlParam)) === false) return false; break;
				case 1:
					$res = $this->_parseLevel2($urlParam);
					if ($res === false) return false;
					list($route['id'], $route['actionId']) = $res;
					break;
				case 2:
					$res = $this->_parseLevel3($urlParam, $urlParamPrev);
					if ($res === false) return false;
					list($route['id'], $route['actionId']) = $res;
					break;
				default; return false; break;
			}
			$urlParamPrev = $urlParam;
		} while (!empty($pathInfo));
		return implode('/', $route);
	}

	private function _createProduct($route, $entityStr, $entityId, $id, $title, $language) {
		$url = $this->_createRazd($entityStr);
		if (empty($url)) return '';

		if (empty($title)) {
			$titles = HrefTitles::get()->getById($entityId, $route, $id);
			if (!empty($titles)) {
				if (!empty($titles[$language])) $title = $titles[$language];
				elseif (!empty($titles['en'])) $title = $titles['en'];
			}
		}
		if (empty($title)) $url .= $id . '/';
		else $url .= $id . '-' . $title . '/';

		return $url;
	}

	private function _createRazd($entityStr) {
		if (empty($entityStr)) return '';
		if (!isset($this->_entitys[$entityStr])) return '';
		return $this->_entitys[$entityStr] . '/';
	}

	private function _createLevel3($route, $entityStr, $entityId, $nameLevel2, $id, $title, $language) {
		$url = $this->_createLevel2($entityStr, $nameLevel2);
		if (empty($url)) return '';

		if (empty($title)) {
			$titles = HrefTitles::get()->getById($entityId, $route, $id);
			if (!empty($titles)) {
				if (!empty($titles[$language])) $title = $titles[$language];
				elseif (!empty($titles['en'])) $title = $titles['en'];
			}
		}
		if (empty($title)) $url .= $id . '/';
		else $url .= $id . '-' . $title . '/';

		return $url;
	}

	private function _createLevel2($entityStr, $name) {
		if (empty($this->_level2[$entityStr][$name])) return '';

		$url = $this->_createRazd($entityStr);
		if (empty($url)) return '';

		return $url . $this->_level2[$entityStr][$name] . '/';
	}

	private function _parseLevel1($urlParam) {
		if ($entity = array_search($urlParam, $this->_entitys)) {
			$_REQUEST['entity'] = $_GET['entity'] = $entity;
			return 'entity';
		}
		return false;
	}

	private function _parseLevel2($urlParam) {
		if (empty($_GET['entity'])) return false;

		if ($nameLevel2 = array_search($urlParam, $this->_level2[$_GET['entity']])) {
			if (isset($this->_routesLevel2[$nameLevel2]))
				return explode('/', $this->_routesLevel2[$nameLevel2]);
		}
		else return $this->_parseLevel3($urlParam, $urlParam, 'product/view');

		return false;
	}

	private function _parseLevel3($urlParam, $urlParamPrev, $route = '') {
		if (empty($_GET['entity'])) return false;
		if (empty($route)) {
			if ($nameLevel2 = array_search($urlParamPrev, $this->_level2[$_GET['entity']])) {
				if (isset($this->_routesLevel3[$nameLevel2])) $route = $this->_routesLevel3[$nameLevel2];
			}
		}
		if (empty($route)) return false;

		$urlParam = explode('-', $urlParam);
		$urlId = array_shift($urlParam);
		$urlId = (int) $urlId;
		if (($urlId > 0)&&!empty($route)&&!empty(self::$_routes[$route]['idName'])) {
			$_REQUEST[self::$_routes[$route]['idName']] = $_GET[self::$_routes[$route]['idName']] = $urlId;
			return explode('/', $route);
		}
		return false;
	}

}
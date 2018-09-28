<?php
/*Created by Кирилл (18.08.2018 21:58)*/

class StaticUrlRule extends CBaseUrlRule {
	public $urlSuffix = '/';
	private $_pages = array();

	static private $_files = array(
		'conditions' => 'MSG_CONDITIONS_OF_USE',
		'conditions_order' => 'YM_CONTEXT_CONDITIONS_ORDER_ALL',
		'conditions_subscription' => 'YM_CONTEXT_CONDITIONS_ORDER_PRD',
		'contact' => 'YM_CONTEXT_CONTACTUS',
		'legal_notice' => 'YM_CONTEXT_LEGAL_NOTICE',
		'faq' => 'A_FAQ',
		'aboutus' => 'A_ABOUTUS',
//		'partners' => 'A_PARTNERS',
//		'links' => 'A_LINKS',
		'ourstore' => 'A_STORE',
		'csr' => 'A_CSR',
		'offers_partners' => 'YM_CONTEXT_OFFERS_PARTNERS',
//		'thawte' => 'MSG_YAHLIST_INFO_THAWTE',
		'safety' => 'MSG_YAHLIST_INFO_PAYMENTS_ARE_SECURE',
		'zone_info' => 'Zone',
		'paypal' => 'MSG_WHAT_IS_PAYPAL',
		'sitemap' => 'A_SITEMAP',
	);

	static function getTitles() { return self::$_files; }
	private $_language = null;

	function __construct($language = null) {
		if (($language === null)||!in_array($language, Yii::app()->params['ValidLanguages'])) $language = Yii::app()->language;
		$this->_language = $language;

		$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$language.'/urlTranslite.php';
		if (file_exists($file)) {
			foreach (include $file as $entityStr=>$urlNames) {
				if ($entityId = Entity::ParseFromString($entityStr)) {}
				elseif (!empty($urlNames)&&is_string($urlNames)) {
					$this->_pages[$entityStr] = $urlNames;
				}
			}
		}
	}

	function createUrl($manager, $route, $params, $ampersand) {
		if (defined('OLD_PAGES')) return false;
		if (
			(mb_strpos($route, 'site/', null, 'utf-8') === false)
			&& (mb_strpos($route, 'bookshelf/', null, 'utf-8') === false)
			&& (mb_strpos($route, 'offers/', null, 'utf-8') === false)
			&& (mb_strpos($route, 'client/', null, 'utf-8') === false)
			&& (mb_strpos($route, 'cart/', null, 'utf-8') === false)//
		) return false;

		$prefix = array();
		if (!empty($params['__langForUrl'])&&in_array($params['__langForUrl'], Yii::app()->params['ValidLanguages'])) {
			$handler = new StaticUrlRule($params['__langForUrl']);
			unset($params['__langForUrl']);
			return $handler->createUrl($manager, $route, $params, $ampersand);
		}
		unset($params['__langForUrl']);

		if ($this->_language === 'rut') $params['language'] = $this->_language;
		else $prefix[] = $this->_language;

		$url = '';

		switch ($route) {
			case 'bookshelf/list':
				if (!empty($this->_pages['bookshelf']))
					$url = $this->_pages['bookshelf'] . '/';
				break;
			case 'bookshelf/view':
				if (!empty($this->_pages['bookshelf'])&&!empty($params['id']))
					$url = $this->_pages['bookshelf'] . '/' . $params['id'] . '/';
				unset($params['id']);
				break;
			case 'offers/special':
				if (!empty($params['mode'])&&!empty($this->_pages['for-' . $params['mode']]))
					$url = $this->_pages['for-' . $params['mode']] . '/';
				unset($params['mode']);
				break;
			case 'offers/list':
				if (isset($this->_pages['offers']))
					$url = $this->_pages['offers'] . '/';
				break;
			case 'offers/view':
				if (isset($this->_pages['offers'])&&!empty($params['oid'])) {
					$url = $this->_pages['offers'] . '/';
					$titles = HrefTitles::get()->getById(0, $route, $params['oid']);
					if (!empty($titles)) {
						if (!empty($titles[$this->_language])) $title = $titles[$this->_language];
						elseif (!empty($titles['en'])) $title = $titles['en'];
					}
					if (empty($title)) $url .= $params['oid'] . '/';
					else $url .= $params['oid'] . '-' . $title . '/';
					unset($params['oid'], $params['title']);
				}
				break;
			case 'site/static':
				if (!empty($params['page'])
					&&isset(self::$_files[$params['page']])
					&&isset($this->_pages[$params['page']])
				)
					$url = $this->_pages[$params['page']] . '/';
				unset($params['page']);
				break;
			case 'client/me':
				if (isset($this->_pages['me']))
					$url = $this->_pages['me'] . '/';
				break;
			case 'cart/view':
				if (isset($this->_pages['cart']))
					$url = $this->_pages['cart'] . '/';
				break;
			default:
				$actions = explode('/', $route);
				$actionId = array_pop($actions);
				if (isset($this->_pages[$actionId])) $url = $this->_pages[$actionId] . '/';
				break;
		}
		if (!empty($url)) {
			if (!empty($prefix)) $url = implode('/', $prefix) . '/' . $url;
			if (!empty($params)) $url .= '?' . http_build_query($params);
			return $url;
		}
		return false;
	}

	function parseUrl($manager, $request, $pathInfo, $rawPathInfo) {
		if (defined('OLD_PAGES')) return false;

		if ($this->urlSuffix !== null) $pathInfo = $manager->removeUrlSuffix($rawPathInfo, $this->urlSuffix);
		// URL suffix required, but not found in the requested URL
		if($manager->useStrictParsing && ($pathInfo === $rawPathInfo)) {
			(($urlSuffix = $this->urlSuffix) === null) ? $manager->urlSuffix : $this->urlSuffix;
			if(($urlSuffix != '') && ($urlSuffix !== '/')) return false;
		}
		$pathInfo = mb_strtolower(trim($pathInfo, '/'));
		if (empty($pathInfo)) return false;

		if (in_array($pathInfo, array('site', 'site/index')))
			throw new CHttpException(404);;

		if ($page = array_search($pathInfo, $this->_pages)) {
			if (isset(self::$_files[$page])) {
				$_REQUEST['page'] = $_GET['page'] = $page;
				if (method_exists($request, 'setParam')) $request->setParam('page', $page);
				return 'site/static';
			}
			elseif ($page == 'bookshelf') return 'bookshelf/list';
			elseif ($page == 'me') return 'client/me';
			elseif ($page == 'cart') return 'cart/view';
			elseif ($page == 'offers') {
				return 'offers/list';
			}
			//
			else {
				switch ($page) {
					case 'for-firms':
						$_REQUEST['mode'] = $_GET['mode'] = 'firms';
						if (method_exists($request, 'setParam')) $request->setParam('mode', 'firms');
						return 'offers/special';
						break;
					case 'for-uni':
						$_REQUEST['mode'] = $_GET['mode'] = 'uni';
						if (method_exists($request, 'setParam')) $request->setParam('mode', 'uni');
						return 'offers/special';
						break;
					case 'for-lib':
						$_REQUEST['mode'] = $_GET['mode'] = 'lib';
						if (method_exists($request, 'setParam')) $request->setParam('mode', 'lib');
						return 'offers/special';
						break;
					case 'for-fs':
						$_REQUEST['mode'] = $_GET['mode'] = 'fs';
						if (method_exists($request, 'setParam')) $request->setParam('mode', 'fs');
						return 'offers/special';
						break;
					case 'for-alle2':
						$_REQUEST['mode'] = $_GET['mode'] = 'alle2';
						if (method_exists($request, 'setParam')) $request->setParam('mode', 'alle2');
						return 'offers/special';
						break;
				}
			}
			return 'site/' . $page;
		}
		elseif (preg_match("/^" . $this->_pages['offers'] . "\/(\d+)/ui", $pathInfo, $m)) {
			$_REQUEST['oid'] = $_GET['oid'] = $m[1];
			if (method_exists($request, 'setParam')) $request->setParam('oid', $m[1]);
			return 'offers/view';
		}
		elseif (preg_match("/^" . $this->_pages['bookshelf'] . "\/(\d+)/ui", $pathInfo, $m)) {
			$_REQUEST['id'] = $_GET['id'] = $m[1];
			if (method_exists($request, 'setParam')) $request->setParam('id', $m[1]);
			return 'bookshelf/view';
		}
		return false;
	}

}
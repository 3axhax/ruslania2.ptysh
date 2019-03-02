<?php

class MyUrlManager extends CUrlManager
{
    public $appendParams=false;
    public $urlRuleClass = 'MyUrlRule';

    public static function RewriteCurrent($controller, $lang, $sel = false) {
        $query = (string)Yii::app()->getRequest()->getQueryString();
        if ($lang === 'rut') {
            $action = $controller->action->id;
            if ($action == 'error') {
                $url = '/' . Yii::app()->getRequest()->getPathInfo() . '/';
                if (!empty($query)) $url .= '?' . $query;
            }
            else {
                $params = $_GET;
                if ($sel) $params['sel'] = 1;
                $params['__langForUrl'] = $lang;
                if (!empty($params['avail'])) unset($params['avail']);
                $ctrl = $controller->id;
                $url = Yii::app()->createUrl($ctrl.'/'.$action, $params);
            }
        }
        else {
            $langPages = $controller->getOtherLangPaths();
            if (!empty($langPages[$lang])) $url = $langPages[$lang];
            else {
                $pathInfo = Yii::app()->getRequest()->getPathInfo();
                if (!empty($pathInfo)&&($pathInfo !== '/')) $url = '/' . $lang . '/' . $pathInfo . '/';
                else $url = '/' . $lang . '/';
            }
            if ($sel) {
                if (empty($query)) $query = 'sel=1';
                else $query .= '&sel=1';
            }

            if (!empty($query)
                &&(Yii::app()->language !== 'rut')//это чтоб убрать lang и language из адреса
            ) {
                if (mb_strpos($url, '?', null, 'utf-8') === false) $url .= '?' . $query;
                else $url .= '&' . $query;
            }
        }
        return $url;
    }

    public static function RewriteCurrency($controller, $currency) {
        /*        $query = (string)Yii::app()->getRequest()->getQueryString();

				$url = Yii::app()->getRequest()->getPathInfo() . '/';
				if (Yii::app()->language !== 'rut') $url = '/' . Yii::app()->language . '/' . $url;

				$query = preg_replace("/\bcurrency=\d?\b/ui", '', $query);
				$query = preg_replace(array("/[&]{2,}/ui"), array('&'), $query);

				if (!empty($query)) $query .= '&';
				$query .= 'currency=' . $currency;
				return $url . '?' . $query;*/


        $params = $_GET;
        $params['currency'] = $currency;
        if (!empty($params['avail'])) unset($params['avail']);
        $ctrl = $controller->id;
        $action = $controller->action->id;
        if ($action == 'error') $url = '/' . Yii::app()->getRequest()->pathInfo;
        else $url = Yii::app()->createUrl($ctrl . '/' . $action, $params);

        return $url;
    }

//    public function createUrl($route,$params=array(),$ampersand='&')
//    {
//        return 'A';
//        Yii::beginProfile('URL = '.$route);
//        $ret = parent::createUrl($route, $params, $ampersand);
//        Yii::endProfile('URL = '.$route);
//        return $ret;
//    }

    function init() {
        if (!defined('OLD_PAGES')) $this->cacheID .= '_' . Yii::app()->language;
        parent::init();
    }
    function parseUrl($request) {
        $rawPathInfo=$request->getPathInfo();
        $pathInfo=$this->removeUrlSuffix($rawPathInfo,$this->urlSuffix);
        $result = parent::parseUrl($request);
        $myIp = '217.118.83.225';
        if ((string)getenv('REMOTE_ADDR') === $myIp) {
            //что бы пока не сделано не ломать то, что есть
            $route = preg_replace("/^\/+/ui", '', $result);
            $route = explode('/', $route);
            $buyActions = array('noregister');
            if (($route[0] === 'cart')&&(!empty($route[1]))&&in_array($route[1], $buyActions)) {
                $route[0] = 'buy';
                return implode('/', $route);
            }
        }
        if ($pathInfo === $result) {
            HrefTitles::get()->redirectOldPage($pathInfo);
        }
        return $result;
    }

    function createUrl($route,$params=array(),$ampersand='&') {
        $result = parent::createUrl($route,$params,$ampersand);
        return $result;
    }

    protected function createUrlDefault($route,$params,$ampersand) {
        if (defined('OLD_PAGES')) return parent::createUrlDefault($route,$params,$ampersand);

        $language = Yii::app()->language;
        if (!empty($params['__langForUrl'])&&in_array($params['__langForUrl'], Yii::app()->params['ValidLanguages'])) {
            //что бы получить путь для другого языка
            $language = $params['__langForUrl'];
        }
        unset($params['__langForUrl']);

        if (!empty($language)) $route = $language . '/' . $route;
        return parent::createUrlDefault($route,$params,$ampersand);
    }

    function parseUrlByPath($path) {

    }

}

class MyUrlRule extends CUrlRule {
    function createUrl($manager,$route,$params,$ampersand) {
        if (defined('OLD_PAGES')) return parent::createUrl($manager,$route,$params,$ampersand);

        $language = Yii::app()->language;
        if (!empty($params['__langForUrl'])&&in_array($params['__langForUrl'], Yii::app()->params['ValidLanguages'])) {
            //что бы получить путь для другого языка
            $language = $params['__langForUrl'];
        }
        unset($params['__langForUrl']);

        if (!empty($params['title'])&&!empty($params['entity'])) {
            $entity = Entity::ParseFromString($params['entity']);
            $idName = HrefTitles::get()->getIdName($entity, $route);
            if (!empty($params[$idName])) {
                if (empty($params['__useTitleParams'])) {
                    $titles = HrefTitles::get()->getById($entity, $route, $params[$idName]);
                    if (!empty($titles)) {
                        if (!empty($titles[$language])) $params['title'] = $titles[$language];
                        elseif (!empty($titles['en'])) $params['title'] = $titles['en'];
                    }
                }
            }
        }
        unset($params['__useTitleParams']);

        $langGood = '';
        $langGoodId = 0;
        if (!empty($params['lang'])) {
            $langGoods = ProductLang::getShortLang();
            if (isset($langGoods[$params['lang']])) {
                $langGood = $langGoods[$params['lang']];
                $langGoodId = $params['lang'];
            }
        }
        unset($params['lang']);

        if ($language === 'rut') $params['language'] = $language;
        $currency = 0;
        if (!empty($params['currency'])) {
            $currency = (int) $params['currency'];
            unset($params['currency']);
        }
        $url = parent::createUrl($manager,$route,$params,$ampersand);

        if ($url !== false) {
            if (!empty($langGood)) {
                if ($language === 'rut') {
                    if (mb_strpos($url, '?', null, 'utf-8') === false) $url .= '?';
                    else $url .= '&';
                    $url .= 'lang=' . $langGoodId;
                }
                else $url = $langGood . '/' . $url;
            }

            if (!empty($language)&&empty($params['language'])) $url = $language . '/' . $url;
            if ($currency > 0) {
                if (mb_strpos($url, '?', null, 'utf-8') === false) $url .= '?';
                else $url .= '&';
                $url .= 'currency=' . $currency;
            }
        }
        return $url;
    }

    function parseUrl($manager,$request,$pathInfo,$rawPathInfo) {
        if (get_class($request) === 'MyRefererRequest') return $this->_parseReferer($manager,$request,$pathInfo,$rawPathInfo);

        $result = parent::parseUrl($manager,$request,$pathInfo,$rawPathInfo);
        if (defined('OLD_PAGES')) return $result;

        if ((mb_strpos($result, 'entity/', null, 'utf-8') === 0)&&!empty($_GET['lang'])) {
            $langGoods = ProductLang::getShortLang();
            if (is_numeric($_GET['lang'])&&!empty($langGoods[$_GET['lang']])) $langId = $_GET['lang'];
            else $langId = array_search($_GET['lang'], $langGoods);
            if (empty($langId)) return false;
            $_GET['lang'] = $langId;
        }
        return $result;
    }

    /** эта заплатка нужна, чтоб не изменять $_GET, когда пытаемся распарсить произвольный адрес
     * @param $manager
     * @param MyRefererRequest $request
     * @param $pathInfo
     * @param $rawPathInfo
     * @return bool|string
     */
    private function _parseReferer($manager, MyRefererRequest $request, $pathInfo,$rawPathInfo) {
        $urlRule = new EntityUrlRule();
        $result = $urlRule->parseUrl($manager, $request, $pathInfo, $rawPathInfo);
        if ($result !== false) return $result;

        $urlRule = new StaticUrlRule();
        $result = $urlRule->parseUrl($manager, $request, $pathInfo, $rawPathInfo);
        if ($result !== false) return $result;

        if($this->verb!==null && !in_array($request->getRequestType(), $this->verb, true))
            return false;

        if($manager->caseSensitive && $this->caseSensitive===null || $this->caseSensitive)
            $case='';
        else
            $case='i';

        if($this->urlSuffix!==null)
            $pathInfo=$manager->removeUrlSuffix($rawPathInfo,$this->urlSuffix);

        // URL suffix required, but not found in the requested URL
        if($manager->useStrictParsing && $pathInfo===$rawPathInfo)
        {
            $urlSuffix=$this->urlSuffix===null ? $manager->urlSuffix : $this->urlSuffix;
            if($urlSuffix!='' && $urlSuffix!=='/')
                return false;
        }

        if($this->hasHostInfo)
            $pathInfo=strtolower($request->getHostInfo()).rtrim('/'.$pathInfo,'/');

        $pathInfo.='/';

        if(preg_match($this->pattern.$case,$pathInfo,$matches)) {
            foreach($this->defaultParams as $name=>$value) {
                $request->setParam($name, $value);
            }
            $tr=array();
            foreach($matches as $key=>$value) {
                if(isset($this->references[$key]))
                    $tr[$this->references[$key]]=$value;
                else if(isset($this->params[$key]))
                    $request->setParam($key, $value);
            }
            if($pathInfo!==$matches[0]) // there're additional GET params
                $manager->parsePathInfo(ltrim(substr($pathInfo,strlen($matches[0])),'/'));
            if($this->routePattern!==null)
                return strtr($this->route,$tr);
            else
                return $this->route;
        }
        else
            return false;
    }
}
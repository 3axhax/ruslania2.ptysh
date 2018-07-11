<?php

class MyUrlManager extends CUrlManager
{
    public $urlRuleClass = 'MyUrlRule';

    public static function RewriteCurrent($controller, $lang) {
        $query = (string)Yii::app()->getRequest()->getQueryString();
        if ($lang === 'rut') {
            $action = $controller->action->id;
            if ($action == 'error') {
                $url = '/' . Yii::app()->getRequest()->getPathInfo() . '/';
                if (!empty($query)) $url .= '?' . $query;
            }
            else {
                $params = $_GET;
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

            if (!empty($query)
                &&(Yii::app()->language !== 'rut')//это чтоб убрать lang и language из адреса
            ) $url .= '?' . $query;
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
        $result = parent::parseUrl($request);
        return $result;
    }

    function createUrl($route,$params=array(),$ampersand='&') {
        $result = parent::createUrl($route,$params,$ampersand);
        return $result;
    }

    protected function createUrlDefault($route,$params,$ampersand) {
        if (defined('OLD_PAGES')) return parent::createUrlDefault($route,$params,$ampersand);

        $language = Yii::app()->language;
        if (!empty($language)) $route = $language . '/' . $route;
        return parent::createUrlDefault($route,$params,$ampersand);
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
        }
        return $url;
    }

    function parseUrl($manager,$request,$pathInfo,$rawPathInfo) {
        $result = parent::parseUrl($manager,$request,$pathInfo,$rawPathInfo);
        if (defined('OLD_PAGES')) return $result;

        if (($result === 'entity/list')&&!empty($_GET['lang'])) {
            $langGoods = ProductLang::getShortLang();
            if (is_numeric($_GET['lang'])&&!empty($langGoods[$_GET['lang']])) $langId = $_GET['lang'];
            else $langId = array_search($_GET['lang'], $langGoods);
            if (empty($langId)) return false;
            $_GET['lang'] = $langId;
        }
        return $result;
    }
}
<?php

class MyUrlManager extends CUrlManager
{
    public $urlRuleClass = 'MyUrlRule';

    public static function RewriteCurrent($controller, $lang) {
        if ($lang === 'rut') {
            $params = $_GET;
            $params['language'] = $lang;
            $ctrl = $controller->id;
            $action = $controller->action->id;
            if ($action == 'error') $url = '/' . Yii::app()->getRequest()->getPathInfo();
            else $url = Yii::app()->createUrl($ctrl.'/'.$action, $params);
        }
        else $url = '/' . $lang . '/' . Yii::app()->getRequest()->getPathInfo();
        return $url;
    }

    public static function RewriteCurrency($controller, $currency)
    {
        $params = $_GET;
        unset($params['currency']);
        $ctrl = $controller->id;
        $param = 'currency='.$currency;
        $action = $controller->action->id;
        if ($action == 'error') {
            $url = '/' . Yii::app()->getRequest()->pathInfo;
        }
        else {
            $url = Yii::app()->createUrl($ctrl . '/' . $action, $params);
        }

        if(strpos($url, '?') === false) $url .= '?'.$param;
        else $url .= '&'.$param;

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
        $this->cacheID .= '_' . Yii::app()->language;
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
        $language = Yii::app()->language;
        if (!empty($language)) $route = $language . '/' . $route;
        return parent::createUrlDefault($route,$params,$ampersand);
    }


}

class MyUrlRule extends CUrlRule {
    function createUrl($manager,$route,$params,$ampersand) {
        $language = Yii::app()->language;
        if ($language === 'rut') $params['language'] = $language;
        $url = parent::createUrl($manager,$route,$params,$ampersand);
        if ($url !== false) {
            if (!empty($language)&&empty($params['language'])) $url = $language . '/' . $url;
        }
        return $url;
    }

    function parseUrl($manager,$request,$pathInfo,$rawPathInfo) {
         return parent::parseUrl($manager,$request,$pathInfo,$rawPathInfo);
    }
}
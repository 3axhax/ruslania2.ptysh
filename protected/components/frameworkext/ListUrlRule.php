<?php
/*Created by Кирилл (28.06.2018 18:10)*/
class ListUrlRule extends CBaseUrlRule {

	function createUrl($manager,$route,$params,$ampersand) {
		$language = Yii::app()->language;
		if (!empty($params['__langForUrl'])&&in_array($params['__langForUrl'], Yii::app()->params['ValidLanguages'])) {
			//что бы получить путь для другого языка
			$language = $params['__langForUrl'];
		}
		unset($params['__langForUrl']);

		if ($language === 'rut') $params['language'] = $language;
		$url = parent::createUrl($manager,$route,$params,$ampersand);
		if ($url !== false) {
			if (!empty($language)&&empty($params['language'])) $url = $language . '/' . $url;
		}
		return $url;
	}

	function parseUrl($manager,$request,$pathInfo,$rawPathInfo) {
		Debug::staticRun(array(111));
		return parent::parseUrl($manager,$request,$pathInfo,$rawPathInfo);
	}

}
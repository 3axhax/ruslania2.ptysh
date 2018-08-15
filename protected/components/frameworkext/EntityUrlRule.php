<?php
/*Created by Кирилл (15.08.2018 22:32)*/

class EntityUrlRule extends CBaseUrlRule {
	public $connectionID = 'db';

	public function createUrl($manager,$route,$params,$ampersand) {
//		return 'ru/knigi';
		/*if ($route==='car/index')
		{
			if (isset($params['manufacturer'], $params['model']))
				return $params['manufacturer'] . '/' . $params['model'];
			else if (isset($params['manufacturer']))
				return $params['manufacturer'];
		}*/
		return false;
	}

	public function parseUrl($manager,$request,$pathInfo,$rawPathInfo) {
		Debug::staticRun(array($pathInfo,$rawPathInfo));
//		if ($pathInfo === 'knigi') {
//			$_REQUEST['entity'] = $_GET['entity'] = 10;
//		return 'entity/list';
//			return 'entity/list';
//		}
		/*if (preg_match('%^(\w+)(/(\w+))?$%', $pathInfo, $matches)) {
			// Проверяем $matches и $matches на предмет
			// соответствия производителю и модели в БД.
			// Если соответствуют, выставляем $_GET['manufacturer'] и/или $_GET['model']
			// и возвращаем строку с маршрутом 'car/index'.
		}*/
		return false;
	}

}
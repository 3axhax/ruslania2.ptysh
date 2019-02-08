<?php
/*Created by Кирилл (08.02.2019 22:21)*/
class SeoEdit extends CMyActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'seo_settings';
	}

	function getParams($path) {
		$request = new MyRefererRequest();
		$request->setFreePath($path);

		$result = array(
			'route'=>Yii::app()->getUrlManager()->parseUrl($request),
		);
		$entity = $request->getParam('entity');
		if (!empty($entity)) $result['entity'] = (int)Entity::ParseFromString($entity);
		if (empty($result['entity'])) $result['id'] = 0;
		else {
			$idName = HrefTitles::get()->getIdName($result['entity'], $result['route']);
			if (!empty($idName)) $result['id'] = $request->getParam($idName);
		}
		if (empty($result['id'])) $result['id'] = 0;
		return $result;
	}

	function getDefaultSettings($params) {
		$handler = Seo_settings::handler($params);
		$result = array();
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$result[$lang] = serialize($handler->getDefaultSettings($lang));
			}
		}

		return $result;
	}
}
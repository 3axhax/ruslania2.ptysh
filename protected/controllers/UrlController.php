<?php
/*Created by Кирилл (19.09.2018 21:26)*/
class UrlController extends MyController {

	function actionGetParams() {
		$request = new MyRefererRequest();
		$request->setFreePath(Yii::app()->getRequest()->getParam('url'));

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
		$this->ResponseJson($result);
	}

}
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

	function actionOfferDay() {
		if ($num = Yii::app()->getRequest()->getParam('num')) {
			$search = new SearchController($this->getId(), $this->getModule());
			$search->beforeAction($this->getAction());
			$code = $search->isCode($num);
			$find = $search->getByCode($code, $num);
			$row = array();
			if (empty($find)) Yii::app()->end();

			$find = array_shift($find);
			$row['entity_id'] = $find['entity'];
			$row['item_id'] = $find['id'];
			$row['title_ru'] = $find['title_ru'];
			$row['image'] = $find['image'];
		}
		else {
			$row = DiscountManager::getOfferDay();
			if (empty($row)) Yii::app()->end();;

			if (!Entity::IsValid($row['entity_id'])) Yii::app()->end();

			$sql = ''.
				'select title_ru, image '.
				'from ' . Entity::GetEntitiesList()[$row['entity_id']]['site_table'] . ' '.
				'where (id = ' . (int) $row['item_id'] . ') '.
				'limit 1 '.
			'';
			$row = array_merge($row, Yii::app()->db->createCommand($sql)->queryRow());
		}
		$row['image'] = Picture::Get($row, Picture::BIG);
		$row['url'] = Yii::app()->createUrl('product/view', array('entity'=>$row['entity_id'], 'id'=>$row['item_id']));
		$this->ResponseJson($row);
	}

	function actionInfoText() {
		$sql = 'select * from info_text order by id desc limit 1';
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		$this->ResponseJson($row);
	}

	/**
	 * эта функция написана, что бы руками добавить в файлы-переводчики недостающие данные
	 */
	function actionLables() {

		$fileSource = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].'ru/uiconst.class.php';
		$dataSource = require_once($fileSource);
		$langs = array('de', 'en', 'es', 'fi', 'fr', 'rut', 'se');
		foreach ($langs as $lang) {
			$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/uiconst.class.php';
			$data = require_once($file);
			echo '<b>' . $lang . '</b><br><br>';
			foreach ($dataSource as $k=>$v) {
				if (!isset($data[$k])) {
					echo "'" . $k . "' => '" . htmlspecialchars($v) . "',<br>";
				}
			}
			echo '<br>';
		}

	}

	function actionHref() {
		$route = Yii::app()->getRequest()->getParam('route');
		$params = [];
		foreach ($_GET as $k=>$v) {
			$params[$k] = $v;
		}
		$href = Yii::app()->createUrl($route, $params);
		$this->ResponseJson(array('href'=>$href));
	}

	function actionPromocodeBriefly() {
		$code = (string) Yii::app()->getRequest()->getParam('code');
		$promocode = Promocodes::model();
		$this->ResponseJson($promocode->briefly($code, false));
	}

	function actionPromocodesBriefly() {
		$ids = explode(',',(string) Yii::app()->getRequest()->getParam('ids'));
		$codes = array();
		$p = Promocodes::model();
		foreach($ids as $id) {
			$promocode = $p->getPromocode($id);
			$code = '';
			if (!empty($promocode['code'])) $code = $promocode['code'];
			$codes[$id] = $p->briefly($code, false);
		}

		$this->ResponseJson($codes);
	}

}
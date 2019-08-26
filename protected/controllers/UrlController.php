<?php
/*Created by Кирилл (19.09.2018 21:26)*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class UrlController extends MyController {

	function actionGetParams() {
		$request = new MyRefererRequest();
		$request->setFreePath(Yii::app()->getRequest()->getParam('url'));

		$result = array(
			'route'=>Yii::app()->getUrlManager()->parseUrl($request),
		);
		if ($result['route'] == 'search/general') {
			unset($result['route']);
		}
		else {
			$result['entity'] = 0;
			$entity = $request->getParam('entity');
			if (!empty($entity)) $result['entity'] = (int)Entity::ParseFromString($entity);

			$result['id'] = 0;
			$idName = HrefTitles::get()->getIdName($result['entity'], $result['route']);
			if (!empty($idName)) $result['id'] = $request->getParam($idName);

			$result['params'] = $request->getParams();
		}

		$this->ResponseJson($result);
	}

	function actionOfferDay() {
		if ($num = Yii::app()->getRequest()->getParam('num')) {
			$model = new SphinxProducts(1, 0);
			$code = $model->isCode($num);
			$find = $model->getByCode($code, $num);

//			$search = new SearchController($this->getId(), $this->getModule());
//			$search->beforeAction($this->getAction());
//			$code = $search->isCode($num);
//			$find = $search->getByCode($code, $num);
			$row = array();
			if (empty($find)) Yii::app()->end();

			$find = array_shift($find);
			$row['entity_id'] = $find['entity'];
			$row['item_id'] = $find['id'];
			$row['title_ru'] = $find['title_ru'];
			$row['image'] = $find['image'];
			$row['eancode'] = $find['eancode'];
		}
		else {
			$row = DiscountManager::getOfferDay();
			if (empty($row)) Yii::app()->end();

			if (!Entity::IsValid($row['entity_id'])) Yii::app()->end();

			$sql = ''.
				'select title_ru, image, eancode '.
				'from ' . Entity::GetEntitiesList()[$row['entity_id']]['site_table'] . ' '.
				'where (id = ' . (int) $row['item_id'] . ') '.
				'limit 1 '.
			'';
			$row = array_merge($row, Yii::app()->db->createCommand($sql)->queryRow());
		}
		$row['image'] = Picture::Get($row, Picture::BIG);
		$row['url'] = Yii::app()->createUrl('product/view', array('entity'=>$row['entity_id'], 'id'=>$row['item_id']));
		$photoTable = Entity::GetEntitiesList()[$row['entity_id']]['photo_table'];
		$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
		/**@var $photoModel ModelsPhotos*/
		$photoModel = $modelName::model();
		$idPhoto = (int)$photoModel->getFirstId($row['item_id']);
		$row['photo_table'] = $photoTable;
		if ($idPhoto > 0) {
			$row['image'] = $photoModel->getHrefPath($idPhoto, 'd', $row['eancode'], 'jpg');
		}

		$this->ResponseJson($row);
	}

	function actionOfferDayCacheClear() {
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			$file = Yii::getPathOfAlias('webroot') . '/protected/runtime/fileCache/mainbannersmall_' . $lang . '.html.php';
			if (file_exists($file)) @unlink($file);
		}
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

		$translite = array();
		$fileSource = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].'ru/uiconst.class.php';
		$dataSource = require_once($fileSource);
		$langs = array('en', /*'de', 'es', 'fi', 'fr', 'se'*/);
		foreach ($langs as $lang) {
			$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/uiconst.class.php';
			$data = require_once($file);
			foreach ($dataSource as $k=>$v) {
				if ($k <> 'A_LANG_RUSSIAN') {
					if (!isset($data[$k])) {
						if (empty($translite[$k])) {
							$translite[$k] = array('ru'=>$v);
							foreach ($langs as $_l) $translite[$k][$_l] = '';
						}
					}
					elseif (preg_match("/[а-я]/ui", $data[$k])) {
						if (empty($translite[$k])) {
							$translite[$k] = array('ru'=>$v);
							foreach ($langs as $_l) $translite[$k][$_l] = '';
						}
					}
				}
			}
		}
		echo '<table><tr><th>key</th><th>ru</th>';
		foreach ($langs as $lang) echo '<th>' . $lang . '</th>';
		echo '</tr>';
		foreach ($translite as $key=>$lines) {
			echo '<tr><td>' . $key . '</td><td>' . htmlspecialchars($lines['ru']) . '</td>';
			foreach ($langs as $lang) echo '<td></td>';
			echo '</tr>';
		}
		echo '</table>';
/*		foreach ($langs as $lang) {
			$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/uiconst.class.php';
			$data = require_once($file);
			$dataCom = require_once(Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].'langv2_com/'.$lang.'/uiconst.class.php');
			echo '<b>' . $lang . '</b><br><br><table>';
			foreach ($dataSource as $k=>$v) {
//				для добавления в в файлы недостающих ключей
//				if (!isset($data[$k])) {
//					echo "<tr><td style='width: 1px'>'" . $k . "</td><td>' => '</td><td style='width: 100%'>" . htmlspecialchars($v) . "',</td></tr>";
//				}

//				для excel
				if ($k <> 'A_LANG_RUSSIAN') {
					if (!isset($data[$k])) {
						echo '<tr><td>' . $k . '</td><td>' . htmlspecialchars($v) . '</td></tr>';
					}
					elseif (preg_match("/[а-я]/ui", $data[$k])) {
						echo '<tr><td style="width: 1px">' . $k . '</td><td style="width: 100%">' . htmlspecialchars($v) . '</td></tr>';
					}
				}


//				для сравнения со старым
//				if (!isset($data[$k])&&isset($dataCom[$k])) {
//					echo "<tr><td>'" . $k . "'</td><td> => </td><td>'" . htmlspecialchars($dataCom[$k]) . "',</td></tr>";
//				}
//				elseif (isset($dataCom[$k])&&preg_match("/[а-я]/ui", $data[$k], $m)) {
//					echo "<tr><td style='width: 1px'>'" . $k . "'</td><td style='width: 1px'> => </td><td style='width: 1px'>'" . htmlspecialchars($dataCom[$k]) . "',</td><td style='width: 100%'>" . print_r($m, true) . "</td></tr>";
//				}
			}
			echo '</table><br>';
		}*/

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


	function actionInstagram() {
		$inst = new Instagram();
		$inst->getUser();
		$inst->getMedia();
	}

	function actionTokken() {
		var_dump($_GET);
		exit;
	}

	function actionItemPhotoAdd() {
		$eid = (int)Yii::app()->getRequest()->getParam('eid');
		$iid = (int)Yii::app()->getRequest()->getParam('iid');
		$result = array('errors'=>array('неудалось закачать фото'));
		if (($iid > 0)&&Entity::IsValid($eid)) {
			$file = CUploadedFile::getInstanceByName('inputImg');
			if (!empty($file)) {
				if(!preg_match('|image|', $file->type)) {
					$failfile = Yii::getPathOfAlias('webroot') . '/protected/runtime/failfile/' . basename($file->tempName);
					$result['errors'] = array('Это не картинка');
					copy($file->tempName, $failfile);
					@unlink($file->tempName);
				}
				else {
					$params = Entity::GetEntitiesList()[$eid];
					$sql = 'select eancode from ' . $params['site_table'] . ' where (id = :id) limit 1';
					$ean = Yii::app()->db->createCommand($sql)->queryScalar(array(':id'=>$iid));

					$modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
					/**@var $model ModelsPhotos*/
					$model = $modelName::model();
					$model->setAttributes(array('iid'=>$iid, 'is_upload'=>1, 'position'=>1), false);
					$model->setIsNewRecord(true);
					$model->id = null;
					$model->insert();
					if ($model->createFotos($file->tempName, $model->id, '')) {
						$sql = 'select id from ' . $params['photo_table'] . ' where (iid = :iid) and (id <> :id)  order by position asc';
						$fotoIds = Yii::app()->db->createCommand($sql)->queryColumn(array(':iid'=>$iid, ':id'=>$model->id));
						if (!empty($fotoIds)) {
							$sql = 'delete from ' . $params['photo_table'] . ' where (iid = :iid) and (id <> :id)';
							Yii::app()->db->createCommand($sql)->execute(array(':iid'=>$iid, ':id'=>$model->id));
							foreach ($fotoIds as $idFoto) $model->remove($idFoto);
						}
						unset($result['errors']);
						$result['src'] = $model->getHrefPath($model->id, 'o', '', 'jpg');
						$result['idFoto'] = $model->id;
						$result['image'] = $ean . '.jpg';
						$this->ResponseJson($result);
						return;
					}
					$sql = 'delete from ' . $params['photo_table'] . ' where (id = :id)';
					Yii::app()->db->createCommand($sql)->execute(array(':id'=>$model->id));
				}
			}
		}
		$this->ResponseJson($result);
	}

	function actionItemPhotoClear() {
		$eid = (int)Yii::app()->getRequest()->getParam('eid');
		$iid = (int)Yii::app()->getRequest()->getParam('iid');
		$result = array('errors'=>array('неудалось удалить фото'));
		if (($iid > 0)&&Entity::IsValid($eid)) {
			$params = Entity::GetEntitiesList()[$eid];
			$sql = 'select id from ' . $params['photo_table'] . ' where (iid = :iid) order by position asc';
			$fotoIds = Yii::app()->db->createCommand($sql)->queryColumn(array(':iid'=>$iid));
			if (!empty($fotoIds)) {
				$sql = 'delete from ' . $params['photo_table'] . ' where (iid = :iid)';
				Yii::app()->db->createCommand($sql)->execute(array(':iid'=>$iid));
				$modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
				/**@var $model ModelsPhotos*/
				$model = $modelName::model();
				foreach ($fotoIds as $idFoto) $model->remove($idFoto);
				unset($result['errors']);
				$sql = 'select eancode from ' . $params['site_table'] . ' where (id = :id) limit 1';
				$ean = Yii::app()->db->createCommand($sql)->queryScalar(array(':id'=>$iid));
				if (!empty($ean)) $result['image'] = $ean . '.jpg';
			}
		}
		$this->ResponseJson($result);
	}

	function actionItemPhotoSrc() {
		$eid = (int)Yii::app()->getRequest()->getParam('eid');
		$iid = (int)Yii::app()->getRequest()->getParam('iid');
		if (($iid > 0)&&Entity::IsValid($eid)) {
			$params = Entity::GetEntitiesList()[$eid];
			$sql = 'select eancode, image from ' . $params['site_table'] . ' where (id = :id) limit 1';
			$row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id'=>$iid));
			if (empty($row)) return;

			$sql = 'select id from ' . $params['photo_table'] . ' where (iid = :iid) and (is_upload = 1) order by position asc limit 1';
			$idFoto = (int)Yii::app()->db->createCommand($sql)->queryScalar(array(':iid'=>$iid));

			if ($idFoto > 0) {
				$modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
				/**@var $model ModelsPhotos*/
				$model = $modelName::model();
				echo $model->getHrefPath($idFoto, Yii::app()->getRequest()->getParam('label', 'd'), $row['eancode'], 'jpg');
			}
			else echo Picture::Get($row, Picture::BIG);
		}
		return;
	}
}
<?php
/*Created by Кирилл (08.02.2019 21:58)*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class SeoController extends MyController {

	function actionEdit() {
		$path = Yii::app()->getRequest()->getParam('path');
		$data = array();
		if (!empty($path)) {
			/** @var $seoModel SeoEdit */
			$seoModel = SeoEdit::model();
			$params = $seoModel->getParams($path);
			$seoSettings = $seoModel->findByAttributes($params);
//			$seoSettings = $seoModel->findByPk(1);
			Debug::staticRun(array($params));
			if (empty($seoSettings)) {
				$settings = $seoModel->getDefaultSettings($params);
				$seoModel->setAttributes(array_merge($params, $settings), false);
				$data['seoModel'] = $seoModel;
			}
			else {
				$data['seoModel'] = $seoSettings;
			}
		}
		$this->render('edit', $data);
	}

	function actionChange() {
		$seoModel = new SeoEdit();

		if(Yii::app()->request->isPostRequest) {
			$params = Yii::app()->getRequest()->getParam('SeoEdit');
			$urlParams = $params;
			unset($urlParams['route']);
			foreach (Yii::app()->params['ValidLanguages'] as $lang) {
				unset($urlParams[$lang]);
				if ($lang !== 'rut') {
					$params[$lang] = serialize($params[$lang]);
				}
			}
			$seoModel->setAttributes($params, false);
			if (!empty($params['id_seo_settings'])) {
				$seoModel->id_seo_settings = $params['id_seo_settings'];
				$seoModel->setIsNewRecord(false);
			}
//			Debug::staticRun(array($params, $seoModel->id, $seoModel->getPrimaryKey()));
			$seoModel->save();
//			if ($seoModel->id) $seoModel->update();
//			else $seoModel->insert();
			$this->redirect(Yii::app()->createUrl('seo/edit'));
		}
	}

}
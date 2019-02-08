<?php
/*Created by Кирилл (08.02.2019 21:58)*/
class SeoController extends MyController {

	function actionEdit() {
		$path = Yii::app()->getRequest()->getParam('path');
		$data = array();
		if (!empty($path)) {
			/** @var $seoModel SeoEdit */
			$seoModel = SeoEdit::model();
			$params = $seoModel->getParams($path);
			$seoSettings = $seoModel->findByAttributes($params);
			if (empty($seoSettings)) {
				$settings = $seoModel->getDefaultSettings($params);
				$seoModel->setAttributes(array_merge($params, $settings));
				$data['seoModel'] = $seoModel;
			}
			else {
				$data['seoModel'] = $seoSettings;
			}
		}
		$this->render('edit', $data);
	}

}
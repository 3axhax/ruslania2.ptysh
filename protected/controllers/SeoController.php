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
			if (empty($params['route'])||!array_key_exists($params['route'], EntityUrlRule::getRoutes())) {
				$data['error'] = 'Страница не найдена!!!';
			}
			else {
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

	function actionFill() {
/*		$tags = array(
			'entity/publisherlist' =>   'publisher',
			'entity/serieslist' =>      'series',
			'entity/authorlist' =>      'authors',
			'entity/bindingslist' =>    'binding',
			'entity/yearslist' =>       'years',
			'entity/yearreleaseslist' =>'yearreleases',
			'entity/performerlist' =>   'performers',
			'entity/medialist' =>       'media',
			'entity/typeslist' =>       'types',//pereodics
			'entity/actorlist' =>       'actors',
			'entity/directorlist' =>    'directors',
			'entity/audiostreamslist' =>'audiostreams',
			'entity/subtitleslist' =>   'subtitles',
			'entity/studioslist' =>     'studios',//video
		);
		foreach (Entity::GetEntitiesList() as $eid=>$params) {
			foreach ($tags as $route=>$paramName) {
				if (Entity::checkEntityParam($eid, $paramName)) {
					$sql = 'INSERT ignore INTO seo_settings (route, entity, id, ru, en, fi, de, fr, se, es) VALUES
					(\'' . $route . '\', ' . $eid . ', 0, \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:40:"{entity_name}: {name} - Руслания";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\')';
					Yii::app()->db->createCommand($sql)->execute();
				}
			}
		}*/
	}

}
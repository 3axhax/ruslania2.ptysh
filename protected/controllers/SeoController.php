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
			if (!$this->_checkAllowPage($params)) {
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
			'entity/categorylist' =>   'categorys',
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
//		(\'' . $route . '\', ' . $eid . ', 0, \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:40:"{entity_name}: {name} - Руслания";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:21:"{entity_name}: {name}";s:5:"title";s:32:"{entity_name}: {name} - Ruslania";s:11:"description";s:21:"{entity_name}: {name}";s:8:"keywords";s:20:"{entity_name} {name}";}\')';
		foreach (Entity::GetEntitiesList() as $eid=>$params) {
			foreach ($tags as $route=>$paramName) {
				if (($paramName == 'categorys')||(Entity::checkEntityParam($eid, $paramName))) {
					$sql = 'delete from seo_settings where (route = \'' . $route . '\') and (entity = ' . $eid . ') and (id = 0)';
					Yii::app()->db->createCommand($sql)->execute();

					$sql = 'INSERT ignore INTO seo_settings (route, entity, id, ru, en, fi, de, fr, se, es) VALUES
					(\'' . $route . '\', ' . $eid . ', 0, \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:49:"{entity_name}: {name} {page_n} - Руслания";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:41:"{entity_name}: {name} {page_n} - Ruslania";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:41:"{entity_name}: {name} {page_n} - Ruslania";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:41:"{entity_name}: {name} {page_n} - Ruslania";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:41:"{entity_name}: {name} {page_n} - Ruslania";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:41:"{entity_name}: {name} {page_n} - Ruslania";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\', \'a:4:{s:2:"h1";s:30:"{entity_name}: {name} {page_n}";s:5:"title";s:41:"{entity_name}: {name} {page_n} - Ruslania";s:11:"description";s:30:"{entity_name}: {name} {page_n}";s:8:"keywords";s:20:"{entity_name} {name}";}\')';
					Yii::app()->db->createCommand($sql)->execute();
				}
			}
		}*/
	}

	function actionFillFile() {
		//перенести в крон
/*		define('cronAction', 1);
		$langs = ['ru', 'en', 'fi', 'de', 'fr', 'se', 'es'];
		foreach ($langs as $lang){
			$file = Yii::getPathOfAlias('webroot') . Yii::app()->params['LangDir'] . $lang . '/seo_category.php';
			if (!file_exists($file)) {
				Yii::app()->setLanguage($lang);
				var_dump($lang, Yii::app()->language); echo '<br>';
				$result = array();
				foreach (Entity::GetEntitiesList() as $eid=>$params) {
					switch ($eid) {
						case 10: SEO::seo_change_meta_books_category($eid, '{counts}', '{name} {params} {lang_predl}', 1); break;
						case 15: SEO::seo_change_meta_sheets_category($eid, '{counts}', '{name} {params} {lang_predl}', 1); break;
						case 30: SEO::seo_change_meta_periodic_category($eid, '{counts}', '{name} {params} {lang_predl}', 1); break;
						case 22: SEO::seo_change_meta_music_category($eid, '{counts}', '{name} {params} {lang_predl}', 1); break;
					}
					$result[$eid] = array(
						'h1'=>'{name} {params} {lang_predl}',
						'title'=>$this->pageTitle,
						'description'=>$this->pageDescription,
						'keywords'=>$this->pageKeywords,
					);
				}
				SEO::seo_change_meta_other_category(0, '{counts}', '{name} {params} {lang_predl}', 1);
				$result[0] = array(
					'h1'=>'{name} {params} {lang_predl}',
					'title'=>$this->pageTitle,
					'description'=>$this->pageDescription,
					'keywords'=>$this->pageKeywords,
				);
				file_put_contents($file, '<?php // FILE: meta data ' . date('d.m.Y H:i:s') . '
return ' . var_export($result, true) . ';');
			}
		}*/
	}

	private function _checkAllowPage($params) {
		if (empty($params['route'])) return false;

		if (array_key_exists($params['route'], EntityUrlRule::getRoutes())) return true;

		$language = Yii::app()->language;
		$pages = array();
		$file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$language.'/urlTranslite.php';
		if (file_exists($file)) {
			foreach (include $file as $entityStr=>$urlNames) {
				if ($entityId = Entity::ParseFromString($entityStr)) {}
				elseif (!empty($urlNames)&&is_string($urlNames)) {
					$pages[$entityStr] = $urlNames;
				}
			}
		}
		if (
			(mb_strpos($params['route'], 'site/', null, 'utf-8') === false)
			&& (mb_strpos($params['route'], 'bookshelf/', null, 'utf-8') === false)
			&& (mb_strpos($params['route'], 'offers/', null, 'utf-8') === false)
			&& (mb_strpos($params['route'], 'client/', null, 'utf-8') === false)
			&& (mb_strpos($params['route'], 'cart/', null, 'utf-8') === false)//
		) return false;

		return true;
	}

}
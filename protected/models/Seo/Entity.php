<?php
/*Created by Кирилл (29.01.2019 9:36)*/

class ModelsSeoEntity extends Seo_settings {
	private $_eid, $_cid = 0, $_id = 0;

	protected function __construct($params = null) {
		parent::__construct($params);
		if ($params === null) {
			$this->_eid = Entity::ParseFromString(Yii::app()->getRequest()->getParam('entity'));
			$idName = HrefTitles::get()->getIdName($this->_eid, $this->_route);
			$this->_id = (int) Yii::app()->getRequest()->getParam($idName);
		}
		else {
			$this->_eid = (int) $params['entity'];
			$idName = HrefTitles::get()->getIdName($this->_eid, $this->_route);
			$this->_id = (int) $params['id'];
		}
		if ($idName == 'cid') $this->_cid = $this->_id;

		if ($params === null) {
			$language = Yii::app()->getLanguage();
			if ($language === 'rut') $language = 'ru';
			$sql = 'select `' . $language . '` from seo_settings where (`route` = :route) and (`entity` = :eid) and (`id` = :id) limit 1';
			$bdSettings = Yii::app()->db->createCommand($sql)->queryRow(true, array('route'=>$this->_route, 'eid'=>$this->_eid, 'id'=>$this->_id));
			if (!empty($bdSettings[Yii::app()->language])) {
				$bdSettings[Yii::app()->language] = unserialize($bdSettings[Yii::app()->language]);
				foreach ($bdSettings[Yii::app()->language] as $k=>$v) {
					$this->_settings[$k] = $v;
				}
			}
		}

		if (empty($this->_settings['h1'])||empty($this->_settings['title'])||empty($this->_settings['description'])||empty($this->_settings['keywords'])) {
			$entitySettings = $this->getDefaultSettings(Yii::app()->language);
			if (!empty($entitySettings)) {
				foreach ($entitySettings as $k=>$v) {
					if (empty($this->_settings[$k])) $this->_settings[$k] = $v;
				}
			}
		}

//		Debug::staticRun(array($this->_settings));
	}

	function getDefaultSettings($lang) {
		if (empty(/*$this->_cid*/$this->_id)) $file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/seo_entity.php';
		else $file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/seo_category.php';
		if (file_exists($file)) {
			$fileSettings = include $file;
			$entitySettings = $fileSettings[0];
			if (isset($fileSettings[$this->_eid])) $entitySettings = $fileSettings[$this->_eid];
			return $entitySettings;
		}
		return array();
	}
//	protected function _getH1() {
//		return '';
//	}

	protected function _fillReplace() {
		parent::_fillReplace();
		$this->_replace['{entity_name}'] = Entity::GetTitle($this->_eid);
		if (empty($this->_id)) {
			$sql = 'SELECT count(*) FROM `' . Entity::GetEntitiesList()[$this->_eid]['site_table'] . '` WHERE (avail_for_order > 0)';
			$this->_replace['{counts}'] = (int)Yii::app()->db->createCommand($sql)->queryScalar();
			switch ($this->_route) {
				case 'entity/categorylist': $this->_replace['{name}'] = Yii::app()->ui->item('LIST_SOFT_CATTREE'); break;
				case 'entity/publisherlist':
					if (in_array($this->_eid, array(Entity::SOFT, Entity::MAPS, Entity::PRINTED))) $this->_replace['{name}'] = Yii::app()->ui->item('PROPERTYLIST_FOR_PROD');
					else $this->_replace['{name}'] = Yii::app()->ui->item('PROPERTYLIST_FOR_PUBLISHERS');
					break;
				case 'entity/serieslist': $this->_replace['{name}'] = Yii::app()->ui->item('A_LEFT_BOOKS_SERIES_PROPERTYLIST'); break;
				case 'entity/authorlist': $this->_replace['{name}'] = Yii::app()->ui->item('PROPERTYLIST_FOR_AUTHORS'); break;
				case 'entity/bindingslist':
					switch ($this->_eid) {
						case Entity::BOOKS:case Entity::SHEETMUSIC: $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_FILTER_TYPE1'); break;
						case Entity::MUSIC: $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_FILTER_TYPE3'); break;
						case Entity::PERIODIC: $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_TYPE_IZD'); break;
						default: $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_FILTER_TYPE2'); break;
					}
//					$this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_TYPOGRAPHY');
					break;
				case 'entity/yearslist': $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_FILTER_YEAR'); break;
				case 'entity/yearreleaseslist': $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_YEAR_REAL'); break;
				case 'entity/performerlist': $this->_replace['{name}'] = Yii::app()->ui->item('A_LEFT_AUDIO_AZ_PROPERTYLIST_PERFORMERS'); break;
				case 'entity/medialist':
					if ($this->_eid == Entity::MUSIC) $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_FILTER_TYPE3');
					else $this->_replace['{name}'] = Yii::app()->ui->item('Media');
					break;
				case 'entity/typeslist': $this->_replace['{name}'] = Yii::app()->ui->item('A_NEW_TYPE_IZD'); break;
				case 'entity/actorlist': $this->_replace['{name}'] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_ACTORS'); break;
				case 'entity/directorlist': $this->_replace['{name}'] = Yii::app()->ui->item('A_LEFT_VIDEO_AZ_PROPERTYLIST_DIRECTORS'); break;
				case 'entity/audiostreamslist': $this->_replace['{name}'] = Yii::app()->ui->item('AUDIO_STREAMS'); break;
				case 'entity/subtitleslist': $this->_replace['{name}'] = Yii::app()->ui->item('Credits'); break;
				case 'entity/studioslist': $this->_replace['{name}'] = Yii::app()->ui->item('STUDIOS'); break;
			}
		}
		elseif (empty($this->_cid)) {
			/*if (mb_strpos($this->_settings['h1'], '{entity_name}') === false)*/ $this->_settings['h1'] = str_replace('{name}', '{entity_name},', $this->_settings['h1']);
			/*if (mb_strpos($this->_settings['title'], '{entity_name}') === false)*/ $this->_settings['title'] = str_replace('{name}', '{entity_name},', $this->_settings['title']);
			/*if (mb_strpos($this->_settings['description'], '{entity_name}') === false)*/ $this->_settings['description'] = str_replace('{name}', '{entity_name},', $this->_settings['description']);
			/*if (mb_strpos($this->_settings['keywords'], '{entity_name}') === false)*/ $this->_settings['keywords'] = str_replace('{name}', '{entity_name},', $this->_settings['keywords']);
		}
		else {
			$category = new Category();
			$cat = $category->GetByIds($this->_eid, array($this->_cid));
			$cat = array_shift($cat);
			$this->_replace['{name}'] = ProductHelper::GetTitle($cat);
			if ($this->_eid == Entity::PERIODIC) {
				$this->_replace['{type_publication}'] = '';
				for ($i=1;$i<4;$i++) {
					if ($cat['avail_items_type_' . $i] > 0) {
						if (empty($this->_replace['{type_publication}'])) $this->_replace['{type_publication}'] = mb_strtolower(Yii::app()->ui->item('PERIODIC_TYPE_PLURAL_' .$i));
						else {
							$this->_replace['{type_publication}'] = 'издание';
							break;
						}
					}
				}
			}
		}
		$this->_replace['{lang_predl}'] = FilterNames::get($this->_eid, $this->_cid)->lang_sel;
		$params = FilterNames::get($this->_eid, $this->_cid)->getParams($this->_route);

		if (!empty($params)) $this->_replace['{params}'] = implode('; ', $params);

		if (!empty($this->_replace['{params}'])) {
			Debug::staticRun(array($this->_route, $this->_replace, $params));
			if ($this->_route == 'entity/list') $this->_replace['{params}'] = '';
			elseif (!empty($this->_id)) $this->_settings['h1'] = str_replace('{entity_name} {params}', '{entity_name}, {params}', $this->_settings['h1']);
		}

//		Debug::staticRun(array($this->_route, $this->_replace, $this->_settings['h1']));

	}

}
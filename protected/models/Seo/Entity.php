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
			$sql = 'select `' . Yii::app()->getLanguage() . '` from seo_settings where (`route` = :route) and (`entity` = :eid) and (`id` = :id) limit 1';
			$bdSettings = Yii::app()->db->createCommand($sql)->queryRow(true, array('route'=>$this->_route, 'eid'=>$this->_eid, 'id'=>$this->_id));
			if (!empty($bdSettings)) {
				$bdSettings = unserialize($bdSettings);
				foreach ($bdSettings as $k=>$v) $this->_settings[$k] = $v;
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
		if (empty($this->_cid)) $file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/seo_entity.php';
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
		if (empty($this->_cid)) {
			$sql = 'SELECT count(*) FROM `' . Entity::GetEntitiesList()[$this->_eid]['site_table'] . '` WHERE (avail_for_order > 0)';
			$this->_replace['{counts}'] = (int)Yii::app()->db->createCommand($sql)->queryScalar();
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
		$params = FilterNames::get($this->_eid, $this->_cid)->getParams();
		if (!empty($params)) $this->_replace['{params}'] = implode('; ', $params);

//		Debug::staticRun(array($this->_replace));

	}

}
<?php
/*Created by Кирилл (29.01.2019 9:36)*/

class ModelsSeoEntity extends Seo_settings {
	private $_eid, $_cid = 0, $_id = 0;

	protected function __construct() {
		parent::__construct();
		$this->_eid = Entity::ParseFromString(Yii::app()->getRequest()->getParam('entity'));
		$idName = HrefTitles::get()->getIdName($this->_eid, $this->_route);
		$this->_id = (int) Yii::app()->getRequest()->getParam($idName);
		if ($idName == 'cid') $this->_cid = $this->_id;

		$sql = 'select `' . Yii::app()->getLanguage() . '` from seo_settings where (`route` = :route) and (`entity` = :eid) and (`id` = :id) limit 1';
		$bdSettings = Yii::app()->db->createCommand($sql)->queryRow(true, array('route'=>$this->_route, 'eid'=>$this->_eid, 'id'=>$this->_id));
		if (!empty($bdSettings)) {
			$bdSettings = unserialize($bdSettings);
			foreach ($bdSettings as $k=>$v) $this->_settings[$k] = $v;
		}
		if (empty($this->_settings['h1'])||empty($this->_settings['title'])||empty($this->_settings['description'])||empty($this->_settings['keywords'])) {
			if (empty($this->_cid)) $file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].Yii::app()->language.'/seo_entity.php';
			else $file = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].Yii::app()->language.'/seo_category.php';
			if (file_exists($file)) {
				$fileSettings = include $file;
				foreach ($fileSettings as $k=>$v) {
					if (empty($this->_settings[$k])) $this->_settings[$k] = $v;
				}
			}
		}

		Debug::staticRun(array($this->_settings));
	}

//	protected function _getH1() {
//		return '';
//	}

	protected function _fillReplace() {
		if (empty($this->_cid)) {
			$sql = 'SELECT count(*) FROM `' . Entity::GetEntitiesList()[$this->_eid]['site_table'] . '` WHERE (avail_for_order > 0)';
			$this->_replace['counts'] = (int)Yii::app()->db->createCommand($sql)->queryScalar();

			$this->_replace['name'] = Entity::GetTitle($this->_eid);
		}
		else {
			$category = new Category();
			$cat = $category->GetByIds($this->_eid, array($this->_cid));
			$cat = array_shift($cat);
			$this->_replace['name'] = ProductHelper::GetTitle($cat);
		}
		$this->_replace['lang_predl'] = FilterNames::get($this->_eid, $this->_cid)->lang_sel;
		$params = FilterNames::get($this->_eid, $this->_cid)->getParams();
		if (!empty($params)) $this->_replace['params'] = implode('; ', $params);

		Debug::staticRun(array($this->_replace));

	}

}
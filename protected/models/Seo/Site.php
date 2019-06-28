<?php
/*Created by Кирилл (28.06.2019 20:40)*/
class ModelsSeoSite extends Seo_settings {
	private $_id = 0;

	protected function __construct($params = null) {
		parent::__construct($params);
		if ($params === null) {
			$idName = HrefTitles::get()->getIdName(0, $this->_route);
			$this->_id = (int) Yii::app()->getRequest()->getParam($idName);
		}
		else {
			$this->_id = (int) $params['id'];
		}

		if ($params === null) {
			$language = Yii::app()->getLanguage();
			if ($language === 'rut') $language = 'ru';
			$sql = 'select `' . $language . '` from seo_settings where (`route` = :route) and (`entity` = :eid) and (`id` = :id) limit 1';
			$bdSettings = Yii::app()->db->createCommand($sql)->queryRow(true, array('route'=>$this->_route, 'eid'=>0, 'id'=>$this->_id));
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
		return array();
	}
//	protected function _getH1() {
//		return '';
//	}

}
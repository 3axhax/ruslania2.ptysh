<?php
/*Created by Кирилл (04.07.2018 18:22)*/

class OffersByItem extends CWidget {
	protected $_params = array('idItem'=>0, 'entity'=>0);//здесь массив начальных значений
	protected $_availLanguages = array('ru', 'en', 'fi'), $_defaultLanguage = 'en';

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		if (!$this->_check()) return;

		$offers = $this->_getOffers();
		if (empty($offers)) return;

		$this->render('offers_by_item', array('offers' => $offers));
	}

	protected function _check() {
		$this->_params['idItem'] = (int)$this->_params['idItem'];
		if ($this->_params['idItem'] <= 0) return false;

		$this->_params['entity'] = (int)$this->_params['entity'];
		if (!Entity::GetUrlKey($this->_params['entity'])) return false;

		return true;
	}

	protected function _getOffers() {
		$lang = Yii::app()->language;
		if (!in_array($lang, $this->_availLanguages)) $lang = $this->_defaultLanguage;

		$sql = ''.
			'select t.id, t.title_' . $lang . ', t.is_special '.
			'from offers t '.
				'join offer_items tI on (tI.offer_id = t.id) '.
					'and (tI.entity_id = ' . (int) $this->_params['entity'] . ') '.
					'and (tI.item_id = ' . (int) $this->_params['idItem'] . ') '.
			'where (t.is_active = 1) '.
			'order by t.is_special desc, t.creation_date desc '.
		'';
		return Yii::app()->db->createCommand($sql)->queryAll();
	}
}
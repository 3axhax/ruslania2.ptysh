<?php
/*Created by Кирилл (05.06.2018 21:23)*/

class LinksToList extends CWidget {
	protected $_params = array('links'=>['publisher', 'authors', 'series', 'actors', 'directors']);//здесь массив начальных значений

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
	}

	function run() {
		$links = array();
		foreach ($this->_params['links'] as $linkName) {
			if ($this->_checkByEntity($linkName)) {
				$funkName = '_' . $linkName . 'Link';
				if (method_exists($this, $funkName)) $links[$linkName] = $this->$funkName();
			}
		}
		if (empty($links)) return;

		$this->render('links_to_list', array('links'=>$links));
	}

	private function _publisherLink() {
		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_PUBLISHERS'),
			'href'=>Yii::app()->createUrl('entity/publisherlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _authorsLink() {
		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_AUTHORS'),
			'href'=>Yii::app()->createUrl('entity/authorlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _seriesLink() {
		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_SERIES_PROPERTYLIST'),
			'href'=>Yii::app()->createUrl('entity/serieslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _actorsLink() {
		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_ACTORS'),
			'href'=>Yii::app()->createUrl('entity/actorlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _directorsLink() {
		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_DIRECTORS'),
			'href'=>Yii::app()->createUrl('entity/directorlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _checkByEntity($linkName) {
		$entitys = Entity::GetEntitiesList();
		if (!empty($entitys[$this->_params['entity']])) return in_array($linkName, $entitys[$this->_params['entity']]['with']);
		return false;
	}


}
<?php
/*Created by Кирилл (23.07.2019 21:30)*/
require_once dirname(__FILE__) . '/Photos/Photos.php';
class Books_photos extends ModelsPhotos {

	static function model($className = __CLASS__) {
		return parent::model($className);
	}
	function tableName() {
		return Entity::GetEntitiesList()[Entity::BOOKS]['photo_table'];
	}

}
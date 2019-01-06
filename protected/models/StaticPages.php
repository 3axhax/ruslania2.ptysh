<?php
/*Created by Кирилл (18.10.2018 22:09)*/
class StaticPages {

	function getPage($page) {
		$sql = ''.
			'select * '.
			'from static_pages '.
			'where (name = :page) '.
			'limit 1'.
		'';
		return Yii::app()->db->createCommand($sql)->queryRow(true, array(':page'=>$page));
	}

	function save($page, $lang, $title, $desc) {
		$sql = ''.
			'insert into static_pages set '.
				'title_' . $lang . ' = :title, '.
				'description_' . $lang . ' = :desc, '.
				'name = :page '.
			'on duplicate key update '.
				(($title === null)?'':'title_' . $lang . ' = :title, ').
				'description_' . $lang . ' = :desc '.
		'';
		return Yii::app()->db->createCommand($sql)->execute(array(':page'=>$page, ':title'=>(string)$title, ':desc'=>$desc));
	}

	function isWordpanel($uid) {
		//72459 - kirill.ruh@gmail.com | kirill
		//5 - aa@ruslania.com
		//60093 - maria.ponomareva@gmail.com
		$allowUsers = array(5, 72459, 60093);
		return in_array($uid, $allowUsers);
	}

}
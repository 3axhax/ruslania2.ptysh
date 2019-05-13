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
		$result = Yii::app()->db->createCommand($sql)->execute(array(':page'=>$page, ':title'=>(string)$title, ':desc'=>$desc));
		if ($lang == 'ru') {
			$sql = ''.
				'insert into static_pages set '.
					'title_rut = :title, '.
					'description_rut = :desc, '.
					'name = :page '.
				'on duplicate key update '.
					(($title === null)?'':'title_rut = :title, ').
					'description_rut = :desc '.
			'';
			$desc = ProductHelper::ToAscii($desc, array('onlyTranslite'=>true, 'lowercase'=>false));
			$desc = str_replace('/ru/', '/rut/', $desc);
			Yii::app()->db->createCommand($sql)->execute(array(':page'=>$page, ':title'=>ProductHelper::ToAscii((string)$title, array('onlyTranslite'=>true, 'lowercase'=>false)), ':desc'=>$desc));
		}
		return $result;
	}

	function isWordpanel($uid) {
		//77925 - kirill.ruh@gmail.com | kirill
		//5 - aa@ruslania.com
		//60093 - maria.ponomareva@gmail.com
		//69481 - sankes@list.ru | Александр
		$allowUsers = array(5, 77925, 60093, 69481);
		return in_array($uid, $allowUsers);
	}

}
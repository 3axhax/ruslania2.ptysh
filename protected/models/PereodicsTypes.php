<?php
/*Created by Кирилл (05.02.2019 21:36)*/
class PereodicsTypes extends CMyActiveRecord
{
	static private $_bindings = null;
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'pereodics_types';
	}

	public function GetBinding($entity, $bid) {
		$bindings = self::getBindings();
		if (isset($bindings[$bid])) return $bindings[$bid];
		return array();
	}

	static function getBindings() {
		if (self::$_bindings === null) {
			self::$_bindings = array();
			$sql = ''.
				'select * '.
				'from ' . self::tableName() . ' ' .
			'';
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			foreach ($rows as $row) self::$_bindings[(int)$row['id']] = $row;
		}
		return self::$_bindings;
	}


}
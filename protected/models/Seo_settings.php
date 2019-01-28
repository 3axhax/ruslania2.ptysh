<?php
/*Created by Кирилл (28.01.2019 20:54)*/

class Seo_settings {
	static private $_self = null;

	final private function __construct() {
		$controller = Yii::app()->getController();
		$ctrlId = $controller->id;
		$actionId = $controller->action->id;
	}
	/**
	 * @return Seo_settings
	 */
	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

}
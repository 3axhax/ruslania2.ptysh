<?php
/*Created by Кирилл (12.07.2018 21:38)*/
class HrefTitles {
	static private $_self = null;
	/**
	 * @return HrefTitles
	 */

	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

	function getByIds($entity, $route, $ids) {

	}

}
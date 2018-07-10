<?php
/*Created by Кирилл (10.07.2018 20:36)*/

class Categories {
	static private $_self = null;
	/**
	 * @return Categories
	 */

	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

}
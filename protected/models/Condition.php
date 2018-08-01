<?php
/*Created by Кирилл (01.08.2018 18:57)*/

class Condition {

	private $_data = array(), $_entity = null, $_cid = null;

	static private $_self = null;

	/**
	 * @return Condition
	 */
	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}


}
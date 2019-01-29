<?php
/*Created by Кирилл (29.01.2019 16:22)*/

class FilterNames {
	static private $_self = array();

	/**
	 * @return FilterNames
	 */
	static function get($entity, $cid) {
		$key = 'e' . (int)$entity . 'c' . (int) $cid;
		if (!isset(self::$_self[$key])) self::$_self[$key] = new self($entity, $cid);
		return self::$_self[$key];
	}

}
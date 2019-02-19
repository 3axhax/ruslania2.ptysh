<?php
/*Created by Кирилл (15.02.2019 18:44)*/

class SphinxQL {
	/**@var mysqli*/
	static protected $_mysqli = null;

	/**
	 * @return SphinxQL
	 */
	final static function getDriver() {
		static $drivers = null;
		if ($drivers === null) $drivers = new SphinxQL();
		return $drivers;
	}

	final function getMysqli(){
		if (!self::$_mysqli) {
			self::$_mysqli = new mysqli('127.0.0.1', null, null, null, 9306);
			if (self::$_mysqli->connect_errno) {
			}
			self::$_mysqli->set_charset('utf8');
		}
		return self::$_mysqli;
	}

	/**
	 * @param $sql
	 * @return bool|mysqli_result
	 */
	final function query($sql) {
		$result = $this->getMysqli()->query($sql);
		if (!$result) {
			Debug::staticRun(array((int) $this->getMysqli()->errno, $this->getMysqli()->error));
		}
		return $result;
	}

	function multiSelect($sql) {
		$result = $this->query($sql);
		if ($result) {
			$return = array();
			while ($row = $result->fetch_assoc()) $return[] = $row;
			$result->free();
			return $return;
		}
		return array();
	}

	function rowSelect($sql) {
		$result = $this->query($sql);
		if ($result) {
			$row = $result->fetch_assoc();
			$result->free();
			return $row;
		}
		return array();
	}

	function queryCol($sql) {
		$result = $this->query($sql);
		if ($result) {
			$return = array();
			while ($row = $result->fetch_row()) $return[] = array_shift($row);
			$result->free();
			return $return;
		}
		return array();
	}

	function simpleQuery($sql) {
		$result = $this->query($sql);
		if ($result) return true;
		return false;
	}

	function fieldSelect($sql) {
		$result = $this->rowSelect($sql);
		return $result ? array_shift($result) : false;
	}

	function snippet($text, $index, $q, $before_match = '<span class="title__bold">', $after_match = '</span>', $limit = 100, $around = 3, $allow_empty = true, $html_strip_mode = 'none') {
		$sql = "".
			"CALL SNIPPETS(".
				"'".$this->getMysqli()->real_escape_string($text)."', ".
				"'".$index."', ".
				"'".$this->getMysqli()->real_escape_string($q)."', ".
		"";
		if (!empty($before_match)) $sql .= "'" . $this->getMysqli()->real_escape_string($before_match) . "' as before_match, ";
		if (!empty($after_match)) $sql .= "'" . $this->getMysqli()->real_escape_string($after_match) . "' as after_match, ";
		if (!empty($limit)) $sql .= $limit . " as limit, ";
		if (!empty($around)) $sql .= $around . " as around, ";
		if (!empty($allow_empty)) $sql .= (int)$allow_empty . " as allow_empty, ";
		if (!empty($html_strip_mode)) $sql .= "'" . $this->getMysqli()->real_escape_string($html_strip_mode) . "' as html_strip_mode, ";
		$sql .= "1 as query_mode, 1 use_boundaries)";
		return $this->fieldSelect($sql);
	}


}
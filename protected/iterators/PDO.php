<?php
/*Created by Кирилл (22.05.2018 22:29)*/

class IteratorsPDO implements Iterator, Countable {
	private $_position, $_maxPosition, $_result;

	function __construct(PDOStatement $result) {
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$this->_position = 1;
		$this->_maxPosition = $result->rowCount();
		$this->_result = $result;
		if (!$this->_maxPosition) $this->_result->closeCursor();
	}

	function rewind() {
		$this->position = 1;
	}

	function current() {
		$result = $this->_result->fetch();
		if ($this->_position == $this->_maxPosition) $this->_result->closeCursor();
		return $result;
	}

	function key() {
		return $this->_position;
	}

	function next() {
		$this->_position++;
	}

	function valid() {
		return ($this->_position <= $this->_maxPosition);
	}

	function count(){
		return $this->_maxPosition;
	}
}
<?php
/*Created by Кирилл (20.06.2018 22:14)*/

class Debug extends CWidget {
	protected $_params = array();//здесь массив начальных значений

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		if (isset($_GET['ha'])) $this->render('debug', array('args'=>$this->_params, 'trace'=> debug_backtrace()));
	}

}
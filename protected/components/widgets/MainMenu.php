<?php
/*Created by Кирилл (05.09.2018 19:25)*/
/**
 * Class MainMenu
 */
class MainMenu extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array();
	/**
	 * @var Category
	 */
	private $_category = null;

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
		$this->_category = new Category();
	}

	function run() {
		$this->render('MainMenu/main_menu', array());
	}

}
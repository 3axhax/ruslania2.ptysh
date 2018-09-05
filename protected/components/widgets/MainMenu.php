<?php
/*Created by Кирилл (05.09.2018 19:25)*/
/**
 * класс недоделанный (нет времени продумать структуру меню)
 * но надо обязательно сделать, чтобы получать все категории одним запросом и одним запросом получать наименования для href
 *
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

	private $_menu = array(
		'A_GOTOBOOKS'=>array(
			Entity::BOOKS => array(181, 16, 206, 211, 189, 65, 67, 202, 213),
			Entity::SHEETMUSIC => array(47),
			'books_sale'=>array('A_NEW_SALE'=>213),
			'books_category'=>array('A_NEW_ALL_CATEGORIES'=>'entity/categorylist'),
		),
		'A_GOTOMUSICSHEETS'=>array(

		),
	);

	private $_availCategories = array(
		'books'=>array(181, 16, 206, 211, 189, 65, 67, 202),
		'sheetmusic'=>array(47, 160, 249, 128, 136),
		'music'=>array(78, 74, 4, 11, 6, 17, 2, 73, 38),
		'periodic'=>array(19, 48, 96, 67),
		'printed'=>array(),
	);

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
<?php
/*Created by Кирилл (05.06.2018 22:17)*/

/**
 * Class LeftCategories
 */
class LeftCategories extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array(
		'cid'=>0,
		'tpl'=>'left_categories',
		'catTitle'=>'',
		'lvl' => 1,
	);
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
		$categories = isset($this->_params['categories'])?$this->_params['categories']:$this->_getCategories();
		if (empty($categories)) return;

		$this->render($this->_params['tpl'], array(
			'categories'=>$categories,
			'cid'=>$this->_params['cid'],
			'entity'=>$this->_params['entity'],
			'catTitle'=>$this->_params['catTitle'],
			'lvl'=>$this->_params['lvl'],
		));
	}

	private function _getCategories($cid = null) {
		if ($cid === null) $cid = $this->_params['cid'];
		$categories = $this->_category->exists_subcategoryes($this->_params['entity'], $cid);
		if (empty($categories)) return array();

		foreach ($categories as $k=>$category) {
			if (empty($category['avail_items_count'])) {
				//убираю категории, если нет товаров в наличии
				unset($categories[$k]);
			}
			else $categories[$k]['childs'] = $this->_getCategories($category['id']);
		}
		return $categories;

	}

}
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
	private $_usePeriodicCategoryTypes = true;//true - что бы категории слева в подписке показывались с учетом типов

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
		$this->_category = new Category();
	}

	function run() {
		$categories = isset($this->_params['categories'])?$this->_params['categories']:$this->_getCategories();

		if ($this->_usePeriodicCategoryTypes&&($this->_params['entity'] === Entity::PERIODIC)) {
			$this->_params['tpl'] = 'left_categories_periodics';
			$categories = $this->_preparePeriodicsCategorys($categories);
		}

		if (empty($categories)) return;

		$this->render($this->_params['tpl'], array(
			'categories'=>$categories,
			'cid'=>$this->_params['cid'],
			'entity'=>$this->_params['entity'],
			'catTitle'=>$this->_params['catTitle'],
			'lvl'=>$this->_params['lvl'],
		));
	}

	private function _preparePeriodicsCategorys($categories) {
		$typeParam = (int) Yii::app()->getRequest()->getParam('type');
		$typeIds = array(2, 1, 3);
		$sql = ''.
			'select t.id '.
			'from `pereodics_types` t '.
			'order by field(id, ' . implode(',',$typeIds) . ') '.
		'';
		$types = Yii::app()->db->createCommand($sql)->queryColumn();
		if (in_array($typeParam, $types)) $types = array($typeParam);
		else {
			$binding = Yii::app()->getRequest()->getParam('binding');
			if (!empty($binding)&&is_array($binding)) {
				foreach ($binding as $i=>$type) {
					if (!in_array($type, $types)) unset($binding[$i]);
				}
			}
			else $binding = array();
			if (!empty($binding)) {
				foreach ($types as $i=>$type)
					if (!in_array($type, $binding))
						unset($types[$i]);
			}
		}
		$result = array();
		foreach ($types as $i=>$type) {
			foreach ($categories as $k=>$category) {
				if (!empty($category['avail_items_type_' . $type])) {
					if (empty($result[$type])) $result[$type] = array();
					$category['childs'] = array();
					$result[$type][] = $category;
				}
			}
		}
		return $result;
	}

	private function _getCategories($cid = null) {
		if ($cid === null) $cid = $this->_params['cid'];
		if ($this->_usePeriodicCategoryTypes&&($this->_params['entity'] === Entity::PERIODIC)) $cid = 0;

		$categories = $this->_category->exists_subcategoryes($this->_params['entity'], $cid);
		if (empty($categories)) return array();

		foreach ($categories as $k=>$category) {
			if (empty($category['avail_items_count'])) {
				//убираю категории, если нет товаров в наличии
				unset($categories[$k]);
			}
			elseif (($this->_params['entity'] != Entity::PERIODIC)||!$this->_usePeriodicCategoryTypes) $categories[$k]['childs'] = $this->_getCategories($category['id']);
		}
		return $categories;

	}

}
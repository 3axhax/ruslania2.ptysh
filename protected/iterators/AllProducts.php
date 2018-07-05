<?php
/*Created by Кирилл (05.07.2018 19:30)*/

/**
 * Class IteratorsAllProducts
 * итератор для получения товаров из разных разделов
 */
class IteratorsAllProducts extends ArrayIterator {
	/**
	 * @var IteratorsPDO
	 */
	private $_items = null;
	/**
	 * @var array в запросе только те поля, которые указаны в массиве
	 */
	private $_fields = array(
		'id'=>'id',
		'title'=>'title_ru',
		'vat'=>'vat',
		'discount'=>'discount',
		'unitweight_skip'=>'unitweight_skip',
		'brutto'=>'brutto',
		'sub_fin_year'=>'0 sub_fin_year',
		'sub_world_year'=>'0 sub_world_year',
		'code'=>'code',
		'subcode'=>'subcode',
		'series_id'=>'series_id',
		'publisher_id'=>'publisher_id',
		'year'=>'year',
	);

	/**
	 * @param array $array [[entity, id],[entity, id],[entity, id], ...]
	 * @param int $flags
	 */
	function __construct($array = array(), $flags = 0) {
		Debug::staticRun(array($array));
		parent::__construct($array, $flags);
	}

	function current() {
		if ($this->_items === null) $this->_getItems();
		$item = parent::current();
		Debug::staticRun(array($item));
		return $item;
	}

//	function valid() {
//		if ($this->_items === null) $this->_getItems();
//		if (empty($this->_items)||!$this->_items->valid()) return false;
//
//		return parent::valid();
//	}

	function setFields($fields, $replace = false) {
		if ($replace) $this->_fields = $fields;
		else $this->_fields = array_merge($fields);
	}

	private function _getItems() {
		$items = $this->getArrayCopy();
		$entityIds = array();
		$order = '';

		foreach ($items as $item) {
			if (!Entity::IsValid($item['entity'])) continue;

			if (!isset($entityIds[$item['entity']])) $entityIds[$item['entity']] = array();
			$entityIds[$item['entity']][] = $item['id'];
			$order .= ', "' . $item['entity'] . '_' . $item['id'] . '"';
		}
		$sql = array();
		$fields = $this->_fields;
		foreach ($entityIds as $entity=>$ids) {
			$fields['entity'] = $entity . ' entity';
			if ($entity == Entity::PERIODIC) {
				if (isset($fields['sub_fin_year'])) $fields['sub_fin_year'] = 'sub_fin_year';
				if (isset($fields['sub_world_year'])) $fields['sub_world_year'] = 'sub_world_year';
			}
			if (isset($fields['year'])&&!Entity::checkEntityParam($entity, 'years')) $fields['year'] = '0 year';
			if (isset($fields['series_id'])&&!Entity::checkEntityParam($entity, 'series')) $fields['series_id'] = '0 series_id';
			if (isset($fields['publisher_id'])&&!Entity::checkEntityParam($entity, 'publisher')) $fields['publisher_id'] = '0 publisher_id';

			$sql[] = 'select ' . implode(',', $fields) . ' from ' . Entity::GetEntitiesList()[$entity]['site_table'] . ' where (id in (' . implode(',', $ids) . '))';
		}
		if (empty($sql)) {
			$this->_items = array();
			return;
		}

		$sql = implode(' union ', $sql);
		$sql .= 'order by field(concat(entity, "_", id)' . $order . ')';
		Debug::staticRun(array($sql));

//		require_once dirname(__FILE__) . '/PDO.php';
//		$pdo = Yii::app()->db->createCommand($sql);
//		$pdo->prepare();
//		$pdo->getPdoStatement()->execute(null);
//		$this->_items = new IteratorsPDO($pdo->getPdoStatement());
	}


}
<?php
/*Created by Кирилл (10.08.2018 20:48)*/

class YouView extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array(
		'id' => 0,
		'entity' => 0,
		'tpl' => 'you_view',
	);
	private $_countItems = 5;

	function init() {

	}

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		$products = $this->_getProducts();
		if (empty($products)) return;

		$this->render($this->_params['tpl'], array('items'=>$products));
	}

	private function _getProducts() {
		$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);
		$products = array();
		$items = array();
		if (!empty($serGoods)) {
			shuffle($serGoods);
			$i = 0;
			foreach ($serGoods as $good) {
				$ex = explode('_', $good);
				$good_id = $ex[0];
				$good_entity = $ex[1];
				if (($good_id == $this->_params['id'])&&($good_entity == $this->_params['entity'])) continue;
				if ($i++ >= $this->_countItems) break;
				if (empty($items[$good_entity])) $items[$good_entity] = array();
				$items[$good_entity][] = $good_id;
			}
			$p = new Product();
			foreach($items as $entity=>$ids) {
				if (!empty($ids)) {
					foreach ($p->GetProductsV2($entity, $ids) as $item) {
						$item['status'] = $p->GetStatusProduct($entity, $item['id']);
						$item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
						$item['priceData']['unit'] = '';
						if ($entity == Entity::PERIODIC) {
							$issues = Periodic::getCountIssues($item['issues_year']);
							if (!empty($issues['show3Months'])) {
								$item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
								$item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
								$item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
								$item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
							}
							elseif (!empty($issues['show6Months'])) {
								$item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
								$item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
								$item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
								$item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
							}
							else {
								$item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
							}
						}
						$products[] = $item;
					}
				}
			}
		}
		return $products;
	}


}
<?php
/*Created by Кирилл (18.07.2019 22:02)*/
class ActionItems extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array(
		'uid' => 0,
		'sid' => '',

	);
	private $_items = null;

	function init() {

	}

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		$file = Yii::getPathOfAlias('webroot') . '/protected/runtime/fileCache/mainactions_' . Yii::app()->language . '.html.php';
		if (file_exists($file)) {
			//храним 1 час
			if (filectime($file) < (time() - 3600)) unlink($file);
		}

		if (!file_exists($file)) {
			$products = $this->_getProducts();
			if (empty($products)) file_put_contents($file, '');
			else file_put_contents($file, $this->render('action_items', array('actionItems'=>$products), true));
		}
		$txt = file_get_contents($file);
		$this->_replace($txt);
		echo $txt;
	}

	private function _getProducts() {
		$actionItems = $this->_getItems();
		if (!empty($actionItems)) {
			$entityIds = array();
			foreach ($actionItems as $item) {
				if (empty($entityIds[$item['entity']])) $entityIds[$item['entity']] = array();
				$entityIds[$item['entity']][] = $item['item_id'];
			}
			$p = new Product();
			$fullInfo = array();
			foreach ($entityIds as $eId=>$ids) {
				$fullInfo[$eId] = array();
				$list = $p->GetProductsV2($eId, $ids, true);
				foreach($entityIds[$eId] as $iid) {
					if(!isset($list[$iid])) continue;

					$av = Availability::GetStatus($list[$iid]);
					if($av == Availability::NOT_AVAIL_AT_ALL) continue; // В подборках нет товаров, которых не заказать

					$list[$iid]['status'] = $p->GetStatusProduct($eId, $list[$iid]['id']);
					$list[$iid]['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $list[$iid]);
					$list[$iid]['priceData']['unit'] = '';
					if ($eId == Entity::PERIODIC) {
						$issues = Periodic::getCountIssues($list[$iid]['issues_year']);
						if (!empty($issues['show3Months'])) {
							$list[$iid]['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
							$list[$iid]['priceData'][DiscountManager::BRUTTO] = $list[$iid]['priceData'][DiscountManager::BRUTTO_FIN]/4;
							$list[$iid]['priceData'][DiscountManager::WITH_VAT] = $list[$iid]['priceData'][DiscountManager::WITH_VAT_FIN]/4;
							$list[$iid]['priceData'][DiscountManager::WITHOUT_VAT] = $list[$iid]['priceData'][DiscountManager::WITHOUT_VAT_FIN]/4;
						}
						elseif (!empty($issues['show6Months'])) {
							$list[$iid]['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
							$list[$iid]['priceData'][DiscountManager::BRUTTO] = $list[$iid]['priceData'][DiscountManager::BRUTTO_FIN]/2;
							$list[$iid]['priceData'][DiscountManager::WITH_VAT] = $list[$iid]['priceData'][DiscountManager::WITH_VAT_FIN]/2;
							$list[$iid]['priceData'][DiscountManager::WITHOUT_VAT] = $list[$iid]['priceData'][DiscountManager::WITHOUT_VAT_FIN]/2;
						}
						else {
							$list[$iid]['priceData'][DiscountManager::BRUTTO] = $list[$iid]['priceData'][DiscountManager::BRUTTO_FIN];
							$list[$iid]['priceData'][DiscountManager::WITH_VAT] = $list[$iid]['priceData'][DiscountManager::WITH_VAT_FIN];
							$list[$iid]['priceData'][DiscountManager::WITHOUT_VAT] = $list[$iid]['priceData'][DiscountManager::WITHOUT_VAT_FIN];
							$list[$iid]['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
						}
					}
					$fullInfo[$eId][$iid] = $list[$iid];
				}
			}
			foreach ($actionItems as $i=>$item) {
				if (empty($fullInfo[$item['entity']][$item['item_id']])) unset($actionItems[$i]);
				else {
					$actionItems[$i]['product'] = $fullInfo[$item['entity']][$item['item_id']];
				}
			}
		}
		else $actionItems = array();
		return $actionItems;
	}

	private function _replace($txt) {
		/**@var $cart Cart*/
/*		$cart = Cart::model();
		$actionItems = $this->_getItems();
		$replace = array();
		foreach ($actionItems as $item) {
			$sCount = $cart->getCountCartItem($item['item_id'], $item['entity'], $this->_params['uid'], $this->_params['sid']);
			if ($sCount > 0) {
				$s = ''.
	'<a class="count' . $sCount . ' cart-action cart_add_slider add_cart list_cart add_cart_plus cartMini' . $item['item_id'] .' green_cart" data-action="add" data-entity="' . $item['entity'] . '" data-id="' . $item['item_id'] . '" data-quantity="1" href="javascript:;" style="width: 177px; " onclick="searchTargets(\'add_cart_index_slider\');">' .
		'<span style="width: auto;">' . sprintf(Yii::app()->ui->item('CARTNEW_IN_CART_BTN'), $sCount) . '</span>' .
	'</a>'.
				'';
			}
			else {
				$s = ''.
	'<a class="cart-action add_cart_plus cartMini' . $item['item_id'] . '" data-action="add" data-entity="' . $item['entity'] . '" data-id="' . $item['item_id'] . '" data-quantity="1" href="javascript:;" style="width: 135px;"  onclick="searchTargets(\'add_cart_index_slider\');">'.
		'<span>' . Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART') . '</span>'.
	'</a>'.
				'';
			}
			$replace['{CART_BUTTON_' . $item['entity'] . '_' . $item['item_id'] . '}'] = $s;
		}
		$txt = str_replace(array_keys($replace), $replace, $txt);*/
//		if (preg_match_all("/{CART_BUTTON_(\d+)_(\d+)}/ui", $txt, $m)) {
//			Debug::staticRun(array($m));
//		}
		return $txt;
	}

	private function _getItems() {
		if ($this->_items === null) {
			$sql = 'SELECT * FROM action_items where (`type` <> 3) Order By id limit 50';
			$this->_items = Yii::app()->db->createCommand($sql)->queryAll();
		}
		return $this->_items;
	}
}
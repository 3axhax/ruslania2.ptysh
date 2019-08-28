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
			//храним 5 минут
			if (filectime($file) < (time() - 5*60)) unlink($file);
		}

		if (!file_exists($file)) {
			$products = $this->_getProducts();
			shuffle($products);
			if (empty($products)) file_put_contents($file, '');
			else file_put_contents($file, $this->render('for_fileCache/action_items', array('actionItems'=>$products), true));
		}
		$txt = file_get_contents($file);
		echo $this->_replace($txt);
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
		$cart = Cart::model();
		$eIds = array();
		$replace = array();
		if (preg_match_all("/{PRICE_(\d+)_(\d+)}/ui", $txt, $m)) {
			foreach ($m[0] as $i=>$k) {
				$replace[$k] = '';
				$replace['{CART_BUTTON_' . $m[1][$i] . '_' . $m[2][$i] . '}'] = '';
				if (empty($eIds[$m[1][$i]])) $eIds[$m[1][$i]] = array();
				$eIds[$m[1][$i]][] = $m[2][$i];
			}
		}
		if (!empty($eIds)) {
			$prices = DiscountManager::getPrices($eIds);
			foreach ($eIds as $eid=>$ids) {
				foreach ($ids as $id) {
					$sCount = $cart->getCountCartItem($id, $eid, $this->_params['uid'], $this->_params['sid']);
					$replace['{CART_BUTTON_' . $eid . '_' . $id . '}'] = $this->render('for_fileCache/_in_cart_button', array('sCount' => $sCount, 'eid'=>$eid, 'id'=>$id), true);
					if (!empty($prices[$eid][$id])) {
						$replace['{PRICE_' . $eid . '_' . $id . '}'] = $this->render('for_fileCache/_price_action_items', array('product' => $prices[$eid][$id]), true);
					}
				}
			}
			$txt = str_replace(array_keys($replace), $replace, $txt);
		}
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
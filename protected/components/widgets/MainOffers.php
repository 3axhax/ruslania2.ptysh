<?php
/*Created by Кирилл (19.07.2019 22:25)*/
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
class MainOffers extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array(
		'uid' => 0,
		'sid' => '',
	);

	function init() {

	}

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		$file = Yii::getPathOfAlias('webroot') . '/protected/runtime/fileCache/mainoffers_' . Yii::app()->language . '.html.php';
		if (file_exists($file)) {
			//храним 1 час
			if (filectime($file) < (time() - 3600)) unlink($file);
		}

		if (!file_exists($file)) {
			$products = $this->_getProducts();
			if (empty($products)) file_put_contents($file, '');
			else file_put_contents($file, $this->render('for_fileCache/main_offers', array('groups'=>$products, 'widget'=>$this), true));
		}
		$txt = file_get_contents($file);
		echo $this->_replace($txt);
	}

	function viewItem($item) {
//		var_dump($item); echo '<br><br>';
		if ($item['entity'] == Entity::PERIODIC) $this->render('for_fileCache/_pereodic_main_offers', array('item' => $item));
		else $this->render('for_fileCache/_item_main_offers', array('item' => $item));
	}


	private function _getProducts() {
		$of = OfferItem::model();
		$groups = $of->forSliderAllData(Offer::INDEX_PAGE);
//		Debug::staticRun(array($groups));
		/**@var $o Offer*/
//		$o = Offer::model();
//		$groups = $o->GetItems(Offer::INDEX_PAGE);
		return $groups;
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
					$replace['{CART_BUTTON_' . $eid . '_' . $id . '}'] = $this->render('for_fileCache/_in_cart_button_offers', array('sCount' => $sCount, 'eid'=>$eid, 'id'=>$id), true);
					if (!empty($prices[$eid][$id])) {
						if ($eid == Entity::PERIODIC) {
							$replace['{PRICE_' . $eid . '_' . $id . '}'] = $this->render('for_fileCache/_price_main_offers_pereodic', array('item' => $prices[$eid][$id]), true);
						}
						else {
							$replace['{PRICE_' . $eid . '_' . $id . '}'] = $this->render('for_fileCache/_price_main_offers', array('item' => $prices[$eid][$id]), true);
						}
					}
				}
			}
			$txt = str_replace(array_keys($replace), $replace, $txt);
		}
		return $txt;
	}

}
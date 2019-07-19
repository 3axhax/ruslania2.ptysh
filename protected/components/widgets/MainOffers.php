<?php
/*Created by Кирилл (19.07.2019 22:25)*/
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
			else file_put_contents($file, $this->render('for_fileCache/action_items', array('actionItems'=>$products), true));
		}
		$txt = file_get_contents($file);
		echo $this->_replace($txt);
	}

	function viewItem() {
//		$entityStr = 'books';
		$entityStr = Entity::GetUrlKey(Entity::BOOKS);
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'], 'title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($category)))),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		$category = $this->_relocated['sheetmusic'][47];
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => 'sheetmusic', 'cid' => $category['id'], 'title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($category)))),
			'name'=>ProductHelper::GetTitle($category)
		);
		usort($rows, array($this, '_sort'));
//		$result = array();
//		foreach ($rows as $row) $result[] = $row;

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/salelist', array('entity' => $entityStr)),//Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/books_menu', array('rows'=>$rows));
	}


	private function _getProducts() {
		/**@var $o Offer*/
		$o = Offer::model();
		$groups = $o->GetItems(Offer::INDEX_PAGE);



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
		$sql = 'SELECT * FROM action_items where (`type` <> 3) Order By id limit 50';
		return Yii::app()->db->createCommand($sql)->queryAll();
	}
}
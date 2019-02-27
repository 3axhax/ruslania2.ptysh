<?php
/*Created by Кирилл (24.02.2019 10:15)*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class BuyController extends MyController {
	public $layout = 'without_menu';

	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('noregister'),
				'users' => array('*')
			),
//			array('allow',
//				'actions' => array('request'),
//				'users' => array('@')
//			),
//			array('deny',
//				'users' => array('*')
//			),
		);
	}

	function actionNoRegister() {
		/**@var $cart Cart*/
		$cart = Cart::model();
		$items = $cart->GetCart($this->uid, $this->sid);
		if (!count($items)) $this->redirect(Yii::app()->createUrl('cart'));
		if (!Yii::app()->user->isGuest) $this->redirect(Yii::app()->createUrl('me'));

		/**@var $order Order*/
		$order = Order::model();
		$total = array('itemsPrice'=>null, 'deliveryPrice'=>null, 'pricesValues'=>null, 'discountKeys'=>null, 'fullweight'=>null);
		list($total['itemsPrice'], $total['deliveryPrice'], $total['pricesValues'], $total['discountKeys'], $total['fullWeight']) = $order->getOrderPrice($this->uid, $this->sid, $items, null, 1, 0);
		$this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
		$this->breadcrumbs[] = 'Оформление заказа';
		$this->render('no_register', array('items'=>$items, 'total'=>$total, 'onlyPereodic'=>$this->_onlyPereodic($items)));
	}

	private function _onlyPereodic($items) {
		foreach ($items as $id => $item) {
			if ($item['entity'] != Entity::PERIODIC) return false;
		}
		return true;
	}

}
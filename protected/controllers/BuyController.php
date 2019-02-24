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
		$cart = new Cart;
		$s = $cart->GetCart($this->uid, $this->sid);
		if (!count($s)) $this->redirect(Yii::app()->createUrl('cart'));
		if (!Yii::app()->user->isGuest) $this->redirect(Yii::app()->createUrl('me'));

		$this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
		$this->breadcrumbs[] = 'Оформление заказа';
		$this->render('no_register');
	}

	private function _getCartInfo() {

		$cart = new Cart();

		$tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);

		$PH = new ProductHelper();

		$cart = CartController::actionGetAll(0);

		$cart = $cart['CartItems'];



		//echo '<pre>';
		//var_dump($tmp);
		//echo '</pre>';
		$cartInfo = '';
		$fullprice = 0;
		$fullweight = 0;
		$price = 0;
		$full_count = 0;
		$cartInfo['items'] = array();
		$t1 = false;
		$t2 = false;


		foreach ($cart as $item) {

			$withVat = $item['UseVAT'];

			$cartInfo['items'][$item['ID']]['title'] =
				$item['Title'];
			$cartInfo['items'][$item['ID']]['weight'] = $item['UnitWeight'];
			if ($item['Entity'] == 30) {

				//var_dump($item);

				if ($item['Price2Use'] == '1') { //фины
					$price = $item['PriceVATFin'];
				} else {
					$price = $item['PriceVATWorld'];
				}
			} else {



				$price = $item['PriceVAT'];
			}
			if (!$withVat) {

				if ($item['Entity'] == 30) {

					if ($item['Price2Use'] == '1') { //фины
						$price = $item['PriceVAT0Fin'];
					} else {
						$price = $item['PriceVAT0World'];
					}
				} else {
					$price = $item['PriceVAT0'];
				}

			}
			$fullweight += $item['UnitWeight'];

			$cartInfo['items'][$item['ID']]['month_count'] = $item['Quantity'];

			if ($item['Entity'] == 30) {
				$fullprice += $price * $item['Quantity'];
				$cartInfo['items'][$item['ID']]['price'] = $price;
				$cartInfo['items'][$item['ID']]['quantity'] = 1;
				$item['Quantity'] = 1;
			} else {
				$fullprice += $price * $item['Quantity'];
				$cartInfo['items'][$item['ID']]['quantity'] = $item['Quantity'];

				$cartInfo['items'][$item['ID']]['price'] = $price;
			}
			$cartInfo['items'][$item['ID']]['entity'] = $item['Entity'];
			$full_count += $item['Quantity'];
		}

		$cartInfo['fullInfo']['count'] = $full_count;
		$cartInfo['fullInfo']['cost'] = $fullprice;
		$cartInfo['fullInfo']['weight'] = $fullweight;




		$user = Yii::app()->user->GetModel();
		$address = new Address;
		$address->receiver_title_name = $user['title_name'];
		$address->receiver_last_name = $user['last_name'];
		$address->receiver_first_name = $user['first_name'];
		$address->receiver_middle_name = $user['middle_name'];
		$address->contact_email = $user['login'];
		$address->type = 2;
		$this->renderPartial('/site/address_form2', array('model' => $address,
			'mode' => 'new',
			'afterAjax' => 'addrInserted', 'cart' => $cartInfo));


		if ($fullprice < 5){
			$fullprice = 5.0;
		}

	}

}
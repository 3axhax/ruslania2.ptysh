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
				'actions' => array('noregister','loadstates','checkpromocode','deliveryinfo','orderadd','loadsp'),
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
		$this->render('no_register', array('items'=>$items, 'total'=>$total, 'onlyPereodic'=>$this->_onlyPereodic($items), 'existPereodic'=>$this->_existPereodic($items), 'countItems'=>$this->_getCountItems($total)));
	}

	function actionCheckPromocode() {
		$ret = array();
		if (Yii::app()->getRequest()->isPostRequest) {
			$dtype = (int) Yii::app()->getRequest()->getParam('dtype');
			$dMode = 0;
			if ($dtype <= 0) $dMode = 1;

			$aid = (int) Yii::app()->getRequest()->getParam('aid');
			$countryId = 0;
			$da = array();
			if ($aid > 0) {
				$a = new Address();
				$da = $a->GetAddress($this->uid, $aid);
				if (!empty($da['country'])) $countryId = $da['country'];
			}
			if (empty($countryId)) {
				$countryId = (int) Yii::app()->getRequest()->getParam('cid');
				$da = Country::GetCountryById($countryId);
				if (!empty($da)) $da['business_number1'] = Yii::app()->getRequest()->getParam('nvat');
			}
			$cart = new Cart();
			$items = $cart->GetCart($this->uid, $this->sid);
			list($ret['itemsPrice'], $ret['deliveryPrice'], $ret['pricesValues'], $ret['discountKeys'], $fullweight) = Order::model()->getOrderPrice($this->uid, $this->sid, $items, $da, $dMode, $dtype);
			$promocode = (string) Yii::app()->getRequest()->getParam('promocode');
			if ($promocode === '') {
				$ret['currency'] = Currency::ToSign(Yii::app()->currency);
				$ret['totalPrice'] = ProductHelper::FormatPrice($ret['itemsPrice'] + $ret['deliveryPrice'], false);
				$ret['briefly'] = '';
			}
			else {
				$ret['currency'] = Currency::ToSign(Yii::app()->currency);
				$ret['totalPrice'] = Promocodes::model()->getTotalPrice(Yii::app()->getRequest()->getParam('promocode'), $ret['itemsPrice'], $ret['deliveryPrice'], $ret['pricesValues'], $ret['discountKeys']);
				$ret['briefly'] = Promocodes::model()->briefly(Yii::app()->getRequest()->getParam('promocode'), true, $ret['itemsPrice']);
				if (!empty($ret['totalPrice'])) $ret['totalPrice'] = ProductHelper::FormatPrice($ret['totalPrice'], false);
			}
		}
		$this->ResponseJson($ret);
	}

	function actionDeliveryInfo() {
		$ret = array('tarif'=>array(), 'smartpost'=>'');
		if (Yii::app()->request->isPostRequest) {
			$aid = (int) Yii::app()->getRequest()->getParam('aid');
			$countryId = 0;
			if ($aid > 0) {
				$a = new Address();
				$da = $a->GetAddress($this->uid, $aid);
				if (!empty($da['country'])) $countryId = $da['country'];
			}
			if (empty($countryId)) $countryId = (int) Yii::app()->getRequest()->getParam('cid');

			$delivery = new PostCalculator();
			$ret['tarif'] = $delivery->GetRates(0, $this->uid, $this->sid, $countryId);
			$ret['smartpost'] = '';

			$deliveryPriceEur = Currency::ConvertToEUR($ret['tarif'][0]['value'], Yii::app()->currency);
			if (($deliveryPriceEur == 7)&&(in_array($countryId, array(62, 68)))) {
				$ret['tarif'][0]['description'] = YII::app()->ui->item('DELIVERY_ECONOMY_FINEST');
				$ret['tarif'][1]['description'] = YII::app()->ui->item('DELIVERY_PRIORITY_FINEST');
				$ret['tarif'][2]['description'] = YII::app()->ui->item('DELIVERY_EXPRESS_FINEST');
				$ret['smartpost'] = $this->renderPartial('smartpost', array('countryId'=>$countryId), true);
			}
			elseif ($deliveryPriceEur < 15) {
				$ret['tarif'][0]['description'] = YII::app()->ui->item('DELIVERY_ECONOMY_OTHER');
				$ret['tarif'][1]['description'] = YII::app()->ui->item('DELIVERY_PRIORITY_OTHER');
				$ret['tarif'][2]['description'] = YII::app()->ui->item('DELIVERY_EXPRESS_OTHER');
			}
			else {
				$ret['tarif'][0]['description'] = YII::app()->ui->item('DELIVERY_ECONOMY_OTHER_YES');
				$ret['tarif'][1]['description'] = YII::app()->ui->item('DELIVERY_PRIORITY_OTHER_YES');
				$ret['tarif'][2]['description'] = YII::app()->ui->item('DELIVERY_EXPRESS_OTHER_YES');
			}
		}
		$this->ResponseJson($ret);
	}

	function actionOrderAdd() {
		$ret = array();
		$ret['errors'] = $this->_checkForm();
		$this->ResponseJson($ret);
	}

	public function actionLoadStates() {
		$states = array();
		if (Yii::app()->request->isPostRequest) $states = Country::GetStatesList((int) Yii::app()->getRequest()->getParam('cid'));
		$this->ResponseJson($states);
	}

	public function actionLoadsp() {
		if (Yii::app()->request->isPostRequest) {
			$points = Cart::model()->cart_getpoints_smartpost(addslashes(htmlspecialchars(Yii::app()->getRequest()->getParam('ind'))), addslashes(htmlspecialchars(Yii::app()->getRequest()->getParam('country'))));
			$this->renderPartial('smartpost_points', array('points' => $points));
		}
	}

	private function _onlyPereodic($items) {
		foreach ($items as $id => $item) {
			if ($item['entity'] != Entity::PERIODIC) return false;
		}
		return true;
	}

	private function _existPereodic($items) {
		foreach ($items as $id => $item) {
			if ($item['entity'] == Entity::PERIODIC) return true;
		}
		return false;
	}

	private function _getCountItems($total) {
		$quantity = 0;
		foreach ($total['discountKeys'] as $k=>$item) {
			if (mb_strpos($k, '30_', null, 'utf-8') === 0) $quantity++;
			else $quantity += $item['quantity'];
		}
		return $quantity;
	}

	private function _checkForm() {
		$cart = Cart::model();
		$items = $cart->GetCart($this->uid, $this->sid);
		$errors = array();
		if (!Yii::app()->getRequest()->getParam('confirm')) $errors['confirm'] = Yii::app()->ui->item('CHECKBOX_TERMS_OF_USE');
		if (Yii::app()->user->isGuest) {
			$requireFields = $this->_requireFieldsAddress($items, 'Reg');
			if (!empty($requireFields)) {
				/**@var $addressModel Address*/
				$addressModel = new Address('edit');
				$addressModel->setAttributes(Yii::app()->getRequest()->getParam('Reg'), false);
				if (!$addressModel->validate()) {
					$addrErrors = $addressModel->getErrors();
					foreach ($requireFields as $field) {
						if (!empty($addrErrors[$field])) {
							$errors['Reg_' . $field] = $addrErrors[$field];
						}
					}
				}
			}
		}
		else {

		}
		return $errors;
	}

	private function _requireFieldsAddress($items, $formName) {
		$requireFields = array('business_title', 'receiver_last_name', 'receiver_first_name', 'country', 'city', 'postindex', 'streetaddress', 'contact_email', 'contact_phone');
		$requireReg = array_flip($requireFields);
		$regFields = (array) Yii::app()->getRequest()->getParam($formName);
		if (!empty($regFields)&&($regFields['type'] == 2)) {
			unset($requireReg['business_title']);
			if (Yii::app()->getRequest()->getParam('check_addressa')&&!$this->_existPereodic($items)) {
				unset($requireReg['country'], $requireReg['city'], $requireReg['postindex'], $requireReg['streetaddress']);
			}
		}
		return array_flip($requireReg);
	}

}
<?php
/*Created by Кирилл (24.02.2019 10:15)*/
class BuyController extends MyController {
	public $layout = 'without_menu';

	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('noregister','loadstates','checkpromocode','deliveryinfo','orderadd','loadsp','orderok','newaddr'),
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

	function actionDoOrder() {
		/**@var $cart Cart*/
		$cart = Cart::model();
		$items = $cart->GetCart($this->uid, $this->sid);
		if (!count($items)) {
			$lastOrderId = (int)Yii::app()->getRequest()->cookies['lastOrderId']->value;
			if ($lastOrderId > 0) $this->redirect(Yii::app()->createUrl('payment/cancel', array('oid' => $lastOrderId, 'tid'=>0)));
			$this->redirect(Yii::app()->createUrl('cart/view'));
		}

		/**@var $order Order*/
		$order = Order::model();
		$total = array('itemsPrice'=>null, 'deliveryPrice'=>null, 'pricesValues'=>null, 'discountKeys'=>null, 'fullweight'=>null);
		list($total['itemsPrice'], $total['deliveryPrice'], $total['pricesValues'], $total['discountKeys'], $total['fullWeight']) = $order->getOrderPrice($this->uid, $this->sid, $items, null, 1, 0);
		$this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
		$this->breadcrumbs[] = 'Оформление заказа';
		if (Yii::app()->user->isGuest) {
			$userInfo = array();
			if (Yii::app()->getRequest()->getParam('useSocial')) $userInfo = $this->_getUserInfoBySocial();
			$this->render('no_register', array(
				'items'=>$items,
				'total'=>$total,
				'onlyPereodic'=>$this->_onlyPereodic($items),
				'existPereodic'=>$this->_existPereodic($items),
				'countItems'=>$this->_getCountItems($total),
				'userInfo'=>$userInfo,
			));
		}
		else {
			$this->render('do_order', array('items'=>$items, 'total'=>$total, 'onlyPereodic'=>$this->_onlyPereodic($items), 'existPereodic'=>$this->_existPereodic($items), 'countItems'=>$this->_getCountItems($total)));
		}
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
			$ret['deliveryName'] = Delivery::ToString($dtype);
			if (empty($ret['deliveryPrice'])&&$this->_onlyPereodic($items)) {
				$ret['deliveryName'] = Delivery::ToString(0);
			}
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

	function actionNewAddr() {
		$ret = array('errors'=>array());
		if (Yii::app()->getRequest()->isPostRequest) {
			$formName = (string) Yii::app()->getRequest()->getParam('alias');
			$requireFields = $this->_requireFieldsAddress(array(), $formName);
			if (!empty($requireFields)) {
				/**@var $addressModel Address*/
				$addressModel = new Address('edit');
				$addressModel->setAttributes(Yii::app()->getRequest()->getParam($formName), false);
				if (!$addressModel->validate()) {
					$addrErrors = $addressModel->getErrors();
					foreach ($requireFields as $field) {
						if (!empty($addrErrors[$field])) {
							$ret['errors'][$formName . '_' . $field] = $addrErrors[$field];
						}
					}
				}
			}
			else $ret['errors'][] = 'error';
			if (empty($ret['errors'])) {
				$addressModel = new Address('new');
				$addressModel->setAttributes(Yii::app()->getRequest()->getParam($formName), false);
				$aid = $addressModel->InsertNew($this->uid, 0);
				$addr = $addressModel->GetAddress($this->uid, $aid);
				$ret['address'] = array('id'=>$aid, 'name'=>CommonHelper::FormatAddress($addr));
			}
		}
		$this->ResponseJson($ret);
	}

	function actionOrderAdd() {
		$ret = array();
		if (Yii::app()->getRequest()->isPostRequest) {
			$ret['errors'] = $this->_checkForm();
			if (empty($ret['errors'])) {
				$aid = $bid = 0;
				$cart = Cart::model();
				$items = $cart->GetCart($this->uid, $this->sid);
				if (Yii::app()->user->isGuest) {
					if ($userId = $this->_regUser()) {
						if (!Yii::app()->getRequest()->getParam('check_addressa')||$this->_existPereodic($items)) {
							//не будет забирать в магазине или есть подписка
							$addressModel = new Address('new');
							$addressModel->setAttributes(Yii::app()->getRequest()->getParam('Reg'), false);
							$aid = $addressModel->InsertNew($userId, 1);
						}
						if (!Yii::app()->getRequest()->getParam('addr_buyer')) {
							$addressModel = new Address('new');
							$addressModel->setAttributes(Yii::app()->getRequest()->getParam('Address'), false);
							$bid = $addressModel->InsertNew($userId, 1);
						}
					}
				}
				else {
					$aid = Yii::app()->getRequest()->getParam('delivery_address_id');
					$bid = 0;
					if (!Yii::app()->getRequest()->getParam('addr_buyer')) $bid = Yii::app()->getRequest()->getParam('billing_address_id');
					$userId = $this->uid;
				}
				$DeliveryMode = 0;
				if ((int) Yii::app()->getRequest()->getParam('dtype') === 0) $DeliveryMode = 1;

				$orderData = array(
					'DeliveryAddressID' => $aid,
					'DeliveryTypeID' => Yii::app()->getRequest()->getParam('dtype'),
					'DeliveryMode' => $DeliveryMode,
					'CurrencyID' => Yii::app()->currency,
					'BillingAddressID' => $bid,
					'Notes' => Yii::app()->getRequest()->getParam('notes'),
					'Mandate' => 0,
					'SmartpostAddress' => Yii::app()->getRequest()->getParam('pickpoint_address')
				);
				$order = new OrderForm($this->sid);
				$order->setAttributes($orderData);

				$orderItems = array();
				foreach ($items as $item) {
					if (ProductHelper::IsAvailableForOrder($item))
						$orderItems[] = $item;
				}
				$o = new Order;
				$o->setPromocode(Yii::app()->getRequest()->getParam('promocode'));
				$id = $o->CreateNewOrder($userId, $this->sid, $order, $orderItems, Yii::app()->getRequest()->getParam('ptype'));
				$this->_mailOrder($id, $cart->BeautifyCart($items, $this->uid));

				Yii::app()->user->setFlash('order', Yii::app()->ui->item('ORDER_MSG_DONE'));
				$cookieOrderId = new CHttpCookie('lastOrderId', $id);
				$cookieOrderId->expire = time() + 300;
				Yii::app()->getRequest()->cookies['lastOrderId'] = $cookieOrderId;
				$orderBaseData = $o->GetOrder($id);
				switch ((int) Yii::app()->getRequest()->getParam('ptype')) {
					case 8: $ret['form'] = $this->widget('PayPalPayment', array('order' => $orderBaseData, 'tpl'=>'paypal_without_button'), true); break;
					case 25: $ret['form'] = $this->widget('PayTrailWidget', array('order' => $orderBaseData, 'tpl'=>'paytrail_without_button'), true); break;
					case 27:
						$ret['idOrder'] = $orderBaseData['id'];
						$ret['urls'] = array(
							'charges' => Yii::app()->createUrl('site/charges'),
							'accept' => Yii::app()->createUrl('payment/accept', array('oid'=>$orderBaseData['id'], 'tid' => $orderBaseData['payment_type_id'])),
							'cancel' => Yii::app()->createUrl('payment/cancel', array('oid'=>$orderBaseData['id'], 'tid' => $orderBaseData['payment_type_id'])),
						);
						$ret['paymentRequest'] = array(
							'countryCode' => mb_strtoupper(Yii::app()->getLanguage()),
                            'currencyCode'=>Currency::ToStr($orderBaseData['currency_id']),
							'total' =>array(
								'label' => Yii::app()->ui->item('ORDER_PAYMENT') . ' ' . $orderBaseData['id'],
								'amount' => $orderBaseData['full_price'],
							),
						);
						break;
					default: $ret['url'] = Yii::app()->createUrl('buy/orderok') . '?id=' . $id; break;
				}
			}
		}
		$this->ResponseJson($ret);
	}

	function actionOrderOk() {
		$id = (int) Yii::app()->getRequest()->getParam('id');
		$o = new Order;
		if(!$o->isMyOrder($this->uid,$id)) throw new CHttpException(404);

		$order = $o->GetOrder($id);
		$this->render('order_ok', array('order' => $order));
	}

	function actionGetCountry() {
		$ret = array();
		if (Yii::app()->request->isPostRequest) {
			$country = Country::model()->findByPk(Yii::app()->getRequest()->getParam('id_country'));
			$ret = $country->getAttributes();
		}
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

	private function _regUser() {
		$cart = new Cart();
		$tmp = $cart->GetCart($this->uid, $this->sid);
		$beautyItems = $cart->BeautifyCart($tmp, $this->uid);

		$m20n = $m10n = $m60n = $m22n = $m15n = $m24n = $m40n = 0;
		$razds = array();
		foreach ($beautyItems as $p) {
			$mn = 'm' . $p['Entity'] . 'n';
			$$mn = 1;
			$razds[$p['Entity']] = Yii::app()->ui->item(Entity::GetEntitiesList()[$p['Entity']]['uikey']);
		}
		$psw = rand(1000000, 9999999) . 'sS';
		$userModel = new User('register');
		$userModel->setAttribute('pwd', $psw);

		foreach (Yii::app()->getRequest()->getParam('Reg') as $k=>$v) {
			switch ($k) {
				case 'contact_email': $userModel->setAttribute('login', $v); break;
				case 'receiver_first_name': $userModel->setAttribute('first_name', $v); break;
				case 'receiver_last_name': $userModel->setAttribute('last_name', $v); break;
				case 'receiver_middle_name': $userModel->setAttribute('middle_name', $v); break;
			}
		}
		$userID = 0;
		if ($userModel->RegisterNew(Language::ConvertToInt(Yii::app()->getLanguage()), Yii::app()->currency, $m20n, $m10n, $m60n, $m22n, $m15n, $m24n, $m40n)) {
			$email = $userModel->getAttribute('login');
			$identity = new RuslaniaUserIdentity($email, $userModel->getAttribute('pwd'));
			if ($identity->authenticate()) {
				Yii::app()->user->login($identity, Yii::app()->params['LoginDuration']);
				$cart->UpdateCartToUid($this->sid, $identity->getId());
				//echo $this->sid;
				$message = new YiiMailMessage(Yii::app()->ui->item('A_REGISTER') . '. Ruslania.com');
				$message->view = 'reg_' . (in_array(Yii::app()->language, array('ru', 'fi', 'en')) ? Yii::app()->language : 'en');
				$message->setBody(array(
					'user' => User::model()->findByPk(Yii::app()->user->id)->attributes,
					'razds' => $razds,
				), 'text/html');
				$message->addTo($email);
				$message->from = 'noreply@ruslania.com';
				$mailResult = Yii::app()->mail->send($message);
				file_put_contents(Yii::getPathOfAlias('webroot') . '/test/mail.log', implode("\t",
					array(
						date('d.m.Y H:i:s'),
						$email,
						serialize($mailResult),
						$message->view,
						serialize($message->from),
					)
				) . "\n", FILE_APPEND);
			}
			$userID = $identity->getId();
			if (($userSocialId = (int) Yii::app()->getRequest()->getParam('userSocialId')) > 0) {
				UsersSocials::model()->updateByPk($userSocialId, array('id_user'=>$userID));
			}
		}
		return $userID;
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
				if (empty($errors)) {
					if(User::model()->checkLogin($addressModel->getAttribute('contact_email'))) {
						$errors['forgot_button'] = $this->renderPartial('forgot_button', array('email' => $addressModel->getAttribute('contact_email')), true);//Yii::app()->ui->item('CARTNEW_ERROR_MAIL_FIND_OK');
					}
				}
			}
			else $errors['Reg'] = 'error';
			if (!Yii::app()->getRequest()->getParam('addr_buyer')) {
				$requireFields = $this->_requireFieldsAddress($items, 'Address');
				if (!empty($requireFields)) {
					/**@var $addressModel Address*/
					$addressModel = new Address('edit');
					$addressModel->setAttributes(Yii::app()->getRequest()->getParam('Address'), false);
					if (!$addressModel->validate()) {
						$addrErrors = $addressModel->getErrors();
						foreach ($requireFields as $field) {
							if (!empty($addrErrors[$field])) {
								$errors['Address_' . $field] = $addrErrors[$field];
							}
						}
					}
				}
				else $errors['Address'] = 'error';
			}
		}
		else {
			$aid = Yii::app()->getRequest()->getParam('delivery_address_id');
			$bid = Yii::app()->getRequest()->getParam('billing_address_id');
			if (empty($aid)&&$this->_existPereodic($items)) $errors['delivery_address_id'] = Yii::app()->ui->item('CARTNEW_ERROR_SELECT_ADDR_DELIVERY');
			if (!Yii::app()->getRequest()->getParam('addr_buyer')&&empty($bid)) {
				$errors['billing_address_id'] = Yii::app()->ui->item('CARTNEW_ERROR_SELECT_ADDR_BUYER');
			}
		}
		return $errors;
	}

	private function _requireFieldsAddress($items, $formName) {
		$requireFields = array('business_title', 'receiver_last_name', 'receiver_first_name', 'country', 'city', 'postindex', 'streetaddress', 'contact_phone');
		if (!empty($items)) $requireFields[] = 'contact_email';
		$requireReg = array_flip($requireFields);
		$regFields = (array) Yii::app()->getRequest()->getParam($formName);
		if (!empty($regFields)&&($regFields['type'] == 2)) {
			unset($requireReg['business_title']);
			if (!empty($items)&&Yii::app()->getRequest()->getParam('check_addressa')&&!$this->_existPereodic($items)) {
				unset($requireReg['country'], $requireReg['city'], $requireReg['postindex'], $requireReg['streetaddress']);
			}
		}
		return array_flip($requireReg);
	}

	private function _mailOrder($id, $beautyItems) {
		$user = User::model()->findByPk(Yii::app()->user->id);
		$order = Order::model()->findByPk($id);
		$message = new YiiMailMessage(sprintf(Yii::app()->ui->item('MSG_ORDER_PRINT_PAGE_TITLE'), $id));
		$message->view = 'thanks_for_order';
		$message->setBody(array(
			'items' => $beautyItems,
			'user' => $user->attributes,
			'order' => $order->attributes,
		), 'text/html');
		$message->addTo($user['login']);
		$message->from = 'noreply@ruslania.com';
		@Yii::app()->mail->send($message);

	}

	private function _getUserInfoBySocial() {
		$ret = array();
		if (isset(Yii::app()->session['user_social'])) {
			$ret = UsersSocials::model()->getUserInfoForAddressForm(Yii::app()->session['user_social']);
		}
		return $ret;
	}
}
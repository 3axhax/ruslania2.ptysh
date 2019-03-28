<?php


class CartController extends MyController {
    public function accessRules() {
        return array(array('allow',
            'actions' => array('view', 'variants', 'doorder', 'doorderjson', 'dorequest', 'register', 'getall', 'getcount', 'add', 'mark', 'noregister', 'result', 'applepay', 'valid', 'loadsp', 'loadsp2', 'orderPay', 'addaddress','addaddress2', 'editaddr', 'getaddress','loadstatesfinal', 'editaddrselect',
                'changequantity', 'remove', 'getdeliveryinfo', 'getdeliveryinfo2', 'getcodecity', 'getcostizmena','loadstates', 'certificatePay', 'checkpromocode','loadheader'),
            'users' => array('*')),
            array('allow', 'actions' => array('request'),
                'users' => array('@')),
            array('deny',
                'users' => array('*')));
    }
	
	public function actionEditAddrSelect() {
		
		//if ( $_POST['val'] ) {
		
		if ($_POST['tip'] == '1') {
			
			$field = 'delivery_address_id';
			
		} else {
			$field = 'billing_address_id';
		}
		
		
		$sql = 'UPDATE users_orders SET '.$field.'=:text, must_upgrade = "1" WHERE id=:id'; Yii::app()->db->createCommand($sql)->execute(array(':text' => (int) $_POST['val'], ':id' => $_POST['id']));
		
	
		
		$json = array();
		
		$addr = Address::GetAddress($this->uid, $_POST['val']);
		
		if ($addr['streetaddress'] == '' OR $addr['postindex'] == '' OR $addr['city'] == '') { $json['hide_btn_next'] = '1'; } else { $json['hide_btn_next'] = '0'; }
		
		
		$addrs = CommonHelper::FormatAddress($addr);
		
		if ($addrs) {
			
			$json['addr_full'] = $addrs;
			
		}
		
		$this->ResponseJson($json);
		
		
	}
	
	public function actionEditAddr() {
		
		$sql = 'UPDATE users_orders SET must_upgrade = "1" WHERE id=:id'; Yii::app()->db->createCommand($sql)->execute(array(':id' => $_POST['orderId']));
		
		
		$sql = 'UPDATE user_address SET '.$_POST['name'].'=:text WHERE id=:id'; Yii::app()->db->createCommand($sql)->execute(array(':text' => $_POST['val'], ':id' => $_POST['id']));
		
		
		$json = array();
		
		$addr = Address::GetAddress($this->uid, $_POST['id']);
		
		//var_dump($addr);
		
		if ($addr['streetaddress'] == '' OR $addr['postindex'] == '' OR $addr['city'] == '') { $json['hide_btn_next'] = '1'; } else { $json['hide_btn_next'] = '0'; }
		
		
		$addrs = CommonHelper::FormatAddress($addr);
		
		if ($addrs) {
			
			$json['addr_full'] = $addrs;
			
		}
		
		$this->ResponseJson($json);
		
/*
        функцию переделал, параметры
        name - поле, которое нужно обновить,
        val - значение,
        tip - плательщик (order_addr_buyer) или получатель(deliveryAddress),
        orderId - номер заказа

        1. по номеру заказа нахожу ид адреса плательщика и ид адреса получателя
        2. если ид адресов одинаковые, то добавляю новый адрес (копия текущего, только поле изменено) и сохраняю его ид в заказ
        3. иначе сохранияю значение в нужном адресе
*/
        // $orderModel = Order::model();
        // $order = $orderModel->findByPk(Yii::app()->getRequest()->getParam('orderId'));
        // $orderData = $order->getAttributes();
        // $json = array();

        // $fieldName = Yii::app()->getRequest()->getParam('name');
        // $fieldValue = trim((string)Yii::app()->getRequest()->getParam('val'));

        // $fieldAddr = '';
        // switch ((string)Yii::app()->getRequest()->getParam('tip')) {
            // case 'order_addr_buyer': $fieldAddr = 'billing_address_id'; break;
            // case 'deliveryAddress': $fieldAddr = 'delivery_address_id'; break;
        // }

        // $addressModel = Address::model();
        // $address = $addressModel->findByPk($orderData[$fieldAddr]);
        // $addressData = $address->getAttributes();

        // if (
            // ((int)$this->uid === (int)$orderData['uid'])
            // &&!empty($orderData[$fieldAddr])
            // &&isset($addressData[$fieldName])
            // &&(trim($addressData[$fieldName]) <> $fieldValue)
        // ) {
            // if (($orderData['delivery_address_id'] == $orderData['billing_address_id'])/*&&($fieldName == 'billing_address_id')*/) {
                // $address->id = null;
                // $address->$fieldName = $fieldValue;
                // $address->setIsNewRecord(true);
                // $address->insert();
                // $addrId = $address->id;
                // $addressModel->addAddresses($this->uid, $addrId, false);
                // $order->$fieldAddr = $addrId;
                // $order->save();
            // }
            // else {
                // $address->$fieldName = $fieldValue;
                // $address->update();
            // }
        // }
        // $addr = $addressModel->GetAddress($this->uid, $address->id);
        // if (($addr['streetaddress'] == '')||($addr['postindex'] == '')||($addr['city'] == '')) $json['hide_btn_next'] = '1';
        // else $json['hide_btn_next'] = '0';
        // $addrs = CommonHelper::FormatAddress($addr);
        // if ($addrs) $json['addr_full'] = $addrs;

        // $this->ResponseJson($json);
	}
	
	
    public function actionLoadStates() {
        $states = Country::GetStatesList((int) $_POST['id']);
        $this->renderPartial('load_states', array('items' => $states));
    }

    function actionLoadHeader() {
        $referer = Yii::app()->getRequest()->getUrlReferrer();
        $request = new MyRefererRequest();
        $request->setFreePath($referer);
        $refererRoute = Yii::app()->getUrlManager()->parseUrl($request);
        $this->renderPartial('header_cart', array('refererRoute'=>$refererRoute));
    }

    public function actionAddAddress() {
        if (Yii::app()->request->isPostRequest) {
            $p = $_POST;
            $type = htmlspecialchars($p['Address']['type']);
            $business_title = htmlspecialchars($p['Address']['business_title']);
            $business_number1 = htmlspecialchars($p['Address']['business_number1']);
            $userID = $this->uid;
            $titul = htmlspecialchars($p['Address']['receiver_title_name']);
            $fam = htmlspecialchars($p['Address']['receiver_last_name']);
            $name = htmlspecialchars($p['Address']['receiver_first_name']);
            $otch = htmlspecialchars($p['Address']['receiver_middle_name']);
            $country = htmlspecialchars($p['Address']['country']);
            $stat = htmlspecialchars($p['Address']['state_id']);
            $city = htmlspecialchars($p['Address']['city']);
            $post_index = htmlspecialchars($p['Address']['postindex']);
            $address = htmlspecialchars($p['Address']['streetaddress']);
            $email = htmlspecialchars($p['Address']['contact_email']);
            $phone = htmlspecialchars($p['Address']['contact_phone']);
            $comment = htmlspecialchars($p['Address']['notes']);
            $sql = 'INSERT INTO user_address (`type`,`business_title`,`business_number1`,`receiver_title_name`,`receiver_first_name`,`receiver_middle_name`,`receiver_last_name`, `country`,`state_id`,`city`,`postindex`,`streetaddress`,`contact_email`,`contact_phone`,`notes`) VALUES '
                . '("' . $type . '", "' . $business_title . '", "' . $business_number1 . '", "' . $titul . '", "' . $name . '", "' . $otch . '", "' . $fam . '", "' . $country . '", "' . $stat . '", "' . $city . '", "' . $post_index . '", "' . $address . '", "' . $email . '", "' . $phone . '", "' . $comment . '")';
            $ret = Yii::app()->db->createCommand($sql)->execute();
            $idAddr2 = Yii::app()->db->getLastInsertID();
            $sql = 'INSERT INTO users_addresses (uid,address_id,if_default) VALUES ("' . $userID . '", "' . $idAddr2 . '", "0")';
            $ret = Yii::app()->db->createCommand($sql)->execute();
            $items = Address::GetAddresses($this->uid);
            $this->renderPartial('address_get_list', array('items' => $items, 'ida'=>$idAddr2));
        }
    }
	
	public function actionAddAddress2() {
        if (Yii::app()->request->isPostRequest) {
            $p = $_POST;
			
			$type = htmlspecialchars($p['Address2']['type']);
            $business_title = htmlspecialchars($p['Address2']['business_title']);
            $business_number1 = htmlspecialchars($p['Address2']['business_number1']);
            $userID = $this->uid;
            $titul = htmlspecialchars($p['Address2']['receiver_title_name']);
            $fam = htmlspecialchars($p['Address2']['receiver_last_name']);
            $name = htmlspecialchars($p['Address2']['receiver_first_name']);
            $otch = htmlspecialchars($p['Address2']['receiver_middle_name']);
            $country = htmlspecialchars($p['Address2']['country']);
            $stat = htmlspecialchars($p['Address2']['state_id']);
            $city = htmlspecialchars($p['Address2']['city']);
            $post_index = htmlspecialchars($p['Address2']['postindex']);
            $address = htmlspecialchars($p['Address2']['streetaddress']);
            $email = htmlspecialchars($p['Address2']['contact_email']);
            $phone = htmlspecialchars($p['Address2']['contact_phone']);
            $comment = ' ';
            $sql = 'INSERT INTO user_address (`type`,`business_title`,`business_number1`,`receiver_title_name`,`receiver_first_name`,`receiver_middle_name`,`receiver_last_name`, `country`,`state_id`,`city`,`postindex`,`streetaddress`,`contact_email`,`contact_phone`,`notes`) VALUES '
                . '("' . $type . '", "' . $business_title . '", "' . $business_number1 . '", "' . $titul . '", "' . $name . '", "' . $otch . '", "' . $fam . '", "' . $country . '", "' . (int) $stat . '", "' . $city . '", "' . $post_index . '", "' . $address . '", "' . $email . '", "' . $phone . '", "' . $comment . '")';
            $ret = Yii::app()->db->createCommand($sql)->execute();
            $idAddr2 = Yii::app()->db->getLastInsertID();
            $sql = 'INSERT INTO users_addresses (uid,address_id,if_default) VALUES ("' . $userID . '", "' . $idAddr2 . '", "0")';
            $ret = Yii::app()->db->createCommand($sql)->execute();
            $items = Address::GetAddresses($this->uid);
            $this->renderPartial('address_get_list', array('items' => $items, 'ida'=>$idAddr2));
        }
    }
	
    public function actionGetAddress() {
        if (Yii::app()->request->isPostRequest) {
            $id_addr = $_POST['id_address'];
            $addr = Address::GetAddress($this->uid, $id_addr);
            echo $addr['country'];
        }
    }
    public function actionGetCodeCity() {
        if (Yii::app()->request->isPostRequest) {
            $s = $_POST['id_country'];
            $sql = 'SELECT * FROM `country_list` WHERE id = ' . $s;
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            echo $rows[0]['phone_code'];
        }
    }
    public function actionGetDeliveryInfo() {
        if (Yii::app()->request->isPostRequest) {
            $delivery = new PostCalculator();
            $id_country = $_POST['id_country'];
            $res = Country::GetCountryById($id_country);
            $r = $delivery->GetRates2($id_country, $this->uid, $this->sid);
            $this->renderPartial('delivery', array('items' => $r));
        }
    }
    public function actionGetDeliveryInfo2() {
        if (Yii::app()->request->isPostRequest||isset($_GET['ha'])) {
            $delivery = new PostCalculator();
            $id_country = Yii::app()->getRequest()->getParam('id_country');
            $res = Country::GetCountryById($id_country);
            $r = $delivery->GetRates2($id_country, $this->uid, $this->sid);
            $this->renderPartial('delivery2', array('items' => $r));
        }
    }
    public function actionLoadsp2() {
        if (Yii::app()->request->isPostRequest) {
            $points = Cart::cart_getpoints_smartpost(addslashes(htmlspecialchars($_POST['ind'])), addslashes(htmlspecialchars($_POST['country'])));
            $this->renderPartial('points', array('points' => $points));
        }
    }
    public function actionLoadsp() {
        $s = addslashes(htmlspecialchars($_POST['s']));
        $sql = 'SELECT * FROM `SmartPost_address` WHERE postindex LIKE "%' . $s . '%"';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        foreach ($rows as $item) {
            echo '<div class="item" onclick="select_row($(this))">' . $item['postindex'] . ', ' . $item['name'] . ', ', $item['number'] . '</div>';
        }
    }
    public function actionApplepay() {
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
        $this->breadcrumbs[] = 'Оплата заказа через ApplePay';
        $this->render('applepay');
    }
    // Просмотр корзины
    public function actionView() {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Cart::CreateUrl('cart/view', $lang);
			}
		}
		
		
        $data = $this->GetFormRequest();
        if (!empty($data)) {
            foreach ($data as $igetalld => $value) {
                $this->actionAdd(array_values($value));
            }
        }
        $this->breadcrumbs[] = Yii::app()->ui->item('A_SHOPCART');
        $data = $this->actionGetAll(false);
        $emptyItem = array(
            "Entity" => '',
            "ID" => '',
            "Title" => '',
            "PriceVAT" => '',
            "PriceVATStr" => '',
            "PriceVAT0" => '',
            "PriceVAT0Str" => '',
            "PriceVATFin" => '',
            "PriceVATFinStr" => '',
            "PriceVAT0Fin" => '',
            "PriceVAT0FinStr" => '',
            "PriceVATWorld" => '',
            "PriceVATWorldStr" => '',
            "PriceVAT0World" => '',
            "PriceVAT0WorldStr" => '',
            "Price2Use" => '',
            "UseVAT" => '',
            "Url" => '',
            "Quantity" => '',
            "UnitWeight" => '',
            "IsAvailable" => '',
            "Availability" => '',
            "AvailablityText" => '',
            "DiscountPercent" => '',
            "PriceOriginal" => '',
            "ReadyVAT" => '',
            "ReadyVAT0" => '',
            "Rate" => '',
            "VAT" => '',
            "InfoField" => '',
        );
        require_once Yii::app()->getBasePath() . '/iterators/Cart.php';
        if (empty($data['CartItems'])) {
            $data['isCart'] = false; //признак - козина пуста
            $data['CartItems'] = new IteratorsCart(array($emptyItem));
        } else {
            $data['isCart'] = true;
            $data['CartItems'] = new IteratorsCart($data['CartItems']);
        }
        if (empty($data['EndedItems'])) {
            $data['isEnded'] = false;
            $data['EndedItems'] = new IteratorsCart(array($emptyItem));
        } else {
            $data['isEnded'] = true;
            $data['EndedItems'] = new IteratorsCart($data['EndedItems']);
        }

        $hrefContinueShopping = getenv('HTTP_REFERER');
        if (empty($hrefContinueShopping)) $hrefContinueShopping = Yii::app()->createUrl('site/index');
        else {
            $request = new MyRefererRequest();
            $request->setFreePath($hrefContinueShopping);

            $routeReferer = Yii::app()->getUrlManager()->parseUrl($request);
            $routeReferer = explode('/', $routeReferer);
            if (in_array($routeReferer[0], array('cart', 'buy', 'payments')))
                $hrefContinueShopping = Yii::app()->createUrl('site/index');
        }
        $data['hrefContinueShopping'] = $hrefContinueShopping;

        $this->render('view', $data);
    }
    public function actionDoOrderJSON() {
        if (Yii::app()->user->isGuest) {
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            $this->render('login_or_register');
            return;
        }
        $cart = new Cart;
        $cartItems = array();
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);
        foreach ($tmp as $item) {
//            $item['Title'] = str_replace('"', '\\"', $item['Title']); // из-за того, что это идет в JSON в виде строки в do_order.php и ломает парсеру жизнь
//            $item['Title'] = str_replace("'", '\\\\\'', $item['Title']);
            if ($item['IsAvailable'])
                $cartItems[] = $item;
        }
        $this->ResponseJson(array('Items' => $cartItems));
    }
    public function actionRegister() {
        if (Yii::app()->user->isGuest) {
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            $this->render('register_form', array());
        } else {
            $this->redirect('/cart/doorder/');
        }
    }
    function decline_goods($num) {
        
		$ui = new RuslaniaUI;
		
		$count = $num;
        $num = $num % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1: {
                    return $count . ' '.$ui->item('CARTNEW_PRODUCTS_TITLE2');
                }

            case 2: case 3: case 4: {
                    return $count . ' '.$ui->item('CARTNEW_PRODUCTS_TITLE1');
                }

            default: {
                    return $count . ' '.$ui->item('CARTNEW_PRODUCTS_TITLE3');
                }
        }
    }
    function actionGetCostIzmena() {
		
		$ui = new RuslaniaUI();
		
		$cart = $this->actionGetAll(0);
		
		$cart = $cart['CartItems'];
		
		//exit();
		
		if ($_POST['id_addr']) {
			
			$country  =Address::GetAddress($this->uid,$_POST['id_addr']);
			
			$_POST['bnum'] = $country['business_number1'];
			$_POST['atype'] = $country['type'];
			
			$country = Country::GetCountryById($country['country']);
			
		} else {
		
			$country = Country::GetCountryById($_POST['id_country']);
		
		}
		
		
		
		if ($_POST['atype'] == '2') {
			$_POST['bnum'] = '';
		}
		
		//var_dump($_POST);
		
		$arrCountry = array (
		
			'code' => $country['code'],
			'country_str' => $country['title_en'],
			'country_name' => $country['title_en'],
			'is_europe' => $country['is_europe'],
			'business_number1' => $_POST['bnum']
		
		);
		
		//var_dump($arrCountry);
		
        $withVat = Address::UseVAT($arrCountry);
		
		$PH = new ProductHelper();		
        $cartInfo = '';
        $fullprice = 0;
        $fullweight = 0;
        $price = 0;
        $full_count = 0;
        $cartInfo['items'] = array();
        //var_dump($cart);
		//return '';
        foreach ($cart as $item) {
            
			//$withVat = $item['UseVAT'];
			
			if ($country['id'] == '68' OR $country['id'] == '246') {
			
				$item['Price2Use'] = 1;
			
			} else {
				
				$item['Price2Use'] = 2;
				
			}
			
			$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $cartInfo['items'][$item['ID']]['title'] = 
			$item['Title'];
            $cartInfo['items'][$item['ID']]['weight'] = $item['UnitWeight'];
            if ($item['Entity'] == 30) {
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
                $cartInfo['items'][$item['ID']]['Quantity'] = 1;
				$item['Quantity'] = 1;
            } else {
                $fullprice += $price * $item['Quantity'];
                $cartInfo['items'][$item['ID']]['Quantity'] = $item['Quantity'];
				
				$cartInfo['items'][$item['ID']]['price'] = $price;
            }
            $cartInfo['items'][$item['ID']]['entity'] = $item['Entity'];
            $full_count += $item['Quantity'];
        }
        $cartInfo['fullInfo']['count'] = $full_count;
        $cartInfo['fullInfo']['cost'] = $fullprice;
        $cartInfo['fullInfo']['weight'] = $fullweight;
        
        $input['footer2'] = $ui->item('CART_COL_SUBTOTAL_DELIVERY') . ': <span class="delivery_name">' . $ui->item('MSG_DELIVERY_TYPE_0').'</span><span class="date" style="display: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: 05.07.2018 </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$ui->item('CARTNEW_TOTAL_WEIGHT_LABEL') . ': ' . $cartInfo['fullInfo']['weight'] . ' '. $ui->item('CARTNEW_WEIGHT_NAME');
		
		if ($fullprice < 5){
			$fullprice = 5.0;
		}
		$input['fullpricehidden'] = $fullprice;
		
        $input['footer3'] = $ui->item('CARTNEW_TOTAL_COST_LABEL') . ': <span class="itogo_cost" id="itogo_cost">'. $PH->FormatPrice($fullprice) . '</span>';		
		
        $input['cart_header'] = sprintf($ui->item('CARTNEW_HEADER_AMOUNT_TITLE'), self::decline_goods($cartInfo['fullInfo']['count']), $PH->FormatPrice($cartInfo['fullInfo']['cost']));
        //$input['cart'] = array();
        foreach ($cartInfo['items'] as $id => $item) :
            $input['cart'][] = '<tr>'.
                //'<td style="width: 31px;"><img width="31" height="31" align="middle" alt="" style="vertical-align: middle" data-bind="attr: { alt: Title}" src="/pic1/cart_ibook.gif"></td>'.
                '<td style="width: 35px; height: 35px"> <span class="entity_icons"><i class="fa e' . $item['entity'] . '"></i></span></td>'.
                '<td>
					'.(($item['weight'] == 0) ? '<div style="float: right; color: #5BB75B;">Бесплатная доставка</div>' : '').'
				
                    <span class="a">'.$item['title'].'</span>
                    <div class="minitext">'.$item['month_count'].' ' . (($item['entity'] == 30) ? $ui->item('MONTH_SMALL') : $ui->item('CARTNEW_COUNT_NAME') . '.' ) . ' x '.$PH->FormatPrice($item['price']).(($item['weight'] > 0) ? '<br /> '.$ui->item('CARTNEW_WEIGHT_LABEL').': '.($item['weight']).' '.$ui->item('CARTNEW_WEIGHT_NAME') : '').'</div>
                </td>
                
            </tr>';
			
			endforeach;
        echo json_encode($input);
    }
    function actionOrderPay() {
		
		
		
		
        $id = (int) Yii::app()->getRequest()->getParam('id');
		
		$o = new Order;
		$order = $o->isMyOrder($this->uid,$id);
        if(!$order) throw new CHttpException(404);
		
        $ptype = (int) Yii::app()->getRequest()->getParam('ptype');
		
		
		if ($_GET['hide_edit'] == '1') {
			
			$sql = 'UPDATE users_orders SET hide_edit_order=:int WHERE id=:id'; Yii::app()->db->createCommand($sql)->execute(array(':int' => 1, ':id' => $id));
			
			$this->redirect(Yii::app()->createUrl('cart/orderPay').'?id='.$id.'&ptype='.$ptype);
			
		}
		
		
		
		
		$sql = 'UPDATE users_orders SET payment_type_id=:ptype, must_upgrade = 1 WHERE id=:id LIMIT 1';
        Yii::app()->db->createCommand($sql)->execute(array(':ptype' => $ptype, ':id' => $id));
        //меняем в базе старого сайта тип оплаты
//        $sql = 'UPDATE users_orders SET must_upgrade = 1 WHERE id=:id LIMIT 1';
//        Yii::app()->db->createCommand($sql)->execute(array(':id' => $id));
		
        if ($ptype <= 1)
            $ptype = 0;
        $o = new Order;
        $order = $o->GetOrder($id);
		
		//if (!$order) {
			
		//	$this->redirect(Yii::app()->createUrl('site/index'));
			
		//}
		
        $data = array();
        $data['order'] = $order;
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
        $this->breadcrumbs[] = 'Оформление заказа';
        $data['number_zakaz'] = $id;

        $data['ptype'] = $ptype;
        //меняем в базе тип оплаты
        
        //выводим соответствующий шаблон
        if ($ptype == '27') {
            $this->render('applepay', $data);
        } elseif ($ptype == '26') {
            $this->render('alipay', $data);
        } elseif ($ptype == '8') {
            $this->render('paypal', $data);
        } elseif ($ptype == '25') {
            $this->render('paytrail', $data);
        } else {
            if ($ptype == '7')
                $namepay = 'Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС';
            if ($ptype == '13')
                $namepay = 'Предоплата на банковский счет в Финляндии';
            if ($ptype == '14')
                $namepay = 'Предоплата на банковский счет в России';
            if ($ptype == '0')
                $namepay = 'Оплата в магазине';
            $data['dop'] = '<br />Вы выбрали способ оплаты: ' . $namepay;
            $this->render('result', $data);
        }
    }
    public function actionResult() {
        $cart = new Cart();
        $tmp = $cart->GetCart($this->uid, $this->sid);
        $post = $_POST;
        foreach ($post['Address'] as $k=>$v) {
            if (!is_string($v)) continue;
            $post['Address'][$k] = addslashes(htmlspecialchars($v));
        }
		
		foreach ($post['Address2'] as $k=>$v) {
            if (!is_string($v)) continue;
            $post['Address2'][$k] = addslashes(htmlspecialchars($v));
        }
		
		$cart = new Cart();
                    $tmp = $cart->GetCart($this->uid, $this->sid);
                    $beautyItems = $cart->BeautifyCart($tmp, $this->uid);
		
            			
					
		
        //Обработчик для зарег. 
        if (!Yii::app()->user->isGuest) {
			
			
			
			
            $adr1 = Address::GetAddress($this->uid, $post['id_address']);
			
			if ($post['addr_buyer'] == '1') {
				
				$post['id_address_b'] = $post['id_address'];
				
			}
			
            $adr2 = Address::GetAddress($this->uid, $post['id_address_b']);			
			
            $idAddr2 = '';
			
			if (!$adr1['address_id']) { $adr1['address_id'] = 0; }
			if (!$adr2['address_id']) { $adr2['address_id'] = 0; }
			
            if ( $post['dtype'] == 0 AND $post['dtype'] != '' ) {
				
				$adr1['address_id'] = 0;
				$adr2['address_id'] = 0;
				
			}
			
			
			
			$DeliveryMode = 0;
			if ($post['dtype'] == '0') { $DeliveryMode = 1; }
			
			if ($post['dtype'] == '') { $post['dtype'] = 3; }
			
            $s['DeliveryAddressID'] = $adr1['address_id'];
            $s['DeliveryTypeID'] = $post['dtype'];
            $s['DeliveryMode'] = $DeliveryMode;
            $s['CurrencyID'] = Yii::app()->currency;
            $s['BillingAddressID'] = $adr2['address_id'];
            $s['Notes'] = ($_POST['Notes']) ? $_POST['Notes'] : '&nbsp;';
            $s['Mandate'] = 0;
            $s['smartpost'] = $_POST['pickpoint_address'];
            //$s['payment'] = $post['ptype'];
            $order = new OrderForm($this->sid);
            $order->attributes = $s;
            //var_dump ($order);
//            $c = new Cart;
            $items = array();
            foreach ($tmp as $item) {
                if (ProductHelper::IsAvailableForOrder($item))
                    $items[] = $item;
            }
            $o = new Order;
            $o->setPromocode(Yii::app()->getRequest()->getParam('promocode'));
            $id = $o->CreateNewOrder($this->uid, $this->sid, $order, $items, $post['ptype']);
            $o = new Order;
            $order = $o->GetOrder($id);
            $data['order'] = $order;
            $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
            $this->breadcrumbs[] = 'Оформление заказа';
            $data['number_zakaz'] = $id;
            $data['ptype'] = $post['ptype'];
			
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

                Yii::app()->user->setFlash('order', Yii::app()->ui->item('ORDER_MSG_DONE'));
			
			
            if (Yii::app()->request->isAjaxRequest) {
                echo Yii::app()->createUrl('cart/orderPay') . '?id=' . $data['number_zakaz'] . '&ptype='.$data['ptype'];
                exit();
            }
            if ($post['ptype'] == '27') {
                $this->render('applepay', $data);
            } elseif ($post['ptype'] == '26') {
                $this->render('alipay', $data);
            } elseif ($post['ptype'] == '8') {
                $this->render('paypal', $data);
            } elseif ($post['ptype'] == '25') {
                $this->render('paytrail', $data);
            } else {
                if ($post['ptype'] == '7')
                    $namepay = 'Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС';
                if ($post['ptype'] == '13')
                    $namepay = 'Предоплата на банковский счет в Финляндии';
                if ($post['ptype'] == '14')
                    $namepay = 'Предоплата на банковский счет в России';
                if ($post['ptype'] == '1')
                    $namepay = 'Оплата в магазине';
                $data['dop'] = '.<br />Вы выбрали способ оплаты: ' . $namepay;
                $this->render('result', $data);
            }
			
			
			
        }
        
		
		//Обработчик для незарег.
		if (Yii::app()->request->isPostRequest) {
            $type = $post['Address']['type'];
            $business_title = $post['Address']['business_title'];
            $business_number1 = $post['Address']['business_number1'];
            $titul = $post['Address']['receiver_title_name'];
            $fam = $post['Address']['receiver_last_name'];
            $name = $post['Address']['receiver_first_name'];
            $otch = $post['Address']['receiver_middle_name'];
            $country = $post['Address']['country'];
            $stat = $post['Address']['state_id'];
            $city = $post['Address']['city'];
            $post_index = $post['Address']['postindex'];
            $address = $post['Address']['streetaddress'];
            $email = $post['Address']['contact_email'];
            $phone = $post['Address']['contact_phone'];
            $comment = $post['Address']['notes'];
            $comment2 = $post['notes'];
			
			if (!$comment2) { $comment2 = '&nbsp;'; }
			
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/protected/runtime/2.txt', print_r($post,1));
			
            if (User::checkLogin($email)) {
                echo '9';
                exit();
            } else {

                $next = false;

                if ($post['check_addressa']) {

                    if ($fam AND $name AND $email) {

                        $next = true;

                    }

                } else {

                    if ($fam AND $name AND $email AND $country AND $phone) {

                        $next = true;

                    }

                }


                if ($next) {
                    /*
                     *
                     * 1. Для начала создаем покупателя и получаем его ID
                     * 2. Выполняем вход покупателя
                     * 3. Добавляем адрес в базу с привязкой покупателя к этому
                     * адресу
                     * 4. Создаем заказ на этого покупателя
                     * 5. Определяем каким способом он выбрал оплату и делаем
                     * переадресацию на соответствующую страницу где уже будет
                     * написан код с переадресацией на страницу сервиса оплаты
                     *
                     */
                    /*
                     *  1. Для начала создаем покупателя и получаем его ID
                     */
                    
                    $m20n = $m10n = $m60n = $m22n = $m15n = $m24n = $m40n = 0;
                    $razds = array();
                    foreach ($beautyItems as $p) {
                        $mn = 'm' . $p['Entity'] . 'n';
                        $$mn = 1;
                        $razds[$p['Entity']] = Yii::app()->ui->item(Entity::GetEntitiesList()[$p['Entity']]['uikey']);
                    }
                    $psw = rand(1000000, 9999999) . 'sS';
                    $langID = Language::ConvertToInt(Yii::app()->language);
                    $sql = 'INSERT INTO users (login, pwd, first_name, last_name, mail_language, mail_audio_news, mail_books_news, '
                        . 'mail_maps_news, mail_music_news, mail_musicsheets_news, mail_soft_news, mail_video_news, currency) VALUES '
                        . '(:login, :pwd, :fName, :lName, :lang, :m20n, :m10n, :m60n, :m22n, :m15n, :m24n, :m40n, :currency)';
                    $ret = Yii::app()->db->createCommand($sql)->execute(array(
                        ':login' => $email,
                        ':pwd' => $psw,
                        ':fName' => $name,
                        ':lName' => $fam,
                        ':lang' => $langID,
                        ':m20n' => $m20n,
                        ':m10n' => $m10n,
                        ':m60n' => $m60n,
                        ':m22n' => $m22n,
                        ':m15n' => $m15n,
                        ':m24n' => $m24n,
                        ':m40n' => $m40n,
                        ':currency' => Yii::app()->currency));
                    $idUser = Yii::app()->db->getLastInsertID(); //получаем ID юзера
                    $identity = new RuslaniaUserIdentity($email, $psw);
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
                        file_put_contents(Yii::getPathOfAlias('webroot') . '/test/mail.log', implode("\t", array(
                                    date('d.m.Y H:i:s'),
                                    $email,
                                    serialize($mailResult),
                                    $message->view,
                                    serialize($message->from),
                                )
                            ) . "\n", FILE_APPEND);
                    }
                    $userID = $identity->getId();
                    /*
                     * 2. Добавляем адрес в базу с привязкой покупателя к этому
                     * адресу
                     */
					 
					 //if (!$post['check_addressa']) {
					 
                    $sql = 'INSERT INTO user_address (`type`,`business_title`,`business_number1`,`receiver_title_name`,`receiver_first_name`,`receiver_middle_name`,`receiver_last_name`, `country`,`state_id`,`city`,`postindex`,`streetaddress`,`contact_email`,`contact_phone`,`notes`) VALUES '
                        . '("' . $type . '", "' . $business_title . '", "' . $business_number1 . '", "' . $titul . '", "' . $name . '", "' . $otch . '", "' . $fam . '", "' . $country . '", "' . $stat . '", "' . $city . '", "' . $post_index . '", "' . $address . '", "' . $email . '", "' . $phone . '", "' . $comment . '")';
                    $ret = Yii::app()->db->createCommand($sql)->execute();
                    $idAddr2 = Yii::app()->db->getLastInsertID();
                    $sql = 'INSERT INTO users_addresses (uid,address_id,if_default) VALUES ("' . $userID . '", "' . $idAddr2 . '", "1")';
                    $ret = Yii::app()->db->createCommand($sql)->execute();
                    $idAddr = Yii::app()->db->getLastInsertID();
					
					
					if ($post['addr_buyer'] != '1') {
						
						$type = $post['Address2']['type'];
            $business_title = $post['Address2']['business_title'];
            $business_number1 = $post['Address2']['business_number1'];
            $titul = $post['Address2']['receiver_title_name'];
            $fam = $post['Address2']['receiver_last_name'];
            $name = $post['Address2']['receiver_first_name'];
            $otch = $post['Address2']['receiver_middle_name'];
            $country = $post['Address2']['country'];
            $stat = $post['Address2']['state_id'];
            $city = $post['Address2']['city'];
            $post_index = $post['Address2']['postindex'];
            $address = $post['Address2']['streetaddress'];
            $email = $post['Address2']['contact_email'];
            $phone = $post['Address2']['contact_phone'];
			$comment = " ";			
						$sql = 'INSERT INTO user_address (`type`,`business_title`,`business_number1`,`receiver_title_name`,`receiver_first_name`,`receiver_middle_name`,`receiver_last_name`, `country`,`state_id`,`city`,`postindex`,`streetaddress`,`contact_email`,`contact_phone`,`notes`) VALUES '
                        . '("' . $type . '", "' . $business_title . '", "' . $business_number1 . '", "' . $titul . '", "' . $name . '", "' . $otch . '", "' . $fam . '", "' . $country . '", "' . $stat . '", "' . $city . '", "' . $post_index . '", "' . $address . '", "' . $email . '", "' . $phone . '", "' . $comment . '")';
						$ret = Yii::app()->db->createCommand($sql)->execute();
						$idAddr2_1 = Yii::app()->db->getLastInsertID();
						$sql = 'INSERT INTO users_addresses (uid,address_id,if_default) VALUES ("' . $userID . '", "' . $idAddr2_1 . '", "1")';
						$ret = Yii::app()->db->createCommand($sql)->execute();
						$idAddr_2 = Yii::app()->db->getLastInsertID();
						
						
						
						
					} else {
						
						
						$idAddr2_1 = $idAddr2;
						
						
					}
					
					
					
					
					
                    if (!$post['dtid']) {
                        $post['dtid'] = 0;
                    }
					
					
					$DeliveryMode = 0;
					if ($post['dtype'] == '0') { $DeliveryMode = 1; }
					
					
                    $s['DeliveryAddressID'] = $idAddr2;
                    $s['DeliveryTypeID'] = $post['dtype'];
                    $s['DeliveryMode'] = $DeliveryMode;
                    $s['CurrencyID'] = Yii::app()->currency;
                    $s['BillingAddressID'] = $idAddr2_1;
                    $s['Notes'] = $comment2;
                    $s['Mandate'] = 0;
                    //$s['payment'] = $post['ptype'];
                    $order = new OrderForm($this->sid);
                    $order->attributes = $s;
                    //var_dump ($order);
//            $c = new Cart;
                    $items = array();
                    foreach ($tmp as $item) {
                        if (ProductHelper::IsAvailableForOrder($item))
                            $items[] = $item;
                    }
                    $o = new Order;
                    $o->setPromocode(Yii::app()->getRequest()->getParam('promocode'));
                    $id = $o->CreateNewOrder($userID, $this->sid, $order, $items, $post['ptype']);
                    
					//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/protected/runtime/1.txt', $id);
					
                    $order = $o->GetOrder($id);

                    $data['order'] = $order;
                    //$this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
                    // $this->breadcrumbs[] = 'Оформление заказа';
                    $data['number_zakaz'] = $id;
                    $data['ptype'] = $post['ptype'];
					
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

					Yii::app()->user->setFlash('order', Yii::app()->ui->item('ORDER_MSG_DONE'));
					
					if (Yii::app()->request->isAjaxRequest) {
                       echo Yii::app()->createUrl('cart/orderPay') . '?id=' . $data['number_zakaz'] . '&ptype='.$data['ptype'];
                       exit();
					}
                    
                    if ($post['ptype'] == '27') {
                        $this->render('applepay', $data);
                    } elseif ($post['ptype'] == '26') {
                        $this->render('alipay', $data);
                    } elseif ($post['ptype'] == '8') {
                        $this->render('paypal', $data);
                    } elseif ($post['ptype'] == '25') {
                        $this->render('paytrail', $data);
                    } else {
                        if ($post['ptype'] == '7')
                            $namepay = 'Оплата после получения по счету для клиентов в Финляндии и организаций в ЕС';
                        if ($post['ptype'] == '13')
                            $namepay = 'Предоплата на банковский счет в Финляндии';
                        if ($post['ptype'] == '14')
                            $namepay = 'Предоплата на банковский счет в России';
                        if ($post['ptype'] == '1')
                            $namepay = 'Оплата в магазине';
                        $data['dop'] = '.<br />Вы выбрали способ оплаты: ' . $namepay;
                        //$this->render('result', $data);
                    }
                    //echo '1';
                }
            }
			
			
			
			
        }
    }
    public function actionValid() {
        if (!Yii::app()->user->isGuest) {
            //$this->redirect('/me/');
            //Проверка регистрированного пользователя
            if ($_POST['id_address'] AND $_POST['id_address_b'] AND $_POST['confirm'] == '1') {
                echo '1';
            }
        } else {
            $post = $_POST;
            if (Yii::app()->request->isPostRequest) {
                $titul = $post['Address']['receiver_title_name'];
                $fam = $post['Address']['receiver_last_name'];
                $name = $post['Address']['receiver_first_name'];
                $otch = $post['Address']['receiver_middle_name'];
                $country = $post['Address']['country'];
                $stat = $post['Address']['state_id'];
                $city = $post['Address']['city'];
                $post_index = $post['Address']['postindex'];
                $address = $post['Address']['streetaddress'];
                $email = $post['Address']['contact_email'];
                $phone = $post['Address']['contact_phone'];
                $comment = $post['Address']['notes'];
                if (User::checkLogin($email)) {
                    echo 'Такой E-mail уже зарегистрирован в системе';
                } else {
                    if ($fam AND $name AND $country AND $city AND $post_index AND $address AND $email AND $phone) {
                        echo '1';
                    } else {
                        echo 'Заполните все обязательные поля!';
                    }
                }
            }
        }
    }
    public function actionNoRegister() {
        //$cart = new Cart;
        //$cartItems = array();
        //$tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);
        //foreach($tmp as $item)
        //{
        //  $item['Title'] = str_replace('"', '\\"', $item['Title']); // из-за того, что это идет в JSON в виде строки в do_order.php и ломает парсеру жизнь
        //if($item['IsAvailable'])
        //     $cartItems[] = $item;
        //}
        $cart = new Cart;
        $s = $cart->GetCart($this->uid, $this->sid);
        if (!count($s)) {
            $this->redirect('/cart/');
        }
        if (!Yii::app()->user->isGuest) {
            $this->redirect('/me/');
        }
        $u = new User;
        $addresses = $u->GetAddresses($this->uid);
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
        $this->breadcrumbs[] = 'Оформление заказа';
        $this->render('no_register');
    }
    public function actionVariants() {
        if (Yii::app()->user->isGuest) {
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            $this->render('variants', array());
        } else {
            $this->redirect('/cart/doorder/');
        }
    }
    public function actionDoOrder() {
        $data = $this->GetFormRequest();
        if (!empty($data)) {
            foreach ($data as $id => $value) {
                $this->actionChangeQuantity(array_values($value));
            }
        }
        if (Yii::app()->user->isGuest) {
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            //$this->render('login_or_register');
            $this->render('login_form');
            //$this->render('variants', array());
            return;
        }
        $cart = new Cart;
        $cartItems = array();
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);
        if (!count($tmp)) {
            $this->redirect('/me/');
        }
        foreach ($tmp as $item) {
            $item['Title'] = str_replace('"', '\\"', $item['Title']); // из-за того, что это идет в JSON в виде строки в do_order.php и ломает парсеру жизнь
            if ($item['IsAvailable'])
                $cartItems[] = $item;
        }
        $u = new User;
        $addresses = $u->GetAddresses($this->uid);
        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
        $this->breadcrumbs[] = Yii::app()->ui->item('CREATE_ORDER');
        $this->render('do_order', array('cartItems' => $cartItems, 'addresses' => $addresses));
    }
    public function actionDoRequest($entity, $iid) {
        $entity = Entity::ParseFromString($entity);
        $iid = intVal($iid);
        $p = new Product();
        $product = $p->GetProduct($entity, $iid);
        if (empty($product))
            throw new CHttpException(404);
        if (Yii::app()->user->isGuest) {
            $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_MY_REQUESTS');
            $this->render('login_or_register_with_request', array('product' => $product));
            return;
        }
        $r = new Request;
        $product['quantity'] = 1; // В заявке всегда 1 шт.
        $items = array($product);
        $rid = $r->CreateNewRequest($this->uid, $items, '');
        if (empty($rid))
            throw new CException('REQUEST_DB_ERROR');
        $this->redirect(Yii::app()->createUrl('request/view', array('rid' => $rid)));
    }
    public function actionGetCount() {
        $cart = new Cart;
        $total_price = $cart->getPriceSum($this->uid, $this->sid, 3);
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid); //var_dump($tmp);
        $inCart = array();
        $endedItems = array();
		$tt=0;
        foreach ($tmp as $item) {
            if ($item['IsAvailable']) {
                $inCart[] = $item;
				
				if ($item['Entity'] == 30) {
					
					$item['Quantity'] = 1;
					
				}
				
				$tt += $item['Quantity'];
            }
        }
		
		$cart = $this->actionGetAll(0);
		
		$cart = $cart['CartItems'];
		
		
        $withVat = false;
        $fullprice = 0;
        $fullweight = 0;
        $price = 0;
		//var_dump($cart);
		//return '';
        foreach ($cart as $item) {
            
			$withVat = $item['UseVAT'];
			
			$item['type'] = $item['Price2Use'];
			
            if ($item['Entity'] == 30) {
                if ($item['type'] == '1') { //фины
                    $price = $item['PriceVATFin'];
                } else {
                    $price = $item['PriceVATWorld'];
                }
            } else {
                $price = $item['PriceVAT'];
            }
            if (!$withVat) {
                
				if ($item['Entity'] == 30) {
				
				if ($item['type'] == '1') { //фины
                    $price = $item['PriceVAT0Fin'];
                } else {
                    $price = $item['PriceVATWorld'];
                }
				} else {
					$price = $item['PriceVAT0'];
				}
				
            }
           
			
            if ($item['Entity'] == 30) {
                
                $fullprice += $price * $item['Quantity'];
				
            } else {
                $fullprice += $price * $item['Quantity'];
                
            }
        }
		
		$total_price = (string) ProductHelper::FormatPrice($fullprice);
		
		
		
        $this->ResponseJson(array('countcart' => $tt, 'totalPrice' => (string) $total_price));
    }
    public function actionGetAll($ajax = true) {
        $cart = new Cart;
        $isMiniCart = 0;
        if ($ajax) {
            $isMiniCart = Yii::app()->request->getParam('is_MiniCart', 0);
            $isMiniCart = intVal($isMiniCart);
        }
        $cartGoods = $cart->GetCart($this->uid, $this->sid, $isMiniCart);
        $tmp = $cart->BeautifyCart($cartGoods, $this->uid, $isMiniCart);
        $inCart = array();
        $endedItems = array();
        foreach ($tmp as $item) {
            if ($item['IsAvailable'])
                $inCart[] = $item;
            else
                $endedItems[] = $item;
        }
        if (!empty($endedItems)) {
            $r = new Request;
            $ended = array();
            foreach ($endedItems as $ei) {
                $endedItem = array('entity' => $ei['Entity'],
                    'id' => $ei['ID'],
                    'quantity' => 1,
                );
                $ended[] = $endedItem;
                $r->CreateNewRequest($this->uid, array($endedItem), '');
                $cart->Remove($ei['Entity'], $ei['ID'], Cart::TYPE_ORDER, $this->uid, $this->sid);
            }
        }
        $ret = array('CartItems' => $inCart,
            'EndedItems' => $endedItems,
//                     'RequestItems' => $inReq
        );
        if ($ajax) {
            $this->ResponseJson($ret);
            return;
        }
        return $ret;
    }
    // Добавить в корзину
    public function actionAdd($data = false) {
        if ($data === false) {
            $data = $this->GetRequest();
        }
        if ($data === false)
            throw new CHttpException('Please do AJAX request');
        list($entity, $id, $quantity, $product, $origQuantity, $finOrWorld) = $data;
        $type = ProductHelper::IsAvailableForOrder($product) ? Cart::TYPE_ORDER : Cart::TYPE_REQUEST;
        if ($type == Cart::TYPE_REQUEST) {
            // TODO:
        } else {
            $cart = new Cart;
            $alreadyInCart = $cart->AddToCart($entity, $id, $quantity, $type, $this->uid, $this->sid, $finOrWorld);

            $message = $entity == Entity::PERIODIC
                ? Yii::app()->ui->item('ADDED_TO_CART', Yii::app()->createUrl('cart/view'))
                : sprintf(Yii::app()->ui->item('ADDED_TO_CART_ALREADY'), Yii::app()->createUrl('cart/view'), $alreadyInCart);

            $already = $entity == Entity::PERIODIC ? Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART') : sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $alreadyInCart);
			
			if ($_POST['hidecount'] == '1') {
				
				$message = str_replace($already, '', $message);
				
			}
			
			
            if (Yii::app()->request->isAjaxRequest) {
                //CARTNEW_IN_CART_BTN
                $this->ResponseJson(array(
                    'hasError' => false, 
                    'msg' => $message,
                    'already' => $already,
                    'buttonName' => Yii::app()->ui->item('CARTNEW_IN_CART_BTN', $alreadyInCart),
                    'buttonName2' => Yii::app()->ui->item('CARTNEW_IN_CART_BTN', $alreadyInCart),
                ));
            }
            else
                $this->redirect(Yii::app()->createUrl('cart/view'));
        }
    }
    // Взять на заметку
    public function actionMark() {
        $data = $this->GetRequest();
        if ($data === false)
            throw new CHttpException('Please do AJAX request');
        list($entity, $id, $quantity, $product) = $data;
        $cart = new Cart;
        if ($quantity == 0)
            $quantity = 1;
        
		
		if ($_POST['mark'] == '1') {
			
			//добавить
			
			$ret = $cart->AddToCart($entity, $id, $quantity, Cart::TYPE_MARK, $this->uid, $this->sid, Cart::FIN_PRICE);
        
			$message = Yii::app()->ui->item('ADDED_TO_MARK');
			
		} else {
			
			//убрать
			
			$ret = $cart->Remove($entity, $id, Cart::TYPE_MARK, $this->uid, $this->sid);
			
			
			$message = Yii::app()->ui->item('DELETED_FROM_MARK');
			
		}
		
		$this->ResponseJson(array('hasError' => false, 'msg' => $message));
        
        //$this->ResponseJsonOk($ret);
    }
    public function actionChangeQuantity($data = false) {
        $returnJson = false;
        if ($data === false) {
            $data = $this->GetRequest();
            $returnJson = true;
        }
        if ($data === false)
            throw new CHttpException('Please do AJAX request');
        list($entity, $id, $quantity, $product, $originalQuantity, $finOrWorldPrice) = $data;
        $type = ProductHelper::IsAvailableForOrder($product) ? Cart::TYPE_ORDER : Cart::TYPE_REQUEST;
        $quantity2 = $originalQuantity;
        // Проверить на SKIP и InternetKolvo
        $p = new Product;
        $availCount = $p->IsQuantityAvailForOrder($entity, $id, $quantity);
        $changed = false;
        $changedStr = '';
        if ($availCount != $originalQuantity) {
            $changed = true;
            if ($entity == Entity::PERIODIC) {
                $product['issues_year'] = Periodic::getCountIssues($product['issues_year']);
                $show3Months = $product['issues_year']['show3Months'];
                $show6Months = $product['issues_year']['show6Months'];
                if (!empty($_POST['decrement'])) {
                    if ($originalQuantity < 3) {
                        $availCount = 3;
                        $originalQuantity = 0;
                    }
                    elseif ($originalQuantity < 6) {
                        if ($show3Months) $availCount = 3;
//                        else $availCount = 0;
                        elseif ($show6Months) {
                            $originalQuantity = 0;
                            $availCount = 6;
                        }
                        else {
                            $originalQuantity = 0;
                            $availCount = 12;
                        }
                    }
                    elseif ($originalQuantity < 12) {
                        if ($show6Months) $availCount = 6;
//                        else $availCount = 0;
                        else {
                            $availCount = 12;
                            $originalQuantity = 0;
                        }
                    } else
                        $availCount = 12;
                }
                else {
                    if ($originalQuantity <= 3) {
                        if ($show3Months)
                            $availCount = 3;
                        elseif ($show6Months)
                            $availCount = 6;
                        else
                            $availCount = 12;
                    }
                    if ($originalQuantity > 3 AND $originalQuantity <= 6) {
                        if ($show6Months)
                            $availCount = 6;
                        else
                            $availCount = 12;
                    }
                }
            }
            $quantity = $availCount;
            if ($entity != 30) $changedStr = sprintf(Yii::app()->ui->item('new (shopping cart)'), $quantity, $quantity);
            elseif (!empty($originalQuantity)) {
                $changedStr = Yii::app()->ui->item('REAL_PEREODIC_COUNTS');
            }
        }
        $cart = new Cart;
        $ret = $cart->ChangeQuantity($entity, $id, $quantity, $type, $this->uid, $this->sid, $finOrWorldPrice);
        if ($returnJson) {
            $this->ResponseJson(array('hasError' => false, 'quantity' => $ret, 'origQuantity' => $originalQuantity,
                'changedStr' => $changedStr,
                'changed' => $changed));
        }
    }
    public function actionRemove() {
        $entity = intVal(@$_POST['entity']);
        $iid = intVal(@$_POST['iid']);
        $type = intVal(@$_POST['type']);
        $types = array(Cart::TYPE_ORDER, Cart::TYPE_REQUEST);
        if (!in_array($type, $types))
            $this->ResponseJsonError('WrongTypes');
        $cart = new Cart;
        $ret = $cart->Remove($entity, $iid, $type, $this->uid, $this->sid);
        $this->ResponseJson(array('hasError' => $ret == 0));
    }
    // Добавить в заявку
    public function actionRequest() {
        $data = $this->GetRequest();
        if ($data === false)
            throw CHttpException('Please do AJAX request');
        list($entity, $id, $quantity, $product) = $data;
        if (empty($product)) {
            if (Yii::app()->request->isAjaxRequest)
                $this->ResponseJsonError('EmptyProduct');
            else
                throw new CHttpException(404);
        }
        $r = new Request();
        $product['quantity'] = 1;
        $items = array($product);
        $rid = $r->CreateNewRequest($this->uid, $items, '');
        $message = ($rid > 0) ? Yii::app()->ui->item('REQUEST_CREATED') : Yii::app()->ui->item('ERROR');
        $this->ResponseJson(array('hasError' => $rid == 0, 'msg' => $message));
    }
    private function GetFormRequest() {
        $result = array();
        $quantity = array();
        if (!empty($_GET['quantity']) && is_array($_GET['quantity']))
            $quantity = $_GET['quantity'];
        foreach ($quantity as $idItem => $count) {
            $idItem = (int) $idItem;
            $count = max((int) $count, 1);
            if ($idItem > 0) {
                $result[$idItem] = array('entity' => 0, 'id' => $idItem, 'quantity' => $count, 'product' => '', 'originalQuantity' => $count, 'type' => 0);
            }
        }
        if (!empty($result)) {
            $entity = array();
            if (!empty($_GET['entity']) && is_array($_GET['entity']))
                $entity = $_GET['entity'];
            foreach ($entity as $idItem => $id) {
                $idItem = (int) $idItem;
                $id = (int) $id;
                if (($idItem > 0) && ($id > 0) && (isset($result[$idItem]))) {
                    if ($id == Entity::PERIODIC) {
                        if ($result[$idItem]['quantity'] != 6 && $result[$idItem]['quantity'] != 12 && $result[$idItem]['quantity'] != 0 && $result[$idItem]['quantity'] != 3)
                            $result[$idItem]['quantity'] = 12;
                    }
                    $product = new Product();
                    $product = $product->GetBaseProductInfo($id, $idItem);
                    if (empty($product))
                        throw new CException('Wrong id');
                    $result[$idItem]['entity'] = $id;
                    $result[$idItem]['product'] = $product;
                } else
                    throw new CException('Wrong id');
            }
            $type = array();
            if (!empty($_GET['type']) && is_array($_GET['type']))
                $type = $_GET['type'];
            $types = array(Cart::FIN_PRICE, Cart::WORLD_PRICE);
            foreach ($type as $idItem => $id) {
                $idItem = (int) $idItem;
                $id = (int) $id;
                if (!in_array($id, $types))
                    $id = Cart::FIN_PRICE;
                if (($idItem > 0) && (isset($result[$idItem]))) {
                    $result[$idItem]['type'] = $id;
                } else
                    throw new CException('Wrong id');
            }
        }
        //return array($entity, $id, $quantity, $product, $originalQuantity, $type);
        return $result;
    }
    private function GetRequest() {
        $entity = 0;
        $id = 0;
        $quantity = 1;
        $type = Cart::FIN_PRICE;
        if (Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest) {
            $entity = intVal(@$_POST['entity']);
            $id = intVal(@$_POST['id']);
            $quantity = intVal(abs(@$_POST['quantity']));
            $originalQuantity = $quantity;
            $type = intVal(@$_POST['type']);
            $types = array(Cart::FIN_PRICE, Cart::WORLD_PRICE);
            if (!in_array($type, $types))
                $type = Cart::FIN_PRICE;
        }
//        else if (!Yii::app()->request->isPostRequest)
//        {
//            $entity = intVal(@$_GET['entity']);
//            $id = intVal(@$_GET['id']);
//            $quantity = intVal(abs(@$_GET['quantity']));
//            $originalQuantity = $quantity;
//
//            $ua = strtolower(trim(Yii::app()->request->getUserAgent()));
//
//            if (empty($ua)) return false;
//            $bots = array('facebookexternalhit', 'googlebot', 'yahoo! slurp', 'bot', 'riddler', 'feedfetcher',
//                          'www.alexa.com', 'java/1', 'spider', 'theoldreader.com', 'feed parser',
//                          'msnbot', 'yandex.com/bots',
//                          'linkdex', 'developers.google.com/+/web/snippet', 'bingpreview', 'flipboard',
//                          'crawler');
//
//            foreach ($bots as $bot)
//            {
//                if (strpos($ua, $bot) !== false) return false;
//            }
//        }
        else {
            throw new CException('Wrong request');
        }
        if ($entity == Entity::PERIODIC) {
            if ($quantity != 6 && $quantity != 12 && $quantity != 0 && $quantity != 3)
                $quantity = 12;
        }
        if ($originalQuantity <= 0)
            $originalQuantity = 1;
        if ($quantity <= 0)
            $quantity = 1;
        if (!Entity::IsValid($entity))
            throw new CException('Wrong entity');
        $product = new Product();
        $product = $product->GetBaseProductInfo($entity, $id);
        if (empty($product))
            throw new CException('Wrong id');
        return array($entity, $id, $quantity, $product, $originalQuantity, $type);
    }
    function actionCertificatePay() {
        $this->breadcrumbs[] = Yii::app()->ui->item('GIFT_CERTIFICATE');
        $id = (int) Yii::app()->getRequest()->getParam('id');
        $certificate = new Certificate();
        $data = array();
        $data['order'] = $certificate->getCertificate($id);
        if (empty($data['order'])) throw new CException('Wrong id');
        $data['order']['id'] = $data['order']['id'] = 'c' . $id;;
        $data['number_zakaz'] = $data['order']['id'];
        $data['ptype'] = (int)$data['order']['payment_type_id'];
        $data['order']['full_price'] = $data['order']['nominal'];
        $data['order']['currency_id'] = $data['order']['currency'];
//выводим соответствующий шаблон
        switch ($data['ptype']) {
//            case 27: $this->render('applepay', $data); break;
//            case 26: $this->render('alipay', $data); break;
            case 25: $data['payName'] = 'PayTrailWidget'; break;
//            case 8: $data['payName'] = 'PayPalPayment'; break;
        }
        if (!empty($data['payName'])) $this->render('certificate_pay', $data);
    }

    function actionCheckPromocode() {
        $ret = array();
        if (Yii::app()->getRequest()->isPostRequest) {
            $dtype = (int) Yii::app()->getRequest()->getParam('dtype');
            $dMode = 0;
            if ($dtype <= 0) $dMode = 1;

            $aid = (int) Yii::app()->getRequest()->getParam('aid');
//            $dtid = (int) Yii::app()->getRequest()->getParam('dtid');
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

}

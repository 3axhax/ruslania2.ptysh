<?php

class CartController extends MyController
{
    public function accessRules()
    {
        return array(array('allow',
                           'actions' => array('view','variants', 'doorder', 'doorderjson', 'dorequest','register', 'getall', 'getcount', 'add', 'mark', 'noregister', 'result',
                                              'changequantity', 'remove',),
                           'users' => array('*')),

                     array('allow', 'actions' => array('request'),
                           'users' => array('@')),

                     array('deny',
                           'users' => array('*')));
    }

    // Просмотр корзины
    public function actionView()
    {
        $data = $this->GetFormRequest();
        if (!empty($data)) {
            foreach ($data as $id=>$value) {
                $this->actionAdd(array_values($value));
            }
        }
        $this->breadcrumbs[] = Yii::app()->ui->item('A_SHOPCART');
        $data = $this->actionGetAll(false);
        $emptyItem = array(
            "Entity"=>'',
            "ID"=>'',
            "Title"=>'',
            "PriceVAT"=>'',
            "PriceVATStr"=>'',
            "PriceVAT0"=>'',
            "PriceVAT0Str"=>'',
            "PriceVATFin"=>'',
            "PriceVATFinStr"=>'',
            "PriceVAT0Fin"=>'',
            "PriceVAT0FinStr"=>'',
            "PriceVATWorld"=>'',
            "PriceVATWorldStr"=>'',
            "PriceVAT0World"=>'',
            "PriceVAT0WorldStr"=>'',
            "Price2Use"=>'',
            "UseVAT"=>'',
            "Url"=>'',
            "Quantity"=>'',
            "UnitWeight"=>'',
            "IsAvailable"=>'',
            "Availability"=>'',
            "AvailablityText"=>'',
            "DiscountPercent"=>'',
            "PriceOriginal"=>'',
            "ReadyVAT"=>'',
            "ReadyVAT0"=>'',
            "Rate"=>'',
            "VAT"=>'',
            "InfoField"=>'',
        );
        require_once Yii::app()->getBasePath() . '/iterators/Cart.php';
        if (empty($data['CartItems'])) {
            $data['isCart'] = false;//признак - козина пуста
            $data['CartItems'] = new IteratorsCart(array($emptyItem));
        }
        else {
            $data['isCart'] = true;
            $data['CartItems'] = new IteratorsCart($data['CartItems']);
        }
        if (empty($data['EndedItems'])) {
            $data['isEnded'] = false;
            $data['EndedItems'] = new IteratorsCart(array($emptyItem));
        }
        else {
            $data['isEnded'] = true;
            $data['EndedItems'] = new IteratorsCart($data['EndedItems']);
        }

        $this->render('view', $data);
    }

    public function actionDoOrderJSON()
    {
        if (Yii::app()->user->isGuest)
        {
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            $this->render('login_or_register');
            return;
        }

        $cart = new Cart;
        $cartItems = array();
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);
        foreach($tmp as $item)
        {
//            $item['Title'] = str_replace('"', '\\"', $item['Title']); // из-за того, что это идет в JSON в виде строки в do_order.php и ломает парсеру жизнь
//            $item['Title'] = str_replace("'", '\\\\\'', $item['Title']);
            if($item['IsAvailable'])
                $cartItems[] = $item;
        }

        $this->ResponseJson(array('Items' => $cartItems));
    }
   
    public function actionRegister() {
        
         if (Yii::app()->user->isGuest)
        {
            
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            
            $this->render('register_form', array());
        
        } else {
            
            $this->redirect('/cart/doorder/');
            
        }
        
    }
    
    public function actionResult() {
        
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
        
        if ($fam AND $name AND $country AND $city AND $post_index AND $address AND $email AND $phone) {
            
            
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
        
            $psw  = rand(1000000, 9999999);
            
        $langID = Language::ConvertToInt(Yii::app()->language);
        $sql = 'INSERT INTO users (login, pwd, first_name, last_name, mail_language, mail_audio_news, mail_books_news, '
        . 'mail_maps_news, mail_music_news, mail_musicsheets_news, mail_soft_news, mail_video_news, currency) VALUES '
        . '(:login, :pwd, :fName, :lName, :lang, 1, 1, 1, 1, 1, 1, 1, :currency)';
        $ret = Yii::app()->db->createCommand($sql)->execute(array(
            ':login' => $email,
            ':pwd' => $psw,
            ':fName' => $name,
            ':lName' => $fam,
            ':lang' => $langID,
            ':currency' => Yii::app()->currency));
               
        /*
         * 2. Добавляем адрес в базу с привязкой покупателя к этому 
         * адресу
         */
        
            $identity = new RuslaniaUserIdentity($email, $psw);
            $identity->authenticate();
            Yii::app()->user->login($identity, Yii::app()->params['LoginDuration']);
            $cart = new Cart();
            $cart->UpdateCartToUid($this->sid, $identity->getId());
            
            
            
        } else {
            echo '0';   
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

        $u = new User;
        $addresses = $u->GetAddresses($this->uid);

        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
        $this->breadcrumbs[] = 'Оформление заказа';
        $this->render('no_register');
    }
    
    public function actionVariants() {
        
        if (Yii::app()->user->isGuest)
        {
            
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            
            $this->render('variants', array());
        
        } else {
            $this->redirect('/cart/doorder/');
        }
    }
    
    public function actionDoOrder()
    {
        $data = $this->GetFormRequest();
        if (!empty($data)) {
            foreach ($data as $id=>$value) {
                $this->actionChangeQuantity(array_values($value));
            }
        }
        if (Yii::app()->user->isGuest)
        {
            $this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_PERSONAL_LOGIN') . ' / ' . Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION');
            //$this->render('login_or_register');
            $this->render('login_form');
            //$this->render('variants', array());
            return;
        }

        $cart = new Cart;
        $cartItems = array();
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);
        foreach($tmp as $item)
        {
            $item['Title'] = str_replace('"', '\\"', $item['Title']); // из-за того, что это идет в JSON в виде строки в do_order.php и ломает парсеру жизнь
            if($item['IsAvailable'])
                $cartItems[] = $item;
        }

        $u = new User;
        $addresses = $u->GetAddresses($this->uid);

        $this->breadcrumbs[Yii::app()->ui->item('A_LEFT_PERSONAL_SHOPCART')] = Yii::app()->createUrl('cart/view');
        $this->breadcrumbs[] = Yii::app()->ui->item('CREATE_ORDER');
        $this->render('do_order', array('cartItems' => $cartItems, 'addresses' => $addresses));
    }

    public function actionDoRequest($entity, $iid)
    {
        $entity = Entity::ParseFromString($entity);
        $iid = intVal($iid);

        $p = new Product();
        $product = $p->GetProduct($entity, $iid);

        if (empty($product)) throw new CHttpException(404);

        if (Yii::app()->user->isGuest)
        {
            $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_MY_REQUESTS');
            $this->render('login_or_register_with_request', array('product' => $product));
            return;
        }

        $r = new Request;
        $product['quantity'] = 1; // В заявке всегда 1 шт.
        $items = array($product);
        $rid = $r->CreateNewRequest($this->uid, $items, '');

        if (empty($rid)) throw new CException('REQUEST_DB_ERROR');

        $this->redirect(Yii::app()->createUrl('request/view', array('rid' => $rid)));
    }
    
    public function actionGetCount()
    {
        
        $cart = new Cart;
        
        $total_price = $cart->getPriceSum($this->uid, $this->sid, 3);
        
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid); //var_dump($tmp);
        $inCart = array();
        $endedItems = array();

        foreach($tmp as $item)
        {
            if($item['IsAvailable']) { $inCart[] = $item; }
        }
        
        $this->ResponseJson(array('countcart' => count($inCart), 'totalPrice' => $total_price));
    
    }
    
    public function actionGetAll($ajax = true)
    {
        $cart = new Cart;
        $isMiniCart = 0;
        if ($ajax) {
            $isMiniCart = Yii::app()->request->getParam('is_MiniCart', 0);
            $isMiniCart = intVal($isMiniCart);
        }
        $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid, $isMiniCart), $this->uid, $isMiniCart);
        
        $inCart = array();
        $endedItems = array();

        foreach($tmp as $item)
        {
            if($item['IsAvailable'])
                $inCart[] = $item;
            else
                $endedItems[] = $item;
        }

        if(!empty($endedItems))
        {
            $r = new Request;
            $ended = array();
            foreach($endedItems as $ei)
            {
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
    public function actionAdd($data = false)
    {
        if ($data === false) {
            $data = $this->GetRequest();
        }
        if($data === false)
            throw new CHttpException('Please do AJAX request');

        list($entity, $id, $quantity, $product, $origQuantity, $finOrWorld) = $data;
        $type = ProductHelper::IsAvailableForOrder($product) ? Cart::TYPE_ORDER : Cart::TYPE_REQUEST;

        if ($type == Cart::TYPE_REQUEST)
        {
            // TODO:
        }
        else
        {
            $cart = new Cart;
            $alreadyInCart = $cart->AddToCart($entity, $id, $quantity, $type, $this->uid, $this->sid, $finOrWorld);

            $message = $entity == Entity::PERIODIC
                        ? Yii::app()->ui->item('ADDED_TO_CART')
                        : sprintf(Yii::app()->ui->item('ADDED_TO_CART_ALREADY'), $alreadyInCart);

            $already = $entity == Entity::PERIODIC
                ? Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART')
                : sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $alreadyInCart);

            if (Yii::app()->request->isAjaxRequest)
                $this->ResponseJson(array('hasError' => false, 'msg' => $message, 'already' => $already));
            else
                $this->redirect(Yii::app()->createUrl('cart/view'));
        }
    }

    // Взять на заметку
    public function actionMark()
    {
        $data = $this->GetRequest();
        if($data === false)
            throw new CHttpException('Please do AJAX request');

        list($entity, $id, $quantity, $product) = $data;
        $cart = new Cart;
        if ($quantity == 0) $quantity = 1;
        $message = Yii::app()->ui->item('ADDED_TO_MARK');
        $ret = $cart->AddToCart($entity, $id, $quantity, Cart::TYPE_MARK, $this->uid, $this->sid, Cart::FIN_PRICE);
		
		$this->ResponseJson(array('hasError' => false, 'msg' => $message));
		
        //$this->ResponseJsonOk($ret);
    }

    public function actionChangeQuantity($data = false)
    {
        $returnJson = false;
        if ($data === false) {
            $data = $this->GetRequest();
            $returnJson = true;
        }
        if($data === false)
            throw new CHttpException('Please do AJAX request');

        list($entity, $id, $quantity, $product, $originalQuantity, $finOrWorldPrice) = $data;
        $type = ProductHelper::IsAvailableForOrder($product) ? Cart::TYPE_ORDER : Cart::TYPE_REQUEST;

        // Проверить на SKIP и InternetKolvo
        $p = new Product;
        $availCount = $p->IsQuantityAvailForOrder($entity, $id, $quantity);
        $changed = false;
        $changedStr = '';
        if($availCount != $originalQuantity)
        {
            $changed = true;

			if ($entity == Entity::PERIODIC) :

			
			
            
                $ie = $product['issues_year'];

                if ($ie < 12) {
                    $inOneMonth = $ie / 12;
                    $show3Months = false;
                    $show6Months = false;

                    $tmp1 = $inOneMonth * 3;
                    if (ctype_digit("$tmp1"))
                        $show3Months = true;
                    $tmp2 = $inOneMonth * 6;
                    if (ctype_digit("$tmp2"))
                        $show6Months = true;
                }
                else {
                    $show3Months = true;
                    $show6Months = true;
                }
				
				//var_dump($show3Months);
				//var_dump($show6Months);
				//var_dump($originalQuantity);
				
				if ($originalQuantity <= 3) {
					
					if ($show3Months) {
						$availCount = 3;
					} elseif($show6Months) {
						$availCount = 6;
					} else {
						$availCount = 12;
					}
					
				}
				
				if ($originalQuantity > 3 AND $originalQuantity <= 6) {
					
					if($show6Months) {
						$availCount = 6;
					} else {
						$availCount = 12;
					}
					
				}
				
				endif;


			$quantity = $availCount;
            $changedStr = sprintf(Yii::app()->ui->item('QUANTITY_CHANGED'), $originalQuantity, $quantity);
        }

        $cart = new Cart;
        $ret = $cart->ChangeQuantity($entity, $id, $quantity, $type, $this->uid, $this->sid, $finOrWorldPrice);
        if ($returnJson) {
            $this->ResponseJson(array('hasError' => false, 'quantity' => $ret, 'origQuantity' => $originalQuantity,
                'changedStr' => $changedStr,
                'changed' => $changed));
        }
    }

    public function actionRemove()
    {
        $entity = intVal(@$_POST['entity']);
        $iid = intVal(@$_POST['iid']);
        $type = intVal(@$_POST['type']);
        $types = array(Cart::TYPE_ORDER, Cart::TYPE_REQUEST);
        if (!in_array($type, $types)) $this->ResponseJsonError('WrongTypes');

        $cart = new Cart;
        $ret = $cart->Remove($entity, $iid, $type, $this->uid, $this->sid);
        $this->ResponseJson(array('hasError' => $ret == 0));
    }

    // Добавить в заявку
    public function actionRequest()
    {
        $data = $this->GetRequest();
        if($data === false)
            throw new CHttpException('Please do AJAX request');

        list($entity, $id, $quantity, $product) = $data;
        if (empty($product))
        {
            if (Yii::app()->request->isAjaxRequest) $this->ResponseJsonError('EmptyProduct');
            else throw new CHttpException(404);
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
        if (!empty($_GET['quantity'])&&is_array($_GET['quantity'])) $quantity = $_GET['quantity'];
        foreach ($quantity as $idItem=>$count) {
            $idItem = (int)$idItem;
            $count = max((int) $count, 1);
            if ($idItem > 0) {
                $result[$idItem] = array('entity'=>0, 'id'=>$idItem, 'quantity'=>$count, 'product'=>'', 'originalQuantity'=>$count, 'type'=>0);
            }
        }
        if (!empty($result)) {
            $entity = array();
            if (!empty($_GET['entity'])&&is_array($_GET['entity'])) $entity = $_GET['entity'];
            foreach ($entity as $idItem=>$id) {
                $idItem = (int)$idItem;
                $id = (int) $id;
                if (($idItem > 0)&&($id > 0)&&(isset($result[$idItem]))) {
                    if ($id == Entity::PERIODIC) {
                        if ($result[$idItem]['quantity'] != 6 && $result[$idItem]['quantity'] != 12 && $result[$idItem]['quantity'] != 0 && $result[$idItem]['quantity'] != 3) $result[$idItem]['quantity'] = 12;
                    }
                    $product = new Product();
                    $product = $product->GetBaseProductInfo($id, $idItem);
                    if (empty($product)) throw new CException('Wrong id');
                    $result[$idItem]['entity'] = $id;
                    $result[$idItem]['product'] = $product;
                }
                else throw new CException('Wrong id');
            }

            $type = array();
            if (!empty($_GET['type'])&&is_array($_GET['type'])) $type = $_GET['type'];
            $types = array(Cart::FIN_PRICE, Cart::WORLD_PRICE);
            foreach ($type as $idItem=>$id) {
                $idItem = (int)$idItem;
                $id = (int) $id;
                if(!in_array($id, $types)) $id = Cart::FIN_PRICE;
                if (($idItem > 0)&&(isset($result[$idItem]))) {
                    $result[$idItem]['type'] = $id;
                }
                else throw new CException('Wrong id');
            }
        }
        //return array($entity, $id, $quantity, $product, $originalQuantity, $type);
        return $result;
    }

    private function GetRequest()
    {
        $entity = 0;
        $id = 0;
        $quantity = 1;
        $type = Cart::FIN_PRICE;
        if (Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest)
        {
            $entity = intVal(@$_POST['entity']);
            $id = intVal(@$_POST['id']);
            $quantity = intVal(abs(@$_POST['quantity']));
            $originalQuantity = $quantity;
            $type = intVal(@$_POST['type']);
            $types = array(Cart::FIN_PRICE, Cart::WORLD_PRICE);
            if(!in_array($type, $types)) $type = Cart::FIN_PRICE;
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
        else
        {
            throw new CException('Wrong request');
        }

        if ($entity == Entity::PERIODIC)
        {
            if ($quantity != 6 && $quantity != 12 && $quantity != 0 && $quantity != 3) $quantity = 12;
        }

        if($originalQuantity <= 0) $originalQuantity = 1;
        if ($quantity <= 0) $quantity = 1;

        if (!Entity::IsValid($entity)) throw new CException('Wrong entity');
        $product = new Product();
        $product = $product->GetBaseProductInfo($entity, $id);
        if (empty($product)) throw new CException('Wrong id');
        return array($entity, $id, $quantity, $product, $originalQuantity, $type);
    }
}
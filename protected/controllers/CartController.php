<?php

class CartController extends MyController
{
    public function accessRules()
    {
        return array(array('allow',
                           'actions' => array('view', 'doorder', 'doorderjson', 'dorequest', 'getall', 'getcount', 'add', 'mark',
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
        $this->breadcrumbs[] = Yii::app()->ui->item('A_SHOPCART');
        $this->render('view');
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

    public function actionDoOrder()
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
    
    public function actionGetAll()
    {
        $cart = new Cart;
        $isMiniCart = Yii::app()->request->getParam('is_MiniCart', 0);
        $isMiniCart = intVal($isMiniCart);
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
        $this->ResponseJson($ret);
    }

    // Добавить в корзину
    public function actionAdd()
    {
        $data = $this->GetRequest();
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

    public function actionChangeQuantity()
    {
        $data = $this->GetRequest();
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
        $this->ResponseJson(array('hasError' => false, 'quantity' => $ret, 'origQuantity' => $originalQuantity,
            'changedStr' => $changedStr,
            'changed' => $changed));
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
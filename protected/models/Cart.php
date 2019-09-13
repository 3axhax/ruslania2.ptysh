<?php

function cmpCart($a, $b)
{
    return strcasecmp($a['Title'], $b['Title']);
}

class Cart extends CActiveRecord
{
    const ALREADY_IN_CART = 1;
    const ADDED_TO_CART = 2;

    // 250 грамм - 1 UnitWeight
    const UNITWEIGHT_VALUE = 250;

    const TYPE_ORDER = 1;
    const TYPE_REQUEST = 2;
    const TYPE_MARK = 3;

    const FIN_PRICE = 1;
    const WORLD_PRICE = 2;

    static private $_itemasByUser = array();

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'shopcarts';
    }

    protected function GetFilter($uid, $sid)
    {
        if (empty($uid))
        {
            $where = ' sidv2=:sid ';
            $params[':sid'] = $sid;
        }
        else
        {
            if(empty($sid))
            {
                $where = ' (uid=:uid) ';
                $params[':uid'] = $uid;
            }
            else
            {
                $where = ' (uid=:uid OR sidv2=:sid) ';
                $params[':uid'] = $uid;
                $params[':sid'] = $sid;
            }
        }

        return array($where, $params);
    }

    public static function CartType($type)
    {
        switch($type)
        {
            case self::TYPE_ORDER : return ' (is_suspended=0 AND is_ordered=0) ';
            case self::TYPE_REQUEST : return ' (is_suspended=0 AND is_ordered=1) ';
            case self::TYPE_MARK : return ' (is_suspended=1 AND is_ordered=0) ';
        }
    }

    function getCountInCart($entity, $id, $type, $uid, $sid) {
        $params = array(':entity' => Entity::ConvertToSite($entity), ':iid' => $id);
        $sql = 'SELECT SUM(quantity) FROM shopcarts '
            . 'WHERE entity=:entity AND iid=:iid AND '.self::CartType($type).' AND ';

        list($where, $p2) = $this->GetFilter($uid, $sid);
        $sql .= $where;

        return (int) Yii::app()->db->createCommand($sql)->queryScalar(array_merge($params, $p2));
    }

    public function AddToCart($entity, $id, $quantity, $type, $uid, $sid, $finOrWorldPrice, $checkCounts = true)
    {
        $params = array(':entity' => Entity::ConvertToSite($entity), ':iid' => $id);
        // Проверить, нет ли уже такого в корзине

        $cnt = 0;
        if ($checkCounts) {
            $cnt = $this->getCountInCart($entity, $id, $type, $uid, $sid);

            if ($cnt > 0) {
                $sql2 = 'DELETE FROM shopcarts '
                    . 'WHERE entity=:entity AND iid=:iid AND '.self::CartType($type).' AND ';

                list($where, $p2) = $this->GetFilter($uid, $sid);
                $sql2 .= $where;

                //удаляем товар с корзины и добавляем заново
                Yii::app()->db->createCommand($sql2)->query(array_merge($params, $p2));
            }
            else $cnt = 0;
        }

		//static::deleteAll(['iid'=>$id, 'uid'=>$uid, 'sidv2'=>$sid]);
		
		//if (!$cart) { $cart = new Cart; }
		
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/1.log', print_r($cnt, 1));
        $availCount = $cnt + $quantity;
        if (($entity == Entity::PERIODIC)&&($availCount > 12)) $availCount = 12;
		$cart = new Cart;
        $cart->entity = Entity::ConvertToSite($entity);
        $cart->iid = $id;
        $cart->uid = $uid;
        $cart->sidv2 = $sid;
        $cart->quantity = $availCount;
        $cart->type = $finOrWorldPrice;
		
        switch($type)
        {
            case self::TYPE_ORDER :
                $cart->is_suspended = 0;
                $cart->is_ordered = 0;
                break;
            case self::TYPE_REQUEST :
                $cart->is_suspended = 0;
                $cart->is_ordered = 1;
                break;
            case self::TYPE_MARK :
                $cart->is_suspended = 1;
                $cart->is_ordered = 0;
                break;
        }

        $cart->save(false);

        $alreadyInCart = 1;

        if($type == self::TYPE_ORDER)
        {

            $data = $this->GetShopcartData($uid, $sid, $type);
            foreach($data as $item)
            {
                if($item['entity'] == $entity && $item['id'] == $id)
                {
                    $alreadyInCart = $item['quantity'];
                }
            }
        }

        return $alreadyInCart;
    }

    public function BeautifyCart($cart, $uid, $isMiniCart = 0)
    {
        if (empty($cart) || !is_array($cart)) return array();

        $defaultAddress = Address::GetDefaultAddress($uid);
        $useVAT = Address::UseVAT($defaultAddress);

        //var_dump($useVAT);

        $ret = array();
        $entities = new Entity();
        foreach ($cart as $c)
        {
            $tmp['Entity'] = Entity::ConvertToHuman($c['entity']);
            $values = DiscountManager::GetPrice($uid, $c);
            $priceVAT = $values[DiscountManager::WITH_VAT];
            $priceVAT0 = $values[DiscountManager::WITHOUT_VAT];

            $priceVATFin = $values[DiscountManager::WITH_VAT_FIN];
            $priceVAT0Fin = $values[DiscountManager::WITHOUT_VAT_FIN];

            $priceVATWorld = $values[DiscountManager::WITH_VAT_WORLD];
            $priceVAT0World = $values[DiscountManager::WITHOUT_VAT_WORLD];

            $tmp = array();
            $tmp['noUseChangeQuantity'] = 0;
            $tmp['Entity'] = Entity::ConvertToHuman($c['entity']);
            $tmp['ID'] = $c['id'];
            if($tmp['Entity'] == Entity::PERIODIC)
            {
                $priceVAT = $priceVAT / 12;
                $priceVAT0 = $priceVAT0 / 12;
                $priceVATFin /= 12;
                $priceVAT0Fin /= 12;
                $priceVATWorld /= 12;
                $priceVAT0World /= 12;
                if (!empty($c['issues_year'])) {
                    $issues = Periodic::getCountIssues($c['issues_year']);
                    $tmp['noUseChangeQuantity'] = (int) (empty($issues['show3Months'])&&empty($issues['show6Months']));
                }
            }

			$tmp['Title'] = ProductHelper::GetTitle($c);
            $tmp['ISBN'] = '';
            if (!empty($c['isbn'])) $tmp['ISBN'] = preg_replace("/[^\dxх]/ui", '', $c['isbn']);
            elseif (!empty($c['eancode'])) $tmp['ISBN'] = preg_replace("/[^\dxх]/ui", '', $c['eancode']);
            if ($isMiniCart == 1) { $tmp['Title'] = ProductHelper::GetTitle($c, 'title', 38); }
			$tmp['PriceVAT'] = $priceVAT;
            $tmp['PriceVATStr'] = ProductHelper::FormatPrice($priceVAT);
            $tmp['PriceVAT0'] = $priceVAT0;
            $tmp['PriceVAT0Str'] = ProductHelper::FormatPrice($priceVAT0);

            $tmp['PriceVATFin'] = $priceVATFin;
            $tmp['PriceVATFinStr'] = ProductHelper::FormatPrice($priceVATFin);
            $tmp['PriceVAT0Fin'] = $priceVAT0Fin;
            $tmp['PriceVAT0FinStr'] = ProductHelper::FormatPrice($priceVAT0Fin);

            $tmp['PriceVATWorld'] = $priceVATWorld;
            $tmp['PriceVATWorldStr'] = ProductHelper::FormatPrice($priceVATWorld);
            $tmp['PriceVAT0World'] = $priceVAT0World;
            $tmp['PriceVAT0WorldStr'] = ProductHelper::FormatPrice($priceVAT0World);

            $tmp['Price2Use'] = intval($c['UseFinOrWorldPrice']); // Какую цену использовать для периодики
            $tmp['UseVAT'] = $useVAT;
            $tmp['Url'] = ProductHelper::CreateUrl($c);
            $tmp['Quantity'] = $c['quantity'];
            $tmp['UnitWeight'] = $c['InCartUnitWeight'] / 1000; // в кг.
            $tmp['IsAvailable'] = (ProductHelper::IsAvailableForOrder($c)||empty($c['out_of_print']));
            $tmp['Availability'] = Availability::GetStatus($c);
            $tmp['AvailablityText'] = Availability::ToStr($c);
            $tmp['DiscountPercent'] = $values[DiscountManager::DISCOUNT];
            $tmp['PriceOriginal'] = ProductHelper::FormatPrice($values[DiscountManager::ORIGINAl_PRICE]);

            if ($tmp['Entity'] == Entity::PERIODIC) {
                if ($tmp['Price2Use'] == 1) {
                    //фины
                    $tmp['PriceOriginal'] = ProductHelper::FormatPrice($values[DiscountManager::BRUTTO_FIN]/12 * $tmp['Quantity']);
                }
                else {
                    $tmp['PriceOriginal'] = ProductHelper::FormatPrice($values[DiscountManager::BRUTTO_WORLD]/12 * $tmp['Quantity']);
                }
            }

            $tmp['ReadyVAT'] = $values[DiscountManager::READY_EUR_PRICE_VAT];
            $tmp['ReadyVAT0'] = $values[DiscountManager::READY_EUR_PRICE_WITHOUT_VAT];
            $tmp['Rate'] = $values[DiscountManager::RATE];
            $tmp['VAT'] = $c['vat'];
            $tmp['InfoField'] = '';
            $tmp['Authors'] = '';
            if (!empty($c['Authors'])) {
                $authots = array();
                foreach ($c['Authors'] as $author) { $authots[] = ProductHelper::GetTitle($author); }
                $tmp['Authors'] = implode(', ', $authots);
            }
            $ret[] = $tmp;
        }
        if (!$isMiniCart)
        {
            uasort($ret, "cmpCart");
        }

        return $ret;
    }

    public function ChangeQuantity($entity, $id, $quantity, $type, $uid, $sid, $finOrWorldPrice)
    {
/*
        $params = array(':entity' =>  Entity::ConvertToSite($entity),
                        ':iid' => $id,
                        ':sid' => $sid,
                        ':quantity' => $quantity);
*/

        $this->Remove($entity, $id, $type, $uid, $sid);
        $this->AddToCart($entity, $id, $quantity, $type, $uid, $sid, $finOrWorldPrice);

        return $quantity;

/*
        list($where, $p) = $this->GetFilter($uid, $sid);

        $sql = 'UPDATE shopcarts SET quantity=:quantity '
            . 'WHERE (entity=:entity AND iid=:iid AND '.self::CartType($type).') AND '
            .$where;

        $params = array_merge($params, $p);

        $cnt = Yii::app()->db->createCommand($sql)->execute($params);
        if ($cnt > 0) return $quantity;

        $sql = 'SELECT quantity FROM shopcarts '
            . 'WHERE (entity=:entity AND iid=:iid AND '.self::CartType($type).') AND '
            .$where;

        unset($params[':quantity']);
        return Yii::app()->db->createCommand($sql)->queryScalar($params);
*/
    }

    public function GetShopcartData($uid, $sid, $type, $isMiniCart = 0)
    {
        $key = md5(serialize(array($uid, $sid, $type, $isMiniCart)));
        if (!isset(self::$_itemasByUser[$key])) {
            $sql = 'SELECT * FROM shopcarts USE INDEX ( sidv2idx, uid ) '
                .'WHERE '.self::CartType($type).' AND ';
            list($where, $params) = $this->GetFilter($uid, $sid);
            $sql .= $where;
            if ($isMiniCart)
                $sql .= ' ORDER BY iid DESC';


            //var_dump($sql);
            //var_dump($params);


            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
            $ret = array();
            $data = array();
            $ids = array();
            //var_dump($sql);
            //var_dump($rows);
            //CVarDumper::dump($rows, 10, true);
            if (!$isMiniCart)
            {
                foreach ($rows as $row)
                {
                    $entity = $row['entity'];
                    $iid = $row['iid'];
                    if(!isset($data[$entity][$iid]))
                        $data[$entity][$iid] = array('id' => $iid, 'quantity' => $row['quantity'], 'type' => $row['type']);
                    else
                        $data[$entity][$iid]['quantity'] += $row['quantity'];
                    //            $data[$row['entity']][$row['iid']] = array('id' => $row['iid'], 'quantity' => $row['quantity']);
                    $ids[$row['entity']][] = $row['iid'];
                }
                $p = new Product;
                //CVarDumper::dump($data, 10, true);
                foreach ($data as $entity => $rows)
                {
                    $result = $p->GetProducts($entity, $ids[$entity], $isMiniCart);
                    //CVarDumper::dump($ids[$entity], 10, true);
                    foreach ($result as $iid => $r)
                    {
                        $product = array_merge($r, $data[$entity][$iid]);
                        // UnitWeight

                        if (empty($r['unitweight'])) {
                            $product['FullUnitWeight'] = 0;
                            $product['InCartUnitWeight'] = 0;
                        }
                        else {
                            $product['FullUnitWeight'] = $data[$entity][$iid]['quantity'] * $r['unitweight'] * self::UNITWEIGHT_VALUE;
                            $product['InCartUnitWeight'] = $product['FullUnitWeight'] * ($r['unitweight_skip'] == 1 ? 0 : 1);
                        }
                        $product['UseFinOrWorldPrice'] = $data[$entity][$iid]['type'];

                        $ret[] = $product;
                    }
                }
            }
            else
            {
                $flag = 0;
                $tmp_data = array();
                foreach ($rows as $row)
                {
                    $entity = $row['entity'];
                    $iid = $row['iid'];
                    if(!isset($data[$entity][$iid]))
                    {
                        $data[$entity][$iid] = array('id' => $iid, 'quantity' => $row['quantity'], 'type' => $row['type']);
                        $flag = 1;
                    }
                    else
                        $data[$entity][$iid]['quantity'] += $row['quantity'];
                    //            $data[$row['entity']][$row['iid']] = array('id' => $row['iid'], 'quantity' => $row['quantity']);
                    $ids[$row['entity']][] = $row['iid'];
                    $tmp_data[0] = $row['iid'];

                    $p = new Product;
                    //CVarDumper::dump($data, 10, true);
                    if ($flag)
                    {

                        $result = $p->GetProducts($entity, $tmp_data, $isMiniCart);
                        //CVarDumper::dump($ids[$entity], 10, true);
                        foreach ($result as $iid => $r)
                        {
                            $product = array_merge($r, $data[$entity][$iid]);
                            // UnitWeight

                            $product['FullUnitWeight'] = $data[$entity][$iid]['quantity'] * $r['unitweight'] * self::UNITWEIGHT_VALUE;
                            $product['InCartUnitWeight'] = $product['FullUnitWeight'] * ($r['unitweight_skip'] == 1 ? 0 : 1);
                            $product['UseFinOrWorldPrice'] = $data[$entity][$iid]['type'];

                            $ret[] = $product;
                        }

                        $flag = 0;
                    }
                }
            }

            self::$_itemasByUser[$key] = $ret;
            //var_dump($rows);
        }

        return self::$_itemasByUser[$key];
    }


    public function GetCart($uid, $sid, $isMiniCart = 0)
    {
        return $this->GetShopcartData($uid, $sid, Cart::TYPE_ORDER, $isMiniCart);
    }

    public function GetEndedItems($uid, $sid)
    {
        return $this->GetShopcartData($uid, $sid, Cart::TYPE_ORDER);
    }

    public function GetMark($uid, $sid)
    {
        return $this->GetShopcartData($uid, $sid, Cart::TYPE_MARK);
    }

    public function GetRequest($uid, $sid)
    {
        return $this->GetShopcartData($uid, $sid, Cart::TYPE_REQUEST);
    }

    public function UpdateCartToUid($sid, $uid)
    {
        $sql = 'UPDATE shopcarts SET uid=:uid WHERE sidv2=:sid';
        Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid, ':sid' => $sid));
    }

    public function ClearCart($uid, $items, $type=Cart::TYPE_ORDER)
    {
        $sql = 'DELETE FROM shopcarts WHERE uid=:uid AND '.self::CartType($type).' AND entity=:entity AND iid=:iid';

        foreach($items as $item)
        {
            Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid,
                                                               ':entity' => Entity::ConvertToSite($item['entity']),
                                                               ':iid' => $item['id']));
        }
    }

    public function ChangeMemo($action, $entity, $iid, $uid, $sid)
    {
        if($action == 'delete')
        {
            list($where, $params) = $this->GetFilter($uid, $sid);
            $sql = 'DELETE FROM shopcarts WHERE '.$where.' AND '.$this->CartType(Cart::TYPE_MARK).' '
                  .'AND entity=:entity AND iid=:iid';
            $params[':entity'] = Entity::ConvertToSite($entity);
            $params[':iid'] = $iid;
            $cnt = Yii::app()->db->createCommand($sql)->execute($params);
            return $cnt;
        }
        else
        {
            $p = new Product;
            $item = $p->GetProduct($entity, $iid);
            if(empty($item)) return 0;

            $type = ProductHelper::IsAvailableForOrder($item) ? self::TYPE_ORDER : self::TYPE_REQUEST;

            $transaction = Yii::app()->db->beginTransaction();

            if($type == self::TYPE_ORDER)
            {
                $this->AddToCart($entity, $iid, 1, $type, $uid, $sid, 1);
            }
            else
            {
                $items = array(
                    array('entity' => $entity,
                          'id' => $iid,
                          'quantity' => 1
                    )
                );
                $r = new Request;
                $r->CreateNewRequest($uid, $items, '');
            }

            try
            {
                list($where, $params) = $this->GetFilter($uid, $sid);
                $sql = 'DELETE FROM shopcarts WHERE '.$where.' AND '.$this->CartType(Cart::TYPE_MARK).' '
                    .'AND entity=:entity AND iid=:iid';
                $params[':entity'] = Entity::ConvertToSite($entity);
                $params[':iid'] = $iid;
                Yii::app()->db->createCommand($sql)->execute($params);
                $transaction->commit();
                return 1;
            }
            catch(Exception $ex)
            {
                CommonHelper::LogException($ex, 'Failed to change memo');
                $transaction->rollback();
                return 0;
            }
        }
    }

    public function Remove($entity, $iid, $type, $uid, $sid)
    {
        $entity = Entity::ConvertToSite($entity);
        list($where, $params) = $this->GetFilter($uid, $sid);
        $sql = 'DELETE FROM shopcarts WHERE '.$where.' AND '.$this->CartType($type).' '
            .'AND entity=:entity AND iid=:iid';
        $params[':entity'] = $entity;
        $params[':iid'] = $iid;
        $cnt = Yii::app()->db->createCommand($sql)->execute($params);
        return $cnt;
    }
    
	function isMark($entity, $iid, $type, $uid, $sid) {
		
		$entity = Entity::ConvertToSite($entity);
        list($where, $params) = Cart::GetFilter($uid, $sid);
        $sql = 'SELECT * FROM shopcarts WHERE '.$where.' AND '.Cart::CartType($type).' '
            .'AND entity=:entity AND iid=:iid';
        $params[':entity'] = $entity;
        $params[':iid'] = $iid;
		
		$rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
		
		if ($rows[0] != '') {
			return true;
		}
		
		return false;
		
	}
	
    function getPriceSum($uid, $sid, $type) {
        
        $sql = 'SELECT * FROM shopcarts USE INDEX ( sidv2, uid ) '
              .'WHERE ' . $this->CartType(self::TYPE_ORDER) .' AND ';
        list($where, $params) = $this->GetFilter($uid, $sid);
        $sql .= $where;

        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);

        $defaultAddress = Address::GetDefaultAddress(Yii::app()->user->id);
        $useVAT = Address::UseVAT($defaultAddress);
                // var_dump($rows);
        
        $priceSum = 0;
        $summa = 0; 
		
		//var_dump($uid);
		//var_dump($sid);

        foreach ($rows as $row) {
            
            $item = Product::GetProduct($row['entity'], $row['iid']);
            
			$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
			
			if (!empty($price[DiscountManager::DISCOUNT])) :
			$summa = ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) * $row['quantity'];
			else :
			$summa = ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) * $row['quantity'];
			endif;
			
			if ($item['entity'] == 30) {



			    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/protected/runtime/1.log', print_r($item,1));
			    //file_put_contents($_SERVER['DOCUMENT_ROOT'].'/protected/runtime/2.log', print_r($price,1));

                $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);

                $priceVATFin = $price[DiscountManager::WITH_VAT_FIN];
                $priceVAT0Fin = $price[DiscountManager::WITHOUT_VAT_FIN];

                $priceVATWorld = $price[DiscountManager::WITH_VAT_WORLD];
                $priceVAT0World = $price[DiscountManager::WITHOUT_VAT_WORLD];

                //echo $useVAT;

                if ($useVAT) {

                    $s_one1 = $priceVATFin;
                    $s_one2 = $priceVATWorld;

                } else {

                    $s_one1 = $priceVAT0Fin;
                    $s_one2 = $priceVAT0World;

                }

			    if ($item['type'] == 2) {
			        $s_one = $s_one2 / 12;
                } else {
                    $s_one = $s_one1 / 12;
                }
			    
				//$s_one = $price[DiscountManager::WITH_VAT] / 12;
				
				if (!empty($price[DiscountManager::DISCOUNT])) :
				$summa = $s_one * $row['quantity'];
				else :
				$summa = $s_one * $row['quantity'];
				endif;
			}
			
            //$ui = Yii::app()->ui;
            $priceSum += $summa;
            
            
        }
		
        return ($priceSum == 0) ? '0 '.Currency::ToSign(Yii::app()->currency) : ProductHelper::FormatPrice($priceSum);
    }
    
    public function cart_getpoints_smartpost($index = 0, $country = 'FI') {
        $file = file_get_contents('https://locationservice.posti.com/location?types=SMARTPOST&types=PICKUPPOINT&countryCode='.$country.'&locationZipCode='.$index.'&top=10');
    
        $arr = json_decode($file, true);
        
        return $arr['locations'];
    }
    
    function getCountCartItem($item, $entity, $uid, $sid) {
		
		$c = new Cart;
        $cart = $c->GetCart($uid, $sid);
        $count = 0;
       // foreach ($items as $idx => $item) {
            foreach ($cart as $cartItem) {
                if ($cartItem['entity'] == $entity && $cartItem['id'] == $item) {
                    $count = $cartItem['quantity'];
                }
            }
       // }
        return $count;
		
	}
	
	public static function CreateUrl($item, $lang = null)
    {
        if ($item === false) return '';
        
        if (!empty($lang)&&($lang !== Yii::app()->language)&&!defined('OLD_PAGES')) $params['__langForUrl'] = $lang;
        return Yii::app()->createUrl('cart/view', $params);
    }

    function checkQuantity($entity, $id, $quantity, $product, $originalQuantity) {
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
            if ($entity != 30) $changedStr = sprintf(Yii::app()->ui->item('COUNTS_IN_ORDER_MAX'), $quantity, $quantity);
            elseif (!empty($originalQuantity)) {
                $changedStr = Yii::app()->ui->item('REAL_PEREODIC_COUNTS');
            }
        }
        return array(
            $quantity,
            $originalQuantity,
            $changed,
            $changedStr,
        );
    }
	
}
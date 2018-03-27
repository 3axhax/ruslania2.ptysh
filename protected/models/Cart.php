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

    public function AddToCart($entity, $id, $quantity, $type, $uid, $sid, $finOrWorldPrice)
    {
        $params = array(':entity' => Entity::ConvertToSite($entity), ':iid' => $id);
        // Проверить, нет ли уже такого в корзине

        $sql = 'SELECT SUM(quantity) FROM shopcarts '
            . 'WHERE entity=:entity AND iid=:iid AND '.self::CartType($type).' AND ';

        list($where, $p2) = $this->GetFilter($uid, $sid);        
		$sql .= $where;

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array_merge($params, $p2));        
		
		$sql2 = 'DELETE FROM shopcarts '
            . 'WHERE entity=:entity AND iid=:iid AND '.self::CartType($type).' AND ';

        list($where, $p2) = $this->GetFilter($uid, $sid);        
		$sql2 .= $where;
		
		//удаляем товар с корзины и добавляем заново
        Yii::app()->db->createCommand($sql2)->query(array_merge($params, $p2));
			
		//static::deleteAll(['iid'=>$id, 'uid'=>$uid, 'sidv2'=>$sid]);
		
		//if (!$cart) { $cart = new Cart; }
		
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/1.log', print_r($cnt, 1));
		
		$cart = new Cart;
        $cart->entity = Entity::ConvertToSite($entity);
        $cart->iid = $id;
        $cart->uid = $uid;
        $cart->sidv2 = $sid;
        $cart->quantity = $cnt + $quantity;
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

            if($tmp['Entity'] == Entity::PERIODIC)
            {
                $priceVAT = $priceVAT / 12;
                $priceVAT0 = $priceVAT0 / 12;
                $priceVATFin /= 12;
                $priceVAT0Fin /= 12;
                $priceVATWorld /= 12;
                $priceVAT0World /= 12;
            }

            $tmp = array();
            $tmp['Entity'] = Entity::ConvertToHuman($c['entity']);
            $tmp['ID'] = $c['id'];
			$tmp['Title'] = ProductHelper::GetTitle($c);
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
            $tmp['IsAvailable'] = ProductHelper::IsAvailableForOrder($c);
            $tmp['Availability'] = Availability::GetStatus($c);
            $tmp['AvailablityText'] = Availability::ToStr($c);
            $tmp['DiscountPercent'] = $values[DiscountManager::DISCOUNT];
            $tmp['PriceOriginal'] = ProductHelper::FormatPrice($values[DiscountManager::ORIGINAl_PRICE]);
            $tmp['ReadyVAT'] = $values[DiscountManager::READY_EUR_PRICE_VAT];
            $tmp['ReadyVAT0'] = $values[DiscountManager::READY_EUR_PRICE_WITHOUT_VAT];
            $tmp['Rate'] = $values[DiscountManager::RATE];
            $tmp['VAT'] = $c['vat'];
            $tmp['InfoField'] = '';
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
        $sql = 'SELECT * FROM shopcarts USE INDEX ( sidv2idx, uid ) '
              .'WHERE '.self::CartType($type).' AND ';
        list($where, $params) = $this->GetFilter($uid, $sid);
        $sql .= $where;
        if ($isMiniCart) 
            $sql .= ' ORDER BY last_date DESC';
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
    
                    $product['FullUnitWeight'] = $data[$entity][$iid]['quantity'] * $r['unitweight'] * self::UNITWEIGHT_VALUE;
                    $product['InCartUnitWeight'] = $product['FullUnitWeight'] * ($r['unitweight_skip'] == 1 ? 0 : 1);
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
        
        
        return $ret;
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
    
    function getPriceSum($uid, $sid, $type) {
        
        $sql = 'SELECT * FROM shopcarts USE INDEX ( sidv2, uid ) '
              .'WHERE ';
        list($where, $params) = $this->GetFilter($uid, $sid);
        $sql .= $where;

        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
        
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
				
				$s_one = $price[DiscountManager::WITH_VAT] / 12;
				
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
    
    
}
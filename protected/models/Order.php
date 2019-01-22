<?php

class Order extends CMyActiveRecord
{
    public $check;
    private $_promocode = null; //здесь должно быть ид промокода

    public static function HavePaidState($states)
    {
        if (!is_array($states)) return false;

        foreach ($states as $state)
        {
            if ($state['state'] == OrderState::PaymentConfirmation) return true;
            if ($state['state'] == OrderState::AutomaticPaymentConfirmation) return true;
        }
        return false;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users_orders';
    }

    public function rules()
    {
        return array(

            array('uid, delivery_address_id, billing_address_id, delivery_type_id, payment_type_id, currency_id, '
                  . 'is_reserved, full_price, items_price, delivery_price, mandate, check', 'required', 'on' => 'newinternet'),
            array('notes', 'safe', 'on' => 'newinternet')
        );
    }

    public function relations()
    {
        return array(
            'items' => array(self::HAS_MANY, 'OrderItem', array('oid' => 'id')),
            'states' => array(self::HAS_MANY, 'OrderState', array('oid' => 'id')),
            'deliveryAddress' => array(self::BELONGS_TO, 'Address', array('delivery_address_id' => 'id')),
            'billingAddress' => array(self::BELONGS_TO, 'Address', array('billing_address_id' => 'id')),
        );
    }

    public function GetOrders($uid)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 't.uid=:uid';
        $criteria->params = array(':uid' => $uid);
        $criteria->order = 't.id DESC';
        $criteria->limit = 2000;
        $list = Order::model()->with('items', 'states',
            'billingAddress', 'billingAddress.billingCountry',
            'deliveryAddress', 'deliveryAddress.deliveryCountry')->findAll($criteria);
		
		//var_dump($list);
		
        $list = $this->FlatOrderList($list);

        return $list;
    }

    private function FlatOrderList($list)
    {
        $ret = array();
        $items = array();
        $itemsData = array();

        // Собрать все ID товаров что бы выбрать инфу по ним 1 запросом
        foreach ($list as $order)
            foreach ($order->items as $item)
            {
                $e = Entity::ConvertToHuman($item['entity']);
                $items[$e][] = $item['iid'];
            }

        $p = new Product;
		
		

        foreach ($items as $entity => $ids)
        {
			
			$result = $p->getProducts3($entity, $ids);
			
            foreach ($result as $item)
                $itemsData[$entity][$item['id']] = $item;
        }

        // Теперь привести список заказов в вид массива
        foreach ($list as $order)
        {
            $ord = $order->attributes;


            $billingAddress = isset($order->billingAddress) ? $order->billingAddress->attributes : array();
            $ord['BillingAddress'] = $billingAddress;
            if(!empty($order->billingAddress))
            {
                $ord['BillingAddress']['country_name'] = $order->billingAddress->billingCountry->attributes['title_en'];
            }

            $deliveryAddress = isset($order->deliveryAddress) ? $order->deliveryAddress->attributes : array();
            $ord['DeliveryAddress'] = $deliveryAddress;
            if(!empty($order->deliveryAddress))
            {
                $ord['DeliveryAddress']['country_name'] = $order->deliveryAddress->deliveryCountry->attributes['title_en'];
            }

            $items = array();
            foreach ($order->items as $item)
            {
                $entity = Entity::ConvertToHuman($item['entity']);
                $iid = $item['iid'];
                $row = false;

                if (isset($itemsData[$entity][$iid]))
                {
                    $row = array_merge($item->attributes, $itemsData[$entity][$iid]);
                }
                $items[] = $row;
            }

            $ord['Items'] = $items;

            foreach ($order->states as $state)
            {
                $ord['States'][] = $state->attributes;
            }

            $ret[] = $ord;
        }

        return $ret;
    }

    public function GetOrder($oid)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 't.id=:oid';
        $criteria->params = array(':oid' => $oid);
        $list = Order::model()->with('items', 'states',
            'billingAddress', 'billingAddress.billingCountry',
            'deliveryAddress', 'deliveryAddress.deliveryCountry')->together()->findAll($criteria);

        $list = $this->FlatOrderList($list);

        if (!empty($list)) {
            usort($list[0]['States'], function($a, $b){ if ($a['timestamp'] == $b['timestamp']) return 0; return ($a['timestamp'] < $b['timestamp']) ? -1 : 1; });
            return $list[0];
        }
        return false;
    }

    /** для подсчета стоимости заказа, без учета промокодов
     * @param $uid
     * @param $sid
     * @param $items array товары
     * @param $address array -это адрес доставки с данными пользователя (Address::GetAddress), но можно просто передать страну (если пользователь не авторизирован). Адрес нужен потому, что ндс зависит от кода предприятия
     * @param $deliveryMode int 0 - считаю стоимость доставки, 1 - несчитаю стоимость доставки
     * @param $deliveryTypeID int - тип доставки
     * @return array [стоимостьТоваров, стоимостьДоставки, [товар=>стоимостьТовара], [товар=>ключи для DiscountManager::GetPrice]]
     */
    function getOrderPrice($uid, $sid, $items, $address, $deliveryMode, $deliveryTypeID, $currencyId = null) {
        if (empty($address)&&!empty($uid)) $address = Address::GetDefaultAddress($uid);
        if ($currencyId === null) $currencyId = Yii::app()->currency;
        $withVAT = Address::UseVAT($address);
        $itemsPrice = 0;
        $pricesValues = array();
        $discountKeys = array();//нужно для получения цены по промокоду, сюда же положил количество товара, что бы не создавать новую переменную
        foreach ($items as $idx=>$item) {
            $values = DiscountManager::GetPrice($uid, $item);
            $key = $withVAT ? DiscountManager::WITH_VAT : DiscountManager::WITHOUT_VAT;
            $keyWithoutDiscount = DiscountManager::BRUTTO;
            $itemKey = $item['entity'].'_'.$item['id'];
            $price = $values[$key];
            if($item['entity'] == Entity::PERIODIC) {
                if($address['code'] == 'FI') {
                    $key = $withVAT ? DiscountManager::WITH_VAT_FIN : DiscountManager::WITHOUT_VAT_FIN;
                    $keyWithoutDiscount = DiscountManager::BRUTTO_FIN;
                }
                else {
                    $key = $withVAT ? DiscountManager::WITH_VAT_WORLD : DiscountManager::WITHOUT_VAT_WORLD;
                    $keyWithoutDiscount = DiscountManager::BRUTTO_WORLD;

                }
                $price = $values[$key];
                $price /= 12;
            }
            $pricesValues[$itemKey] = $price;
            $discountKeys[$itemKey] = ['discountPrice'=>$key, 'originalPrice'=>$keyWithoutDiscount, 'quantity'=>$item['quantity']];
            $itemsPrice += $item['quantity'] * $price;

            if($values[DiscountManager::DISCOUNT_TYPE] != DiscountManager::TYPE_NO_DISCOUNT)
            {
                $items[$idx]['info'] = DiscountManager::ToStr($values[DiscountManager::DISCOUNT_TYPE]).': '.$values[DiscountManager::DISCOUNT].'%';
            }
        }

        if ($deliveryMode == 0) {
            $p = new PostCalculator();
            $list = $p->GetRates(0, $uid, $sid, isset($address['country'])?$address['country']:$address['id']);
            $deliveryPrice = false;
            foreach ($list as $l)
                if ($l['id'] == $deliveryTypeID) $deliveryPrice = $l['value'];
        }
        else $deliveryPrice = 0;

        $rates = Currency::GetRates();
        $rate = $rates[$currencyId];
        $minOrderPrice = Yii::app()->params['OrderMinPrice'] * $rate;
        if($itemsPrice < $minOrderPrice) $itemsPrice = $minOrderPrice;

        return [$itemsPrice, $deliveryPrice, $pricesValues, $discountKeys];
    }

    public function CreateNewOrder($uid, $sid, OrderForm $order, $items, $ptype)
    {
        $transaction = Yii::app()->db->beginTransaction();
        $a = new Address();
        $da = $a->GetAddress($uid, $order->DeliveryAddressID);
        list($itemsPrice, $deliveryPrice, $pricesValues, $discountKeys) = $this->getOrderPrice($uid, $sid, $items, $da, $order->DeliveryMode, $order->DeliveryTypeID, $order->CurrencyID);


/*        $withVAT = Address::UseVAT($da);
            
        $itemsPrice = 0;
        $pricesValues = array();
        foreach ($items as $idx=>$item)
        {
            $values = DiscountManager::GetPrice($uid, $item);
            $key = $withVAT ? DiscountManager::WITH_VAT : DiscountManager::WITHOUT_VAT;
            $itemKey = $item['entity'].'_'.$item['id'];
            $price = $values[$key];
            if($item['entity'] == Entity::PERIODIC)
            {
                if($da['is_finland'])
                    $key = $withVAT ? DiscountManager::WITH_VAT_FIN : DiscountManager::WITHOUT_VAT_FIN;
                else
                    $key = $withVAT ? DiscountManager::WITH_VAT_WORLD : DiscountManager::WITHOUT_VAT_WORLD;
                $price = $values[$key];
                $price /= 12;
            }
            $pricesValues[$itemKey] = $price;
            $itemsPrice += $item['quantity'] * $price;

            if($values[DiscountManager::DISCOUNT_TYPE] != DiscountManager::TYPE_NO_DISCOUNT)
            {
                $items[$idx]['info'] = DiscountManager::ToStr($values[DiscountManager::DISCOUNT_TYPE]).': '.$values[DiscountManager::DISCOUNT].'%';
            }
        }

        if ($order->DeliveryMode == 0)
        {
            $p = new PostCalculator();
            $list = $p->GetRates($order->DeliveryAddressID, $uid, $sid);
            
            //var_dump($list);
            
            $deliveryPrice = false;
            foreach ($list as $l)
                if ($l['id'] == $order->DeliveryTypeID) $deliveryPrice = $l['value'];
        }
        else
        {
            $deliveryPrice = 0;
        }

        $rates = Currency::GetRates();
        $rate = $rates[$order->CurrencyID];
        $minOrderPrice = Yii::app()->params['OrderMinPrice'] * $rate;
        if($itemsPrice < $minOrderPrice) $itemsPrice = $minOrderPrice;
        $fullPrice = $itemsPrice + $deliveryPrice;*/


        $promocodeId = 0;
        if (empty($this->_promocode)) $fullPrice = $itemsPrice + $deliveryPrice;
        else {
            $promocode = Promocodes::model();
            $code = $promocode->getPromocode($this->_promocode)['code'];
            $fullPrice = $promocode->getTotalPrice($code, $itemsPrice, $deliveryPrice, $pricesValues, $discountKeys);
            $promocodeId = $this->_promocode;
        }

        try
        {
            $sql = 'INSERT INTO users_orders (uid, delivery_address_id, billing_address_id, delivery_type_id, '
                . 'payment_type_id, currency_id, is_reserved, full_price, items_price, delivery_price, notes, mandate, promocode_id) VALUES '
                . '(:uid, :daid, :baid, :dtid, :ptid, :cur, :isres, :full, :items, :delivery, :notes, :mandate, :promocodeId)';

            Yii::app()->db->createCommand($sql)->execute(
                array(':uid' => $uid,
                      ':daid' => $order->DeliveryAddressID,
                      ':baid' => $order->BillingAddressID,
                      ':dtid' => $order->DeliveryTypeID,
                      ':ptid' => $ptype, // payment in next step
                      ':cur' => $order->CurrencyID,
                      ':isres' => $order->DeliveryMode == 1 ? 1 : 0, // 1 - выкуп в магазине
                      ':full' => $fullPrice,
                      ':items' => $itemsPrice,
                      ':delivery' => $deliveryPrice,
                      ':notes' => $order->Notes,
                      ':mandate' => $order->Mandate,
                      ':promocodeId' => $promocodeId,
                ));

            $orderID = Yii::app()->db->lastInsertID;

            if (($orderID > 0)&&!empty($this->_promocode)) {
                if ($fullPrice == 0) $this->AddStatus($orderID, OrderState::AutomaticPaymentConfirmation);
                $promocode = Promocodes::model();
                $promocode->used($this->_promocode);
            }

            // NOTE: calculate order invoice reference number
            // Что такое refnumber историкам выяснить не удалось
            // Код ниже тупо скопипащен со старой версии сайта "создание заказа"
            $weight = 0;
            $invoiceId = (string)($orderID);
            $sz = strlen($invoiceId);
            $weights = 0;

            for ($i = 0; $i < $sz; $i++)
            {
                $digit = $invoiceId{$sz - 1 - $i};
                if ($weight == 7) $weight = 3;
                elseif ($weight == 3) $weight = 1;
                else $weight = 7;
                $weights += $digit * $weight;
            }
            $refNumber = $invoiceId . (ceil($weights / 10) * 10 - $weights);
            $sql = 'UPDATE users_orders SET invoice_refnum=:ref WHERE id=:id LIMIT 1';
            Yii::app()->db->createCommand($sql)->execute(array(':ref' => $refNumber, ':id' => $orderID));


            // Добавить статус
            $sql = 'INSERT INTO users_orders_states (oid, state, `timestamp`) VALUES (:oid, :state, CURRENT_TIMESTAMP())';
            Yii::app()->db->createCommand($sql)->execute(array(':oid' => $orderID,
                                                               ':state' => OrderState::SavedInSystem));

            // Добавить товары
            $sql = 'INSERT INTO users_orders_items (oid, entity, iid, quantity, items_price, price, info) VALUES '
                . '(:oid, :entity, :iid, :quantity, :subtotal, :price, :info)';

            foreach ($items as $item)
            {
                $itemKey = $item['entity'].'_'.$item['id'];
                $price = $pricesValues[$itemKey];

                Yii::app()->db->createCommand($sql)->execute(
                    array(
                         ':oid' => $orderID,
                         ':entity' => Entity::ConvertToSite($item['entity']),
                         ':iid' => $item['id'],
                         ':quantity' => $item['quantity'],
                         ':subtotal' => $item['quantity'] * $price,
                         ':price' => $price,
                         ':info' => empty($item['info']) ? null : $item['info']
                    ));
            }

            // очистить корзину на эти товары
            $c = new Cart;
            $c->ClearCart($uid, $items);

            $transaction->commit();
            return $orderID;
        }
        catch (Exception $ex)
        {
            CommonHelper::LogException($ex, 'Failed to create order');
            $transaction->rollback();
            
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/protected/runtime/21212.txt', print_r($ex,1));
            
            return 0;
        }
    }

    public function ChangeOrderPaymentType($uid, $oid, $type)
    {
        $payments = Payment::GetPaymentList();
        $ok = false;
        foreach($payments as $payment) if($payment['id'] == $type) $ok = true;

        if(!$ok) return;

        $sql = 'UPDATE users_orders SET payment_type_id=:type WHERE uid=:uid AND id=:id LIMIT 1';
        Yii::app()->db->createCommand($sql)->execute(array(':type' => $type, ':uid' => $uid, ':id' => $oid));
    }

    public function AddStatus($oid, $state)
    {
        $params = array(':oid' => $oid, ':state' => $state);

        $sql = 'SELECT COUNT(*) FROM users_orders_states WHERE oid=:oid AND state=:state';
        $already = Yii::app()->db->createCommand($sql)->queryScalar($params);

        if($already > 0) return -1;

        $sql = 'INSERT INTO users_orders_states (oid, state) VALUES (:oid, :state)';
        $cnt = Yii::app()->db->createCommand($sql)->execute($params);
        return $cnt;
    }

    public function RegenerateOrder($oid)
    {
        $oid = intVal($oid);
        $transaction = Yii::app()->db->beginTransaction();

        $sql = 'INSERT INTO users_orders '
            .'(uid, delivery_address_id, billing_address_id, delivery_type_id, payment_type_id, currency_id, is_reserved, full_price, items_price, delivery_price, notes,  invoice_number, invoice_number_2, invoice_for_pereodics, language_id, type_id, personnel) '
            .'SELECT uid, delivery_address_id, billing_address_id, delivery_type_id, payment_type_id, currency_id, is_reserved, full_price, items_price, delivery_price, notes,  invoice_number, invoice_number_2, invoice_for_pereodics, language_id, type_id, personnel '
            .'FROM users_orders WHERE id='.$oid;
        Yii::app()->db->createCommand($sql)->execute();
        $newOid = Yii::app()->db->lastInsertID;

        $sql = 'INSERT INTO users_orders_items (oid, entity, iid, quantity, items_price, price, myodbc_stub, info) '
            .'SELECT '.$newOid.' AS oid, entity, iid, quantity, items_price, price, myodbc_stub, info '
            .'FROM users_orders_items WHERE oid='.$oid;
        Yii::app()->db->createCommand($sql)->execute();

        $weight=0;
        $invoiceId = (string)($oid);
        $sz = strlen($invoiceId);
        $weights = 0;

        for($i = 0; $i < $sz; $i++)
        {
            $digit = $invoiceId{$sz - 1 - $i};
            if ($weight == 7) $weight = 3;
            elseif ($weight == 3) $weight = 1;
            else $weight = 7;
            $weights += $digit * $weight;
        }
        $refNumber = $invoiceId.(ceil($weights / 10) * 10 - $weights);
        $mandate = md5(uniqid(rand(), true));
        $sql = 'UPDATE users_orders SET invoice_refnum='.$refNumber.', mandate="'.$mandate.'" WHERE id='.$newOid.' LIMIT 1';
        Yii::app()->db->createCommand($sql)->execute();

        $sql = 'INSERT INTO users_orders_states(oid, state) '
            .'VALUES ('.$newOid.', '.OrderState::SavedInSystem.')';
        Yii::app()->db->createCommand($sql)->execute();
        // step 4 - add new state CLOSED and REGENERATED to old order
        $sql = 'INSERT INTO users_orders_states(oid, state) '
            .'VALUES ('.$oid.', '.OrderState::Cancelled.')';
        Yii::app()->db->createCommand($sql)->execute();

        $sql = 'INSERT INTO users_orders_states(oid, state) '
            .'VALUES ('.$oid.', '.OrderState::Regenerated.')';
        Yii::app()->db->createCommand($sql)->execute();

        $sql = 'INSERT INTO users_orders_states(oid, state) '
            .'VALUES ('.$newOid.', '.OrderState::Regenerated.')';
        Yii::app()->db->createCommand($sql)->execute();
        $transaction->commit();
        return $newOid;
    }

    function setPromocode($code) {
        $promocode = Promocodes::model();
        if ($promocode->check($promocode->getPromocodeByCode($code)) === 0) $this->_promocode = $promocode->getPromocodeByCode($code)['id'];
    }
}
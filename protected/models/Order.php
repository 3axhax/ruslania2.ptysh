<?php

class Order extends CMyActiveRecord
{
    public $check;
    private $_promocode = null, $_certificate = null; //здесь должно быть ид промокода

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

            array('hide_edit_order,hide_edit_payment, uid, delivery_address_id, billing_address_id, delivery_type_id, payment_type_id, currency_id, '
                  . 'is_reserved, full_price, items_price, delivery_price, mandate, check', 'required', 'on' => 'newinternet', ),
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

    public function GetOrders($uid, $orderIds = array())
    {
        $criteria = new CDbCriteria;
        $criteria->condition = ''.
            '(t.uid=:uid)'.
            (empty($orderIds)?'':' and (t.id in (' . implode(',',$orderIds) . '))') .
        '';
        $criteria->params = array(':uid' => $uid);
        $criteria->order = 't.id DESC';
        $criteria->limit = 2000;
        $list = Order::model()->with('items', 'states',
            'billingAddress', 'billingAddress.billingCountry',
            'deliveryAddress', 'deliveryAddress.deliveryCountry')->findAll($criteria);

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
     * @return array [стоимостьТоваров, стоимостьДоставки, [товар=>стоимостьТовара], [товар=>ключи для DiscountManager::GetPrice], общийВесПосылки, withVAT(да|нет), естьТоварСоСкидкой(да|нет)]
     */
    function getOrderPrice($uid, $sid, $items, $address, $deliveryMode, $deliveryTypeID, $currencyId = null, $useDefaultAddr = true, $usePersonDiscount = true) {
        if (empty($address)&&!empty($useDefaultAddr)&&!empty($uid)) $address = Address::GetDefaultAddress($uid);
        if ($currencyId === null) $currencyId = Yii::app()->currency;
        $withVAT = Address::UseVAT($address);
        $itemsPrice = $fullweight = 0;
        $pricesValues = array();
        $discountKeys = array();//нужно для получения цены по промокоду, сюда же положил количество товара, что бы не создавать новую переменную
        $isDiscount = false;//признак, что есть товар со скидкой
        foreach ($items as $idx=>$item) {
            if ($usePersonDiscount) $values = DiscountManager::GetPrice($uid, $item);
            else $values = DiscountManager::GetPrice(0, $item, 0, false);
            $key = $withVAT ? DiscountManager::WITH_VAT : DiscountManager::WITHOUT_VAT;
            $keyWithoutDiscount = DiscountManager::BRUTTO;
            $itemKey = $item['entity'].'_'.$item['id'];
            $price = $values[$key];
            if($item['entity'] == Entity::PERIODIC) {
                if(!empty($address['code'])&&($address['code'] == 'FI')) {
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
            $pricesValues[$itemKey] = sprintf("%.2f", round($price, 2));
            $discountKeys[$itemKey] = [
                'discountPrice'=>$key,
                'originalPrice'=>$keyWithoutDiscount,
                'quantity'=>$item['quantity'],
                'info'=>'',
            ];
            if ($values[DiscountManager::DISCOUNT_TYPE] != DiscountManager::TYPE_NO_DISCOUNT) {
                $isDiscount = true;
                $discountKeys[$itemKey]['info'] = DiscountManager::ToStr($values[DiscountManager::DISCOUNT_TYPE]).': '.$values[DiscountManager::DISCOUNT].'%';
            }
            $itemsPrice += $item['quantity'] * $price;
            if (!empty($item['InCartUnitWeight'])) $fullweight += ($item['InCartUnitWeight']/1000);

            if($values[DiscountManager::DISCOUNT_TYPE] != DiscountManager::TYPE_NO_DISCOUNT)
            {
                $items[$idx]['info'] = DiscountManager::ToStr($values[DiscountManager::DISCOUNT_TYPE]).': '.$values[DiscountManager::DISCOUNT].'%';
            }
        }

        if ($deliveryMode == 0) {
            $p = new PostCalculator();
            $list = $p->GetRates(0, $uid, $sid, isset($address['country'])?$address['country']:$address['id']);
            $deliveryPrice = 0;
            foreach ($list as $l)
                if ($l['id'] == $deliveryTypeID) $deliveryPrice = $l['value'];
        }
        else $deliveryPrice = 0;

        $rates = Currency::GetRates();
        $rate = $rates[$currencyId];
        $minOrderPrice = Yii::app()->params['OrderMinPrice'] * $rate;
        if($itemsPrice < $minOrderPrice) $itemsPrice = $minOrderPrice;

        return [sprintf("%.2f", round($itemsPrice, 2)), sprintf("%.2f", round($deliveryPrice, 2)), $pricesValues, $discountKeys, $fullweight, (bool)$withVAT, (bool)$isDiscount];
    }

    public function CreateNewOrder($uid, $sid, OrderForm $order, $items, $ptype)
    {
        $transaction = Yii::app()->db->beginTransaction();
        $a = new Address();
        $da = $a->GetAddress($uid, $order->DeliveryAddressID);
        list($itemsPrice, $deliveryPrice, $pricesValues, $discountKeys, $fullweight) = $this->getOrderPrice($uid, $sid, $items, $da, $order->DeliveryMode, $order->DeliveryTypeID, $order->CurrencyID, false);

        $notes = trim((string)$order->Notes);

        $promocodeId = 0;
        $promocodes = array();
        if (empty($this->_promocode)) $fullPrice = $itemsPrice + $deliveryPrice;
        else {
            $promocode = Promocodes::model();
            $code = $promocode->getPromocode($this->_promocode)['code'];
            if ($promocode->getPromocode($this->_promocode)['type_id'] == Promocodes::CODE_GIFT) {
                list($itemsPrice, $deliveryPrice, $pricesValues, $discountKeys, $fullweight) = $this->getOrderPrice($uid, $sid, $items, $da, $order->DeliveryMode, $order->DeliveryTypeID, $order->CurrencyID, false, false);
            }
            if (((int)$promocode->getPromocode($this->_promocode)['type_id'] === Promocodes::CODE_WITHOUTPOST)&&($order->DeliveryTypeID != 3)) {
                $fullPrice = $itemsPrice + $deliveryPrice;
            }
            else {
                $fullPrice = $promocode->getTotalPrice($code, $itemsPrice, $deliveryPrice, $pricesValues, $discountKeys);
            }
            $promocodeId = $this->_promocode;
            $promocodes[] = $this->_promocode;
            if (!empty($notes)) $notes .= ' ';
            $notes .= Yii::app()->ui->item('PROMOCODE_USE', $code) . '. ';
            $briefly = $promocode->briefly($code, false, $itemsPrice);
            if (!empty($briefly['promocodeValue'])) $notes .= $briefly['promocodeValue'] . ' ';
            if (!empty($briefly['promocodeUnit'])) $notes .= $briefly['promocodeUnit'] . ' ';
            if (!empty($briefly['name'])) $notes .= strip_tags($briefly['name']) . ' ';
            if (((int)$promocode->getPromocode($this->_promocode)['type_id'] === Promocodes::CODE_WITHOUTPOST)&&($order->DeliveryTypeID == 3)) {
                $deliveryPrice = 0;
            }
        }
		
		
        if (empty($this->_certificate)) {}
        else {
            $promocode = Promocodes::model();
            $code = $promocode->getPromocode($this->_certificate)['code'];
            if ($promocode->getPromocode($this->_certificate)['type_id'] == Promocodes::CODE_CERTIFICATE) {
                $fullPrice = $promocode->getTotalPrice($code, $fullPrice, 0, $pricesValues, $discountKeys);
            }
            $promocodes[] = $this->_certificate;
            if (!empty($notes)) $notes .= ' ';
            $notes .= Yii::app()->ui->item('PROMOCODE_USE', $code) . '. ';
            $briefly = $promocode->briefly($code, false, $itemsPrice);
            if (!empty($briefly['promocodeValue'])) $notes .= $briefly['promocodeValue'] . ' ';
            if (!empty($briefly['promocodeUnit'])) $notes .= $briefly['promocodeUnit'] . ' ';
            if (!empty($briefly['name'])) $notes .= strip_tags($briefly['name']) . ' ';
        }

        try
        {
            $hiddenNotes = '';
            $spAdderss = (string) $order->SmartpostAddress;
            if (!empty($spAdderss)) {
                if ($spAdderss = @unserialize($spAdderss)) {
                    $hiddenNotes .= 'Smartpost address: ';
                    $hiddenNotes .= ''.
                        $spAdderss['labelName']['fi'] . ': ' . $spAdderss['locationName']['fi'] . "\r\n".
                        $spAdderss['address']['fi']['address'] . ' ' . $spAdderss['address']['fi']['postalCode'] . ' ' . $spAdderss['address']['fi']['postalCodeName'] . "\r\n".
                        '';
                }
                else {
                    $hiddenNotes = 'Smartpost: ' . $spAdderss . '. ' . "\r\n\r\n";
                }
            }
            $user = User::model()->findAllByPk($uid);
            if (!empty($user[0])&&($user = $user[0]->getAttributes())) {
                //'contact_email'=>$user['login'], 'first_name'=>$user['first_name'], 'last_name'=>$user['last_name']
                $ba = array('contact_email'=>'', 'receiver_first_name'=>'', 'receiver_last_name'=>'');
                if (!empty($order->BillingAddressID)) $ba = $a->GetAddress($uid, $order->BillingAddressID);
                else $ba = $da;
                $client = array();
                if (($user['first_name'] != $da['receiver_first_name'])&&($user['first_name'] != $ba['receiver_first_name'])) $client['name'] = $user['last_name'] . ' ' . $user['first_name'];
                elseif (($user['last_name'] != $da['receiver_last_name'])&&($user['last_name'] != $ba['receiver_last_name'])) $client['name'] = $user['last_name'] . ' ' . $user['first_name'];
                if (($user['login'] != $da['contact_email'])&&($user['login'] != $ba['contact_email'])&&(!empty($da['contact_email'])||!empty($ba['contact_email']))) {
                    $client['name'] = $user['last_name'] . ' ' . $user['first_name'];
                    $client['email'] = $user['login'] . "\r\n";
                }
                if (!empty($client)) $hiddenNotes .= 'Заказ оформил: ' . implode(' ', $client) . '. ' . "\r\n\r\n";
            }
            /*if (!empty($data['verkkolaskuosoite'])||!empty($data['operaattoritunnus'])) {
                if (!empty($data['verkkolaskuosoite']))
                    $hiddenNotes .= 'verkkolaskuosoite: ' . $data['verkkolaskuosoite'] . "\r\n";
                if (!empty($data['operaattoritunnus']))
                    $hiddenNotes .= 'operaattoritunnus: ' . $data['operaattoritunnus'] . "\r\n";
                $hiddenNotes .= "\r\n";
            }*/

            $sql = 'INSERT INTO users_orders (uid, delivery_address_id, billing_address_id, delivery_type_id, '
                . 'payment_type_id, currency_id, is_reserved, full_price, items_price, delivery_price, notes, mandate, promocode_id, smartpost_address, promocodes, hidden_notes) VALUES '
                . '(:uid, :daid, :baid, :dtid, :ptid, :cur, :isres, :full, :items, :delivery, :notes, :mandate, :promocodeId, :smartpost_address, :promocodes, :hidden_notes)';

            Yii::app()->db->createCommand($sql)->execute(
                array(':uid' => $uid,
                      ':daid' => $order->DeliveryAddressID,
                      ':baid' => $order->BillingAddressID,
                      ':dtid' => $order->DeliveryTypeID,
                      ':ptid' => (int) $ptype, // payment in next step
                      ':cur' => $order->CurrencyID,
                      ':isres' => $order->DeliveryMode == 1 ? 1 : 0, // 1 - выкуп в магазине
                      ':full' => $fullPrice,
                      ':items' => $itemsPrice,
                      ':delivery' => $deliveryPrice,
                      ':notes' => $notes,
                      ':mandate' => $order->Mandate,
                      ':promocodeId' => $promocodeId,
                      ':smartpost_address' => (string) $order->SmartpostAddress,
                      ':promocodes' => serialize($promocodes),
                      ':hidden_notes' => $hiddenNotes
                ));

            $orderID = Yii::app()->db->lastInsertID;

            if ($orderID > 0) {
                if (!empty($this->_promocode)||!empty($this->_certificate)) {
                    if ($fullPrice == 0) $this->AddStatus($orderID, OrderState::AutomaticPaymentConfirmation);
                }
                if (!empty($this->_promocode)) {
                    $promocode = Promocodes::model();
                    $promocode->used($this->_promocode);
                }
                if (!empty($this->_certificate)) {
                    $promocode = Promocodes::model();
                    $promocode->used($this->_certificate);
                }
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

            foreach ($items as $item) {
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
                         ':info' => empty($discountKeys[$itemKey]['info']) ? '' : $discountKeys[$itemKey]['info']
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

        $sql = 'UPDATE users_orders SET must_upgrade = 1 WHERE id=:id LIMIT 1';
        Yii::app()->db->createCommand($sql)->execute(array(':id' => $oid));

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

    function setPromocode($code, $certificate = '') {
        $promocode = Promocodes::model();
        if ($promocode->check($promocode->getPromocodeByCode($code)) === 0) $this->_promocode = $promocode->getPromocodeByCode($code)['id'];
        if (($certificate !== $code)&& ($promocode->check($promocode->getPromocodeByCode($certificate)) === 0)) $this->_certificate = $promocode->getPromocodeByCode($certificate)['id'];
    }
	
	function GetCountOrders($uid){
		
		$sql = 'SELECT COUNT(*) FROM users_orders WHERE uid=:uid';
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array('uid'=>$uid));
		
		return $cnt;
		
	}
	
	function isMyOrder($uid, $oid){
		
		$sql = 'SELECT COUNT(*) FROM users_orders WHERE uid=:uid AND id=:id';
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array('uid'=>$uid, 'id'=>$oid));
		
		return $cnt;
		
	}

    function getItemsByOrder($order) {
        if (empty($order['Items'])) return array();
        foreach ($order['Items'] as $i=>$item) {
            if (empty($r['unitweight'])) {
                $order['Items'][$i]['FullUnitWeight'] = 0;
                $order['Items'][$i]['InCartUnitWeight'] = 0;
            }
            else {
                $order['Items'][$i]['FullUnitWeight'] = $item['quantity'] * $item['unitweight'] * Cart::UNITWEIGHT_VALUE;
                $order['Items'][$i]['InCartUnitWeight'] = $item['FullUnitWeight'] * ($r['unitweight_skip'] == 1 ? 0 : 1);
            }
        }
        return $order['Items'];
    }

}
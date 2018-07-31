<?php

class PostCalculator
{
    const UNIT_WEIGHT = 0.250; // 250 грамм в одном UnitWeight

    public function GetRates($aid, $uid, $sid)
    {
        $a = new Address();

        $address = $a->GetAddress($uid, $aid);
        if (empty($address)) return array();

        $country = Country::GetCountryById($address['country']);
        if (empty($country)) return array();
        $isFinland = $country['code'] == 'FI';
        $isEurope = $country['is_europe'];
        $group = $country['post_group'];

        $c = new Cart;
        $cart = $c->GetCart($uid, $sid);

        // посчитать сумму UnitWeight и "не влезает в конверт"
        if (empty($cart)) return array();
        $totalUW = 0;
        $realTotalUW = 0;

        $notInEnvelope = false;
        $onlySubscription = true;

        foreach ($cart as $c)
        {
            $realTotalUW += ($c['unitweight'] * $c['quantity']);
            if ($c['entity'] != Entity::PERIODIC) $onlySubscription = false;
            $price = DiscountManager::GetPrice($uid, $c);
            if ($price[DiscountManager::TYPE_FREE_SHIPPING]) continue;
            if ($c['unitweight_skip']) continue; // без почтовых - то ничего не считаем

            $totalUW += ($c['unitweight'] * $c['quantity']);
            if ($c['not_in_envelope']) $notInEnvelope = true;
        }

        $free = array('type' => Delivery::ToString(Delivery::TYPE_FREE) . ' (Economy)',
            'id' => Delivery::TYPE_FREE,
            'currency' => Currency::EUR,
            'currencyName' => 'EUR',
            'deliveryTime' => $this->GetDeliveryTime($isEurope, $isFinland, true, Delivery::TYPE_ECONOMY),
            'value' => 0);

        if ($onlySubscription)
        {
            return array($free);
        }

//        $class = 'Group' . $group . 'PostCalc';
//        $obj = new $class($group, $totalUW, $realTotalUW, $notInEnvelope, $address, Yii::app()->currency);
//        $rates = $obj->GetRates();

        $obj = new PostCostCalculator($group, $totalUW, $realTotalUW, $notInEnvelope, $address, Yii::app()->currency);
        $rates = $obj->GetRates();

        if (empty($totalUW))
        {
            $found = false;
            foreach ($rates as $idx => $rate)
            {
                if ($rate['id'] == Delivery::TYPE_ECONOMY)
                {
                    $rates[$idx] = $free;
                    $found = true;
                }
            }
            if (!$found && !$obj->IsOrc())
            {
                array_push($rates, $free);
            }
        }
        foreach ($rates as $idx => $rate)
        {
            $rates[$idx]['deliveryTime'] = $this->GetDeliveryTime($isEurope, $isFinland, $rate['id'] == Delivery::TYPE_FREE, $rate['id']);
        }

        return $rates;
    }
    
    public function GetRates2($country, $uid, $sid)
    {
        
        $country = Country::GetCountryById($country);
        if (empty($country)) return array();
        $isFinland = $country['code'] == 'FI';
        $isEurope = $country['is_europe'];
        $group = $country['post_group'];

        $c = new Cart;
        $cart = $c->GetCart($uid, $sid);

        // посчитать сумму UnitWeight и "не влезает в конверт"
        if (empty($cart)) return array();
        $totalUW = 0;
        $realTotalUW = 0;

        $notInEnvelope = false;
        $onlySubscription = true;

        foreach ($cart as $c)
        {
            $realTotalUW += ($c['unitweight'] * $c['quantity']);
            if ($c['entity'] != Entity::PERIODIC) $onlySubscription = false;
            $price = DiscountManager::GetPrice($uid, $c);
            if ($price[DiscountManager::TYPE_FREE_SHIPPING]) continue;
            if ($c['unitweight_skip']) continue; // без почтовых - то ничего не считаем

            $totalUW += ($c['unitweight'] * $c['quantity']);
            if ($c['not_in_envelope']) $notInEnvelope = true;
        }

        $free = array('type' => Delivery::ToString(Delivery::TYPE_FREE) . ' (Economy)',
            'id' => Delivery::TYPE_FREE,
            'currency' => Currency::EUR,
            'currencyName' => 'EUR',
            'deliveryTime' => $this->GetDeliveryTime($isEurope, $isFinland, true, Delivery::TYPE_ECONOMY),
            'value' => 0);

        if ($onlySubscription)
        {
            return array($free);
        }

//        $class = 'Group' . $group . 'PostCalc';
//        $obj = new $class($group, $totalUW, $realTotalUW, $notInEnvelope, $address, Yii::app()->currency);
//        $rates = $obj->GetRates();

        $obj = new PostCostCalculator($group, $totalUW, $realTotalUW, $notInEnvelope, $address, Yii::app()->currency);
        $rates = $obj->GetRates();

        if (empty($totalUW))
        {
            $found = false;
            foreach ($rates as $idx => $rate)
            {
                if ($rate['id'] == Delivery::TYPE_ECONOMY)
                {
                    $rates[$idx] = $free;
                    $found = true;
                }
            }
            if (!$found && !$obj->IsOrc())
            {
                array_push($rates, $free);
            }
        }
        foreach ($rates as $idx => $rate)
        {
            $rates[$idx]['deliveryTime'] = $this->GetDeliveryTime($isEurope, $isFinland, $rate['id'] == Delivery::TYPE_FREE, $rate['id']);
        }

        return $rates;
    }

    private function GetDeliveryTime($isEurope, $isFinland, $isFree, $type)
    {
        $isEurope = $isEurope ? true : false;
        $isFinland = $isFinland ? true : false;
        $isFree = $isFree ? true : false;

        if ($isFree)
        {
            $values = array(Delivery::TYPE_ECONOMY => '1-15');
            return $values[Delivery::TYPE_ECONOMY];
        }
        else if ($isFinland)
        {
            $values = array(
                Delivery::TYPE_ECONOMY => '2-5',
                Delivery::TYPE_PRIORITY => '1-3',
                Delivery::TYPE_EXPRESS => '1-2',
            );
            if(isset($values[$type])) return $values[$type];
            return $values[Delivery::TYPE_ECONOMY];
        }
        else if ($isEurope)
        {
            $values = array(
                Delivery::TYPE_ECONOMY => '3-10',
                Delivery::TYPE_PRIORITY => '3-6',
                Delivery::TYPE_EXPRESS => '2-4',
            );
            if(isset($values[$type])) return $values[$type];
            return $values[Delivery::TYPE_ECONOMY];
        }
        else
        {
            $values = array(
                Delivery::TYPE_ECONOMY => '5-15',
                Delivery::TYPE_PRIORITY => '3-10',
                Delivery::TYPE_EXPRESS => '3-7',
            );
            if(isset($values[$type])) return $values[$type];
            return $values[Delivery::TYPE_ECONOMY];
        }
    }

}
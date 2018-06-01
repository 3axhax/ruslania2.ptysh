<?php

class TestShippingCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $addresses = [
            ['code' => 'FI', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'EE', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'LT', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'DE', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'IT', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'AL', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'AU', 'business_number1' => 'SOMEVATNUMBER'],
            ['code' => 'AZ', 'business_number1' => 'SOMEVATNUMBER'],
        ];

        $carts = [
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 1, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 1, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 1, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 0, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 4, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 1, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 4, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 0, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 9, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 1, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 9, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 0, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 5, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 0, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::BOOKS, 'unitweight' => 15, 'quantity' => 1, 'unitweight_skip' => 1,
                    'not_in_envelope' => 0, 'free_shipping' => 0],
            ],
            [
                ['entity' => Entity::PERIODIC, 'unitweight' => 5, 'quantity' => 1, 'unitweight_skip' => 0,
                    'not_in_envelope' => 0, 'free_shipping' => 1],
            ],
        ];


        foreach ($addresses as $addr)
        {
            $realAddr = $this->GetRealCountryInfo($addr['code']);
            $log = [];

            $cartID = 0;
            foreach ($carts as $cart)
            {
                $cartID++;
                $totalUW = 0;
                $realTotalUW = 0;

                $notInEnvelope = false;
                $onlySubscription = true;

                foreach ($cart as $c)
                {
                    $realTotalUW += ($c['unitweight'] * $c['quantity']);
                    if ($c['entity'] != Entity::PERIODIC) $onlySubscription = false;
                    if (isset($c['free_shipping']) && $c['free_shipping']) continue;
                    if ($c['unitweight_skip']) continue; // без почтовых - то ничего не считаем

                    $totalUW += ($c['unitweight'] * $c['quantity']);
                    if ($c['not_in_envelope']) $notInEnvelope = true;
                }

                foreach ($cart as $c)
                {
                    $log[] = $c['entity'] . ', ' . $c['quantity'] . ' kpl uw=' . $c['unitweight'] . ', !envelope=' . $c['not_in_envelope'].' uw_skip=['.$c['unitweight_skip'].'] free_shipping=' . $c['free_shipping'].' ('.'cart: '.$cartID.')';
                }

                $calc = new PostCostCalculator($realAddr['post_group'], $totalUW, $realTotalUW, $notInEnvelope, $realAddr, Currency::EUR);
                $rates = $calc->GetRates();
                $shippingType = $calc->GetType();

                $free = array('type' => Delivery::ToString(Delivery::TYPE_FREE) . ' (Economy)',
                    'id' => Delivery::TYPE_FREE,
                    'currency' => Currency::EUR,
                    'currencyName' => 'EUR',
                    'deliveryTime' => $calc->GetDeliveryTime($realAddr['is_europe'], $realAddr['code'] == 'FI', true, Delivery::TYPE_ECONOMY),
                    'value' => 0);

                if ($onlySubscription)
                {
                    $rates = [];
                    $rates[] = $free;
                }
                else
                {

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
                        if (!$found && !$calc->IsOrc())
                        {
                            array_push($rates, $free);
                        }
                    }
                }

                $log[] = "Shipping to " . $addr['code'] . ' PostGroup=' . $realAddr['post_group'];
                $log[] = "UW=" . $totalUW . ', RealUW=' . $realTotalUW . ', NotInEnvelope=' . $notInEnvelope;
                $log[] = 'Shipping in [' . $shippingType . ']';
                foreach ($rates as $type => $info)
                {
                    $log[] = $info['type'] . "=" . $info['value'] . 'e ';
                }


                $log[] = "\n\n-----\n\n";
            }

            $postGroup = str_pad($realAddr['post_group'], 2, '0', STR_PAD_LEFT);
            $fName = 'group_' . $postGroup . '.log';
            $logStr = implode("\n", $log);
            file_put_contents('./postcalc/' . $fName, $logStr);

        }
    }

    private function Calc($addr, $cart)
    {
        $isEurope = $addr['is_europe'];
        $group = $addr['post_group'];

        $totalUW = 0;
        $realTotalUW = 0;

        $notInEnvelope = false;
        $onlySubscription = true;

        foreach ($cart as $c)
        {
            $realTotalUW += ($c['unitweight'] * $c['quantity']);
            if ($c['entity'] != Entity::PERIODIC) $onlySubscription = false;
            if (isset($c['free_shipping']) && $c['free_shipping']) continue;
            if ($c['unitweight_skip']) continue; // без почтовых - то ничего не считаем

            $totalUW += ($c['unitweight'] * $c['quantity']);
            if ($c['not_in_envelope']) $notInEnvelope = true;
        }

        $free = array('type' => Delivery::ToString(Delivery::TYPE_FREE) . ' (Economy)',
            'id' => Delivery::TYPE_FREE,
            'currency' => Currency::EUR,
            'currencyName' => 'EUR',
            'deliveryTime' => $this->GetDeliveryTime($isEurope, Delivery::TYPE_ECONOMY),
            'vatUsed' => false,
            'value' => 0);

        if ($onlySubscription)
        {
            return array($free);
        }

        $class = 'Group' . $group . 'PostCalc';
        echo 'Using ' . $class . ' class' . "\n";
        $currency = Currency::EUR;
        $obj = new $class($group, $totalUW, $realTotalUW, $notInEnvelope, $addr, $currency);
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
            $rates[$idx]['deliveryTime'] = $this->GetDeliveryTime($isEurope, $rate['id']);
        }

        return $rates;

    }

    private function GetDeliveryTime($isEurope, $type)
    {
        $times = array(
            true => array(
                Delivery::TYPE_ECONOMY => '3-10',
                Delivery::TYPE_PRIORITY => '3-6',
                Delivery::TYPE_EXPRESS => '2-4',
            ),
            false => array(
                Delivery::TYPE_ECONOMY => '10-20',
                Delivery::TYPE_PRIORITY => '5-10',
                Delivery::TYPE_EXPRESS => '3-5',
            )
        );

        $isEurope = $isEurope ? true : false;
        if (isset($times[$isEurope][$type])) return $times[$isEurope][$type];
        return '10-20';
    }

    private function GetRealCountryInfo($code)
    {
        $sql = 'SELECT * FROM tblHelper_CountryList WHERE code=:code';
        return Yii::app()->mssql->createCommand($sql)->queryRow(true, [':code' => $code]);
    }


}
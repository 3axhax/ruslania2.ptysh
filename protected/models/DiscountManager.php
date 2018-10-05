<?php

class DiscountManager
{
    const WITH_VAT = 100;
    const WITH_VAT_FIN = 101;
    const WITH_VAT_WORLD = 102;

    const WITHOUT_VAT = 200;
    const WITHOUT_VAT_FIN = 201;
    const WITHOUT_VAT_WORLD = 202;

    const DISCOUNT = 300;

    const BRUTTO = 400;
    const BRUTTO_FIN = 401;
    const BRUTTO_WORLD = 402;

    const DISCOUNT_TYPE = 500;
    const ORIGINAl_PRICE = 600;
    const READY_EUR_PRICE_VAT = 700;
    const READY_EUR_PRICE_WITHOUT_VAT = 800;
    const RATE = 900;

    const TYPE_RUSLANIA_DAYS = 1;
    const TYPE_CATEGORY = 2;
    const TYPE_SERIES = 3;
    const TYPE_PUBLISHER = 4;
    const TYPE_FREE_SHIPPING = 5;
    const TYPE_PERSONAL = 6;
    const TYPE_ITEM = 7;
    const TYPE_NO_DISCOUNT = 8;
    const TYPE_YEAR = 9;
    const TYPE_PART = 10; // скидка на раздел

    private static $rusDays = null;
    private static $personalDiscount = null;
    private static $rusDaysInfo = null;
    private static $discounts = null;

    private static $_offerDay = null;

    static function getOfferDay() {
        if (self::$_offerDay === null) {
            $sql = ''.
                'select entity entity_id, item_id, discount '.
                'from action_items '.
                'where (type = 3) '.
                'limit 1 '.
            '';
            self::$_offerDay = Yii::app()->db->createCommand($sql)->queryRow();
        }
        return self::$_offerDay;
    }

    public static function GetPrice($uid, $item)
    {
        $discountType = null;
        $priceFin = 0;
        $priceWorld = 0;

        if (isset($item['entity']) && $item['entity'] == Entity::PERIODIC)
        {
            $price = $item['sub_fin_year'];
            $priceFin = $item['sub_fin_year'];
            $priceWorld = $item['sub_world_year'];
        }
        else
        {
            $priceWorld = $priceFin = $price = (isset($item['brutto'])) ? $item['brutto'] : 0;
        }

        $campaignDiscount = self::CalcDiscount($item);

        $allDiscounts = array();

        if(!empty($campaignDiscount))
            $allDiscounts = $campaignDiscount;

        if(!empty(self::$personalDiscount)) $personalDiscount = self::$personalDiscount;
        else $personalDiscount = (!empty($uid)) ? Yii::app()->user->GetPersonalDiscount() : 0;

        self::$personalDiscount = floatVal($personalDiscount);

        if(!empty(self::$personalDiscount) && self::$personalDiscount > 0)
            $allDiscounts[] = array('Type' => self::TYPE_PERSONAL, 'Value' => self::$personalDiscount);

        $itemDiscount = 0;

        $vat = intVal($item['vat']); // сколько НДСа
        $vat0 = $price * 100 / (100+$vat);

        $vat0WorldYear = $priceWorld * 100 / (100+$vat);
        $vat0FinYear = $priceFin * 100 / (100+$vat);

        if(isset($item['discount']) && !empty($item['discount']))
        {
            $id = floatVal($item['discount']);
            if($id > 0)
            {
                if(isset($item['entity']) && $item['entity'] == Entity::PERIODIC) $itemDiscount = $item['discount']; //$itemDiscount = $item['discount'] * 100;
                else $itemDiscount = (1 - ($item['discount'] / $price)) * 100;
            }
        }

        $offerDay = self::getOfferDay();
        if (!empty($offerDay['discount'])&&($offerDay['entity_id'] == $item['entity'])&&($offerDay['item_id'] == $item['id'])&&($offerDay['discount'] > $itemDiscount)) {
            $itemDiscount = $offerDay['discount'];
        }

        if(!empty($itemDiscount))
            $allDiscounts[] = array('Type' => self::TYPE_ITEM, 'Value' => $itemDiscount);

        $haveFreeShipping = (isset($item['unitweight_skip']) && $item['unitweight_skip'] > 0) || (isset($item['entity']) && $item['entity'] == Entity::PERIODIC);

        $maxDiscount = null;
        foreach($allDiscounts as $discount)
        {
            if($discount['Type'] == self::TYPE_FREE_SHIPPING) $haveFreeShipping = true;
            if(empty($maxDiscount))
            {
                $maxDiscount = $discount;
                continue;
            }

            if(!empty($maxDiscount) && $discount['Value'] > $maxDiscount['Value'])
                $maxDiscount = $discount;
        }

        $rates = Currency::GetRates();
        $rate = $rates[Yii::app()->currency];
        $origPrice = $price;
        $origPriceFin = $priceFin;
        $origPriceWorld = $priceWorld;

        if(empty($maxDiscount))
        {
            $type = self::TYPE_NO_DISCOUNT;
            $percent = 0;
        }
        else
        {
            $type = $maxDiscount['Type'];
            $percent = $maxDiscount['Value'];
        }

        $newALV0 = $vat0 - ($vat0 * $percent / 100);
        $newALV = $newALV0 * (1 + ($vat/100));

        $newALV0PriceFin = $vat0FinYear - ($vat0FinYear * $percent / 100);
        $newALVPriceFin = $newALV0PriceFin * (1 + ($vat/100));

        // HACK! REMOVE IT
        $oldPercent = $percent;
        if(isset($item['entity']) && $item['entity'] == Entity::PERIODIC && $item['id'] == 319 && $percent > 14 && $percent < 15)
        {
            $percent = 0;
        }

        $newALV0PriceWorld = $vat0WorldYear - ($vat0WorldYear * $percent / 100);
        $newALVPriceWorld = $newALV0PriceWorld * (1 + ($vat/100));

        // HACK! REMOVE IT
        if(isset($item['entity']) && $item['entity'] == Entity::PERIODIC && $item['id'] == 319)
        {
            $percent = $oldPercent;
        }


        $ret = array(self::ORIGINAl_PRICE => floatVal($origPrice * $rate),
                     self::BRUTTO => floatVal($origPrice * $rate),

                     self::BRUTTO_FIN => floatVal($origPriceFin * $rate),
                     self::BRUTTO_WORLD => floatVal($origPriceWorld * $rate),

                     self::DISCOUNT => round($percent, 2),

                     self::WITH_VAT => round($newALV * $rate, 2),
                     self::WITH_VAT_FIN => round($newALVPriceFin * $rate, 2),
                     self::WITH_VAT_WORLD => round($newALVPriceWorld * $rate, 2),

                     self::WITHOUT_VAT => round($newALV0 * $rate, 2),
                     self::WITHOUT_VAT_FIN => round($newALV0PriceFin * $rate, 2),
                     self::WITHOUT_VAT_WORLD => round($newALV0PriceWorld * $rate, 2),


                     self::READY_EUR_PRICE_VAT => round($newALV, 2),
                     self::READY_EUR_PRICE_WITHOUT_VAT => round($newALV0, 2),
                     self::DISCOUNT_TYPE => $type,
                     self::RATE => $rate,
                     self::TYPE_FREE_SHIPPING => $haveFreeShipping,
        );

        return $ret;
    }

    public static function GetDiscounts()
    {
        if(self::$discounts === null)
        {
            $now = date('Y-m-d');
            $sql = 'SELECT * FROM discounts WHERE start_date <= :now1 AND end_date >= :now2 ';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':now1' => $now, ':now2' => $now));
            if(empty($rows))
            {
                self::$discounts = array('Count' => 0);
                return self::$discounts;
            }

            foreach($rows as $row)
            {
                self::$discounts['Types'][$row['type_id']][] = $row;
                self::$discounts['Entity'][$row['entity_id']][] = $row;
            }
            self::$discounts['Count'] = count($rows);

            // Посчитаем категории для которых есть скидки
            if(isset(self::$discounts['Types'][self::TYPE_CATEGORY]))
            {
                $catsWithDiscount = array();
                $c = new Category;

                foreach(self::$discounts['Types'][self::TYPE_CATEGORY] as $category)
                {
                    $entity = $category['entity_id'];
                    $childIds = $c->GetChildren($entity, $category['category_id']);
                    array_push($childIds, $category['category_id']);
                    $catsWithDiscount[$entity][] = array('Discount' => $category['discount'],
                                                         'DID' => $category['did'],
                                                         'Type' => $category['type_id'],
                                                         'IDs' => $childIds);
                }

                self::$discounts[self::TYPE_CATEGORY] = $catsWithDiscount;
            }

            // Посчитаем скидки на серии
            if(isset(self::$discounts['Types'][self::TYPE_SERIES]))
            {
                $seriesWithDiscount = array();
                foreach(self::$discounts['Types'][self::TYPE_SERIES] as $serie)
                {
                    $entity = $serie['entity_id'];
                    $seriesWithDiscount[$entity][$serie['serie_id']] = array('Discount' => $serie['discount'],
                                                           'DID' => $serie['did'],
                                                           'Type' => $serie['type_id'],
                                                           'ID' => $serie['serie_id']
                                                          );

                }
                self::$discounts[self::TYPE_SERIES] = $seriesWithDiscount;
            }

            if(isset(self::$discounts['Types'][self::TYPE_PUBLISHER]))
            {
                $pubDiscount = array();
                foreach(self::$discounts['Types'][self::TYPE_PUBLISHER] as $publisher)
                {
                    $entity = $publisher['entity_id'];
                    $pubDiscount[$entity][$publisher['publisher_id']] = array('Discount' => $publisher['discount'],
                                                                             'DID' => $publisher['did'],
                                                                             'Type' => $publisher['type_id'],
                                                                             'ID' => $publisher['publisher_id']
                    );
                }
                self::$discounts[self::TYPE_PUBLISHER] = $pubDiscount;
            }

            if(isset(self::$discounts['Types'][self::TYPE_RUSLANIA_DAYS]))
            {
                $rusDay = array();
                foreach(self::$discounts['Types'][self::TYPE_RUSLANIA_DAYS] as $rD)
                {
                    $rusDay[] = array('Discount' => $rD['discount'], 'DID' => $rD['did'], 'Type' => $rD['type_id']);
                }
                self::$discounts[self::TYPE_RUSLANIA_DAYS] = $rusDay;
            }

            if(isset(self::$discounts['Types'][self::TYPE_FREE_SHIPPING]))
            {
                $fs = array();
                foreach(self::$discounts['Types'][self::TYPE_FREE_SHIPPING] as $f)
                {
                    $fs[$f['entity_id']] = array('Discount' => 0, 'DID' => $f['did'], 'Type' => $f['type_id'], 'FreeShipping' => true);
                }
                self::$discounts[self::TYPE_FREE_SHIPPING] = $fs;
            }

            if(isset(self::$discounts['Types'][self::TYPE_YEAR]))
            {
                $year = array();
                foreach(self::$discounts['Types'][self::TYPE_YEAR] as $y)
                {
                    $year[$y['entity_id']][$y['year']][] = array('Discount' => $y['discount'], 'DID' => $y['did'], 'Type' => $y['type_id']);
                }
                self::$discounts[self::TYPE_YEAR] = $year;
            }

            if(isset(self::$discounts['Types'][self::TYPE_PART]))
            {
                $parts = array();
                foreach(self::$discounts['Types'][self::TYPE_PART] as $p)
                {
                    $parts[$p['entity_id']][] = array('Discount' => $p['discount'], 'DID' => $p['did'], 'Type' => $p['type_id']);
                }
                self::$discounts[self::TYPE_PART] = $parts;
            }
        }
        return self::$discounts;
    }

    private static function CalcDiscount($item)
    {
        $ret = array();
        $discounts = self::GetDiscounts();
        if($discounts['Count'] == 0) return array();;

        if(!empty($discounts[self::TYPE_CATEGORY]) && isset($discounts[self::TYPE_CATEGORY][$item['entity']]))
        {
            foreach($discounts[self::TYPE_CATEGORY][$item['entity']] as $catDiscount)
            {
                $ids = $catDiscount['IDs'];
                if(in_array($item['code'], $ids) || in_array($item['subcode'],  $ids))
                {
                    $ret[] = array('Type' => self::TYPE_CATEGORY, 'Value' => $catDiscount['Discount'], 'DID' => $catDiscount['DID']);
                }
            }
        }

        if(!empty($discounts[self::TYPE_SERIES])
            && isset($discounts[self::TYPE_SERIES][$item['entity']])
            && isset($item['series_id'])
            && isset($discounts[self::TYPE_SERIES][$item['entity']][$item['series_id']])
        )
        {
            $ret[] = array('Type' => self::TYPE_SERIES,
                           'Value' => $discounts[self::TYPE_SERIES][$item['entity']][$item['series_id']]['Discount'],
                           'DID' => $discounts[self::TYPE_SERIES][$item['entity']][$item['series_id']]['DID']
            );
        }

        if(!empty($discounts[self::TYPE_PUBLISHER])
            && isset($discounts[self::TYPE_PUBLISHER][$item['entity']])
            && isset($item['publisher_id']) && !empty($item['publisher_id'])
            && isset($discounts[self::TYPE_PUBLISHER][$item['entity']][$item['publisher_id']])
        )
        {
            $ret[] = array('Type' => self::TYPE_PUBLISHER,
                           'Value' => $discounts[self::TYPE_PUBLISHER][$item['entity']][$item['publisher_id']]['Discount'],
                           'DID' => $discounts[self::TYPE_PUBLISHER][$item['entity']][$item['publisher_id']]['DID']
            );
        }

        if(!empty($discounts[self::TYPE_RUSLANIA_DAYS]))
        {
            foreach($discounts[self::TYPE_RUSLANIA_DAYS] as $rD)
            {
                $ret[] = array('Type' => self::TYPE_RUSLANIA_DAYS,
                               'Value' => $rD['Discount'],
                               'DID' => $rD['DID']
                );
            }
        }

        if(!empty($discounts[self::TYPE_FREE_SHIPPING]) && isset($discounts[self::TYPE_FREE_SHIPPING][$item['entity']]))
        {
            foreach ($discounts[self::TYPE_FREE_SHIPPING] as $free)
            {
                $ret[] = array('Type' => self::TYPE_FREE_SHIPPING,
                               'Value' => 0,
                               'DID' => $free['DID'],
                );
            }
        }

        if(!empty($discounts[self::TYPE_YEAR])
            && isset($item['year']) && !empty($item['year'])
            && isset($discounts[self::TYPE_YEAR][$item['entity']]))
        {
            $itemYear = intVal($item['year']);

            foreach($discounts[self::TYPE_YEAR][$item['entity']] as $year=>$data)
            {
                if($itemYear <= $year)
                {
                    foreach($data as $d)
                    {
                       $ret[] = array('Type' => self::TYPE_YEAR,
                              'Value' => $d['Discount'],
                              'DID' => $d['DID']);
                    }
                }
            }
        }

        if(!empty($discounts[self::TYPE_PART]) && isset($discounts[self::TYPE_PART][$item['entity']]))
        {
            foreach($discounts[self::TYPE_PART][$item['entity']] as $item)
            {
                $ret[] = array('Type' => self::TYPE_PART, 'Value' => $item['Discount'], 'DID' => $item['DID']);
            }
        }

        return $ret;
    }

    public static function ToStr($tid)
    {
        switch($tid)
        {
            case self::TYPE_RUSLANIA_DAYS : return 'Дни Руслании';
            case self::TYPE_PUBLISHER : return 'Скидка на издателя';
            case self::TYPE_SERIES : return 'Скидка на серию';
            case self::TYPE_CATEGORY : return 'Скидка на категорию';
            case self::TYPE_FREE_SHIPPING: return 'Без почтовых';
            case self::TYPE_YEAR: return 'Скидка на год издания';
            case self::TYPE_PERSONAL: return 'Персональная скидка';
            case self::TYPE_ITEM: return 'Скидка на товар';
            case self::TYPE_PART: return 'Скидка на раздел';
            default: return 'Непонятная скидка ID: '.$tid;
        }
    }
}
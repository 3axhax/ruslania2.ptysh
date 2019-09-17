<?php

class Availability
{
    const MIN_ITEMS = 5;

    const AVAIL_IN_SHOP = 1;    //В магазине
    const ENDING_IN_SHOP = 2;   //Заканчивается в магазине
    const TO_ORDER_FAST = 3;    // Надо заказывать у поставщиков быстрые поставщики
    const TO_ORDER_SLOW = 4;    // Надо заказывать у поставщиков медленные поставщики
    const TEMPORARY_OUT = 5;    // Временно отсутствует

    const NOT_AVAIL_AT_ALL = 6; // Нет в ассортименте

    public static function GetStatus($item)
    {
        if (isset($item['avail_for_order'])&&empty($item['avail_for_order'])) return self::NOT_AVAIL_AT_ALL;

        if ($item['in_shop'] > 0)
        {
            if ($item['in_shop'] <= self::MIN_ITEMS &&
                (!empty($item['entity']) || $item['entity'] != Entity::PERIODIC)
            )
            {
                return self::ENDING_IN_SHOP;
            }
            else
            {
                return self::AVAIL_IN_SHOP;
            }
        }
        else if ($item['econet_skip'] > 0) // in_stock
        {
            $ret = self::TO_ORDER_SLOW;

            if (isset($item['Publisher']))
            {
                $fastCountries = array(68, 62);
                if (!empty($item['Publisher']['country_id']))
                {
                    if (in_array($item['Publisher']['country_id'], $fastCountries)) $ret = self::TO_ORDER_FAST;
                }
            }

            $fastVendors = array(5, 7, 431);
            if (!empty($item['vendor']) && in_array($item['vendor'], $fastVendors)) $ret = self::TO_ORDER_FAST;

            return $ret;
        }
        else
        {
            return self::NOT_AVAIL_AT_ALL;
//            if (empty($item['stock_id']))
//                return self::NOT_AVAIL_AT_ALL;
//            else
//                return self::TEMPORARY_OUT;
        }
    }

    public static function ToStr($item)
    {
        $code = self::GetStatus($item);

        if ($item['entity'] == 30) {
            return Yii::app()->ui->item('CARTNEW_NAME_TABLE_AVAILABILITY');
        }

        if (!empty($item['presale'])) {
            $datePresale = mktime(23, 59, 59, date('m', $item['presale'])+1, -1, date('Y', $item['presale']));
            if ($datePresale > time()) return '';
        }

        switch ($code)
        {
            case self::AVAIL_IN_SHOP :
                return Yii::app()->ui->item("ITEM_AVAIBLE_STATUS_AVAIBLE_SHOP"); //В магазине
            case self::ENDING_IN_SHOP :
                return Yii::app()->ui->item("ITEM_AVAIBLE_STATUS_AVAIBLE_LESS_5"); //заканчивается в магазине
            case self::TEMPORARY_OUT :
                //Андреас по скайпу сказал, что нет такого и если, что то ставить Нет в нашем ассортименте
                //return Yii::app()->ui->item("ITEM_AVAIBLE_STATUS_NOT_AVAIBLE"); //временно отсутствует
            case self::NOT_AVAIL_AT_ALL :
                return Yii::app()->ui->item("ITEM_AVAIBLE_STATUS_NOT_AVAILABLE_AT_ALL"); // Нет в нашем ассортименте.
            case self::TO_ORDER_FAST : // return Yii::app()->ui->item('ITEM_FAST_DELIVERY');
            case self::TO_ORDER_SLOW : //return Yii::app()->ui->item("ITEM_SLOW_DELIVERY"); // поступление от 2 до 15 дней
            {
                if(!isset($item['DeliveryTime']) || $item['DeliveryTime'] === false) return Yii::app()->ui->item("ITEM_SLOW_DELIVERY"); // поступление от 2 до 15 дней

                $unit = $item['DeliveryTime']['delivery_unit'];
                $name = $item['DeliveryTime']['delivery_type_name'];
                $key = 'SHIPPING_WEEKS';
                if($unit == 1)
                {
                    if($name == 1) $key = 'SHIPPING_DAY';
                    else $key = 'SHIPPING_DAYS';
                }
                else if($unit == 2)
                {
                    if(strpos($name, '-') !== false) $key = 'SHIPPING_WEEKS';
                    else $key = 'SHIPPING_WEEK';
                }

                return Yii::app()->ui->item('SHIPPING').' '.$name.' '.Yii::app()->ui->item($key).' '.Yii::app()->ui->item('SHIPPING_AFTER');
                break;
            }

        }
    }

    static function toSchema($item) {
        $code = self::GetStatus($item);
        switch ($code) {
            case self::AVAIL_IN_SHOP :
            case self::ENDING_IN_SHOP :
                return 'InStock';
            case self::TEMPORARY_OUT :
            case self::NOT_AVAIL_AT_ALL :
                return 'SoldOut';
        }
        return 'PreOrder';
    }

    public static function ItemsSort($a, $b)
    {
        $codeA = self::GetStatus($a);
        $codeB = self::GetStatus($b);
        if (($codeA == $codeB) && (($codeA == self::ENDING_IN_SHOP) || ($codeA == self::AVAIL_IN_SHOP) || ($codeA == self::TEMPORARY_OUT) || ($codeA == self::NOT_AVAIL_AT_ALL))) return 0;
        if ($codeA != $codeB) {
            if ($codeA == self::ENDING_IN_SHOP) return -1;
            if ($codeB == self::ENDING_IN_SHOP) return 1;
        }
        if ((($codeA == self::TO_ORDER_FAST) || ($codeA == self::TO_ORDER_SLOW)) && (($codeB == self::TO_ORDER_FAST) || ($codeB == self::TO_ORDER_SLOW))) {
            if($a['DeliveryTime']['delivery_unit'] > $b['DeliveryTime']['delivery_unit']) return 1;
            elseif ($a['DeliveryTime']['delivery_unit'] < $b['DeliveryTime']['delivery_unit']) return -1;
            else {
                if (explode('-', $a['DeliveryTime']['delivery_type_name'])[0] > explode('-', $b['DeliveryTime']['delivery_type_name'])[0]) return 1;
                elseif (explode('-', $a['DeliveryTime']['delivery_type_name'])[0] < explode('-', $b['DeliveryTime']['delivery_type_name'])[0]) return -1;
                else {
                    if (explode('-', $a['DeliveryTime']['delivery_type_name'])[1] > explode('-', $b['DeliveryTime']['delivery_type_name'])[1]) return 1;
                    elseif (explode('-', $a['DeliveryTime']['delivery_type_name'])[1] < explode('-', $b['DeliveryTime']['delivery_type_name'])[1]) return -1;
                    else return 0;
                }
            }
        }
        if ($codeA > $codeB) return -1;
        if ($codeA < $codeB) return 1;
        return 0;
        //if($a['DeliveryTime']['dtid'] == $b['DeliveryTime']['dtid']) return 0;
        //return ($a['DeliveryTime']['dtid'] > $b['DeliveryTime']['dtid']) ? 1 : -1;
    }
}
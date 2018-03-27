<?php

class Packet3 extends Packet
{
    public function GetShippingCost()
    {
        $ret = array();
        $ret[Delivery::TYPE_ECONOMY] = array('Price' => 12, 'CalcVAT' => false);
        $ret[Delivery::TYPE_PRIORITY] = array('Price' => 18, 'CalcVAT' => false);
        $ret[Delivery::TYPE_EXPRESS] = array('Price' => 30, 'CalcVAT' => false);
        return $ret;
    }
}
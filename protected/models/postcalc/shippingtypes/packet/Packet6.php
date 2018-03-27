<?php

class Packet6 extends Packet
{
    public function GetShippingCost()
    {
        $ret = array();

        $perusEconomy = 16;
        $kerroinEconomy = 1.18;
        $ret[Delivery::TYPE_ECONOMY] = array('Price' => $perusEconomy + ($this->kg * $kerroinEconomy), 'CalcVAT' => true);

        $ret[Delivery::TYPE_PRIORITY] = array('Price' => ($perusEconomy + ($this->realKg * $kerroinEconomy)) * 1.5, 'CalcVAT' => true);

        $perusExpress = 27.2816;
        $kerroinExpress = 3.74;

        $ret[Delivery::TYPE_EXPRESS] = array('Price' => $perusExpress + ($this->realKg * $kerroinExpress), 'CalcVAT' => true);
        return $ret;
    }
}
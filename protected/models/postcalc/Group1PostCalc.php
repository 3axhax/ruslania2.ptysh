<?php

class Group1PostCalc extends BasePostCalc
{
    public function GetRates()
    {
        $this->calcVAT = false;
        $ret = array();
        
        var_dump($this->unitWeight);
        
        $economyPrice = 3.9;
        if($this->unitWeight <=4 && !$this->notInEnvelope) $economyPrice = 3.9;
        else if(($this->unitWeight <=4 && $this->notInEnvelope) || ($this->unitWeight > 4 && $this->unitWeight  <= 8)) $economyPrice = 7;
        else $economyPrice = 7;

        $ret[Delivery::TYPE_ECONOMY] = array('Price' => $economyPrice, 'CalcVAT' => false);

        $priorityPrice = 7;
        if($this->realUnitWeight <=4 && !$this->notInEnvelope) $priorityPrice = 7;
        else if(($this->realUnitWeight <=4 && $this->notInEnvelope) || ($this->realUnitWeight > 4 && $this->realUnitWeight  <= 8)) $priorityPrice = 10;
        else $priorityPrice = 10;

        $ret[Delivery::TYPE_PRIORITY] = array('Price' => $priorityPrice, 'CalcVAT' => false);

        $expressPrice = array('Price' => 15, 'CalcVAT' => false);;
        if($this->realUnitWeight <=4 && !$this->notInEnvelope) $expressPrice['Price'] = 15;
        else if(($this->realUnitWeight <=4 && $this->notInEnvelope) || ($this->realUnitWeight > 4 && $this->realUnitWeight  <= 8))
            $expressPrice['Price'] = 20;
        else $expressPrice['Price'] = 20;

        $ret[Delivery::TYPE_EXPRESS] = $expressPrice;

/*
        if($this->unitWeight <= 4 && !$this->notInEnvelope)
        {
            $ret[Delivery::TYPE_ECONOMY] = 3.9;  // 2.9
            $ret[Delivery::TYPE_PRIORITY] = 7; // 5
            $ret[Delivery::TYPE_EXPRESS] = 15; // 10
        }
        else if(($this->unitWeight <= 4 && $this->notInEnvelope) || ($this->unitWeight > 4 && $this->unitWeight <=8))
        {
            $ret[Delivery::TYPE_ECONOMY] = 7; //5
            $ret[Delivery::TYPE_PRIORITY] = 10; //7
            $ret[Delivery::TYPE_EXPRESS] = 15; // 10
        }
        else
        {
            $ret[Delivery::TYPE_ECONOMY] = 7;
            $ret[Delivery::TYPE_PRIORITY] = 10;
            $ret[Delivery::TYPE_EXPRESS] = 15;
        }
*/

        return $this->Format($ret);
    }

    public function Calc4Packet()
    {
        throw new CException('Not implemented for group 1');
    }
}
<?php

class PostCostCalculator
{
    const VATPercent = 24;
    protected $group = 0;
    protected $unitWeight = 0;
    protected $originalUnitWeight;
    protected $realUnitWeight;
    protected $kg = 0;
    protected $realKg = 0;
    protected $notInEnvelope = false;
    protected $country = null;
    protected $currency;

    public function __construct($group, $unitWeight, $realUnitWeight, $notInEnvelope, $country, $currency)
    {
        $this->group = $group;
        $this->originalUnitWeight = $unitWeight;
        $this->unitWeight = ceil($unitWeight); // округляем вес в кг. всегда вверх до целого
        $this->kg = ceil($this->unitWeight * PostCalculator::UNIT_WEIGHT); //вес в килограммах для пакетов

        $this->realUnitWeight = $realUnitWeight; // Реальный вес без учета "без почтовых"
        $this->realKg = ceil($this->realUnitWeight * PostCalculator::UNIT_WEIGHT); // Реальный вес в кг без учета без почтовых

        $this->notInEnvelope = $notInEnvelope;
        $this->country = $country;

        $this->calculator = $this->CreateCalculator();

        $this->currency = $currency;
    }

    /**
     * @return BaseShippingType
     */
    protected function CreateCalculator()
    {
        $class = '';
        if($this->unitWeight <=4 && !$this->notInEnvelope) $class = 'Envelope'.$this->group;
        else if($this->unitWeight > 5 && $this->unitWeight <=8 && !$this->notInEnvelope) $class = 'MiniPacket'.$this->group;
        else if($this->notInEnvelope && $this->unitWeight <= 8) $class = 'MiniPacket'.$this->group;
        else $class= 'Packet'.$this->group;

        $obj = new $class($this->unitWeight, $this->realUnitWeight, $this->kg, $this->realKg, $this->notInEnvelope);
        return $obj;
    }

    public function GetRates()
    {
        $ret = $this->calculator->GetShippingCost();

        $rez = array();
        $rates = Currency::GetRates();
        $useVAT = Address::UseVAT($this->country);

        foreach($ret as $idx=>$data)
        {
            $value = $data['Price'];
            $calcVAT = $data['CalcVAT'];

            $value = $value * $rates[$this->currency];
            if($useVAT && $calcVAT && $this->group > 1 && $this->group <=6)
            {
                $value *= (100+self::VATPercent) / 100;
            }

            $ret[$idx] = round($value, 1);

            $rez[] = array('type' => Delivery::ToString($idx),
                'id' => $idx,
                'currency' => $this->currency,
                'currencyName' => Currency::ToStr($this->currency),
                'value' => $ret[$idx]);
        }

        return $rez;
    }

    public function GetType()
    {
        return $this->calculator->GetType();
    }


    public function GetDeliveryTime($isEurope, $isFinland, $isFree, $type)
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
<?php

class UpdateCurrencyCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $data = @file_get_contents('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');
        if(empty($data))
        {
            echo "Empty currency data";
            return;
        }

        $matches = array();
        preg_match_all("/<Cube currency='([a-z]*)' rate='([\\d.]*)'\/>/i", $data, $matches);

        if(empty($matches) || !isset($matches[1]) || !isset($matches[2]))
        {
            echo "Empty matches";
            return;
        }

        $currencies = array();
        foreach($matches[1] as $idx=>$currency)
        {
            $currencies[$currency] = $matches[2][$idx];
        }

        if(!empty($currencies['USD']) && $currencies['USD'] > 0)
        {
            $sql = 'UPDATE currencies SET eur_rate=:rate, add_date=NOW() WHERE id=2';
            Yii::app()->db->createCommand($sql)->execute(array(':rate' => $currencies['USD']));
            echo "USD updated\n";
        }

        if(!empty($currencies['GBP']) && $currencies['GBP'] > 0)
        {
            $sql = 'UPDATE currencies SET eur_rate=:rate, add_date=NOW() WHERE id=3';
            Yii::app()->db->createCommand($sql)->execute(array(':rate' => $currencies['GBP']));
            echo "GBP updated\n";
        }
   }
}
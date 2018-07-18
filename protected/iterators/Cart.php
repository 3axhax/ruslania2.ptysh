<?php
/*Created by Кирилл (15.05.2018 14:33)*/

class IteratorsCart extends ArrayIterator {
	private $_sumEur = 0, $_usingVAT = true;
	private $_rate = 1;//почему то так

	function current() {
		$item = parent::current();
		$item['ReadyPriceStr'] = $this->_readyPriceStr($item);
		$price = $this->_lineTotalVAT($item);
		$this->_sumEur += $price;
		$this->_rate = (float) $item['Rate'];
		$this->_usingVAT = $this->_usingVAT || $item['UseVAT'];
		$item['LineTotalVAT'] = ProductHelper::FormatPrice($price, false);
		Debug::staticRun(array($item));
		return $item;
	}

	private function _readyPrice($item) {
		if((int)$item['Entity'] !== (int) Entity::PERIODIC)
			return $item['UseVAT'] ? $item['PriceVAT'] : $item['PriceVAT0'];

		if($item['Price2Use'] == Cart::FIN_PRICE)
			return $item['UseVAT'] ? $item['PriceVATFin'] : $item['PriceVAT0Fin'];

		return $item['UseVAT'] ? $item['PriceVATWorld'] : $item['PriceVAT0World'];
	}

	private function _readyPriceStr($item) {
		if((int)$item['Entity'] !== (int) Entity::PERIODIC)
			return $item['UseVAT'] ? $item['PriceVATStr'] : $item['PriceVAT0Str'];

		if($item['Price2Use'] == Cart::FIN_PRICE)
			return $item['UseVAT'] ? $item['PriceVATFinStr'] : $item['PriceVAT0FinStr'];

		return $item['UseVAT'] ? $item['PriceVATWorldStr'] : $item['PriceVAT0WorldStr'];

    }

	private function _lineTotalVAT($item) {
        return ((int)$item['Quantity'] * (float) $this->_readyPrice($item));
    }

	function isVATInPrice() {
	    return $this->_usingVAT
	        ? Yii::app()->ui->item('WITH_VAT')
	        : Yii::app()->ui->item('WITHOUT_VAT');
	}

	function totalVAT() {
		if($this->_sumEur < Yii::app()->params['OrderMinPrice']) {
			return ProductHelper::FormatPrice(Yii::app()->params['OrderMinPrice']*$this->_rate, false);
		}

		return ProductHelper::FormatPrice($this->_sumEur, false);
	}

}
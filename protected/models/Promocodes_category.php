<?php
/*Created by Кирилл (04.12.2018 23:00)*/
class Promocodes_category extends CActiveRecord {
	static private $_certificates = array();//для кеша промокодов
	static private $_codeIds = array(); // для кеша только ид промокодов

	function tableName() {
		return 'promocodes_category';
	}

	static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @param $id int ид сертификата
	 * @return array сертификат
	 */
	function getCertificate($id) {
		return $this->_cacheCertificate($id);
	}

	function getByPromocode($promocodeId) {
		return $this->_cacheCertificate(null, $promocodeId);
	}

	/**
	 * @param $id int ид сертификата
	 * @param $currencyId int ид валюты, в которой нужно вернуть номинал
	 * @return int|float номинал сертификата
	 */
	function getNominal($id, $currencyId) {
		$certificate = $this->getCertificate($id);
		if (empty($certificate['promocode_id'])) return 0;
		if ($certificate['nominal'] <= 0) return 0;

		/** @var $promocode Promocodes */
		$promocode = Promocodes::model();
		$code = $promocode->getPromocode($certificate['promocode_id']);
		if (($check = $promocode->check($code, false)) > 0) return 0;

		return Currency::convertToCurrency($certificate['nominal'], $certificate['currency'], $currencyId);
	}

	/**
	 * @param $id int ид сертификата
	 * @param $currencyId
	 * @param $itemsPrice float цена товаров
	 * @param $deliveryPrice float цена доставки
	 * @param $deliveryPrice array [товар=>цена]
	 * @return mixed конечная цена с учетом промокода
	 */
	function getTotalPrice($id, $currencyId, $itemsPrice, $deliveryPrice, $pricesValues) {
		$nominal = $this->getNominal($id, $currencyId);
		$total = $itemsPrice + $deliveryPrice - $nominal;
		if ($total < 0) $total = 0;
		return $total;
	}

	function briefly($id, $currencyId) {
		$certificate = $this->getCertificate($id);
		if (empty($certificate['promocode_id'])) return null;
		if ($certificate['nominal'] <= 0) return null;
		return [
			'promocodeValue'=>$certificate['nominal'],
			'promocodeUnit'=>($certificate['unit'] == 1)?'%':Currency::ToSign(Currency::EUR),
//			'realValue'=>$this->getNominal($id, $currencyId),
//			'realUnit'=>Currency::ToSign(Yii::app()->currency),
			'name'=>'Категория (раздел)',
		];
	}

	function used($id, $promocodeId) {
		return Promocodes::model()->updateByPk($promocodeId, array('is_used'=>1));
	}

	private function _cacheCertificate($id = null, $promocodeId = null) {
		if ($id !== null) {
			if (!isset(self::$_certificates[$id])) {
				self::$_certificates[$id] = $this->findByPk($id)->attributes?:array();
				if (!empty(self::$_certificates[$id]['promocode_id'])) self::$_codeIds[self::$_certificates[$id]['promocode_id']] = $id;
			}
			return self::$_certificates[$id];
		}
		if ($promocodeId !== null) {
			if (!isset(self::$_codeIds[$promocodeId])) {
				$certificate = $this->findByAttributes(array('promocode_id'=>$promocodeId))->attributes?:array();
				if (!empty($certificate['id'])) {
					self::$_certificates[$certificate['id']] = $certificate;
					self::$_codeIds[$promocodeId] = $certificate['id'];
				}
				else self::$_codeIds[$promocodeId] = 0;
			}
			if (!empty(self::$_codeIds[$promocodeId])) return $this->_cacheCertificate(self::$_codeIds[$promocodeId]);
			return array();
		}
		return null;
	}


}
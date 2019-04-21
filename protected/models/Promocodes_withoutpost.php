<?php
/*Created by Кирилл (07.12.2018 17:50)*/
class Promocodes_withoutpost extends CActiveRecord {

	static private $_certificates = array();//для кеша промокодов
	static private $_codeIds = array(); // для кеша только ид промокодов

	function tableName() {
		return 'promocodes_withoutpost';
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

	function check($id, $currencyId, $itemsPrice) {
		$certificate = $this->getCertificate($id);

		if ($itemsPrice !== null) {
			if (($certificate['uid'] > 0)&&($certificate['uid'] <> (int)Yii::app()->user->id)) return false;
			$itemsPrice = Currency::ConvertToEUR($itemsPrice, $currencyId);
			if ($itemsPrice < (float) $certificate['min_price']) return false;
		}

		if (empty($certificate['promocode_id'])) return false;
		return true;
	}

	/**
	 * @param $id int ид сертификата
	 * @param $currencyId int ид валюты, в которой нужно вернуть номинал
	 * @return int|float номинал сертификата
	 */
	function getNominal($id, $currencyId, $itemsPrice = 0, $pricesValues = array(), $discountKeys = array()) {
		return 0;
	}

	/**
	 * @param $id int ид сертификата
	 * @param $currencyId
	 * @param $itemsPrice float цена товаров
	 * @param $deliveryPrice float цена доставки
	 * @param $deliveryPrice array [товар=>цена]
	 * @return mixed конечная цена с учетом промокода
	 */
	function getTotalPrice($id, $currencyId, $itemsPrice, $deliveryPrice, $pricesValues, $discountKeys) {
		return $itemsPrice;
	}

	function briefly($id, $currencyId, $itemsPrice = null) {
		if (!$this->check($id, $currencyId, $itemsPrice)) return null;
		$certificate = $this->getCertificate($id);
		$names = array();
		if (!empty($certificate['min_price'])&&((float)$certificate['min_price'] > 0)) $names[] = '<div>' . Yii::app()->ui->item('MSG_ORDER_FROM_SUMM', $certificate['min_price'] . Currency::ToSign(Currency::EUR)) . '</div>';
		if (!empty($certificate['uid'])&&($itemsPrice === null)) $names[] = '<div>Только для ' . $certificate['uid'] . ' (ID клиента на новом сайте)</div>';
//		$names = implode('', $names);
		return [
			'promocodeValue'=>Yii::app()->ui->item('FREE_SHIPPING_OFFER'),
			'promocodeUnit'=>'',
//			'realValue'=>$this->getNominal($id, $currencyId),
//			'realUnit'=>Currency::ToSign(Yii::app()->currency),
			'name'=>implode('', $names),
		];
	}

	function used($id, $promocodeId) {
		$certificate = $this->getCertificate($id);
		if (!empty($certificate['single_use'])) return Promocodes::model()->updateByPk($promocodeId, array('is_used'=>1));
		return true;
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

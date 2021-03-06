<?php
/*Created by Кирилл (03.01.2019 21:21)*/
//промокод на товар (стоимость товара)
class Promocodes_good extends CActiveRecord {
	static private $_certificates = array();//для кеша промокодов
	static private $_codeIds = array(); // для кеша только ид промокодов

	function tableName() {
		return 'promocodes_good';
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
		if (empty($certificate['items'])) return false;

		if ($itemsPrice !== null) {
			if (($certificate['uid'] > 0)&&($certificate['uid'] <> (int)Yii::app()->user->id)) return false;
			$itemsPrice = Currency::ConvertToEUR($itemsPrice, $currencyId);
			if ($itemsPrice < (float) $certificate['min_price']) return false;
		}

		if (empty($certificate['promocode_id'])) return false;
		if ($certificate['nominal'] <= 0) return false;
		return true;
	}

	/**
	 * @param $id int ид сертификата
	 * @param $currencyId int ид валюты, в которой нужно вернуть номинал
	 * @return int|float номинал сертификата
	 */
	function getNominal($id, $currencyId, $itemsPrice = 0, $pricesValues = array(), $discountKeys = array()) {
		if (!$this->check($id, $currencyId, $itemsPrice)) return 0;
		$certificate = $this->getCertificate($id);

		/** @var $promocode Promocodes */
		$promocode = Promocodes::model();
		$code = $promocode->getPromocode($certificate['promocode_id']);
		if (($check = $promocode->check($code, false)) > 0) return 0;

		//что бы промокод сработал все товары из промокода должны быть в заказе
		foreach ($certificate['items'] as $eid=>$ids) {
			foreach ($ids as $itemId) {
				if (!isset($pricesValues[$eid . '_' . $itemId])) return 0;
			}
		}

		$nominal = Currency::convertToCurrency($certificate['nominal'], Currency::EUR, $currencyId);
		$price = $this->_getPrice($certificate['items'], $pricesValues, $discountKeys, $nominal);
		return $price['onlyPromocode'];
	}

	/**
	 * @param $id int ид сертификата
	 * @param $currencyId
	 * @param $itemsPrice float цена товаров
	 * @param $deliveryPrice float цена доставки
	 * @param $pricesValues array [товар=>цена]
	 * @return mixed конечная цена с учетом промокода
	 */
	function getTotalPrice($id, $currencyId, $itemsPrice, $deliveryPrice, $pricesValues, $discountKeys) {
		$nominal = $this->getNominal($id, $currencyId, $itemsPrice, $pricesValues, $discountKeys);
		$total = $itemsPrice - $nominal;
		if ($total < 0) $total = 0;
		$total += $deliveryPrice;
		return $total;
	}

	function briefly($id, $currencyId, $itemsPrice = null) {
		if (!$this->check($id, $currencyId, $itemsPrice)) return null;
		$certificate = $this->getCertificate($id);
		$names = array();
		$product = new Product();
		foreach ($certificate['items'] as $eid=>$itemIds) {
			$name = Yii::app()->ui->item(Entity::GetEntitiesList()[$eid]['uikey']);
			if (!empty($itemIds)) {
				$name .= ':';
				foreach ($product->GetProducts($eid, $itemIds) as $item) {
					$name .= ProductHelper::GetTitle($item) . ', ';
				}
				$name = '<span>' . mb_substr($name, 0, -2, 'utf-8') . '</span>';
			}
			$names[] = '<div>' . $name . '</div>';
		}
		if (!empty($certificate['min_price'])&&((float)$certificate['min_price'] > 0)) $names[] = '<div>' . Yii::app()->ui->item('MSG_ORDER_FROM_SUMM', $certificate['min_price'] . Currency::ToSign(Currency::EUR)) . '</div>';
		if (!empty($certificate['uid'])&&($itemsPrice === null)) $names[] = '<div>Только для ' . $certificate['uid'] . ' (ID клиента на новом сайте)</div>';
//		$names = implode('', $names);
		return [
			'promocodeValue'=>$certificate['nominal'],
			'promocodeUnit'=>Currency::ToSign(Currency::EUR),
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
				if (!empty(self::$_certificates[$id]['items'])) {
					self::$_certificates[$id]['items'] = $this->_getItems(unserialize(self::$_certificates[$id]['items']));
				}
				else self::$_certificates[$id]['items'] = array();
			}

			return self::$_certificates[$id];
		}
		if ($promocodeId !== null) {
			if (!isset(self::$_codeIds[$promocodeId])) {
				$certificate = $this->findByAttributes(array('promocode_id'=>$promocodeId))->attributes?:array();
				if (!empty($certificate['id'])) {
					self::$_certificates[$certificate['id']] = $certificate;
					self::$_codeIds[$promocodeId] = $certificate['id'];
					if (!empty(self::$_certificates[$certificate['id']]['items'])) {
						self::$_certificates[$certificate['id']]['items'] = $this->_getItems(unserialize(self::$_certificates[$certificate['id']]['items']));
					}
					else self::$_certificates[$certificate['id']]['items'] = array();
				}
				else self::$_codeIds[$promocodeId] = 0;
			}
			if (!empty(self::$_codeIds[$promocodeId])) return $this->_cacheCertificate(self::$_codeIds[$promocodeId]);
			return array();
		}
		return null;
	}

	private function _getItems($items) {
		$result = array();
		foreach ($items as $item) {
			$item = explode('-', $item);
			$eid = array_shift($item);
			$id = array_shift($item);
			if (!empty($id)) {
				if (!isset($result[$eid])) $result[$eid] = array();
				$result[$eid][] = $id;
			}
		}
		foreach ($result as $entity=>$itemIds) {
			$result[$entity] = array_unique($itemIds);
		}
		return $result;
	}

	/** цена с учетом промокода
	 * @param $items array товары из сертификата, по которым начисляется скидка
	 * @param $pricesValues array товар->цена
	 * @param $discountKeys array товар->ключи для DiscountManager::GetPrice
	 * @return array
	 */
	private function _getPrice($items, $pricesValues, $discountKeys, $certificateNominal) {
		//на случай если в заказе товары из промокода указаны по несколько шт., я нахожу мин кол-во и по этому кол-ву будет приминен промокод
		// в функции check уже есть проверка, что все товары из промокода есть в заказе
		$quantityForPromocode = 0;
		foreach ($items as $eid=>$ids) {
			foreach ($ids as $itemId) {
				$itemKey = $eid . '_' . $itemId;
				if (empty($quantityForPromocode)) $quantityForPromocode = $discountKeys[$itemKey]['quantity'];
				else $quantityForPromocode = min($quantityForPromocode, $discountKeys[$itemKey]['quantity']);
			}
		}

		$priceForSale = array('withDiscount'=>0, 'withoutDiscount'=>0, 'onlyPromocode'=>0);
		$pricePromocode = 0;
		$product = new Product();
		foreach ($pricesValues as $itemKey=>$price) {
			list($eid, $itemId) = explode('_', $itemKey);
			$corrector = 1;
			if ($eid == Entity::PERIODIC) $corrector = 12;

			$item = $product->GetBaseProductInfo($eid, $itemId);
			$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item);
			if (!empty($items[$eid])) {
				if (in_array($itemId, $items[$eid])) {
//					$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item);
					$priceForSale['onlyPromocode'] += ($discount[$discountKeys[$itemKey]['discountPrice']]/$corrector)*$quantityForPromocode;
					$pricePromocode += ($certificateNominal/$corrector)*$quantityForPromocode;
				}
			}
			$priceForSale['withDiscount'] += ($discount[$discountKeys[$itemKey]['discountPrice']]/$corrector)*$discountKeys[$itemKey]['quantity'];
			$priceForSale['withoutDiscount'] += ($discount[$discountKeys[$itemKey]['originalPrice']]/$corrector)*$discountKeys[$itemKey]['quantity'];
		}
		$priceForSale['onlyPromocode'] -= $pricePromocode;
		return $priceForSale;
	}
}
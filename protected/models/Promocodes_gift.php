<?php
/*Created by Кирилл (10.12.2018 20:40)*/
class Promocodes_gift extends CActiveRecord {

	static private $_certificates = array();//для кеша промокодов
	static private $_codeIds = array(); // для кеша только ид промокодов

	function tableName() {
		return 'promocodes_gift';
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
		if (!$this->check($id, $currencyId, $itemsPrice)) return 0;
		$certificate = $this->getCertificate($id);

		/** @var $promocode Promocodes */
		$promocode = Promocodes::model();
		$code = $promocode->getPromocode($certificate['promocode_id']);
		if (($check = $promocode->check($code, false)) > 0) return 0;

		$price = $this->_getPrice($pricesValues, $discountKeys);
		return $price['onlyPromocode'];
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
		if (!empty($certificate['min_price'])&&((float)$certificate['min_price'] > 0)) $names[] = '<div>' . Yii::app()->ui->item('MSG_ORDER_FROM_SUMM', $certificate['min_price'] . Currency::ToSign(Currency::EUR)) . '</div>';
		if (!empty($certificate['uid'])&&($itemsPrice === null)) $names[] = '<div>Только для ' . $certificate['uid'] . ' (ID клиента на новом сайте)</div>';
//		$names = implode('', $names);
		return [
			'promocodeValue'=>Yii::app()->ui->item('3_FOR_PRICE_2'),
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

	/** цена с учетом промокода
	 * @param $pricesValues array товар->цена
	 * @param $discountKeys array товар->ключи для DiscountManager::GetPrice
	 * @return array
	 */
	private function _getPrice($pricesValues, $discountKeys) {
		$priceForSale = array('withDiscount'=>0, 'withoutDiscount'=>0, 'onlyPromocode'=>0);
		$product = new Product();
		$priceCounts = array();
		foreach ($pricesValues as $itemKey=>$price) {
			list($eid, $itemId) = explode('_', $itemKey);

			$item = $product->GetBaseProductInfo($eid, $itemId);
			$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item);

			$corrector = 1;
			if ($eid == Entity::PERIODIC) $corrector = 12;
			else {
//			    это, если подарок из общего количества товаров
				$p = (string)$discount[$discountKeys[$itemKey]['discountPrice']];//string нужен, что бы был правильный ключ
				if (!isset($priceCounts[$p])) $priceCounts[$p] = $discountKeys[$itemKey]['quantity'];
				else $priceCounts[$p] += $discountKeys[$itemKey]['quantity'];
			}
//			это, если подарок только из количества одного товара
//			elseif ($discountKeys[$itemKey]['quantity'] > 2) {
//				$priceForSale['onlyPromocode'] += ($discount[$discountKeys[$itemKey]['discountPrice']]/$corrector);
//			}
			$priceForSale['withDiscount'] += ($discount[$discountKeys[$itemKey]['discountPrice']]/$corrector)*$discountKeys[$itemKey]['quantity'];
			$priceForSale['withoutDiscount'] += ($discount[$discountKeys[$itemKey]['originalPrice']]/$corrector)*$discountKeys[$itemKey]['quantity'];
		}
		if (!empty($priceCounts)) {
//			это, если подарок из общего количества товаров
			ksort($priceCounts);
			$quantity = array_sum($priceCounts);
			if ($quantity > 2) {
				$countGift = floor($quantity/3);
				foreach ($priceCounts as $priceItem=>$countsItem) {
					if ($countsItem < $countGift) {
						$countGift -= $countsItem;
						$priceForSale['onlyPromocode'] += ($priceItem*$countsItem);
					}
					else {
						$priceForSale['onlyPromocode'] += ($priceItem*$countGift);
						break;
					}
				}
			}
		}
		return $priceForSale;
	}

}

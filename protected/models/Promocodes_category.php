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

	function check($id, $currencyId, $itemsPrice) {
		$certificate = $this->getCertificate($id);

//		if (($certificate['uid'] > 0)&&($certificate['uid'] <> (int)Yii::app()->user->id)) return false;
		if ($itemsPrice !== null) {
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


		switch((int) $certificate['unit']) {
			case 1: //проценты
				$price = $this->_getPrice($certificate['categorys'], $pricesValues, $discountKeys, $certificate['nominal']);
				if ($price['withDiscount'] > $itemsPrice) return 0;
				return ($itemsPrice - $price['withDiscount']);
				break;
			case 2: //евро
				$price = $this->_getPrice($certificate['categorys'], $pricesValues, $discountKeys, 0);
				$nominal = Currency::convertToCurrency($certificate['nominal'], Currency::EUR, $currencyId);
				if ($price['onlyPromocode'] > $nominal) return $nominal;
				return $price['onlyPromocode'];
				break;
		}

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
		$category = new Category();
		foreach ($certificate['categorys'] as $eid=>$catIds) {
			$name = Yii::app()->ui->item(Entity::GetEntitiesList()[$eid]['uikey']);
			if (!empty($catIds)) {
				$name .= ':';
				foreach ($category->GetByIds($eid, $catIds) as $cat) {
					$name .= ProductHelper::GetTitle($cat);
				}
				$name = '<span>' . mb_substr($name, 0, -2, 'utf-8') . '</span>';
			}
			$names[] = '<div>' . $name . '</div>';
		}
		if (!empty($certificate['min_price'])) $names[] = '<div>' . Yii::app()->ui->item('MSG_ORDER_FROM_SUMM', $certificate['min_price'] . Currency::ToSign(Currency::EUR)) . '</div>';
		if (!empty($certificate['uid'])&&($itemsPrice === null)) $names[] = '<div>Только для ' . $certificate['uid'] . '</div>';
//		$names = implode('', $names);
		return [
			'promocodeValue'=>$certificate['nominal'],
			'promocodeUnit'=>($certificate['unit'] == 1)?'%':Currency::ToSign(Currency::EUR),
//			'realValue'=>$this->getNominal($id, $currencyId),
//			'realUnit'=>Currency::ToSign(Yii::app()->currency),
			'name'=>implode('', $names),
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
				if (!empty(self::$_certificates[$id]['categorys'])) {
					self::$_certificates[$id]['categorys'] = $this->_getCategorys(unserialize(self::$_certificates[$id]['categorys']));
				}
				else self::$_certificates[$id]['categorys'] = array();
			}

			return self::$_certificates[$id];
		}
		if ($promocodeId !== null) {
			if (!isset(self::$_codeIds[$promocodeId])) {
				$certificate = $this->findByAttributes(array('promocode_id'=>$promocodeId))->attributes?:array();
				if (!empty($certificate['id'])) {
					self::$_certificates[$certificate['id']] = $certificate;
					self::$_codeIds[$promocodeId] = $certificate['id'];
					if (!empty(self::$_certificates[$certificate['id']]['categorys'])) {
						self::$_certificates[$certificate['id']]['categorys'] = $this->_getCategorys(unserialize(self::$_certificates[$certificate['id']]['categorys']));
					}
					else self::$_certificates[$certificate['id']]['categorys'] = array();
				}
				else self::$_codeIds[$promocodeId] = 0;
			}
			if (!empty(self::$_codeIds[$promocodeId])) return $this->_cacheCertificate(self::$_codeIds[$promocodeId]);
			return array();
		}
		return null;
	}

	private function _getCategorys($categorys) {
		$result = array();
		$modelCategory = new Category();
		foreach ($categorys as $cat) {
			$cat = explode('-', $cat);
			$eid = array_shift($cat);
			if (!isset($result[$eid])) $result[$eid] = array();
			$cid = array_shift($cat);
			if (!empty($cid)) {
				$result[$eid][] = $cid;
				foreach ($modelCategory->GetChildren($eid, $cid) as $childId) $result[$eid][] = $childId;
			}
		}
		foreach ($result as $entity=>$catIds) {
			$result[$entity] = array_unique($catIds);
		}
		return $result;
	}

	/** цена с учетом промокода
	 * @param $categorys array категории из сертификата, по которым начисляется скидка
	 * @param $pricesValues array товар->цена
	 * @param $discountKeys array товар->ключи для DiscountManager::GetPrice
	 * @return array
	 */
	private function _getPrice($categorys, $pricesValues, $discountKeys, $percent) {
		$priceForSale = array('withDiscount'=>0, 'withoutDiscount'=>0, 'onlyPromocode'=>0);
		$product = new Product();
		if (!empty($categorys)&&is_array($categorys)) {
			foreach ($pricesValues as $itemKey=>$price) {
				list($eid, $itemId) = explode('_', $itemKey);
				$item = $product->GetBaseProductInfo($eid, $itemId);
				$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item);
				if (isset($categorys[$eid])) {
					if (empty($categorys[$eid])) {
						$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item, $percent);
						$priceForSale['onlyPromocode'] += $discount[$discountKeys[$itemKey]['originalPrice']]*$discountKeys[$itemKey]['quantity'];
					}
					else {
						$itemCategorys = array();
						if (!empty($item['code'])) $itemCategorys[] = $item['code'];
						if (!empty($item['subcode '])) $itemCategorys[] = $item['subcode '];
						foreach ($itemCategorys as $catId) {
							if (in_array($catId, $categorys[$eid])) {
								$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item, $percent);
								$priceForSale['onlyPromocode'] += $discount[$discountKeys[$itemKey]['originalPrice']]*$discountKeys[$itemKey]['quantity'];
								break;
							}
						}
					}
				}
				$priceForSale['withDiscount'] += $discount[$discountKeys[$itemKey]['discountPrice']]*$discountKeys[$itemKey]['quantity'];
				$priceForSale['withoutDiscount'] += $discount[$discountKeys[$itemKey]['originalPrice']]*$discountKeys[$itemKey]['quantity'];
			}
		}
		else {
			foreach ($pricesValues as $itemKey=>$price) {
				list($eid, $itemId) = explode('_', $itemKey);
				$item = $product->GetBaseProductInfo($eid, $itemId);
				$discount = DiscountManager::GetPrice(Yii::app()->user->id, $item, $percent);
				$priceForSale['withDiscount'] += $discount[$discountKeys[$itemKey]['discountPrice']]*$discountKeys[$itemKey]['quantity'];
				$priceForSale['withoutDiscount'] += $discount[$discountKeys[$itemKey]['originalPrice']]*$discountKeys[$itemKey]['quantity'];
			}
		}
		return $priceForSale;
	}
}
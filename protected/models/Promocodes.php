<?php /*Created by Кирилл (19.11.2018 22:23)*/

class Promocodes extends CActiveRecord {
	private $_secret = 'ainalsur';
	static private $_promocodes = array(); // для кеша промокодов
	static private $_codes = array(); // для кеша только кодов

	const CODE_CERTIFICATE = 1;
	const CODE_CATEGORY = 2;

	private $_messages = array(
		1 => 'PROMOCODE_ERROR_1',
		2 => 'PROMOCODE_ERROR_2',
		3 => 'PROMOCODE_ERROR_3',
		4 => 'PROMOCODE_ERROR_1',
		5 => 'PROMOCODE_ERROR_1',
		6 => 'PROMOCODE_ERROR_1',
	);

	function rules() {
		return array(
			array('type_id, settings', 'safe'),
		);
	}


	function beforeSave() {
		if ($this->isNewRecord) {
			$this->setAttribute('date_add', date('Y-m-d H:i:s'));
			$this->setAttribute('code', $this->_getCode());
		}
		return parent::beforeSave();
	}

	function tableName() {
		return 'promocodes';
	}

	static function model($className = __CLASS__) {
		return parent::model($className);
	}

	function getPromocode($id) {
		return $this->_cachePromocode($id);
	}

	function getPromocodeByCode($code) {
		if (empty($code)) return null;
		return $this->_cachePromocode(null, $code);
	}

	/**
	 * @param $code string промокод
	 * @param $itemsPrice float цена товаров
	 * @param $deliveryPrice float цена доставки
	 * @return mixed конечная цена с учетом промокода
	 */
	function getTotalPrice($code, $itemsPrice, $deliveryPrice, $pricesValues, $discountKeys) {
		$promocode = $this->getPromocodeByCode($code);
		if (($check = $this->check($promocode)) > 0) return $itemsPrice + $deliveryPrice;

		$saleHandler = $this->_getSaleHandler($promocode['type_id']);
		$sale = $saleHandler->getByPromocode($promocode['id']);
		return $saleHandler->getTotalPrice($sale['id'], Yii::app()->currency, $itemsPrice, $deliveryPrice, $pricesValues, $discountKeys);
	}

	function briefly($code, $checkUsed = true, $itemsPrice = null) {
		if (empty($code)) return ['message'=>''];
		$promocode = $this->getPromocodeByCode($code);
		if (($check = $this->check($promocode, true, $checkUsed, $itemsPrice)) > 0) return ['message'=>Yii::app()->ui->item($this->_messages[$check])];

		$saleHandler = $this->_getSaleHandler($promocode['type_id']);
		$sale = $saleHandler->getByPromocode($promocode['id']);
		return $saleHandler->briefly($sale['id'], Yii::app()->currency, $itemsPrice);
	}

	function check($promocode, $checkHandler = true, $checkUsed = true, $itemsPrice = null) {
		if (empty($promocode)) return 1;//не найден
		if ($checkUsed) {
			if (!empty($promocode['is_used'])) return 2;//использован
			if (!empty($promocode['date_end'])) {
				$date = new DateTime($promocode['date_end']);
				$dateEnd = $date->getTimestamp();
				if ($dateEnd < time()) return 3;//закончился срок действия
			}
		}
		if ($checkHandler) {
			$saleHandler = $this->_getSaleHandler($promocode['type_id']);
			if (empty($saleHandler)) return 4;//не найден обработчик промокода
			$sale = $saleHandler->getByPromocode($promocode['id']);
			if (empty($sale)) return 5;//нет информации о скидке
			if (!$saleHandler->check($sale['id'], Yii::app()->currency, $itemsPrice)) return 6;//не проходит по условиям промокода
		}
		return 0;//все ок
	}

	/** В зависимости от обработчика промокода нужно установить или неустановить, что промокод использован
	 * потому нужно этот признак устанавливать в обработчтке
	 * @param $id
	 * @return bool
	 */
	function used($id) {
		$promocode = $this->getPromocode($id);
		$saleHandler = $this->_getSaleHandler($promocode['type_id']);
		$sale = $saleHandler->getByPromocode($id);
		return $saleHandler->used($sale['id'], $id);
	}

	/** здесь получение промокода
	 * @return string
	 */
	private function _getCode() {
		$s = microtime(true) . $this->_secret;
		$code = mb_substr(md5($s), 0, 10, 'utf-8');
		$sql = 'select 1 from ' . $this->tableName() . ' where (code = :code)';
		if (Yii::app()->db->createCommand($sql)->queryScalar(array('code'=>$code))) return $this->_getCode();
		return $code;
	}

	private function _cachePromocode($id = null, $code = null) {
		if ($id !== null) {
			if (!isset(self::$_promocodes[$id])) {
				self::$_promocodes[$id] = $this->findByPk($id)->attributes?:array();
				if (!empty(self::$_promocodes[$id]['code'])) self::$_codes[self::$_promocodes[$id]['code']] = $id;
			}
			return self::$_promocodes[$id];
		}
		if ($code !== null) {
			if (!isset(self::$_codes[$code])) {
				$promocode = $this->findByAttributes(array('code'=>$code))->attributes?:array();
				if (!empty($promocode['id'])) {
					self::$_promocodes[$promocode['id']] = $promocode;
					self::$_codes[$code] = $promocode['id'];
				}
				else self::$_codes[$code] = 0;
			}
			if (!empty(self::$_codes[$code])) return $this->_cachePromocode(self::$_codes[$code]);
			return array();
		}
		return null;
	}

	private function _getSaleHandler($typeId) {
		//все возвращаемые классы должны иметь методы:
		//function getByPromocode();
		//function getTotalPrice();
		//function briefly();
		//function used();

		switch ((int) $typeId) {
			case self::CODE_CERTIFICATE: return Certificate::model(); break;
			case self::CODE_CATEGORY: return Promocodes_category::model(); break;
		}
		return null;
	}

}

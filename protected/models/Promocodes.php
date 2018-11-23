<?php /*Created by Кирилл (19.11.2018 22:23)*/

class Promocodes extends CActiveRecord {
	private $_secret = 'ainalsur';
	static private $_promocodes = array(); // для кеша промокодов
	static private $_codes = array(); // для кеша только кодов

	const CODE_CERTIFICATE = 1;

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
		return $this->_cachePromocode(null, $code);
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
		switch ((int) $typeId) {
			case self::CODE_CERTIFICATE: return Certificate::model(); break;
		}
		return null;
	}

}
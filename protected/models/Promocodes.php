<?php /*Created by Кирилл (19.11.2018 22:23)*/

class Promocodes extends CActiveRecord {
	private $_secret = 'ainalsur';
	static private $_promocodes = array(); // для кеша промокодов

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
		if (!isset(self::$_promocodes[$id])) {
			self::$_promocodes[$id] = $this->findByPk($id)->attributes?:array();
		}
		return self::$_promocodes[$id];
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

}
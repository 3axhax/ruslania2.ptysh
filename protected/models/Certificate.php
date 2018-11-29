<?php /*Created by Кирилл (16.11.2018 17:55)*/
class Certificate extends CActiveRecord {
	static private $_certificates = array();//для кеша сертификатов
	static private $_codeIds = array(); // для кеша только ид промокодов

	function rules() {
		return array(
			array('maket_id, nominal, payment_type_id', 'required'),
			array('fio_dest', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_DEST_NAME')),
			array('email_dest', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_DEST_EMAIL')),
			array('fio_source', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_SOURCE_NAME')),
			array('email_source', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_SOURCE_EMAIL')),
			array('txt_dest', 'safe'),
		);
	}

	function beforeSave() {
		$this->setAttribute('uid', Yii::app()->user->id);
		$this->setAttribute('currency', Yii::app()->currency);
		return parent::beforeSave();
	}

	function tableName() {
		return 'certificate_orders';
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

	function paid($certificate) {
		$model = new Promocodes();
		$model->setAttributes(array(
			'type_id'=>$model::CODE_CERTIFICATE,
			'settings'=>serialize(array($certificate)),
		));
		$promocodeId = 0;
		$code = '';
		if ($model->save()) {
			$promocodeId = (int) $model->id;
			$code = $model->getAttribute('code');
		}

		$sql = ''.
			'update ' . $this->tableName() . ' set '.
			'date_pay = CURRENT_TIMESTAMP, '.
			'promocode_id = ' . $promocodeId . ' '.
			'where (id = ' . (int) $certificate['id'] . ') '.
		'';
		Yii::app()->db->createCommand($sql)->execute();
		if (isset(self::$_certificates[$certificate['id']])) {
			self::$_certificates[$certificate['id']]['promocode_id'] = $promocodeId;
			self::$_certificates[$certificate['id']]['date_pay'] = date('Y-m-d H:i:s');
		}
		//TODO:: добавить отправку писем
		return $code;
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
		if (empty($certificate['promocode_id'])) return null;//['promocodeValue'=>0, 'promocodeUnit'=>'', 'realValue'=>0, 'realUnit'=>''];
		if ($certificate['nominal'] <= 0) return null;//['promocodeValue'=>0, 'promocodeUnit'=>'', 'realValue'=>0, 'realUnit'=>''];
		return [
			'promocodeValue'=>$certificate['nominal'],
			'promocodeUnit'=>Currency::ToSign($certificate['currency']),
			'realValue'=>$this->getNominal($id, $currencyId),
			'realUnit'=>Currency::ToSign(Yii::app()->currency),
		];
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
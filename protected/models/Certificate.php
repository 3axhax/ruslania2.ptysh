<?php /*Created by Кирилл (16.11.2018 17:55)*/
class Certificate extends CActiveRecord {
	static private $_certificates = array();//для кеша сертификатов

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

	function getCertificate($id) {
		if (!isset(self::$_certificates[$id])) {
			self::$_certificates[$id] = $this->findByPk($id)->attributes?:array();
		}
		return self::$_certificates[$id];
	}

	function paid($certificate) {
		$promocodeId = 0;
		$model = new Promocodes();
		$model->setAttributes(array(
			'type_id'=>$model::CODE_CERTIFICATE,
			'settings'=>serialize(array($certificate)),
		));
		$promocodeId = 0;
		if ($model->save()) $promocodeId = (int) $model->id;

		/** @var $promocode Promocodes */
/*		$promocode = Promocodes::model();
//		$promocode->setAttribute('type_id', $promocode::CODE_CERTIFICATE);
//		$promocode->setAttribute('settings', serialize($certificate));
		if ($promocode->save(false)) $promocodeId = (int) $promocode->id;*/

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
	}

	function getPrice($id, $currencyId) {
		$certificate = $this->getCertificate($id);
		if (empty($certificate['promocode_id'])) return 0;


	}

}
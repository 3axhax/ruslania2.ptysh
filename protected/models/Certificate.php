<?php /*Created by Кирилл (16.11.2018 17:55)*/
class Certificate extends CActiveRecord {

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
		$criteria = new CDbCriteria;
		$criteria->condition = 't.id=:id';
		$criteria->params = array(':id' => $id);
		$list = Certificate::model()->findAll($criteria);

		if (!empty($list)) return $list[0]->attributes;
		return array();
	}

	function paid($id) {
		$sql = 'update ' . $this->tableName() . ' set date_pay = CURRENT_TIMESTAMP where (id = ' . (int) $id . ')';
		Yii::app()->db->createCommand($sql)->execute();
		//TODO:: добавить отправку писем
	}

}
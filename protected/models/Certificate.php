<?php /*Created by Кирилл (16.11.2018 17:55)*/
class Certificate extends CActiveRecord {

	public function rules() {
		return array(
			array('maket_id, nominal, payment_type_id', 'required'),
			array('fio_dest', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_DEST_NAME')),
			array('email_dest', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_DEST_EMAIL')),
			array('fio_source', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_SOURCE_NAME')),
			array('email_source', 'required', 'message'=>Yii::app()->ui->item('YOU_MUST_FILL_THE_FIELD') . ' ' . Yii::app()->ui->item('CERTIFICATE_SOURCE_EMAIL')),
			array('txt_dest', 'safe'),
		);
	}

	public function beforeSave() {
		$this->setAttribute('uid', Yii::app()->user->id);
		$this->setAttribute('currency', Yii::app()->currency);
		return parent::beforeSave();
	}

	public function tableName() {
		return 'certificate_orders';
	}

}
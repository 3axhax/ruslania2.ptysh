<?php
/*Created by Кирилл (18.03.2019 21:37)*/

class SendCalls extends CMyActiveRecord
{
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'send_calls';
	}

	public function rules() {
		return array(
			array('face, phone', 'required'),
			array('phone', 'checkPhone'),
		);
	}

	function checkPhone($attr, $params) {
		$phone = trim($this->$attr);
		if (mb_strlen($phone, 'utf-8') < 6) {
			$this->addError($attr, Yii::app()->ui->item('PHONE_WITH_CODE'));
		}
	}

}
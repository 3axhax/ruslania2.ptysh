<?php
/*Created by Кирилл (17.03.2019 15:48)*/

class UsersSocials extends CMyActiveRecord
{
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'users_socials';
	}

	public function rules() {
		return array(
			array('id_user, id_social, type_social, user_info ', 'safe'),
		);
	}
	function getUserInfoForAddressForm($idUserSocial) {
		$ret = array();
		$uS = $this->findByPk($idUserSocial);
		if (!empty($uS)) {
			$ret = array(
				'users_socials_id'=>$uS->getAttribute('users_socials_id'),
				'is_business' => 0,
				'business_title' => '',
				'email'=>'',
				'phone'=>'',
				'receiver_first_name'=>'',
				'receiver_last_name'=>'',
			);
			$userInfo = $uS->getAttribute('user_info');
			if (empty($userInfo)) $userInfo = array();
			else $userInfo = unserialize($userInfo);
			switch ((string) $uS->getAttribute('type_social')) {
				case Instagram::SHORTNAME:
					$ret['is_business'] = empty($userInfo['is_business'])?0:1;
					$ret['business_title'] = empty($userInfo['full_name'])?'':$userInfo['full_name'];
					break;
				case Vk::SHORTNAME:
					$ret['receiver_first_name'] = empty($userInfo['first_name'])?'':$userInfo['first_name'];
					$ret['receiver_last_name'] = empty($userInfo['last_name'])?'':$userInfo['last_name'];
					$ret['email'] = empty($userInfo['email'])?'':$userInfo['email'];
					break;
			}
		}
		return $ret;
	}

}
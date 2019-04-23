<?php
/*Created by Кирилл (17.03.2019 20:44)*/
class Vk {
	const SHORTNAME = 'vk';

	private $_clientId = '';
	private $_clientSecret = '';
	private $_serviceKey = '';
	private $_v = '';
	private $_authUrl = '';
	private $_tokenUrl = '';
	private $_userUrl = '';
	private $_redirectUrl = '';

	function __construct() {
		$cfg = include Yii::getPathOfAlias('webroot') . '/cfg/social.php';
		$this->_clientId = $cfg[Vk::SHORTNAME]['clientId'];
		$this->_clientSecret = $cfg[Vk::SHORTNAME]['clientSecret'];
		$this->_serviceKey = $cfg[Vk::SHORTNAME]['serviceKey'];
		$this->_v = $cfg[Vk::SHORTNAME]['v'];
		$this->_authUrl = $cfg[Vk::SHORTNAME]['authUrl'];
		$this->_tokenUrl = $cfg[Vk::SHORTNAME]['tokenUrl'];
		$this->_userUrl = $cfg[Vk::SHORTNAME]['userUrl'];
		$this->_redirectUrl = $cfg[Vk::SHORTNAME]['redirectUrl'];
		switch (Yii::app()->getLanguage()) {
			case 'ru': break;
			case 'rut': $this->_redirectUrl = str_replace('/ru/', '/', $this->_redirectUrl) . '?language=rut'; break;
			default: $this->_redirectUrl = str_replace('/ru/', '/' . Yii::app()->getLanguage() . '/', $this->_redirectUrl); break;
		}
	}

	function urlCode() {
		$param = array(
			'client_id' => $this->_clientId,
			'redirect_uri' => $this->_redirectUrl,
			'response_type' => 'code',
			'v' => $this->_v,
			'display'=>'page',
			'scope'=>'email',
		);
		return $this->_authUrl . '?' . http_build_query($param);
	}

	function getToken($code) {
		$param = array(
			'client_id' => $this->_clientId,
			'client_secret' => $this->_clientSecret,
			'redirect_uri' => $this->_redirectUrl,
			'code' => $code,
		);
		$url = $this->_tokenUrl;
		$curl = curl_init($url);
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);
		curl_close($curl);
		return json_decode($result, true);
	}

	function getUser($vkUserId, $token) {
		$param = array(
			'user_id' => $vkUserId,
			'access_token'=>$token,
			'v' => $this->_v,
		);
		$url = $this->_userUrl;
		$curl = curl_init($url);
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);
		curl_close($curl);
		$result = json_decode($result, true);
		return $result;
	}

}
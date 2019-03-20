<?php
/*Created by Кирилл (17.03.2019 20:44)*/
class Vk {
	const SHORTNAME = 'vk';

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

<?php
/*Created by Кирилл (15.03.2019 18:02)*/

class Instagram {
	const SHORTNAME = 'insta';

	private $_clientId = '';
	private $_clientSecret = '';
	private $_authUrl = '';
	private $_redirectUrl = '';
	private $_tokenUrl = '';
	private $_login = '';
	private $_accessToken = '';

	function __construct() {
		$cfg = include Yii::getPathOfAlias('webroot') . '/cfg/social.php';
		$this->_clientId = $cfg[Instagram::SHORTNAME]['clientId'];
		$this->_clientSecret = $cfg[Instagram::SHORTNAME]['clientSecretId'];
		$this->_authUrl = $cfg[Instagram::SHORTNAME]['authUrl'];
		$this->_redirectUrl = $cfg[Instagram::SHORTNAME]['redirectUrl'];
		$this->_tokenUrl = $cfg[Instagram::SHORTNAME]['tokenUrl'];
		$this->_login = $cfg[Instagram::SHORTNAME]['login'];
		$this->_accessToken = $cfg[Instagram::SHORTNAME]['accessToken'];
	}

	function urlCode() {
		$param = array(
			'client_id' => $this->_clientId,
			'redirect_uri' => $this->_redirectUrl,
			'response_type' => 'code',
		);
//		file_put_contents(Yii::getPathOfAlias('webroot') . '/test/instagram.log', implode("\t", array(
//				$this->_authUrl . '?' . http_build_query($param),
//			)
//		) . "\n", FILE_APPEND);
		return $this->_authUrl . '?' . http_build_query($param);
	}

	function getUser($code = null) {
		if ($code !== null) {
			$param = array(
				'client_id' => $this->_clientId,
				'client_secret' => $this->_clientSecret,
				'grant_type' => 'authorization_code',
				'redirect_uri' => $this->_redirectUrl,
				'code' => $code,
			);
			$curl = curl_init($this->_tokenUrl);
			curl_setopt($curl,CURLOPT_POST,true);
			curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$res = curl_exec($curl);
			curl_close($curl);
			$res = json_decode($res, true);
//			file_put_contents(Yii::getPathOfAlias('webroot') . '/test/instagram.log', implode("\t", array(
//					$this->_tokenUrl,
//					http_build_query($param),
//					serialize($res),
//				)
//			) . "\n", FILE_APPEND);
			if (!empty($res['access_token'])) {
				$accessToken = $res['access_token'];
				if (!empty($res['user'])) return array('data'=>$res['user']);
			}
		}
		else $accessToken = $this->_accessToken;
		if (empty($accessToken)) return array();
//		else {
			$param = array(
				'access_token' => $accessToken,
			);
			$url = 'https://api.instagram.com/v1/users/self/?' . http_build_query($param);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($curl);
			curl_close($curl);
			$result = json_decode($result, true);
//			file_put_contents(Yii::getPathOfAlias('webroot') . '/test/instagram.log', implode("\t", array(
//					'https://api.instagram.com/v1/users/self/?' . http_build_query($param),
//					serialize($result),
//				)
//			) . "\n", FILE_APPEND);
//		}
		return $result;
	}

	function getMedia() {
		$param = array(
			'access_token' => $this->_accessToken,
			'count' => 9
		);
		$url = 'https://api.instagram.com/v1/users/self/media/recent/?' . http_build_query($param);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);
		curl_close($curl);
		$result = json_decode($result, true);
		return $result;
	}

	function generate_sig($endpoint, $params, $secret) {
		$sig = $endpoint;
		ksort($params);
		foreach ($params as $key => $val) {
			$sig .= "|$key=$val";
		}
		return hash_hmac('sha256', $sig, $secret, false);
	}
	/*
	 $endpoint = '/media/657988443280050001_25025320';
$params = array(
  'access_token' => 'fb2e77d.47a0479900504cb3ab4a1f626d174d2d',
  'count' => 10,
);
$secret = '6dc1787668c64c939929c17683d7cb74';

$sig = generate_sig($endpoint, $params, $secret);
echo $sig;*/

}
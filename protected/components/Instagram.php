<?php
/*Created by Кирилл (15.03.2019 18:02)*/

class Instagram {
	const SHORTNAME = 'insta';



	function __construct($login = true) {
		if ($login) {
			$this->_clientId = '0ead008ed078401b8681ad43286882cd';
			$this->_clientSecret = '25623aa2b303471fa70a642d87a6bab8 ';
		}
	}

	function urlCode() {
		$param = array(
			'client_id' => $this->_clientId,
			'redirect_uri' => $this->_redirectUrl,
			'response_type' => 'code',
		);
		return $this->_authUrl . '?' . http_build_query($param);
	}

	function getUser($code = null) {
		$accessToken = $this->_accessToken;
		if ($code !== null) $accessToken = $code;
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
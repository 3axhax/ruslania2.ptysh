<?php
/*Created by Кирилл (15.03.2019 18:02)*/

class Instagram {
	const SHORTNAME = 'insta';

	private $_clientId = '563249cf22a94cf98837e74c962c2acb';
	private $_clientSecret = '599805cb8720423fa42ed3780ff7dfdd';
	private $_authUrl = 'https://api.instagram.com/oauth/authorize/';
	private $_redirectUrl = 'https://beta.ruslania.com/ru/widgets/dataInstagram/';
	private $_login = 'ruslaniabooks';
	private $_accessToken = '1744713549.563249c.e55128f09b30458aa00f49f11685de1b';


	function __construct($login = true) {
		if ($login) {
			$this->_clientId = '0ead008ed078401b8681ad43286882cd';
			$this->_clientSecret = '25623aa2b303471fa70a642d87a6bab8 ';
		}
	}
/*	function getToken() {
		$param = array(
			'client_id' => $this->_clientId,
			'redirect_uri' => $this->_redirectUrl,
			'response_type' => 'token',
			'scope' => 'basic',
		);
		$url = 'https://api.instagram.com/oauth/authorize?' . http_build_query($param);
		$curl = curl_init($url);    // we init curl by passing the url
		curl_setopt($curl,CURLOPT_POST,true);   // to send a POST request
//		curl_setopt($curl,CURLOPT_POSTFIELDS,$param);   // indicate the data to send
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer certificate.
		$result = curl_exec($curl);   // to perform the curl session
		curl_close($curl);   // to close the curl session
		Debug::staticRun(array($url, $result));
	}*/

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
		$curl = curl_init($url);    // we init curl by passing the url
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer certificate.
		$result = curl_exec($curl);   // to perform the curl session
		curl_close($curl);   // to close the curl session
		$result = json_decode($result, true);
		return $result;
	}

	function getMedia() {
		$param = array(
			'access_token' => $this->_accessToken,
			'count' => 9
		);
		$url = 'https://api.instagram.com/v1/users/self/media/recent/?' . http_build_query($param);
		$curl = curl_init($url);    // we init curl by passing the url
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer certificate.
		$result = curl_exec($curl);   // to perform the curl session
		curl_close($curl);   // to close the curl session
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
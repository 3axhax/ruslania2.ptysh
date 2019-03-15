<?php
/*Created by Кирилл (15.03.2019 18:02)*/

class Instagram {

	private $_clientId = '563249cf22a94cf98837e74c962c2acb';
	private $_clientSecret = '599805cb8720423fa42ed3780ff7dfdd';
	private $_authUrl = 'https://api.instagram.com/oauth/authorize/';
	private $_tokkenUrl = 'https://api.instagram.com/oauth/access_token';
	private $_redirectUrl = 'https://beta.ruslania.com/ru/url/tokken/';
	private $_login = 'ruslaniabooks';
	private $_accessToken = '1744713549.563249c.e55128f09b30458aa00f49f11685de1b';

/*	function getTokken() {
		$access_token_parameters = array(
			'client_id'                =>     $this->_clientId,
			'client_secret'            =>     $this->_clientSecret,
			'grant_type'               =>     'authorization_code',
			'redirect_uri'             =>     $this->_redirectUrl,
			'code'                     =>     $code
		);

		$curl = curl_init($this->_tokkenUrl);    // we init curl by passing the url
		curl_setopt($curl,CURLOPT_POST,true);   // to send a POST request
		curl_setopt($curl,CURLOPT_POSTFIELDS,$access_token_parameters);   // indicate the data to send
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer certificate.
		$result = curl_exec($curl);   // to perform the curl session
		curl_close($curl);   // to close the curl session

	}*/

	function getToken() {
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
	}

	function getUser() {
		$param = array(
			'access_token' => $this->_accessToken,
		);
		$url = 'https://api.instagram.com/v1/users/self/?' . http_build_query($param);
		$curl = curl_init($url);    // we init curl by passing the url
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer certificate.
		$result = curl_exec($curl);   // to perform the curl session
		curl_close($curl);   // to close the curl session
		$result = json_decode($result, true);
		Debug::staticRun(array($url, $result));
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
		Debug::staticRun(array($url, $result));
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
<?php
/*Created by Кирилл (18.03.2019 20:34)*/
require_once dirname(dirname(__FILE__)) . '/extensions/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter {
	const SHORTNAME = 'tw';


	function urlCode() {
		$connection = new TwitterOAuth($this->_apiKey, $this->_apiSecret);
		$temporary_credentials = $connection->oauth('oauth/request_token', array("oauth_callback" =>$this->_redirectUrl));
//		$oauth_token = $temporary_credentials['oauth_token'];
//		$oauth_token_secret = $temporary_credentials['oauth_token_secret'];
		return $connection->url("oauth/authorize", array("oauth_token" => $temporary_credentials['oauth_token']));
	}

	function getUser($oauth_verifier, $oauth_token) {
		$connection = new TwitterOAuth($this->_apiKey, $this->_apiSecret);
		$params=array("oauth_verifier" => $oauth_verifier,"oauth_token"=>$oauth_token);
		$access_token = $connection->oauth("oauth/access_token", $params);
		$connection = new TwitterOAuth($this->_apiKey, $this->_apiSecret, $access_token['oauth_token'],$access_token['oauth_token_secret']);
		$res = $connection->get("account/verify_credentials");
		$user = array();
		if ($res) {
			$res = get_object_vars($res);
			if (!empty($res['id'])) $user['id'] = $res['id'];
			if (!empty($res['name'])) $user['name'] = $res['name'];
			if (!empty($res['location'])) $user['location'] = $res['location'];
		}
		return $user;
	}

}
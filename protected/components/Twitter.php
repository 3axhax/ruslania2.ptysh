<?php
/*Created by Кирилл (18.03.2019 20:34)*/
require_once dirname(dirname(__FILE__)) . '/extensions/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter {
	const SHORTNAME = 'tw';

	private $_apiId = 1519126;
	private $_apiKey = 'Gh87DOTWmpij1ihZSmoXA';
	private $_apiSecret = 'mVlbON6gEgWdeUP0Iw9E5BTatkkgkG4Pr6HYTwCa8';
	private $_accessToken = '460037939-K7D6KlsyCAINzn2IS67JNz6NkMBVSREJsDQelLgp';
	private $_accessTokenSecret = 'BCsVEJxoMqrua2PqVwaOKV8nmpQwpa0LocsXBZROQ';
	private $_redirectUrl = 'https://beta.ruslania.com/ru/widgets/datatwitter/';
	private $_authUrl = 'https://api.twitter.com/oauth/authorize';
	private $_accessTokenUrl = 'https://api.twitter.com/oauth/access_token';
	private $_requestTokenUrl = 'https://api.twitter.com/oauth/request_token';
	private $_userUrl = 'https://api.twitter.com/1.1/users/show.json';
	private $_v = '1.1';

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
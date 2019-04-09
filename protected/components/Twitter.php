<?php
/*Created by Кирилл (18.03.2019 20:34)*/
require_once dirname(dirname(__FILE__)) . '/extensions/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter {
	const SHORTNAME = 'tw';

	private $_apiId = '';
	private $_apiKey = '';
	private $_apiSecret = '';
	private $_accessToken = '';
	private $_accessTokenSecret = '';
	private $_redirectUrl = '';
	private $_authUrl = '';
	private $_accessTokenUrl = '';
	private $_requestTokenUrl = '';
	private $_userUrl = '';
	private $_v = '';

	function __construct() {
		$cfg = include Yii::getPathOfAlias('webroot') . '/cfg/social.php';
		$this->_apiId = $cfg[Twitter::SHORTNAME]['apiId'];
		$this->_apiKey = $cfg[Twitter::SHORTNAME]['apiKey'];
		$this->_apiSecret = $cfg[Twitter::SHORTNAME]['apiSecret'];
		$this->_accessToken = $cfg[Twitter::SHORTNAME]['accessToken'];
		$this->_accessTokenSecret = $cfg[Twitter::SHORTNAME]['accessTokenSecret'];
		$this->_redirectUrl = $cfg[Twitter::SHORTNAME]['redirectUrl'];
		$this->_authUrl = $cfg[Twitter::SHORTNAME]['authUrl'];
		$this->_accessTokenUrl = $cfg[Twitter::SHORTNAME]['accessTokenUrl'];
		$this->_requestTokenUrl = $cfg[Twitter::SHORTNAME]['requestTokenUrl'];
		$this->_userUrl = $cfg[Twitter::SHORTNAME]['userUrl'];
		$this->_v = $cfg[Twitter::SHORTNAME]['v'];
	}

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
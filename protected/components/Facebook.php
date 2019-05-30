<?php
/*Created by Кирилл (19.03.2019 15:07)*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once dirname(dirname(__FILE__)) . '/extensions/Facebook/autoload.php';
class Facebook {
	const SHORTNAME = 'fb';
	private $_apiId = '';
	private $_apiSecret = '';
	private $_v = '';
	private $_redirectUrl = '';
	/**
	 * @var Facebook\Facebook
	 */
	protected $_fb;

	function __construct() {
		$referer = Yii::app()->getRequest()->getUrlReferrer();
		$request = new MyRefererRequest();
		$request->setFreePath($referer);
		$refererRoute = Yii::app()->getUrlManager()->parseUrl($request);
		$dataUrl = array();
		if ($refererRoute == 'site/register') $dataUrl['page'] = 'register';

		$cfg = include Yii::getPathOfAlias('webroot') . '/cfg/social.php';
		$this->_apiId = $cfg[Facebook::SHORTNAME]['apiId'];
		$this->_apiSecret = $cfg[Facebook::SHORTNAME]['apiSecret'];
		$this->_v = $cfg[Facebook::SHORTNAME]['v'];
		$this->_redirectUrl = $cfg[Facebook::SHORTNAME]['redirectUrl'];
		switch (Yii::app()->getLanguage()) {
			case 'ru': break;
			case 'rut':
				$dataUrl['language'] = 'rut';
				$this->_redirectUrl = str_replace('/ru/', '/', $this->_redirectUrl);
				break;
			default:
				$this->_redirectUrl = str_replace('/ru/', '/' . Yii::app()->getLanguage() . '/', $this->_redirectUrl);
				break;
		}
		if (!empty($dataUrl)) $this->_redirectUrl .= '?' . http_build_query($dataUrl);

		$this->_fb = new Facebook\Facebook([
			'app_id' => $this->_apiId,
			'app_secret' => $this->_apiSecret,
			'default_graph_version' => $this->_v,
		]);
	}

	function urlCode() {
		$helper = $this->_fb->getRedirectLoginHelper();
		$permissions = ['scope'=>'email']; // Optional permissions
		return $helper->getLoginUrl($this->_redirectUrl, $permissions);
	}

	function getUser() {
		$accessToken = $this->_getToken();
		$userInfo = $this->_userInfo($accessToken);
		return $userInfo;

/*		$accessToken = $this->_accessToken;
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
		return $result;*/
	}

	protected function _getToken() {
		$helper = $this->_fb->getRedirectLoginHelper();

		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		if (! isset($accessToken)) {
			if ($helper->getError()) {
				header('HTTP/1.0 401 Unauthorized');
				echo "Error: " . $helper->getError() . "\n";
				echo "Error Code: " . $helper->getErrorCode() . "\n";
				echo "Error Reason: " . $helper->getErrorReason() . "\n";
				echo "Error Description: " . $helper->getErrorDescription() . "\n";
			} else {
				header('HTTP/1.0 400 Bad Request');
				echo 'Bad request';
			}
			exit;
		}
// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->_fb->getOAuth2Client();
// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		return $accessToken;
	}

	protected function _userInfo($accessToken) {
		$response = $this->_fb->get('/me?fields=id,name,email', $accessToken->getValue());
		$userNode = $response->getGraphUser();
		$result = [
			'id'=>$userNode->getField('id'),
			'name'=>$userNode->getField('name'),
			'email'=>$userNode->getField('email'),
		];
		return $result;
	}


}
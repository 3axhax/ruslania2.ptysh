<?php
/*Created by Кирилл (15.03.2019 20:11)*/
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
class WidgetsController extends MyController {
	private $_locale = array('se'=>'sv_SE', 'fi'=>'fi_FI', 'en'=>'en_US', 'de'=>'de_DE', 'fr'=>'fr_FR', 'ru'=>'ru_RU', 'es'=>'es_LA');

	function actionInstagram() {
		$instaData = [];
		$file = Yii::getPathOfAlias('webroot') . '/test/instagram.php';
		if (file_exists($file)) {
			$dateFile = filemtime($file);
			Debug::staticRun(array($dateFile, date('d.m.Y H:i:s'), date('d.m.Y H:i:s', $dateFile)));
			if ($dateFile > (time() - 3600)) $instaData = include($file);
		}
		if (empty($instaData)) {
			$insta = new Instagram();
			$media = $insta->getMedia();
			$user = $insta->getUser();
			$instaData = array('user'=>$user['data'], 'images'=>$media['data']);
			file_put_contents($file, '<?php return ' . var_export($instaData, true) . ';');
		}
		$this->renderPartial('instagram', $instaData);
	}

	function actionDataInstagram() {
		$ret = array();
		$url = Yii::app()->createUrl('cart/noregister') . '?useSocial=1';
		$code = (string) Yii::app()->getRequest()->getParam('code');
		if (!empty($code)) {
			$insta = new Instagram();
			$user = $insta->getUser($code);
			if (!empty($user['data'])) {
				$ret = $user['data'];
				if (!empty($ret['id'])) {
					$isAuth = $this->_saveSocial($ret['id'], Instagram::SHORTNAME, $ret);
					if ($isAuth) $url = Yii::app()->createUrl('cart/doorder');
				}
			}
			else $ret = $user;
		}
		$this->renderPartial('user_instagram', array('userInfo'=>$ret, 'url'=>$url));
	}

	function actionAuthInstagram() {
		$insta = new Instagram();
		$this->redirect($insta->urlCode());
	}

	function actionDataVk() {
		$user = array();
		$url = Yii::app()->createUrl('cart/noregister') . '?useSocial=1';
		$code = (string) Yii::app()->getRequest()->getParam('code');
		if (!empty($code)) {
			$vk = new Vk();
			$token = $vk->getToken($code);
			if (!empty($token['user_id'])) {
				$res = $vk->getUser($token['user_id'], $token['access_token']);
				if (!empty($res['response'])) $user = array_shift($res['response']);
			}
			if (!empty($token['email'])) $user['email'] = $token['email'];

			$isAuth = $this->_saveSocial($user['id'], Vk::SHORTNAME, $user);
			if ($isAuth) $url = Yii::app()->createUrl('cart/doorder');
		}
		$this->renderPartial('user_vk', array('userInfo'=>$user, 'url'=>$url));
	}

	function actionAuthVk() {
		$vk = new Vk();
		$this->redirect($vk->urlCode());
	}

	function actionDataTwitter() {
		$user = array();
		$url = Yii::app()->createUrl('cart/noregister') . '?useSocial=1';
		$oauth_verifier = (string) Yii::app()->getRequest()->getParam('oauth_verifier');
		$oauth_token = (string) Yii::app()->getRequest()->getParam('oauth_token');
		if (!empty($oauth_verifier)&&!empty($oauth_token)) {
			$tw = new Twitter();
			$user = $tw->getUser($oauth_verifier, $oauth_token);
			if (!empty($user['id'])) {
				$isAuth = $this->_saveSocial($user['id'], Twitter::SHORTNAME, $user);
				if ($isAuth) $url = Yii::app()->createUrl('cart/doorder');
			}
		}
		$this->renderPartial('user_twitter', array('userInfo'=>$user, 'url'=>$url));
	}

	function actionAuthTwitter() {
		$tw = new Twitter();
		$this->redirect($tw->urlCode());
	}

	function actionDataFacebook() {
		$url = Yii::app()->createUrl('cart/noregister') . '?useSocial=1';

		$facebook = new Facebook();
		$user = $facebook->getUser();
		if (!empty($user['id'])) {
			$isAuth = $this->_saveSocial($user['id'], Facebook::SHORTNAME, $user);
			if ($isAuth) $url = Yii::app()->createUrl('cart/doorder');
		}
		$this->renderPartial('user_facebook', array('userInfo'=>$user, 'url'=>$url));
	}

	function actionAuthFacebook() {
		$fb = new Facebook();
		$this->redirect($fb->urlCode()/* . '&locale=' . $this->_locale[Yii::app()->getLanguage()]*/);
	}

	private function _auth($uid) {
		$user = User::model()->findByPk($uid);
		$result = false;
		if ($user) {
			$identity = new RuslaniaUserIdentity($user->getAttribute('login'), $user->getAttribute('pwd'));
			if ($identity->authorize($user)) {
				$result = Yii::app()->user->login($identity, Yii::app()->params['LoginDuration']);
				$cart = new Cart();
				$cart->UpdateCartToUid($this->sid, $identity->getId());
			}
		}
		return $result;
	}

	private function _saveSocial($id, $name, $data) {
		$isAuth = false;
		if (!empty($id)) {
			$userSocialModel = UsersSocials::model();
			$uS = $userSocialModel->findByAttributes(array('type_social'=>$name, 'id_social'=>$id));
			if (!empty($uS)) {
				$idUserSocial = $uS->getAttribute('users_socials_id');
				if ($this->_auth($uS->getAttribute('id_user'))) {
					$isAuth = true;
				}
				else {
					unset($data['bio']);
					$userSocialModel->updateByPk($uS->getAttribute('users_socials_id'), array('user_info'=>serialize($data)));
				}
			}
			else {
				unset($data['bio']);
				$userSocialModel->setAttributes(array('type_social'=>$name, 'id_social'=>$id,'user_info'=>serialize($data)));
				$userSocialModel->setIsNewRecord(true);
				$userSocialModel->insert();
				$idUserSocial = $userSocialModel->users_socials_id;
			}
			Yii::app()->session['user_social'] = $idUserSocial;
		}
		return $isAuth;
	}
}
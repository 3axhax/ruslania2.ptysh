<?php
/*Created by Кирилл (15.03.2019 20:11)*/
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class WidgetsController extends MyController {

	function actionInstagram() {
		$instaData = [];
		$file = Yii::getPathOfAlias('webroot') . '/test/instagram.php';
		if (file_exists($file)) {
			$dateFile = filemtime($file);
			if ($dateFile < (time() - 3600)) $instaData = include($file);
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
			$user = $insta->getUser();
			$ret = $user['data'];
			if (!empty($ret['id'])) {
				$userSocialModel = UsersSocials::model();
				$uS = $userSocialModel->findByAttributes(array('type_social'=>Instagram::SHORTNAME, 'id_social'=>$ret['id']));
				if (!empty($uS)) {
					$idUserSocial = $uS->getAttribute('users_socials_id');
					if ($this->_auth($uS->getAttribute('id_user'))) {
						$url = Yii::app()->createUrl('cart/doorder');
					}
					else {
						$userSocialModel->updateByPk($uS->getAttribute('users_socials_id'), array('user_info'=>serialize($ret)));
					}
				}
				else {
					unset($ret['bio']);
					$userSocialModel->setAttributes(array('type_social'=>Instagram::SHORTNAME, 'id_social'=>$ret['id'],'user_info'=>serialize($ret)));
					$userSocialModel->setIsNewRecord(true);
					$userSocialModel->insert();
					$idUserSocial = $userSocialModel->users_socials_id;
				}
				Yii::app()->session['user_social'] = $idUserSocial;
			}
		}
		$this->renderPartial('user_instagram', array('userInfo'=>$ret, 'url'=>$url));
	}

	function actionAuthInstagram() {
		$insta = new Instagram();
		$this->redirect($insta->urlCode());
	}

	private function _auth($uid) {
		$user = User::model()->findByPk($uid);
		$result = false;
		if ($user) {
			$identity = new RuslaniaUserIdentity($user->getAttribute('login'), $user->getAttribute('pwd'));
			if ($identity->authorize($user)) {
				$result = Yii::app()->user->login($identity, Yii::app()->params['LoginDuration']);
			}
		}
		return $result;
	}

}
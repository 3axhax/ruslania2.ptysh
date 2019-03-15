<?php
/*Created by Кирилл (15.03.2019 20:11)*/
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

}
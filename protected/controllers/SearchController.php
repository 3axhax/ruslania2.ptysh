<?php
/*Created by Кирилл (09.06.2018 23:37)*/
class SearchController extends MyController {

	function actionLive() {
		$q = Yii::app()->getRequest()->getParam('q');
		if (!empty($q)) {

		}
	}

}
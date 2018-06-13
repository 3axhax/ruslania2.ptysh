<?php
/*Created by Кирилл (11.06.2018 20:55)*/

class LiveSearchController extends MyController {

	function actionAuthors() {
		if (!$this->_check('authors')) return;

		$entity = Yii::app()->getRequest()->getParam('entity');
		$authors = SearchAuthors::get()->getAuthors($entity, (string)Yii::app()->getRequest()->getParam('q'));

		$url ='/entity/byauthor';
		$param = array('entity' => Entity::GetUrlKey($entity), 'aid' => 0, 'title' => '');
		$titleField = 'title_' . SearchAuthors::get()->getSiteLang();
		foreach ($authors as $i=>$author) {
			$authors[$i]['title'] = $author[$titleField];
			unset($authors[$i][$titleField]);
			$param['aid'] = $author['id'];
			$param['title'] = ProductHelper::ToAscii($authors[$i]['title']);
			$authors[$i]['href'] = Yii::app()->createUrl($url, $param);
		}
		$this->ResponseJson($authors);
	}


	private function _check($tag) {
		$requers = Yii::app()->getRequest();
		$entity = $requers->getParam('entity');

		if (!Entity::IsValid($entity)) return false;
		if (!Entity::checkEntityParam($entity, $tag)) return false;

		return true;
	}

}
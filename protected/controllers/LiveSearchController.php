<?php
/*Created by Кирилл (11.06.2018 20:55)*/

class LiveSearchController extends MyController {

	function actionGeneral() {
		$result = array();
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		if (!empty($q)) {
			$sController = new SearchController($this->getId(), $this->getModule());
			$sController->beforeAction($this->getAction());

			$isCode = false;
			if ($code = $sController->isCode($q)) {
				$list = $sController->getByCode($code, $q);
				if (!empty($list)) $isCode = true;
			}

			if (!$isCode) {
				$list = $sController->getList($q, 1, 10);
				$list = $sController->inDescription($list, $q);
			}


			if (!$isCode) {
				$abstractInfo = $sController->getEntitys($q);
				if (!empty($abstractInfo))
					$result[] = $this->renderPartial('/search/entitys', array('q' => $q, 'abstractInfo' => $abstractInfo), true);
			}

			if (!empty($list))
				$result[] = $this->renderPartial('/search/live_header', array('q' => $q), true);

			if (!$isCode) {
				$didYouMean = $sController->getDidYouMean($q);
				if (!empty($didYouMean))
					$result[] = $this->renderPartial('/search/did_you_mean', array('q' => $q, 'items' => $didYouMean), true);
			}

			if (!empty($list)) {
				foreach ($list as $row) {
					$result[] = $this->renderPartial('/search/live_list', array('q' => $q, 'item' => $row), true);
				}
			}
		}
		$this->ResponseJson($result);
	}

	function actionGeneralHa() {
		$result = array();
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		if (!empty($q)) {
			$sController = new SearchController($this->getId(), $this->getModule());
			$sController->beforeAction($this->getAction());

			$isCode = false;
			if ($code = $sController->isCode($q)) {
				$list = $sController->getByCode($code, $q);
				if (!empty($list)) $isCode = true;
			}

			if (!$isCode) $list = $sController->getList($q, 1, 10);

			if (!empty($list)) $list = $sController->inDescription($list, $q);

			if (!$isCode) {
				$abstractInfo = $sController->getEntitys($q);
				if (!empty($abstractInfo))
					$result[] = $this->renderPartial('/search/entitys', array('q' => $q, 'abstractInfo' => $abstractInfo));
			}

			if (!empty($list))
				$result[] = $this->renderPartial('/search/live_header', array('q' => $q));

			if (!$isCode) {
				$didYouMean = $sController->getDidYouMean($q);
				if (!empty($didYouMean))
					$result[] = $this->renderPartial('/search/did_you_mean', array('q' => $q, 'items' => $didYouMean));
			}

			if (!empty($list)) {
				foreach ($list as $row) {
					$result[] = $this->renderPartial('/search/live_list', array('q' => $q, 'item' => $row));
				}
			}

		}
	}


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

	function actionActors() {
		if (!$this->_check('actors')) return;

		$entity = Yii::app()->getRequest()->getParam('entity');
		$items = SearchActors::get()->getActors($entity, (string)Yii::app()->getRequest()->getParam('q'));

		$url ='/entity/byactor';
		$param = array('entity' => Entity::GetUrlKey($entity), 'aid' => 0, 'title' => '');
		$titleField = 'title_' . SearchActors::get()->getSiteLang();
		foreach ($items as $i=>$item) {
			$items[$i]['title'] = $item[$titleField];
			unset($items[$i][$titleField]);
			$param['aid'] = $item['id'];
			$param['title'] = ProductHelper::ToAscii($items[$i]['title']);
			$items[$i]['href'] = Yii::app()->createUrl($url, $param);
		}
		$this->ResponseJson($items);
	}

	function actionDirectors() {
		if (!$this->_check('directors')) return;

		$entity = Yii::app()->getRequest()->getParam('entity');
		$items = SearchDirectors::get()->getDirectors($entity, (string)Yii::app()->getRequest()->getParam('q'));

		$url ='/entity/bydirector';
		$param = array('entity' => Entity::GetUrlKey($entity), 'did' => 0, 'title' => '');
		$titleField = 'title_' . SearchDirectors::get()->getSiteLang();
		foreach ($items as $i=>$item) {
			$items[$i]['title'] = $item[$titleField];
			unset($items[$i][$titleField]);
			$param['did'] = $item['id'];
			$param['title'] = ProductHelper::ToAscii($items[$i]['title']);
			$items[$i]['href'] = Yii::app()->createUrl($url, $param);
		}
		$this->ResponseJson($items);
	}

	function actionPerformers() {
		if (!$this->_check('performers')) return;

		$entity = Yii::app()->getRequest()->getParam('entity');
		$items = SearchPerformers::get()->getPerformers($entity, (string)Yii::app()->getRequest()->getParam('q'));

		$url ='/entity/byperformer';
		$param = array('entity' => Entity::GetUrlKey($entity), 'pid' => 0, 'title' => '');
		$titleField = 'title_' . SearchPerformers::get()->getSiteLang();
		foreach ($items as $i=>$item) {
			$items[$i]['title'] = $item[$titleField];
			unset($items[$i][$titleField]);
			$param['pid'] = $item['id'];
			$param['title'] = ProductHelper::ToAscii($items[$i]['title']);
			$items[$i]['href'] = Yii::app()->createUrl($url, $param);
		}
		$this->ResponseJson($items);
	}

	function actionPublishers() {
		if (!$this->_check('publisher')) return;

		$entity = Yii::app()->getRequest()->getParam('entity');
		$items = SearchPublishers::get()->getPublishers($entity, (string)Yii::app()->getRequest()->getParam('q'));

		$url ='/entity/bypublisher';
		$param = array('entity' => Entity::GetUrlKey($entity), 'pid' => 0, 'title' => '');
		$titleField = 'title_' . SearchPublishers::get()->getSiteLang();
		foreach ($items as $i=>$item) {
			$items[$i]['title'] = $item[$titleField];
			unset($items[$i][$titleField]);
			$param['pid'] = $item['id'];
			$param['title'] = ProductHelper::ToAscii($items[$i]['title']);
			$items[$i]['href'] = Yii::app()->createUrl($url, $param);
		}
		$this->ResponseJson($items);
	}


	private function _check($tag) {
		$requers = Yii::app()->getRequest();
		$entity = $requers->getParam('entity');

		if (!Entity::IsValid($entity)) return false;
		if (!Entity::checkEntityParam($entity, $tag)) return false;

		return true;
	}

	function actionFilter_Authors () {
	    if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
	    $cid = Yii::app()->getRequest()->getParam('cid');
	    $items = SearchAuthors::get()->getAuthorsForFilters($entity, $q, $cid);
	    $this->ResponseJson($items);
    }

    function actionFilter_Publishers () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchPublishers::get()->getPublishersForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }

    function actionFilter_Series () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchSeries::get()->getSeriesForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }

    function actionFilter_Performers () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchPerformers::get()->getPerformersForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }
}
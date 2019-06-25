<?php
/*Created by Кирилл (11.06.2018 20:55)*/
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
class LiveSearchController extends MyController {

	function actionGeneral() {
		$availForOrder = $this->GetAvail(1);
		$model = new SearchProducts($availForOrder);
		$result = array();
		$q = mb_strtolower(trim((string) Yii::app()->getRequest()->getParam('q')), 'utf-8');
		if (!empty($q)) {

			$isCode = false;
			if ($code = $model->isCode($q)) {
				$list = $model->getByCode($code, $q);
				if (!empty($list)) $isCode = true;
			}
			else {
				$likeCode = preg_replace("/[^0-9x]/ui", '', $q);
				if (((mb_strlen($q, 'utf-8') - mb_strlen($likeCode, 'utf-8')) < 5)&&($code = $model->isCode($likeCode))) {
					$list = $model->getByCode($code, $likeCode);
					if (!empty($list)) $isCode = true;
				}
			}

			if (!$isCode) {
				$list = $model->getByPath($q);
				if (!empty($list)) $isCode = true;
			}

			if (!$isCode) {
				$list = $model->getList($q, 1, 10);
				$list = $model->inDescription($list, $q);
				list($searchWords, $realWords, $useRealWord) = $model->getNormalizedWords($q);
				if (!$model->isFromNumeric($searchWords)) {
					$didYouMean = $model->getDidYouMean($q);
					$abstractInfo = $model->getEntitys($q);
				}
			}

			if (empty($list)&&empty($abstractInfo)&&empty($didYouMean)) {
				if ($availForOrder) {
					$availForOrder = 0;
					$model = new SearchProducts($availForOrder);
					$list = $model->getList($q, 1, 10);
				}
				if (empty($list)) $this->ResponseJson(array());
			}

			if (!$isCode) {
				if (!empty($abstractInfo))
					$result['entitys'] = $this->renderPartial('/search/entitys', array('q' => $q, 'abstractInfo' => $abstractInfo), true);
			}

//			if (!empty($list)||!empty($abstractInfo)||!empty($didYouMean))
				$result['header'] = $this->renderPartial('/search/live_header', array('q' => $q, 'availForOrder'=>$availForOrder), true);

			if (!$isCode) {
				if (!empty($didYouMean))
					$result['did_you_mean'] = $this->renderPartial('/search/did_you_mean', array('q' => $q, 'items' => $didYouMean), true);
			}

			if (empty($list)&&!empty($didYouMean)) $list = $model->getListByDidYouMean($didYouMean);
			if (!empty($list)) {
				$result['list'] = array();
				foreach ($list as $row) {
					$result['list'][] = $this->renderPartial('/search/live_list', array('q' => $q, 'item' => $row), true);
				}
			}

			if ($availForOrder&&(count($result) == 1)) {
				$model = new SearchProducts(0);
				$list = $model->getList($q, 1, 10);
				if (!empty($list)) {
					$list = $model->inDescription($list, $q);
					$result['list'] = array();
					foreach ($list as $row) {
						$result['list'][] = $this->renderPartial('/search/live_list', array('q' => $q, 'item' => $row), true);
					}
				}

			}

		}
		$this->ResponseJson(array($this->renderPartial('/search/live', array('q' => $q, 'result' => $result), true)));
	}

	function actionGeneralHa() {
		$start = microtime_float();
		$availForOrder = $this->GetAvail(1);
		$model = new SearchProducts($availForOrder);
		$result = array();
		$q = mb_strtolower(trim((string) Yii::app()->getRequest()->getParam('q')), 'utf-8');
//		$this->_haList($q, $model);
//		Debug::staticRun(array($q));
		if (!empty($q)) {
			Debug::staticRun(array(1, number_format(microtime_float() - $start, 4)));
			Debug::staticRun(array($q));
			$isCode = false;
			if ($code = $model->isCode($q)) {
				$list = $model->getByCode($code, $q);
				if (!empty($list)) $isCode = true;
			}
			else {
				$likeCode = preg_replace("/[^0-9x]/ui", '', $q);
				if (((mb_strlen($q, 'utf-8') - mb_strlen($likeCode, 'utf-8')) < 5)&&($code = $model->isCode($likeCode))) {
					$list = $model->getByCode($code, $likeCode);
					if (!empty($list)) $isCode = true;
				}
			}

			Debug::staticRun(array(2, number_format(microtime_float() - $start, 4)));
			if (!$isCode) {
				$list = $model->getByPath($q);
				if (!empty($list)) $isCode = true;
			}

			Debug::staticRun(array(3, number_format(microtime_float() - $start, 4)));
			if (!$isCode) {
				$list = $model->getList($q, 1, 100);
				Debug::staticRun(array(31, number_format(microtime_float() - $start, 4)));
				$list = $model->inDescription($list, $q);
				Debug::staticRun(array(32, number_format(microtime_float() - $start, 4)));
				$didYouMean = $model->getDidYouMean($q);
				Debug::staticRun(array(33, number_format(microtime_float() - $start, 4)));
				$abstractInfo = $model->getEntitys($q);
				Debug::staticRun(array(34, number_format(microtime_float() - $start, 4)));
			}

			Debug::staticRun(array(4, number_format(microtime_float() - $start, 4)));
			if (empty($list)&&empty($abstractInfo)&&empty($didYouMean)) {
				if ($availForOrder) {
					$model = new SearchProducts(0);
					$list = $model->getList($q, 1, 10);
				}
				if (empty($list)) $this->ResponseJson(array());
			}

			Debug::staticRun(array(5, number_format(microtime_float() - $start, 4)));
			if (!$isCode) {
				if (!empty($abstractInfo))
					$result['entitys'] = $this->renderPartial('/search/entitys', array('q' => $q, 'abstractInfo' => $abstractInfo));
			}

//			if (!empty($list)||!empty($abstractInfo)||!empty($didYouMean))
			Debug::staticRun(array(6, number_format(microtime_float() - $start, 4)));
			$result['header'] = $this->renderPartial('/search/live_header', array('q' => $q), true);

			if (!$isCode) {
				if (!empty($didYouMean))
					$result['did_you_mean'] = $this->renderPartial('/search/did_you_mean', array('q' => $q, 'items' => $didYouMean));
			}

			Debug::staticRun(array(7, number_format(microtime_float() - $start, 4)));
			if (empty($list)&&!empty($didYouMean)) $list = $model->getListByDidYouMean($didYouMean);
			if (!empty($list)) {
				$result['list'] = array();
				foreach ($list as $row) {
					$result['list'][] = $this->renderPartial('/search/live_list', array('q' => $q, 'item' => $row));
				}
			}
			Debug::staticRun(array(8, number_format(microtime_float() - $start, 4)));

		}
		$this->ResponseJson(array($this->renderPartial('/search/live', array('q' => $q, 'result' => $result))));
		Debug::staticRun(array(9, number_format(microtime_float() - $start, 4)));
	}


	function actionAuthors() {
		if (!$this->_check('authors')) return;

		$entity = Yii::app()->getRequest()->getParam('entity');
		$authors = SearchAuthors::get()->getAuthors($entity, (string)Yii::app()->getRequest()->getParam('q'), 20, false);

		$url ='/entity/byauthor';
		$param = array('entity' => Entity::GetUrlKey($entity), 'aid' => 0, 'title' => '');
		$titleField = 'title_' . SearchAuthors::get()->getSiteLang();
		foreach ($authors as $i=>$author) {
			unset($param['avail']);
			$authors[$i]['title'] = $author[$titleField];
			unset($authors[$i][$titleField]);
			$param['aid'] = $author['id'];
			$param['title'] = ProductHelper::ToAscii($authors[$i]['title']);
			if (isset($author['availItems'])&&empty($author['availItems'])) $param['avail'] = 0;
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

    function actionSelect_Filter_Publishers () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchPublishers::get()->getPublishersSelectFilters($entity, $cid);
        $this->ResponseJson($items);
    }

    function actionFilter_Series () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchSeries::get()->getSeriesForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }

    function actionSelect_Filter_Series () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchSeries::get()->getSeriesSelectFilters($entity, $cid);
        $this->ResponseJson($items);
    }

    function actionFilter_Performers () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchPerformers::get()->getPerformersForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }

    function actionFilter_Directors () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchDirectors::get()->getDirectorsForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }

    function actionFilter_Actors () {
        if (!($entity = Yii::app()->getRequest()->getParam('entity')) ||
            !($q = Yii::app()->getRequest()->getParam('q'))) return;
        $cid = Yii::app()->getRequest()->getParam('cid');
        $items = SearchActors::get()->getActorsForFilters($entity, $q, $cid);
        $this->ResponseJson($items);
    }


	protected function _haList($q, SearchProducts $model) {
		$model->getList($q, 1, 100);
//		$text = 'вызывает столь громкий звук; есть ли различие между предметом и его отражением и во сколько раз лупа позволяет увеличить следы преступления? А главное, как знание физики помогло знаменитым сыщикам из произведений Артура Конан Дойла, Агаты Кристи, Джона Гришема, Жоржа Сименона, Найо Марш и других распутать десятки преступлений!';
//		Debug::staticRun(array(SphinxQL::getDriver()->snippet($text, 'гришем')));

	}

}
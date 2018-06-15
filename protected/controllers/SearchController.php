<?php
/*Created by Кирилл (09.06.2018 23:37)*/
class SearchController extends MyController {
	//количество в результате поиска
	private $_counts = null;

	/**
	 * @var DGSphinxSearch
	 */
	private $_search;

	function actionGeneral() {
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		if (empty($q)) {
			$this->_viewEmpty($q);
			return;
		}

		$avail = $this->GetAvail(1);
		$page = $this->_getNumPage();
	}

	function getEntitys() {

	}

	function getDidYouMean() {

	}

	function getList() {

	}


	public function afterAction($action) {
		parent::afterAction($action);
		$q = trim((string) Yii::app()->getRequest()->getParam('q'));
		$e = (int) Yii::app()->getRequest()->getParam('e');
		SearchHelper::LogSearch(Yii::app()->user->id, $q, array('e' => $e, 'page' => $this->_getNumPage()), (int)$this->_counts);
	}

	function beforeAction($action) {
		$result = parent::beforeAction($action);
		if ($result) $this->_search = SearchHelper::Create();
		return $result;
	}



	private function _getNumPage() {
		return min(100000, max((int) Yii::app()->getRequest()->getParam('page'), 1));
	}

	private function _viewEmpty($q) {
		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
		$this->render(
			'search',
			array(
				'result' => array(),
				'q' => $q,
				'products' => array(),
				'paginatorInfo' => new CPagination(0),
			)
		);
	}

	private function _filterManyInShop($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop > 5) && ($avail > 0);
	}

	private function _filterFewInShop($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop <= 5) && ($avail > 0);
	}

	private function _filterUnderOrder($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop === 0) && ($avail > 0);
	}

	private function _filterNotAvailable($arr) {
		$isShop = (int)$arr['in_shop'];
		$avail = (int)$arr['avail_for_order'];
		return ($isShop === 0) && ($avail === 0);
	}

}
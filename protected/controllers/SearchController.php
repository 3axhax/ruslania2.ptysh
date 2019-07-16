<?php
/*Created by Кирилл (09.06.2018 23:37)*/
class SearchController extends MyController {
	//количество в результате поиска
	private $_counts = null;
	protected $_exactMatch = false;

	/**
	 * @var DGSphinxSearch
	 */
	private $_search;

	function actionGeneral() {
		$q = mb_strtolower(trim((string) Yii::app()->getRequest()->getParam('q')), 'utf-8');
		if (empty($q)) {
			$this->_viewEmpty($q);
			return;
		}

		$availForOrder = $this->GetAvail(1);
		$eId = (int) Yii::app()->getRequest()->getParam('e');
		if (isset($_GET['ha'])) $model = new SphinxProducts($availForOrder, $eId);
		else $model = new SearchProducts($availForOrder, $eId);
		Debug::staticRun(array($model));
		$model->savePhrase($q);
		$list = array();
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

		$page = $this->_getNumPage();
		$abstractInfo = array();
		$didYouMean = array();
		if (!$isCode) {
			$list = $model->getList($q, $page, Yii::app()->params['ItemsPerPage'], (int) Yii::app()->getRequest()->getParam('e'));
//			$list = $this->getList($q, $page, Yii::app()->params['ItemsPerPage']);
			$list = $model->inDescription($list, $q, 300);
			list($searchWords, $realWords, $useRealWord) = $model->getNormalizedWords($q);
			if (!$model->isFromNumeric($searchWords)) {
				$abstractInfo = $model->getEntitys($q);
				$didYouMean = $model->getDidYouMean($q);
			}
		}

		if (empty($list)&&!empty($didYouMean)) $list = $model->getListByDidYouMean($didYouMean);
		if (empty($abstractInfo)) $paginatorInfo = new CPagination(count($list));
		else {
			$e = (int) Yii::app()->getRequest()->getParam('e');
			if (Entity::IsValid($e)) {
				if (empty($abstractInfo[$e])) $paginatorInfo = new CPagination(count($list));
				else $paginatorInfo = new CPagination($abstractInfo[$e]);
			}
			else $paginatorInfo = new CPagination(array_sum($abstractInfo));
		}
		$paginatorInfo->setPageSize(Yii::app()->params['ItemsPerPage']);

		if ($availForOrder&&empty($eId)&&((int)$paginatorInfo->getItemCount() <= 0)) {
			$referer = getenv('REQUEST_URI');
			$request = new MyRefererRequest();
			$request->setFreePath($referer);
			$refererRoute = Yii::app()->getUrlManager()->parseUrl($request);
			$get = $_GET;
			$get['avail'] = 0;
			$this->redirect(Yii::app()->createUrl($refererRoute, $get));
		}

		$this->breadcrumbs[] = Yii::app()->ui->item('A_LEFT_SEARCH_WIN');
		if (!empty($list)) {
			$list = $this->_appendCartInfo($list, $this->uid, $this->sid);
		}
		$this->render('list', array(
			'q' => $q,
			'items' => $didYouMean,
			'products' => $list,
			'abstractInfo'=>$abstractInfo,
			'paginatorInfo' => $paginatorInfo,
			'eid' => $eId,
		));
	}

	/** функция проверяет найденное в title_. Если не нашло, то в результирующий массив добавляет inDescription
	 * @param $list
	 * @param $query
	 * @param int $countChars
	 * @return mixed
	 */
	function inDescription($list, $query, $countChars = 100) {
		foreach ($list as $k=>$item) {
			$isTitle = false;
			if (in_array($item['dictionary_position'], array(1, 3))) continue;//найдено по названию

			foreach (Yii::app()->params['ValidLanguages'] as $lang) {
				if (empty($item['title_' . $lang])) continue;

				if (mb_strpos($item['title_' . $lang], $query, null, 'utf-8') !== false) {
					$isTitle = true;
					break;
				}
			}
			if ($isTitle) continue;

			foreach (Yii::app()->params['ValidLanguages'] as $lang) {
				if (empty($item['description_' . $lang])) continue;

				if (($pos = mb_strpos($item['description_' . $lang], $query, null, 'utf-8')) !== false) {
					$list[$k]['inDescription'] = '';
					$posStart = 0;
					if ($pos >= ceil($countChars/2)) {
						$list[$k]['inDescription'] .= '... ';
						$posStart = mb_strpos($item['description_' . $lang], ' ', $pos - ceil($countChars/2), 'utf-8');
					}
					$posEnd = mb_strpos($item['description_' . $lang], ' ', $pos + mb_strlen($query, 'utf-8'), 'utf-8');
					$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], $posStart, ($pos-$posStart), 'utf-8');
					$list[$k]['inDescription'] .= '<span class="title__bold">' . $query . '</span>';
					$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], ($pos+mb_strlen($query, 'utf-8')), ($posEnd - ($pos+mb_strlen($word, 'utf-8'))), 'utf-8');

					if ($posEnd < mb_strlen($item['description_' . $lang])) $list[$k]['inDescription'] .= ' ...';
					break;
				}
			}

		}
		return $list;

		/*		горе от ума
		foreach ($list as $k=>$item) {
					$isTitle = false;
					$words = array_filter(preg_split("/\W+/ui", $query), function($word){ return mb_strlen($word, 'utf-8') > 2; });
					if (empty($words)) continue;

					foreach ($words as $word) {
						foreach (Yii::app()->params['ValidLanguages'] as $lang) {
							if (empty($item['title_' . $lang])) continue;

							if (mb_strpos($item['title_' . $lang], $word, null, 'utf-8') !== false) {
								$isTitle = true;
								break 2;
							}
						 }
					}
					if ($isTitle) continue;

					foreach ($words as $word) {
						foreach (Yii::app()->params['ValidLanguages'] as $lang) {
							if (empty($item['description_' . $lang])) continue;

							if (($pos = mb_strpos($item['description_' . $lang], $word, null, 'utf-8')) !== false) {
								$list[$k]['inDescription'] = '';
								$posStart = 0;
								if ($pos >= ceil($countChars/2)) {
									$list[$k]['inDescription'] .= '... ';
									$posStart = mb_strpos($item['description_' . $lang], ' ', $pos - ceil($countChars/2), 'utf-8');
								}
								$posEnd = mb_strpos($item['description_' . $lang], ' ', $pos + mb_strlen($word, 'utf-8'), 'utf-8');
								$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], $posStart, ($pos-$posStart), 'utf-8');
								$list[$k]['inDescription'] .= '<span class="title__bold">' . $word . '</span>';
								$list[$k]['inDescription'] .= mb_substr($item['description_' . $lang], ($pos+mb_strlen($word, 'utf-8')), ($posEnd - ($pos+mb_strlen($word, 'utf-8'))), 'utf-8');

								if ($posEnd < mb_strlen($item['description_' . $lang])) $list[$k]['inDescription'] .= ' ...';
								break 2;
							}
						}
					}

				}
		*/
	}

/*	protected function _isAuthors($entity, $ids) {
		$entityParam = Entity::GetEntitiesList()[$entity];
		$tableItemsAuthors = $entityParam['author_table'];
		$sql = 'select author_id from ' . $tableItemsAuthors . ' where (author_id in (' . implode(',',$ids) . ')) group by author_id';
		return Yii::app()->db->createCommand($sql)->queryColumn();
	}*/

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
			'not_found',
			array(
				'result' => array(),
				'q' => $q,
				'products' => array(),
				'paginatorInfo' => new CPagination(0),
			)
		);
	}

	private function _appendCartInfo($items, $uid, $sid) {
		$c = new Cart;
		$cart = $c->GetCart($uid, $sid);
		foreach ($items as $idx => $item) {
			foreach ($cart as $cartItem) {
				if ($cartItem['entity'] == $item['entity'] && $cartItem['id'] == $item['id']) {
					$items[$idx]['AlreadyInCart'] = $cartItem['quantity'];
				}
			}
		}
		return $items;
	}



}
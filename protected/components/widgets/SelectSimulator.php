<?php
/*Created by Кирилл (16.05.2018 19:24)*/
class SelectSimulator extends CWidget {
	protected $_params = array('paramName'=>'', 'items'=>[]);//здесь массив начальных значений
	protected $_lang, $_sort; //уже выбранные значения языка и сортировки соответственно

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
		$request = Yii::app()->getRequest();
		$this->_lang = (int) $request->getParam('lang');
		$this->_sort = (int) $request->getParam('sort');
	}

    function run() {
	    $data = array();

	    foreach ($this->_params as $name=>$values) $data[$name] = $values;
	    if (!empty($data['dataParam']['avail'])) unset($data['dataParam']['avail']);
	    switch ($this->_params['paramName']) {
		    case 'lang':
			    if (empty($data['selected'])) $data['selected'] = $this->_lang;
			    if (!empty($this->_sort)) $data['dataParam']['sort'] = $this->_sort;
				unset($data['dataParam']['lang']);
			    break;
		    case 'sort':
			    if (empty($data['selected'])) $data['selected'] = $this->_sort;
			    if (!empty($this->_lang)) $data['dataParam']['lang'] = $this->_lang;
			    unset($data['dataParam']['sort']);
			    break;
	    }
	    if (empty($data['items'][$data['selected']])) $data['items'][$data['selected']] = 0;
	    $this->render('select_simulator', $this->_prepareData($data));
    }

	private function _prepareData($data) {
		if (!empty($data['route']))
			switch ($data['route']) {
				case 'curPage':
					$data['route'] = Yii::app()->getController()->id . '/' . Yii::app()->getController()->action->id;
					break;
				case 'refererPage':
					$referer = Yii::app()->getRequest()->getUrlReferrer();
					if (empty($referer)) $data['route'] = 'entity/list';
					else {
						$request = new MyRefererRequest();
						$request->setFreePath($referer);
						$data['route'] = Yii::app()->getUrlManager()->parseUrl($request);
						$data['dataParam'] = $request->getParams();
					}
					break;
			}
		return $data;
	}

}
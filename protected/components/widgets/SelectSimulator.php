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
	    $data = array(
		    'href'=>'/'.Yii::app()->getRequest()->getPathInfo(),
		    'selected'=>$this->_lang,
		    'dataParam'=>[],
	    );

	    if (empty($data['items'][$data['selected']])) $data['items'][$data['selected']] = 0;
	    switch ($this->_params['paramName']) {
		    case 'lang':
			    if (!empty($this->_sort)) $data['dataParam']['sort'] = $this->_sort;
			    break;
		    case 'sort':
			    if (!empty($this->_lang)) $data['dataParam']['lang'] = $this->_lang;
			    break;
	    }
	    foreach ($this->_params as $name=>$values) $data[$name] = $values;
	    $this->render('select_simulator', $data);
    }

}
<?php
/*Created by Кирилл (16.05.2018 19:24)*/
class SelectSimulator extends CWidget {
	protected $_params = array('paramName'=>'', 'items'=>[]);//здесь массив начальных значений
	protected $_lang, $_sort; //уже выбранные значения языка и сортировки соответственно
    protected $_entity;

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
		$request = Yii::app()->getRequest();
		$this->_lang = (int) $request->getParam('lang');
		$this->_sort = (int) $request->getParam('sort');
		$this->_entity = (int) $request->getParam('entity');
	}

    function run() {
	    $data = array(
		    //'href'=>'/'.Yii::app()->getRequest()->getPathInfo(),
		    'href'=> '/'.((isset($this->_entity) && $this->_entity != 0) ? Entity::GetUrlKey($this->_entity) : Yii::app()->getRequest()->getPathInfo()),
		    'selected'=>$this->_lang,
		    'dataParam'=>[],
	    );

	    if (empty($data['items'][$data['selected']])) $data['items'][$data['selected']] = 0;
	    switch ($this->_params['paramName']) {
		    case 'lang':
			    if (!empty($this->_sort)) $data['dataParam']['sort'] = $this->_sort;
			    if (!empty($this->_entity)) $data['dataParam']['entity'] = $this->_entity;
			    break;
		    case 'sort':
			    if (!empty($this->_lang)) $data['dataParam']['lang'] = $this->_lang;
                if (!empty($this->_entity)) $data['dataParam']['entity'] = $this->_entity;
			    break;
	    }
	    foreach ($this->_params as $name=>$values) $data[$name] = $values;
	    $this->render('select_simulator', $data);
    }

}
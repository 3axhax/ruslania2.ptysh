<?php

class MyLinkPager extends CLinkPager
{
    public $separator = null;


	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected)
			$class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
		
		$char = (isset($this->htmlOptions['char'])) ? $this->htmlOptions['char'] : '';
		
		$params = $this->getGetParams();
		$params['page'] = $page;
		
		if (isset($_GET['qa']) && ($_GET['qa'] == '')) {
			$params['char'] = $char;
		}
		$urlParams = '';
		foreach($params as $key => $val)
		{
			if(!empty($urlParams))
				$urlParams .= '&';
			
			$urlParams .= $key.'='.$val;
		}
		
		if(!empty($urlParams))
			$urlParams = '?'.$urlParams;
		
		$arrUrl = explode('?', Yii::app()->request->url);

		if (substr_count($arrUrl[0], 'ggfilter') > 0) $url = $this->getPages()->route.$urlParams;
		else $url = $arrUrl[0].$urlParams;
		

		if (!$label) return '';
		$u = $this->getPages()->route;
		return '<li class="'.$class.'">'.CHtml::link($label,$url).'</li>';
	}	
	
	private function getGetParams()
	{

		$path = urldecode(getenv('REQUEST_URI'));
		$ind = mb_strpos($path, "?", null, 'utf-8');
		$q = ($ind === false)?'':mb_substr($path, $ind, null, 'utf-8');
		unset($path);

		$query = $_GET;
		foreach ($query as $k=>$v) {
			//убираю параметры, которые кто-то зачем-то в скриптах положил в $_GET
			if (!preg_match("/\b" . $k . "\b/ui", $q)) unset($query[$k]);
			//пустые параметры тоже уберу, их не доложно быть
			elseif ($v === '') unset($query[$k]);
		}
		return $query;

		//Исправил потому, что если адрес /books/?cavail=1 то будет формировать /books/?cavail=1&avail=1&page=2
		$res = [];
		
		$paramsStart = strpos(Yii::app()->request->url, '?');
		
		if($paramsStart)
			$params = strtolower(substr(Yii::app()->request->url, $paramsStart+1));
		else
			$params = '';

		foreach($_GET as $key => $val)
		{
			if(strpos($params, strtolower($key)) !== false and strtolower($key) != 'page')
				$res[$key] = $val;
		}

		return $res;
	}
   

//    public function run()
//    {
//        $this->registerClientScript();
//        $buttons=$this->createPageButtons();
//        if(empty($buttons))
//            return;
//
//        $ret = $this->header.CHtml::tag('ul',$this->htmlOptions,implode("\n",$buttons)).$this->footer;
//        echo $ret;
//    }
}
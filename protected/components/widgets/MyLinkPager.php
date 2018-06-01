<?php

class MyLinkPager extends CLinkPager
{
    public $separator = null;


	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected)
			$class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
		
		$char = $this->htmlOptions['char'];
		
		$params = $this->getGetParams();
		$params['page'] = $page;
		
		if ($_GET['qa'] == '') {
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
		
		$url = $arrUrl[0].$urlParams;
		if (!$label) return '';
		return '<li class="'.$class.'">'.CHtml::link($label,$url).'</li>';
	}	
	
	private function getGetParams()
	{
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
<?php

class BannersNew
{
	private $lang = 'ru';
	
	const BANNERS_DIR = '/pictures/banners/';
	
	function __construct()
	{
		$curLang = strtolower(Yii::app()->language);
		if($curLang == 'rut')
			$this->lang = 'ru';
		else
			$this->lang = $curLang;
	}
	
	public function getSmallMainBanners()
	{
		$res = '';
		
		$sql = "SELECT * FROM banners_new WHERE type='2' AND language='".$this->lang."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();		
		if($row)
			$res .= '<div class="span6"><a href="'.$row['url'].'"><img src="'.self::BANNERS_DIR.$row['image'].'" alt=""/></a></div>';
		
		$sql = "SELECT * FROM banners_new WHERE type='3' AND language='".$this->lang."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();		
		if($row)
			$res .= '<div class="span6"><a href="'.$row['url'].'"><img src="'.self::BANNERS_DIR.$row['image'].'" alt=""/></a></div>';		
		
		if(!empty($res))
			$res = '<div class="banners"><div class="container">'.$res.'</div></div>';
		
		echo $res;
	}
	
	public function ckeckMainBanner()
	{		
		$sql = "SELECT * FROM banners_new WHERE type='1' AND language='".$this->lang."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();		
		if($row)
			return true;
		else
			return false;
	}		
	
	public function getMainBanner()
	{		
		$sql = "SELECT * FROM banners_new WHERE type='1' AND language='".$this->lang."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();		
		if($row)
			echo '<a href="'.$row['url'].'"><img src="'.self::BANNERS_DIR.$row['image'].'" alt=""/></a>';

	}		
	
	public function getActionItems()
	{		
		$sql = 'SELECT * FROM action_items where (`type` <> 3) Order By id';
		$actionItems = Yii::app()->db->createCommand($sql)->queryAll();
		if($actionItems)
			return $actionItems;
		else
			return false;
	}		
	
}
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
	
	public function getActionItems() {
		$actionItems = Yii::app()->memcache->get('main_action_items');
		if ($actionItems === false) {
			$sql = 'SELECT * FROM action_items where (`type` <> 3) Order By id limit 50';
			$actionItems = Yii::app()->db->createCommand($sql)->queryAll();
			if (!empty($actionItems)) {
				$entityIds = array();
				foreach ($actionItems as $item) {
					if (empty($entityIds[$item['entity']])) $entityIds[$item['entity']] = array();
					$entityIds[$item['entity']][] = $item['item_id'];
				}
				$p = new Product();
				$fullInfo = array();
				foreach ($entityIds as $eId=>$ids) {
					$fullInfo[$eId] = array();
					$list = $p->GetProductsV2($eId, $ids, true);
					foreach($entityIds[$eId] as $iid) {
						if(!isset($list[$iid])) continue;

						$av = Availability::GetStatus($list[$iid]);
						if($av == Availability::NOT_AVAIL_AT_ALL) continue; // В подборках нет товаров, которых не заказать
						$fullInfo[$eId][$iid] = $list[$iid];
					}
				}
				foreach ($actionItems as $i=>$item) {
					if (empty($fullInfo[$item['entity']][$item['item_id']])) unset($actionItems[$i]);
					else $actionItems[$i]['product'] = $fullInfo[$item['entity']][$item['item_id']];
				}
			}
			else $actionItems = array();
			Yii::app()->memcache->set('main_action_items', $actionItems, 3600);
		}
		else {
			$entityIds = array();
			foreach ($actionItems as $item) {
				if (empty($entityIds[$item['entity']])) $entityIds[$item['entity']] = array();
				$entityIds[$item['entity']][] = $item['item_id'];
			}
			foreach ($entityIds as $eId=>$ids) {
				Product::setOfferItems($eId, $ids);
				Product::setActionItems($eId, $ids);
			}
		}
		return $actionItems;
	}
	
}
<?php

if(Banners::checkBigBanner()) $this->widget('Banners', array('type'=>'big'));
else {
		$this->widget('ActionItems', array('uid'=>$this->uid, 'sid'=>$this->sid));
/*
		$bannersNew = new BannersNew();
		$actionItems = $bannersNew->getActionItems();
		if(count($actionItems) > 0)
			$this->renderPartial('action_items', array('actionItems' => $actionItems));
*/
}
$this->widget('Banners', array('type'=>'small', 'useFilecache'=>1));

	$this->widget('MainOffers', array('uid'=>$this->uid, 'sid'=>$this->sid));
/*
	foreach ($groups as $entity => $group) {
		if (empty($group['items'])) continue;
		$this->renderPartial(
			'/entity/_entity_index',
			array('group' => $group['items'], 'entity' => $group['entity'])
		);
	}
*/
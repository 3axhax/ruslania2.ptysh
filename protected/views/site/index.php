<?php

if(Banners::checkBigBanner()) $this->widget('Banners', array('type'=>'big'));
else {
	if (!isset($_GET['showTime'])) {
		$this->widget('ActionItems', array('uid'=>$this->uid, 'sid'=>$this->sid));
	}
	else {
		$bannersNew = new BannersNew();
		$actionItems = $bannersNew->getActionItems();
		if(count($actionItems) > 0)
			$this->renderPartial('action_items', array('actionItems' => $actionItems));
	}
}
$this->widget('Banners', array('type'=>'small', 'useFilecache'=>1));

if (!isset($_GET['showTime'])) {
	$this->widget('MainOffers', array('uid'=>$this->uid, 'sid'=>$this->sid));
}
else {
	foreach ($groups as $entity => $group) {
		if (empty($group['items'])) continue;
		$this->renderPartial(
			'/entity/_entity_index',
			array('group' => $group['items'], 'entity' => $group['entity'])
		);
	}
}
?>
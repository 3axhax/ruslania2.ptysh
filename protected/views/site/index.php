<?php
if(Banners::checkBigBanner()) $this->widget('Banners', array('type'=>'big'));
else {
	$bannersNew = new BannersNew();
	$actionItems = $bannersNew->getActionItems();
	if(count($actionItems) > 0)
		$this->renderPartial('action_items', array('actionItems' => $actionItems));
}
$this->widget('InfoText');
$this->widget('Banners', array('type'=>'small'));

foreach($groups as $entity=>$group) {
	if (count($group['items']) == 0) continue;
	$this->renderPartial(
		'/entity/_entity_index',
		array('group' => $group['items'], 'entity' => $group['entity'])
	);
}
?>
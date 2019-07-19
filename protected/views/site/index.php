<?php
$start = microtime_float();

if(Banners::checkBigBanner()) $this->widget('Banners', array('type'=>'big'));
else {
	if (isset($_GET['showTime'])) {
		$this->widget('ActionItems', array('uid'=>$this->uid, 'sid'=>$this->sid));
	}
	else {
		$bannersNew = new BannersNew();
		$actionItems = $bannersNew->getActionItems();
		if(count($actionItems) > 0)
			$this->renderPartial('action_items', array('actionItems' => $actionItems));
	}
}
$end = microtime_float() - $start;
if (isset($_GET['showTime'])) var_dump(array(date('d.m.Y H:i:s'), number_format($end, 4) . ' сек'));
$start = microtime_float();
$this->widget('Banners', array('type'=>'small', 'useFilecache'=>isset($_GET['showTime'])));
$end = microtime_float() - $start;
if (isset($_GET['showTime'])) var_dump(array(date('d.m.Y H:i:s'), number_format($end, 4) . ' сек'));


foreach($groups as $entity=>$group) {
	$start = microtime_float();
	if (empty($group['items'])) continue;
	$this->renderPartial(
		'/entity/_entity_index',
		array('group' => $group['items'], 'entity' => $group['entity'])
	);
	$end = microtime_float() - $start;
	if (isset($_GET['showTime'])) var_dump(array(date('d.m.Y H:i:s'), number_format($end, 4) . ' сек'));
}
?>
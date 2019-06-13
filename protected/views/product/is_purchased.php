<?php /*Created by Кирилл (13.06.2019 22:06)*/
if (!Yii::app()->user->isGuest):
	$dateBuy = Product::isPurchased(Yii::app()->user->id, $entity, $item['id']);
	if($dateBuy):
?>
<div>You purchased this item on <?= $dateBuy->format('d.m.Y') ?></div>
<?php endif; endif; ?>

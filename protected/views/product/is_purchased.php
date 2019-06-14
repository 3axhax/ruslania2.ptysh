<?php /*Created by Кирилл (13.06.2019 22:06)*/
if (!Yii::app()->user->isGuest):
	$dateBuy = Product::isPurchased(Yii::app()->user->id, $entity, $item['id']);
	if($dateBuy):
?>
<div class="purchased_msg">
	<span class="fa"></span>
	<div>You purchased this item on <?= $dateBuy->format('d') . ' ' . Yii::app()->ui->item('A_NEW_M' . (int)$dateBuy->format('m')) . ' ' . $dateBuy->format('Y') ?></div>
	<div><a href="<?= Yii::app()->createUrl('my/orders', array('eid'=>$entity, 'iid'=>$item['id'])); ?>">View this order</a></div>
</div>
<?php endif; endif; ?>

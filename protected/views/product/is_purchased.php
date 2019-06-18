<?php /*Created by Кирилл (13.06.2019 22:06)*/
if (!Yii::app()->user->isGuest):
	$dateBuy = Product::isPurchased(Yii::app()->user->id, $entity, $item['id']);
	if($dateBuy):
?>
<div class="purchased_msg">
	<span class="fa"></span>
	<div><?= Yii::app()->ui->item('YOU_PURCHASED') ?> <?= $dateBuy->format('j') . ' ' . Yii::app()->ui->item('A_NEW_M' . (int)$dateBuy->format('m')) . ' ' . $dateBuy->format('Y') ?></div>
	<div><a href="<?= Yii::app()->createUrl('my/orders', array('eid'=>$entity, 'iid'=>$item['id'])); ?>"><?= Yii::app()->ui->item('VIEW_THIS_ORDER') ?></a></div>
</div>
<?php endif; endif; ?>

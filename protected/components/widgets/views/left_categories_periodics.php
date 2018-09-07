<?php /*Created by Кирилл (05.06.2018 23:19)*/ ?>
<h2 class="cattitle"><?= Yii::app()->ui->item('A_NEW_CATEGORYES') ?>:</h2>
<?php foreach ($categories as $type=>$categorys) :?>
<ul class="left_list divider">
	<?php //if (count($categories) > 1):?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => Entity::GetUrlKey($entity), 'type' => $type)) ?>">
			<span class="title__bold"><?= Yii::app()->ui->item('PERIODIC_TYPE_PLURAL_' . $type) ?></span>
		</a>
	</li>
	<?php //endif; ?>
	<?php
	foreach ($categorys as $i=>$cat) : ?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)), 'binding' => array($type))); ?>"><?= ProductHelper::GetTitle($cat); ?></a>
	</li>
	<?php endforeach; ?>
</ul>
<?php endforeach; ?>

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
	$hide = false;
	foreach ($categorys as $i=>$cat) :
		$style = '';
		if ($i > 9):
			$hide = true;
			$style = ' style="display:none; " ';
		endif;
		?>
	<li<?= $style ?>>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)), 'binding' => array($type))); ?>"><?= ProductHelper::GetTitle($cat); ?></a>
	</li>
	<?php endforeach; ?>
	<?php if ($hide): ?>
	<li style="cursor: pointer; text-align: right;" onclick="$(this).hide().siblings().show();" title="<?= Yii::app()->ui->item('A_NEW_VIEW_ALL_CATEGORY'); ?>">
		<img src="/new_img/btn_right.png" style="transform: rotate(90deg); ">
	</li>
	<?php endif; ?>
</ul>
<?php endforeach; ?>

<?php /*Created by Кирилл (05.06.2018 23:19)*/
list($availCategory2, $availCategory1, $availCategorySale) = (new MainMenu)->getPeriodicCatIds();
foreach ($categories[2] as $i=>$cat): if (isset($availCategory2[$cat['id']])):
	$availCategory2[$cat['id']] = ProductHelper::GetTitle($cat);
endif; endforeach;
foreach ($categories[1] as $i=>$cat): if (isset($availCategory1[$cat['id']])):
	$availCategory1[$cat['id']] = ProductHelper::GetTitle($cat);
endif; endforeach;
?>
<h2 class="cattitle"><?= Yii::app()->ui->item('CATEGORY_POPULAR') ?>:</h2>
<ul class="left_list divider">
<?php foreach ($availCategorySale as $idCat=>$name):?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $idCat, 'title' => Yii::app()->ui->item($name))); ?>"><?= Yii::app()->ui->item($name) ?></a>
	</li>
<?php endforeach; ?>
<?php foreach ($availCategory2 as $idCat=>$name): ?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii($name))); ?>"><?= $name; ?></a>
	</li>
<?php endforeach; ?>
<?php foreach ($availCategory1 as $idCat=>$name): ?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii($name))); ?>"><?= $name; ?></a>
	</li>
<?php endforeach; ?>
</ul>

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
	foreach ($categorys as $i=>$cat): if (!isset($availCategorySale[$cat['id']])):
		$style = '';
		if ($i > 9):
			$hide = true;
			$style = ' style="display:none; " ';
		endif;
		?>
	<li<?= $style ?>>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)), /*'binding' => array($type)*/)); ?>"><?= ProductHelper::GetTitle($cat); ?></a>
	</li>
	<?php endif; endforeach; ?>
	<?php if ($hide): ?>
	<li onclick="$(this).hide().siblings().show();" class="category_all">
		<?=Yii::app()->ui->item('A_NEW_VIEW_ALL_CATEGORY'); ?>
	</li>
		<?php /*
	<li style="cursor: pointer; text-align: right;" onclick="$(this).hide().siblings().show();" title="<?= Yii::app()->ui->item('A_NEW_VIEW_ALL_CATEGORY'); ?>">
		<img src="/new_img/btn_right.png" style="transform: rotate(90deg); ">
	</li>
	<?php */ endif; ?>
</ul>
<?php endforeach; ?>

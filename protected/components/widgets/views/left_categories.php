<?php /*Created by Кирилл (05.06.2018 23:19)*/ ?>
<div class="poht" style="margin: 10px 0 0 0; "><?= Yii::app()->ui->item('A_NEW_CATEGORYES') ?>:</div>
<ul class="left_list divider">
	<?php if (empty($cid)): ?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/salelist', array('entity' => Entity::GetUrlKey($entity))); ?>"><?= Entity::GetTitle($entity) ?> <?= mb_strtolower(Yii::app()->ui->item('REDUCED_PRICES'), 'utf-8') ?></a>
	</li>
	<?php endif; ?>
	<?php /*
	<li>
<?php if (empty($cid)): ?>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity))) ?>">
			<span class="title__bold"><?= Entity::GetTitle($entity) ?></span>
		</a>
<?php else: ?>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cid)) ?>">
			<span class="title__bold"><?=  $catTitle ?></span>
		</a>
<?php endif; ?>
	</li>
	<?php */
	foreach ($categories as $i=>$cat) : ?>
	<li>
		<?php if (!empty($cat['childs'])): ?>
			<a data-lvl="1" class="open_subcat subcatlvl1"></a>
		<?php endif; ?>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)))); ?>"><?= ProductHelper::GetTitle($cat); ?></a>
		<?php $this->widget('LeftCategories', array('entity'=>$entity, 'cid'=>$cat['id'], 'catTitle'=>ProductHelper::GetTitle($cat), 'categories'=>$cat['childs'], 'tpl'=>'sub_left_categories')); ?>
	</li>
	<?php endforeach; ?>
</ul>

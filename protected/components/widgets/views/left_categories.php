<?php /*Created by Кирилл (05.06.2018 23:19)*/ ?>
<h2 class="cattitle"><?= Yii::app()->ui->item('A_NEW_CATEGORYES') ?>:</h2>
<ul class="left_list divider"><?php /*
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
			<a class="open_subcat subcatlvl1" onclick="show_sc($('ul.sc<?= $cat['id'] ?>'), $(this), 1); return false;"></a>
		<?php endif; ?>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)))); ?>"><?= ProductHelper::GetTitle($cat); ?></a>
		<?php $this->widget('LeftCategories', array('entity'=>$entity, 'cid'=>$cat['id'], 'catTitle'=>ProductHelper::GetTitle($cat), 'categories'=>$cat['childs'], 'tpl'=>'sub_left_categories')); ?>
	</li>
	<?php endforeach; ?>
</ul>

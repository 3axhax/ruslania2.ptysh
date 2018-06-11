<?php /*Created by Кирилл (05.06.2018 23:37)*/ ?>
<ul style="margin-right: 20px;" class="subcat sc<?= $cid ?> lvlcat<?= $lvl ?>" rel="<?= $cid ?>">
<?php foreach ($categories as $cat) : ?>
	<li>
	<?php if (!empty($cat['childs'])): ?>
		<a class="open_subcat subcatlvl<?= ($lvl+1) ?>" onclick="show_sc($('ul.sc<?= $cat['id'] ?>'), $(this), <?= ($lvl+1) ?>); return false;"></a>
	<?php endif; ?>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)))) ?>"><?= ProductHelper::GetTitle($cat) ?></a>
		<?php $this->widget('LeftCategories', array('entity'=>$entity, 'cid'=>$cat['id'], 'catTitle'=>ProductHelper::GetTitle($cat), 'categories'=>$cat['childs'], 'tpl'=>'sub_left_categories', 'lvl'=>($lvl+1))); ?>
	</li>
<?php endforeach; ?>
</ul>
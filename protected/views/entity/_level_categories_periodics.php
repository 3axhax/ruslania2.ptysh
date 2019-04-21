<?php
/*Created by Кирилл (22.08.2018 22:36)*/
if (!empty($tree) && count($tree) > 0):
	if (empty($parent)): ?>
<ul class="b-category-list__item-outer" style="padding-right: 30px;">
	<?php else: ?>
<ul class="b-category-list__inner-list js-slide-content-inner-list tglvl<?= $lvl ?>">
	<?php endif; ?>
	<?php foreach ($tree as $node):
		$cross = '';
		if (!$node['children']) $cross = ' cross3 ';
		if (!empty($parent)): ?>
	<li class="b-category-list__item-inner <?= $cross ?> lvl<?= $lvl ?>">
		<?php else: ?>
	<li class="b-category-list__item <?= $cross ?> lvl<?= $lvl ?>">
		<?php endif; ?>
		<a title="<?= ProductHelper::GetTitle($node['payload']) ?>" class="b-category-list__link" href="<?= Yii::app()->createUrl('entity/list',
				array('entity' => Entity::GetUrlKey($entity),
					'cid' => $node['payload']['id'],
					'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($node['payload'])),
					/*'binding' => array($typeId),*/
				)) ?>"><?= ProductHelper::GetTitle($node['payload']) ?>
		</a>
		<?php if ($node['children']):
			if ($lvl > 1): ?>
		<div class="b-category-list__cross cross2 js-slide-toggle" data-slidecontext=".lvl<?= $lvl ?>" data-slideclasstoggle=".lvl<?= $lvl ?>" data-slidetoggle=".tglvl<?= ($lvl+1) ?>"></div>
			<?php else: ?>
		<div class="b-category-list__cross cross1 js-slide-toggle" data-slidecontext=".lvl<?= $lvl ?>" data-slideclasstoggle=".lvl<?= $lvl ?>" data-slidetoggle=".tglvl<?= ($lvl+1) ?>"></div>
			<?php endif;
		endif;
		$this->renderPartial('/entity/_level_categories_periodics', array('tree' => $node['children'], 'entity' => $entity, 'parent'=>true, 'lvl'=>$lvl+1, 'typeId'=>$typeId));
		?>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

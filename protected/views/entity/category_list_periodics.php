<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<section class="b-all-category">
	<div class="container b-all-category__wrapper">
		<div class="b-category-list">
			<div class="b-category-list__topic"><?= Yii::app()->ui->item('A_NEW_CATEGORYES') ?></div>
<?php foreach ($types as $type): ?>
			<div style="float: left; margin-right: 10px; width: <?= (100/count($types)) ?>%">
				<?php /*<div><a href="<?= Yii::app()->createUrl('entity/bytype', array('type'=>$type['id'], 'entity'=>Entity::PERIODIC)) ?>"><?= ProductHelper::GetTitle($type) ?></a></div> */ ?>
	<?php $this->renderPartial('/entity/_level_categories', array('tree' => $type['categories'], 'entity' => $entity, 'parent'=>false, 'lvl'=>1)); ?>
			</div>
<?php endforeach; ?>
			<div class="clearBoth"></div>
		</div>
		<div class="b-user-seen">
			<?php $this->widget('YouView', array('tpl'=>'you_view_categories')); ?>
	    </div>
	</div>
</section>
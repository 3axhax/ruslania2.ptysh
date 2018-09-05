<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<section class="b-all-category">
	<div class="container b-all-category__wrapper">
		<div class="b-category-list">
			<div class="b-category-list__topic"><?= Yii::app()->ui->item('A_NEW_CATEGORYES') ?></div>
<?php foreach ($types as $type): ?>
			<div style="float: left; width: <?= (100/count($types)) ?>%">
				<div>
					<a href="<?= Yii::app()->createUrl('entity/bytype', array('entity'=>'periodics', 'type'=>$type['id'], 'title'=>ProductHelper::ToAscii($type['title']))) ?>">
						<?= $type['title'] ?>
					</a>
				</div>
	<?php $this->renderPartial('/entity/_level_categories_periodics', array('tree' => $type['categories'], 'entity' => $entity, 'parent'=>false, 'lvl'=>1, 'typeId'=>$type['id'])); ?>
			</div>
<?php endforeach; ?>
			<div class="clearBoth"></div>
		</div>
		<div class="b-user-seen">
			<?php $this->widget('YouView', array('tpl'=>'you_view_categories')); ?>
	    </div>
	</div>
</section>
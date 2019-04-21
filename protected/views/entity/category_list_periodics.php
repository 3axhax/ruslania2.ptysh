<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<section class="b-all-category">
	<div class="container b-all-category__wrapper">
		<div class="b-category-list">
			<?php /*
			<div class="b-category-list__topic"><?= Yii::app()->ui->item('A_NEW_CATEGORYES') ?></div>
 */ ?>
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?php
				$breadcrumbs = $this->breadcrumbs;
				$h1 = Seo_settings::get()->getH1();
				if (empty($h1)):
				$h1 = array_pop($breadcrumbs);
				unset($breadcrumbs) ;
				$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
				if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . Yii::app()->ui->item('PAGES_N', $page);
			endif;
			?><?= $h1 ?></h1>
<?php foreach ($types as $type): ?>
			<div style="float: left; width: <?= (100/count($types)) ?>%">
				<div>
					<a href="<?= Yii::app()->createUrl('entity/bytype', array('entity'=>'periodics', 'type'=>$type['id'], 'title'=>ProductHelper::ToAscii($type['title']))) ?>">
						<?= Yii::app()->ui->item('PERIODIC_TYPE_PLURAL_' . $type['id']) ?>
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
<?php /*Created by Кирилл (19.07.2019 22:47)*/ ?>
<?php $url = ProductHelper::CreateUrl($item);
/**@var $photoModel ModelsPhotos*/
$photoModel = Pereodics_photos::model();
$photoId = $photoModel->getFirstId($item['id']);
?>
<div class="img" style="position: relative">
	<?php Yii::app()->getController()->renderStatusLables($item['status'], $size = '-sm', true)?>
	<a title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" href="<?=$url; ?>">
		<?php if (empty($photoId)): ?>
			<img src="<?= Picture::srcLoad() ?>" data-lazy="<?=Picture::Get($item, Picture::SMALL); ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" />
		<?php else: ?>
			<picture>
				<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $item['eancode'], 'webp') ?>" type="image/webp">
				<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $item['eancode'], 'jpg') ?>" type="image/jpeg">
				<img src="<?= $photoModel->getHrefPath($photoId, 'o', $item['eancode'], 'jpg') ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" />
			</picture>
		<?php endif; ?>
	</a>
</div>
<div class="title_book">
	<a href="<?=$url; ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>">
		<?=ProductHelper::GetTitle($item, 'title'); ?><span class="gradient_link"></span>
	</a>
</div>
<?php if (!empty($item['avail_for_order'])): ?>
	{PRICE_<?= $item['entity'] ?>_<?= $item['id'] ?>}
<?php endif; ?>
<div class="more">
	<a class="fa" href="<?=$url?>"><span><?=Yii::app()->ui->item('A_NEW_MORE3');?></span></a>
</div>


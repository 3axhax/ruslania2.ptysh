<?php /*Created by Кирилл (19.07.2019 22:46)*/ ?>
<?php $url = ProductHelper::CreateUrl($item);
$photoTable = Entity::GetEntitiesList()[$item['entity']]['photo_table'];
$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
/**@var $photoModel ModelsPhotos*/
$photoModel = $modelName::model();
$photoId = $photoModel->getFirstId($item['id']);
?>
<div class="img" style="position: relative">
	<?php Yii::app()->getController()->renderStatusLables($item['status'], $size = '-sm', true)?>
	<a title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" href="<?=$url; ?>">
		<?php if (empty($photoId)): ?>
			<img src="<?= Picture::srcLoad() ?>" data-lazy="<?=Picture::Get($item, Picture::SMALL); ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" />
		<?php else: ?>
			<picture class="main-bannerImg">
				<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $item['eancode'], 'webp') ?>" type="image/webp">
				<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $item['eancode'], 'jpg') ?>" type="image/jpeg">
				<img src="<?= Picture::Get($item, Picture::SMALL) ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" />
			</picture>
		<?php endif; ?>
	</a>
</div>
<div class="title_book">
	<a href="<?=$url; ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>">
		<?=ProductHelper::GetTitle($item, 'title'); ?><span class="gradient_link"></span>
	</a>
</div>
<div class="author">
<?php if (!empty($item['Authors']) OR !empty($item['Performers']) OR !empty($item['Directors'])): ?>
	<?php $tmp = array(); if (!empty($item['Authors'])): ?>
		<?php foreach ($item['Authors'] as $author):
			$authorTitle = ProductHelper::GetTitle($author);
			$tmp[] = $authorTitle;
		endforeach; ?>
	<?php endif; ?>
	<?php if (empty($tmp)&&!empty($item['Performers'])) : ?>
		<?php foreach ($item['Performers'] as $performer):
			$tmp[] = ProductHelper::GetTitle($performer);
		endforeach; ?>
	<?php endif; ?>
	<?php if (empty($tmp)&&!empty($item['Directors'])) : ?>
		<?php foreach ($item['Directors'] as $director) : ?>
			<? $tmp[] = ProductHelper::GetTitle($director); ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (empty($tmp)&&!empty($item['Directors'])) : ?>
		<?php foreach ($item['Directors'] as $director) : ?>
			<? $tmp[] = ProductHelper::GetTitle($director); ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (!empty($tmp)):
		if (count($tmp) > 1): ?>
			<?= $tmp[0] ?>,...
		<?php else: ?>
			<?= implode(', ', array_unique($tmp)) ?>
		<?php endif;
	endif; ?>
<?php endif; ?>
</div>
<?php if (!empty($item['avail_for_order'])): ?>
	{PRICE_<?= $item['entity'] ?>_<?= $item['id'] ?>}
<?php endif; ?>
<div class="addcart">{CART_BUTTON_<?= $item['entity']; ?>_<?= $item['id']; ?>}</div>

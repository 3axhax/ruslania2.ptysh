<?php /*Created by Кирилл (19.07.2019 22:46)*/ ?>
<?php $url = ProductHelper::CreateUrl($item); ?>
<div class="img" style="position: relative">
	<?php Yii::app()->getController()->renderStatusLables($item['status'], $size = '-sm', true)?>
	<a title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" href="<?=$url; ?>"><img src="<?= Picture::srcLoad() ?>" data-lazy="<?=Picture::Get($item, Picture::SMALL); ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" /></a>
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

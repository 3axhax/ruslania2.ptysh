<?php /*Created by Кирилл (19.07.2019 22:47)*/ ?>
<?php $url = ProductHelper::CreateUrl($item); ?>
<div class="img" style="position: relative">
	<?php $this->renderStatusLables($item['status'], $size = '-sm', true)?>
	<a title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" href="<?=$url; ?>"><img src="<?= Picture::srcLoad() ?>" data-lazy="<?=Picture::Get($item, Picture::SMALL); ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" /></a>
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
	<a class="fa" href="<?=$url?>"><span><?=$ui->item('A_NEW_MORE3');?></span></a>
</div>


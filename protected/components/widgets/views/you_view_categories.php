<?php /*Created by Кирилл (10.08.2018 20:52)*/ ?>
<?php /*
<div class="b-user-seen__topic"><?=Yii::app()->ui->item('A_NEW_VIEWD_ITEMS')?>:</div>
 */ ?>
<div class="poht"><?=Yii::app()->ui->item('A_NEW_VIEWD_ITEMS')?>:</div>
<div class="you_view">
	<ul>
<?php foreach ($items as $item):
	$url = ProductHelper::CreateUrl($item);
	$title = ProductHelper::GetTitle($item, 'title', 30);
	?>
	<li>
		<div class="span1 photo new">
			<?php $url = ProductHelper::CreateUrl($item); ?>
			<a href="<?=$url; ?>" title="<?= htmlspecialchars($title) ?>"><img alt="<?= htmlspecialchars($title) ?>" src="<?=Picture::Get($item, Picture::SMALL); ?>" /></a>
		</div>
		<div class="span2 text">
			<div class="title"><a href="<?= $url; ?>" title="<?=htmlspecialchars($title); ?>"><?= $title; ?></a></div>
			<?php if (!empty($item['avail_for_order'])): ?>
			<div class="cost">
	<?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])) : ?>
				<span class="without_discount">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
				<span class="price with_discount"<?php if ($item['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>>
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
	<?php else : ?>
				<span class="price">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
	<?php endif; ?>
			</div>
			<div class="nds"><?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $item['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
			<?php endif; ?>
		</div>
		<div class="clearfix"></div>
	</li>
<?php endforeach; ?>
	</ul>
</div>
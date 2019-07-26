<?php /*Created by Кирилл (10.08.2018 20:52)*/ ?>
<div class="poht"><?=Yii::app()->ui->item('A_NEW_VIEWD_ITEMS')?>:</div>
<div class="you_view">
	<ul>
<?php foreach ($items as $item):
	$url = ProductHelper::CreateUrl($item);
	$title = ProductHelper::GetTitle($item, 'title', 30);
	$photoTable = Entity::GetEntitiesList()[$item['entity']]['photo_table'];
	$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
	/**@var $photoModel ModelsPhotos*/
	$photoModel = $modelName::model();
	$photoId = $photoModel->getFirstId($item['id']);
	?>
	<li>
		<div class="span1 photo new">
			<?php $url = ProductHelper::CreateUrl($item); ?>
			<a href="<?=$url; ?>" title="<?= htmlspecialchars($title) ?>">
				<?php if (empty($photoId)): ?>
					<img alt="<?= htmlspecialchars($title) ?>" src="<?= Picture::srcLoad() ?>" lazySrc="<?=Picture::Get($item, Picture::SMALL); ?>" />
				<?php else: ?>
					<picture class="main-bannerImg">
						<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $item['eancode'], 'webp') ?>" type="image/webp">
						<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $item['eancode'], 'jpg') ?>" type="image/jpeg">
						<img alt="<?= htmlspecialchars($title) ?>" src="<?= Picture::Get($item, Picture::SMALL) ?>"/>
					</picture>
				<?php endif; ?>
			</a>
		</div>
		<div class="span2 text">
			<div class="title"><a href="<?= $url; ?>" title="<?=htmlspecialchars($title); ?>"><?= $title; ?></a></div>
			<?php if (!empty($item['avail_for_order'])): ?>
			<div class="cost">
	<?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])) : ?>
				<span class="without_discount">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
                </span>
				<span class="price with_discount" style="font-size: 15px;<?php if ($item['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> color: #42b455;<?php endif; ?>">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
	<?php else : ?>
				<span class="price" style="font-size: 15px;">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
	<?php endif; ?>
			</div>
			<div class="nds"<?php if($item['entity'] == Entity::PERIODIC):?> style="display: none;" <?php endif; ?>><?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $item['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
			<?php endif; ?>
		</div>
		<div class="clearfix"></div>
	</li>
<?php endforeach; ?>
	</ul>
</div>
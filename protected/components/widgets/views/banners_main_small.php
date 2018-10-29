<?php /*Created by Кирилл (26.09.2018 20:41)*/ ?>
<div class="banners">
	<div class="container">
<?php if (!empty($offerDay)):
	$url = ProductHelper::CreateUrl($offerDay);
	$productPicture = Picture::Get($offerDay, Picture::SMALL);
	$productTitle = ProductHelper::GetTitle($offerDay, 'title');
	?>
		<div class="span6 main-banner-content" style="background: url(/new_img/poslozhka.jpg) 100% 100% no-repeat; background-size: contain;">
			<div class="photo">
				<a href="<?= $url ?>"><img src="<?= $productPicture ?>" alt=""/></a>
			</div>
	<?php if (!empty($offerDay['priceData'][DiscountManager::DISCOUNT])) : ?>
			<div class="discount"><?= Yii::app()->ui->item('PRODUCT_OF_DAY_INFO', $offerDay['priceData'][DiscountManager::DISCOUNT]) ?></div>
	<?php endif; ?>
			<div class="title"><div><a href="<?= $url ?>"><?= $productTitle ?><?php /*<span class="gradient_link"></span> */ ?></a></div></div>
<?php /*
			<div class="cost">
                <?= Yii::app()->ui->item('CART_COL_PRICE') ?>
                <span><?= ProductHelper::FormatPrice($offerDay['priceData'][DiscountManager::WITH_VAT]); ?>
                <?= $offerDay['priceData']['unit'] ?></span>
			</div>
			<div class="nds">
				(<?= ProductHelper::FormatPrice($offerDay['priceData'][DiscountManager::WITHOUT_VAT]); ?>
				<?= $offerDay['priceData']['unit'] ?>
				<?=Yii::app()->ui->item('WITHOUT_VAT'); ?>)
			</div>
*/ ?>
			<div class="cost_nds">
<?= Yii::app()->ui->item('CART_COL_PRICE') ?> <?= ProductHelper::FormatPrice($offerDay['priceData'][DiscountManager::WITH_VAT]); ?> <?= $offerDay['priceData']['unit'] ?>
(<?= ProductHelper::FormatPrice($offerDay['priceData'][DiscountManager::WITHOUT_VAT]); ?> <?= $offerDay['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?>)
			</div>
		</div>
<?php elseif (!empty($leftBanner)): ?>
		<div class="span6 main-banner-content"><a href="<?= $leftBanner['href'] ?>"><img class="main-bannerImg" src="<?= $leftBanner['img'] ?>" alt="<?= htmlspecialchars($leftBanner['title']) ?>"/></a></div>
<?php endif; ?>
<?php if (!empty($rightBanner)): ?>
		<div class="span6 main-banner-content"><a href="<?= $rightBanner['href'] ?>"><img class="main-bannerImg" src="<?= $rightBanner['img'] ?>" alt="<?= htmlspecialchars($rightBanner['title']) ?>"/></a></div>
<?php endif; ?>
	</div>
</div>

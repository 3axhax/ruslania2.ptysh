<?php /*Created by Кирилл (26.09.2018 20:41)*/ ?>
<div class="banners">
	<div class="container">
<?php if (!empty($offerDay)):
	$url = ProductHelper::CreateUrl($offerDay);
	$productPicture = Picture::Get($offerDay, Picture::SMALL);
	$productTitle = ProductHelper::GetTitle($offerDay, 'title');
	?>
		<a href="<?= $url ?>"><div class="span6 main-banner-content" style="background: url(/new_img/day_fon.jpg) 100% 100% no-repeat; background-size: contain;">
			<div class="photo">
				<div><img src="<?= $productPicture ?>" alt=""/></div>
			</div>
			{DISCOUNTPERCENT}
			<div class="title"><div id="js_offerDay"><?= $productTitle ?></div></div>
			{PRICE_<?= $offerDay['entity'] ?>_<?= $offerDay['id'] ?>}
			<?php if (!empty($offerDay['extraTxt'])): ?>
				<div class="extra-txt"><span><?= $offerDay['extraTxt'] ?></span></div>
			<?php endif; ?>
		</div></a>
<?php elseif (!empty($leftBanner)): ?>
		<div class="span6 main-banner-content"><a href="<?= $leftBanner['href'] ?>">
			<picture class="main-bannerImg">
				<source srcset="<?= All_banners::model()->getHrefPath($leftBanner['bannerId'], 'ms', $leftBanner['lang'], 'webp') ?>" type="image/webp">
				<source srcset="<?= All_banners::model()->getHrefPath($leftBanner['bannerId'], 'ms', $leftBanner['lang'], 'jpg') ?>" type="image/jpeg">
				<img class="main-bannerImg" src="<?= $leftBanner['img'] ?>" alt="<?= htmlspecialchars($leftBanner['title']) ?>"/>
			</picture>
		</a></div>
<?php endif; ?>
<?php if (!empty($rightBanner)): ?>
		<div class="span6 main-banner-content"><a href="<?= $rightBanner['href'] ?>">
			<picture class="main-bannerImg">
				<source srcset="<?= All_banners::model()->getHrefPath($rightBanner['bannerId'], 'ms', $rightBanner['lang'], 'webp') ?>" type="image/webp">
				<source srcset="<?= All_banners::model()->getHrefPath($rightBanner['bannerId'], 'ms', $rightBanner['lang'], 'jpg') ?>" type="image/jpeg">
				<img class="main-bannerImg" src="<?= $rightBanner['img'] ?>" alt="<?= htmlspecialchars($rightBanner['title']) ?>"/>
			</picture>
		</a></div>
<?php endif; ?>
	</div>
</div>

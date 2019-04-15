<?php /*Created by Кирилл (26.09.2018 20:41)*/ ?>
<div class="banners">
	<div class="container">
<?php if (!empty($offerDay)):
	$url = ProductHelper::CreateUrl($offerDay);
	$productPicture = Picture::Get($offerDay, Picture::SMALL);
	$productTitle = ProductHelper::GetTitle($offerDay, 'title');
	?>
		<div onclick="window.location.href = '<?= $url ?>'" class="span6 main-banner-content" style="background: url(/new_img/day_fon.jpg) 100% 100% no-repeat; background-size: contain; cursor: pointer;">
			<div class="photo">
				<div><img src="<?= $productPicture ?>" alt=""/></div>
			</div>
	<?php if (!empty($offerDay['priceData'][DiscountManager::DISCOUNT])) : ?>
			<div class="discount"<?php if ($offerDay['entity'] == Entity::PERIODIC): ?> style="margin-top: 9px;" <?php endif; ?>><?= Yii::app()->ui->item('PRODUCT_OF_DAY_INFO', $offerDay['priceData'][DiscountManager::DISCOUNT]) ?></div>
	<?php endif; ?>
			<div class="title"><div id="js_offerDay"><?= mb_substr($productTitle, 0, 120, 'utf-8') ?></div></div>
			<div class="cost_nds"<?php if ($offerDay['entity'] == Entity::PERIODIC): ?> style="line-height: 20px; width: 140px;" <?php endif; ?>>
<?= ProductHelper::FormatPrice($offerDay['priceData'][DiscountManager::WITH_VAT]); ?> <?= $offerDay['priceData']['unit'] ?>
				<span>(<span><?= trim(ProductHelper::FormatPrice($offerDay['priceData'][DiscountManager::BRUTTO]) . ' ' . $offerDay['priceData']['unit']) ?></span>)</span>
			</div>
			<?php if (!empty($offerDay['extraTxt'])): ?>
				<div class="extra-txt"><span><?= $offerDay['extraTxt'] ?></span></div>
			<?php endif; ?>
		</div>
<?php elseif (!empty($leftBanner)): ?>
		<div class="span6 main-banner-content"><a href="<?= $leftBanner['href'] ?>"><img class="main-bannerImg" src="<?= $leftBanner['img'] ?>" alt="<?= htmlspecialchars($leftBanner['title']) ?>"/></a></div>
<?php endif; ?>
<?php if (!empty($rightBanner)): ?>
		<div class="span6 main-banner-content"><a href="<?= $rightBanner['href'] ?>"><img class="main-bannerImg" src="<?= $rightBanner['img'] ?>" alt="<?= htmlspecialchars($rightBanner['title']) ?>"/></a></div>
<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		var i = 10;
		function otrezat(str) {
			console.log(str);
			var lastIndex = str.lastIndexOf(" ");
			return str.substring(0, lastIndex);
		}

		function findPhrase() {
			i--;
			var $block = $('#js_offerDay');
			if (($block.outerHeight(1) > ($block.closest('div.title').outerHeight(1) + 1))&&(i > 0)) {
				$block.html(otrezat($block.html()));
				findPhrase();
			}
//			else $block.html(otrezat($block.html()));
		}
		findPhrase();
	});
</script>
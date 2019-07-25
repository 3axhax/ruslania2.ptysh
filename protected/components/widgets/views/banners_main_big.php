<?php /*Created by Кирилл (26.09.2018 20:11)*/ ?>
<div class="slider_bg">
	<div class="container slider_container">
		<div class="overflow_box">
			<div class="container_slides" style="width: 1170px;height: 100%;text-align: center;">
				<a href="<?= $href ?>" title="<?= htmlspecialchars($title) ?>">
					<picture class="main-bannerImg">
						<source srcset="<?= All_banners::model()->getHrefPath($bannerId, 'mb', $lang, 'webp') ?>" type="image/webp">
						<source srcset="<?= All_banners::model()->getHrefPath($bannerId, 'mb', $lang, 'jpg') ?>" type="image/jpeg">
						<img class="main-bannerImg" src="<?= $img ?>" alt="<?= htmlspecialchars(htmlspecialchars($title)) ?>"/>
					</picture>
					<?php /*<img class="main-bannerImg" class="list-bannerImg" src="<?= $img ?>" alt="<?= htmlspecialchars($title) ?>">*/?>
				</a>
			</div>
		</div>
	</div>
</div>
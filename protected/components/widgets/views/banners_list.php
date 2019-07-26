<?php /*Created by Кирилл (04.07.2018 22:12)*/ ?>
<li class="list-banner-content">
<a href="<?= $href ?>" title="<?= htmlspecialchars($title) ?>">
	<picture class="main-bannerImg">
		<source srcset="<?= All_banners::model()->getHrefPath($bannerId, 'l', $lang, 'webp') ?>" type="image/webp">
		<source srcset="<?= All_banners::model()->getHrefPath($bannerId, 'l', $lang, 'jpg') ?>" type="image/jpeg">
		<img class="list-bannerImg" src="<?= $img ?>" alt="<?= htmlspecialchars($title) ?>" width="900">
	</picture>
</a>
</li>
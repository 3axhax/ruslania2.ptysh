<?php /**Created by Кирилл kirill.ruh@gmail.com 11.09.2019 8:35 */ ?>
<?php
$photoTable = Entity::GetEntitiesList()[$entity]['photo_table'];
$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
/**@var $photoModel ModelsPhotos*/
$photoModel = $modelName::model();
$photoId = $photoModel->getFirstId($item['id']);
$this->renderStatusLables($item['status']); ?>
<?php if (empty($photoId)): ?>
	<img class="img-view_product" alt="<?= $title ?>" src="<?= Picture::Get($item, Picture::BIG); ?>">
<?php elseif ($photoModel->isExternal($photoId)): ?>
	<img class="img-view_product" alt="<?= $title ?>" src="<?= $photoModel->getHrefPath($photoId, 'd', $item['eancode'], 'jpg') ?>">
<?php else: ?>
	<picture>
		<source srcset="<?= $photoModel->getHrefPath($photoId, 'd', $item['eancode'], 'webp') ?>" type="image/webp">
		<source srcset="<?= $photoModel->getHrefPath($photoId, 'd', $item['eancode'], 'jpg') ?>" type="image/jpeg">
		<img class="img-view_product" alt="<?= $title ?>" src="<?= $photoModel->getHrefPath($photoId, 'o', $item['eancode'], 'jpg') ?>">
	</picture>
<?php endif; ?>


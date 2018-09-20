<?php /*Created by Кирилл (05.09.2018 19:32)*/ ?>
<ul id="books_menu">
	<?
	$availCategory = array(181, 16, 206, 211, 189, 65, 67, 202);
	$rows = Category::GetCategoryList(Entity::BOOKS, 0, $availCategory);
	foreach ($rows as $row) {
		?>
		<li>
			<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(10), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
		</li>
	<? } ?>
	<?php $row = Category::GetByIds(Entity::SHEETMUSIC, 47)[0] ?>
	<li>
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
	</li>
	<?php $row = Category::GetByIds(Entity::BOOKS, 213)[0] ?>
	<li id="books_sale">
		<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
	</li>
	<li id="books_category">
		<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::BOOKS))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
	</li>
</ul>
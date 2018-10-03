<?php /*Created by Кирилл (05.09.2018 19:32)*/ ?>
<div class="click_arrow"></div>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS))); ?>"><?= Yii::app()->ui->item("A_GOTOBOOKS"); ?></a>
<div class="dd_box_bg list_subcategs" style="left: 0;">
	<div class="span10">
		<ul id="books_menu">
			<?php foreach ($rows as $row): ?>
				<li><a href="<?= $row['href'] ?>"><?= $row['name'] ?></a></li>
				<div class="clearfix"></div>
			<?php endforeach;  ?>
		</ul>
	</div>
</div>

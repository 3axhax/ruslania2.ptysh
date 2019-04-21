<?php /*Created by Кирилл (03.10.2018 00:24)*/ ?>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => 6)); ?>"><?= Yii::app()->ui->item("A_NEW_PRINT_PRODUCTS"); ?></a>
<div class="click_arrow"></div>
<div class="dd_box_bg dd_box_bg-slim list_subcategs">
	<ul class="list_vertical">
		<?php foreach ($rows as $row): ?>
			<li><a href="<?= $row['href'] ?>"><?= $row['name'] ?></a></li>
		<?php endforeach;  ?>
	</ul>
</div>

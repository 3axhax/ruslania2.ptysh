<?php /*Created by Кирилл (02.10.2018 23:57)*/ ?>
<div class="click_arrow"></div>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT))); ?>"><?= Yii::app()->ui->item("A_GOTOSOFT"); ?></a>
<div class="dd_box_bg dd_box_bg-slim list_subcategs">
	<ul class="list_vertical">
		<?php foreach ($rows as $row): ?>
			<li><a href="<?= $row['href'] ?>"><?= $row['name'] ?></a></li>
		<?php endforeach;  ?>
	</ul>
</div>

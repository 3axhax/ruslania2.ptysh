<?php /*Created by Кирилл (02.10.2018 23:50)*/ ?>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS))); ?>"><?= Yii::app()->ui->item("A_GOTOMAPS"); ?></a>
<div class="click_arrow"></div>
<div class="dd_box_bg dd_box_bg-slim list_subcategs">
	<ul class="list_vertical">
		<?php foreach ($rows as $row): ?>
			<li><a href="<?= $row['href'] ?>"><?= $row['name'] ?></a></li>
		<?php endforeach;  ?>
	</ul>
</div>

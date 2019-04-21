<?php /*Created by Кирилл (02.10.2018 23:15)*/ ?>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC))); ?>"><?= Yii::app()->ui->item("Music catalog"); ?></a>
<div class="click_arrow"></div>
<div class="dd_box_bg list_subcategs" style="left: -170px;">
	<div class="span10">
		<ul id="music_menu">
			<?php foreach ($rows as $row): ?>
				<li><a href="<?= $row['href'] ?>"><?= $row['name'] ?></a></li>
				<div class="clearfix"></div>
			<?php endforeach;  ?>
		</ul>
	</div>
</div>

<?php /*Created by Кирилл (02.10.2018 23:15)*/ ?>
<div class="click_arrow"></div>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC))); ?>"><?= (Yii::app()->getLanguage() == 'fr')?'Partitions':Yii::app()->ui->item("A_GOTOMUSICSHEETS"); ?></a>
<div class="dd_box_bg list_subcategs" style="left: -80px;">
	<div class="span10">
		<ul id="sheet_music_menu">
			<?php foreach ($rows as $row): ?>
				<li><a href="<?= $row['href'] ?>"><?= $row['name'] ?></a></li>
				<div class="clearfix"></div>
			<?php endforeach;  ?>
		</ul>
	</div>
</div>

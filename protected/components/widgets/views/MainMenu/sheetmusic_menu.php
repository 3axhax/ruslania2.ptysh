<?php /*Created by Кирилл (02.10.2018 23:15)*/
$name = Yii::app()->ui->item("A_GOTOMUSICSHEETS");
switch (Yii::app()->getLanguage()) {
	case 'fr': $name = 'Partitions'; break;
	case 'en': $name = 'Sheet music'; break;
	case 'fi': $name = 'Nuotit'; break;
	case 'es': $name = 'Partituras'; break;
//	case 'ru': $name = 'Ноты и книги о музыке'; break;
}
?>
<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC))); ?>"><?= $name ?></a>
<div class="click_arrow"></div>
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

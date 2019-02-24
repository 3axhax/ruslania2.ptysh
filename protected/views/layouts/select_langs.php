<?php /*Created by Кирилл (24.02.2019 11:06)*/ ?>
<div class="select_lang">
	<?php
	$arrLangsTitle = array(
		'ru' => $ui->item('A_LANG_RUSSIAN'),
		'rut' => $ui->item('A_LANG_TRANSLIT'),
		'fi' => $ui->item('A_LANG_FINNISH'),
		'en' => $ui->item('A_LANG_ENGLISH'),
		'de' => $ui->item('A_LANG_GERMAN'),
		'fr' => $ui->item('A_LANG_FRENCH'),
		'es' => $ui->item('A_LANG_ESPANIOL'),
		'se' => $ui->item('A_LANG_SWEDISH')
	);
	?>
	<div class="dd_select_lang">
		<div class="lable_empty" onclick="$('.dd_select_lang').toggle(); $('.label_lang.view_lang').toggleClass('act').parent().toggleClass('act')"></div>
		<?php foreach ($arrLangsTitle as $k => $v): ?>
			<div class="label_lang">
				<span class="lang <?= $k ?>"><a href="<?= MyUrlManager::RewriteCurrent($this, $k); ?>"><?= $v ?></a></span>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="label_lang view_lang" onclick="$('.dd_select_lang').toggle(); $(this).toggleClass('act'); $(this).parent().toggleClass('act')">
		<span class="lang <?= Yii::app()->language; ?>"><a href="javascript:;"><?= $arrLangsTitle[Yii::app()->language]; ?></a> <span class="fa fa-angle-down"></span></span>
	</div>
</div>


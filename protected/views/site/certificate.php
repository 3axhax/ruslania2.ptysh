<?php /*Created by Кирилл (31.10.2018 22:54)*/ ?>
<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$breadcrumbs = $this->breadcrumbs;
$h1 = array_pop($breadcrumbs);
unset($breadcrumbs) ;
$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');

KnockoutForm::RegisterScripts();

/*
 * Вид сертификата
 * Имя
 * Адрес
 * Город
 * Штат
 * Zipcode
 * Текст Вашего сообщения (будет отпечатан на сертификате)
 * Стоимость
 * */
?>

<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?= $h1 ?></h1>
			<div class="text gift_certificate">
				<div class="sample"><img src="/new_img/gift1.jpg" id="gifg_preview"></div>
				<div class="form">
					<?= CHtml::beginForm(Yii::app()->createUrl('site/certificate'), 'post'); ?>
					<div class="form_row">
						<div class="row_name">Вид сертификата</div>
						<div class="row_value"><?= CHtml::dropDownList('gift[maket]', 1, array(1=>1,2=>2,3=>3), array('style'=>'width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Имя<span>*</span></div>
						<div class="row_value"><?= CHtml::textField('gift[fio]', '', array('style'=>'width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Адрес<span>*</span></div>
						<div class="row_value"><?= CHtml::textField('gift[address]', '', array('style'=>'width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Город<span>*</span></div>
						<div class="row_value"><?= CHtml::textField('gift[city]', '', array('style'=>'width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Штат<span>*</span></div>
						<div class="row_value"><?= CHtml::textField('gift[state]', '', array('style'=>'width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Почтовый индекс<span>*</span></div>
						<div class="row_value"><?= CHtml::textField('gift[zipcode]', '', array('style'=>'width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Текст Вашего сообщения (будет отпечатан на сертификате)</div>
						<div class="row_value"><?= CHtml::textArea('gift[txt]', '', array('style'=>'height: 100px; width: 240px;')); ?></div>
					</div>
					<!--<div class="form_row">
						<div class="row_name">Стоимость</div>
						<div class="row_value"></div>
						<div class="clearBoth"></div>
					</div>-->
					<?= CHtml::endForm(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$('#gift_maket').change(function () {
			$('#gift_preview').attr('src', '/new_img/gift' + this.value + '.jpg');
			console.log($('#gift_preview').attr('src'));
		});
	});

</script>

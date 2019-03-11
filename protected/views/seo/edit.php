<?php /*Created by Кирилл (08.02.2019 22:02)*/
/** @var $seoModel SeoEdit */
?>
<style>
	.tab-content .form .form_row { display: table-row; }
	.tab-content .form .form_row div {display: table-cell; }
	.tab-content .form .form_row div.row_name { width: 110px; vertical-align: top; text-align: right; padding-right: 15px; }
	.tab-content .form .form_row div.row_name span { color: #ed1d24; width: 5px }
	.tab-content .form .form_row div.row_value textarea { width:740px; height:80px }
	div.span2 div {margin-bottom: 10px;}
	div.span2 div span {font-weight: bold;}
</style>
<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename">SEO настройки</h1>
			<div class="text charbox">
				<form method="get" class="search_aut">
					<input placeholder="сюда адрес страницы" type="text" name="path" value="<?= Yii::app()->getRequest()->getParam('path') ?>"/>
					<input type="submit" value="Найти"/>
				</form>
			</div>
			<?php if (!empty($seoModel)): ?>
			<div>
				<ul class="nav nav-tabs">
					<?php $i = 0; foreach ($seoModel->getAttributes() as $k=>$v): if (!in_array($k, array('id_seo_settings', 'route', 'entity', 'id'))):?>
						<li<?php if (empty($i)): ?> class="active"<?php endif; ?>><a href="#<?= $k ?>" data-toggle="tab" onclick="return false;"><?= $k ?></a></li>
					<?php $i++; endif; endforeach; ?>
				</ul>
				<?php $form = $this->beginWidget('CActiveForm', array(
					'action' => Yii::app()->createUrl('seo/change'),
					'id' => 'seo-settings',
					'enableAjaxValidation' => true,
					'enableClientValidation' => false,
					'clientOptions' => array('validateOnSubmit' => true, 'validateOnChange' => false)
				)); ?>
				<?php $seoData = $seoModel->getAttributes(); ?>
				<?= $form->hiddenField($seoModel, 'route'); ?>
				<?= $form->hiddenField($seoModel, 'entity'); ?>
				<?= $form->hiddenField($seoModel, 'id'); ?>
				<?php if (!empty($seoData['id_seo_settings'])): ?>
					<?= $form->hiddenField($seoModel, 'id_seo_settings'); ?>
				<?php endif; ?>
				<div class="tab-content">
					<?php $i = 0; foreach ($seoModel->getAttributes() as $k=>$v): if (!in_array($k, array('id_seo_settings', 'route', 'entity', 'id'))):
						$seoData[$k] = unserialize($seoData[$k]);
						?>
						<div class="tab-pane<?php if (empty($i)): ?> active<?php endif; ?>" id="<?= $k ?>">
							<h2><?= $k ?></h2>
							<div class="form">
								<div class="form_row">
									<div class="row_name">H1</div>
									<div class="row_value">
										<textarea name="SeoEdit[<?= $k ?>][h1]"><?= empty($seoData[$k]['h1'])?'':$seoData[$k]['h1'] ?></textarea>
									</div>
								</div>
								<div class="form_row">
									<div class="row_name">TITLE</div>
									<div class="row_value">
										<textarea name="SeoEdit[<?= $k ?>][title]"><?= empty($seoData[$k]['title'])?'':$seoData[$k]['title'] ?></textarea>
									</div>
								</div>
								<div class="form_row">
									<div class="row_name">DESCRIPTION</div>
									<div class="row_value">
										<textarea name="SeoEdit[<?= $k ?>][description]"><?= empty($seoData[$k]['description'])?'':$seoData[$k]['description'] ?></textarea>
									</div>
								</div>
								<div class="form_row">
									<div class="row_name">KEYWORDS</div>
									<div class="row_value">
										<textarea name="SeoEdit[<?= $k ?>][keywords]"><?= empty($seoData[$k]['keywords'])?'':$seoData[$k]['keywords'] ?></textarea>
									</div>
								</div>
							</div>
						</div>
					<?php $i++; endif; endforeach; ?>
				</div>
				<input type="submit" class="btn btn-success" value="Сохранить"/>
				<?php $this->endWidget(); ?>
			</div>
			<?php elseif (!empty($error)): ?>
				<h2><?= $error ?></h2>
			<?php endif; ?>
		</div>
		<div class="span2">
			<div class="poht">Для автозамены:</div>
			<div><span>{geoip_country}</span> - название страны, определяется по IP</div>
			<div><span>{domain}</span> - Rusliania.com</div>
			<div><span>{page_n}</span> - Страница N (N > 1)</div>
			<div><span>{entity_name}</span> - Название раздела</div>
			<div><span>{counts}</span> - Количество в разделе</div>
			<div><span>{type_publication}</span> - тип издания (для подписки)</div>
			<div><span>{lang_predl}</span> - язык товара (предложный падеж)</div>
			<div><span>{params}</span> - Через ";" перечисляется выбранный фильтр (автор, год, цена и др)</div>
			<div><span>{name}</span> - Название, зависит от открыртой страницы, может быть названием тега или категории или товара или др.</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	var $tabContent = $('.tab-content');
	$('.nav-tabs li a').on('click', function(){
		var $li = $(this).closest('li');
		$li.siblings().removeClass('active');
		$li.addClass('active');
		var $tab = $tabContent.children('div' + this.getAttribute('href'));
		$tab.siblings().removeClass('active');
		$tab.addClass('active');
	});
});
</script>
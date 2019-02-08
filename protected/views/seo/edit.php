<?php /*Created by Кирилл (08.02.2019 22:02)*/
/** @var $seoModel SeoEdit */
?>
<style>
	.tab-content .form .form_row { display: table-row; }
	.tab-content .form .form_row div {display: table-cell; }
	.tab-content .form .form_row div.row_name { width: 230px; vertical-align: top; text-align: right; padding-right: 15px; }
	.tab-content .form .form_row div.row_name span { color: #ed1d24; width: 5px }
</style>
<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename">SEO настройки</h1>
			<div class="text charbox">
				<form method="get" class="search_aut">
					<input placeholder="сюда адрес страницы" type="text" name="path" value=""/>
					<input type="submit" value="Найти"/>
				</form>
			</div>
			<?php if (!empty($seoModel)): ?>
			<div>
				<ul class="nav nav-tabs">
					<?php $i = 0; foreach ($seoModel->getAttributes() as $k=>$v): if (!in_array($k, array('id_seo_settings', 'route', 'entity', 'id'))):?>
						<li<?php if (empty($i)): ?> class="active"<?php endif; ?>><a href="#<?= $k ?>" data-toggle="tab"><?= $k ?></a></li>
					<?php $i++; endif; endforeach; ?>
				</ul>
				<?php $form = $this->beginWidget('CActiveForm', array(
					'action' => Yii::app()->createUrl('seo/change'),
					'id' => 'seo-settings',
					'enableAjaxValidation' => true,
					'enableClientValidation' => false,
					'clientOptions' => array('validateOnSubmit' => true, 'validateOnChange' => false)
				)); ?>
				<div class="tab-content">
					<?php $i = 0; foreach ($seoModel->getAttributes() as $k=>$v): if (!in_array($k, array('id_seo_settings', 'route', 'entity', 'id'))):?>
						<div class="tab-pane<?php if (empty($i)): ?> active<?php endif; ?>" id="<?= $k ?>">
							<p><?= $k ?></p>
							<div class="form">
							</div>
						</div>
					<?php $i++; endif; endforeach; ?>
				</div>
				<?php $this->endWidget(); ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
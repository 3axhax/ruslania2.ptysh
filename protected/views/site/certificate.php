<?php /*Created by Кирилл (31.10.2018 22:54)*/ ?>
<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$breadcrumbs = $this->breadcrumbs;
$h1 = array_pop($breadcrumbs);
unset($breadcrumbs) ;
$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');

KnockoutForm::RegisterScripts();
?>
<style>
	.errorSummary {	width: 400px; float: right; border-collapse: collapse; margin-bottom: 5px; padding: 10px; background-color: rgba(255, 0, 0, 0.1); }
	.errorSummary ul {list-style-type: none;}
	.errorSummary p { font-weight: bold; }
</style>
<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?= $h1 ?></h1>
			<?= CHtml::beginForm(Yii::app()->createUrl('site/certificate'), 'post'); ?>
			<?= CHtml::errorSummary($model); ?>
			<div class="text gift_certificate">
				<div class="sample"><img src="/new_img/gift1.jpg" id="gift_preview"></div>
				<div class="form">
					<div class="form_row">
						<div class="row_name"><?= Yii::app()->ui->item('CERTIFICATE_MAKET') ?></div>
						<div class="row_value"><?= CHtml::activeDropDownList($model, 'maket_id', array(1=>1,2=>2,3=>3), array('style'=>'width: 50px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_DEST_NAME') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'fio_dest', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_DEST_EMAIL') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'email_dest', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><?= Yii::app()->ui->item('CERTIFICATE_TXT') ?></div>
						<div class="row_value"><?= CHtml::activeTextArea($model, 'txt_dest', array('style'=>'height: 100px; width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_SOURCE_NAME') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'fio_source', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_SOURCE_EMAIL') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'email_source', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_NOMINAL') ?></div>
						<div class="row_value span11" style="margin: 0; width: inherit;">
							<div><?= CHtml::activeDropDownList($model, 'nominal', $nominals, array('style'=>'width: 70px;', 'class'=>'periodic')); ?>&nbsp;<?= Currency::ToSign(Yii::app()->currency) ?></div>
							<?php /*<div class="mb5 periodic_world" style="white-space:nowrap; ">
								<?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
									<span class="without_discount"><?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO_WORLD]); ?></span>
									<span class="price">
								<b class="pwvat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT_WORLD]); ?></b>
								<span class="pwovat"><span><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT_WORLD]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
							</span>
								<?php else : ?>
									<span class="price">
								<span class="pwvat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT_WORLD]); ?></span>
								<span class="pwovat"><span><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT_WORLD]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
							</span>
								<?php endif; ?>
								<input type="hidden" value="1" class="worldmonthpriceoriginal"/>
								<input type="hidden" value="<?= round($price[DiscountManager::WITH_VAT_WORLD] / $price[DiscountManager::BRUTTO_WORLD], 2); ?>" class="worldmonthpricevat"/>
								<input type="hidden" value="<?= round($price[DiscountManager::WITHOUT_VAT_WORLD] / $price[DiscountManager::BRUTTO_WORLD], 2); ?>" class="worldmonthpricevat0"/>
							</div> */ ?>
						</div>
					</div>
				</div>
			</div>
			<?php $this->renderPartial('/payment/_payment_only_online', array('pid'=>Yii::app()->getRequest()->getPost('payment_type_id', 25))) ?>
			<div><?= CHtml::submitButton(Yii::app()->ui->item('CERTIFICATE_BUTTON_PAY'), array('style'=>'min-width: 180px; background-color: #5bb75b; border-radius: 4px; border: 0; padding: 9px 20px; text-align: center; font-size: 14px; color: #fff; font-weight: bold; float:right;', 'onclick'=>'yaCounter53579293.reachGoal(\'add_sert\');')) ?></div>
			<?= CHtml::endForm(); ?>
		</div>
	</div>
	<div style="margin-top:30px;"><?= Yii::app()->ui->item('MSG_FAIL_PAY'); ?></div>
</div>

<script type="text/javascript">
	$(function() {
		$('#Certificate_maket_id').change(function () {
			$('#gift_preview').attr('src', '/new_img/gift' + this.value + '.jpg');
		});
	});

</script>

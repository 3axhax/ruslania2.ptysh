<?php /*Created by Кирилл (31.10.2018 22:54)*/ ?>
<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$breadcrumbs = $this->breadcrumbs;
$h1 = array_pop($breadcrumbs);
unset($breadcrumbs) ;
$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');

$gift = Yii::app()->getRequest()->getPost('gift', array());
$selectPrice = isset($gift['nominal'])?$gift['nominal']:50;

KnockoutForm::RegisterScripts();

$nominals = array();
for ($i=1;$i<=100;$i++) $nominals[$i] = $i;
$rates = Currency::GetRates();
$item = array(
	'brutto' => $selectPrice/$rates[Yii::app()->currency],
	'vat' => 24,
	'entity' => 0,
	'id' => 0,
);
$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
?>

<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?= $h1 ?></h1>
			<?= CHtml::beginForm(Yii::app()->createUrl('site/certificate'), 'post'); ?>
			<div class="text gift_certificate">
				<div class="sample"><img src="/new_img/gift1.jpg" id="gift_preview"></div>
				<div class="form">
					<div class="form_row">
						<div class="row_name"><?= Yii::app()->ui->item('CERTIFICATE_MAKET') ?></div>
						<div class="row_value"><?= CHtml::dropDownList('gift[maket]', isset($gift['maket'])?$gift['maket']:1, array(1=>1,2=>2,3=>3), array('style'=>'width: 50px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_DEST_NAME') ?></div>
						<div class="row_value"><?= CHtml::textField('gift[fio_dest]', isset($gift['fio_dest'])?$gift['fio_dest']:1, array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_DEST_EMAIL') ?></div>
						<div class="row_value"><?= CHtml::textField('gift[email_dest]', isset($gift['email_dest'])?$gift['email_dest']:1, array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><?= Yii::app()->ui->item('CERTIFICATE_TXT') ?></div>
						<div class="row_value"><?= CHtml::textArea('gift[txt_dest]', isset($gift['txt_dest'])?$gift['txt_dest']:1, array('style'=>'height: 100px; width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_SOURCE_NAME') ?></div>
						<div class="row_value"><?= CHtml::textField('gift[fio_source]', isset($gift['fio_source'])?$gift['fio_source']:1, array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_SOURCE_NAME') ?></div>
						<div class="row_value"><?= CHtml::textField('gift[email_source]', isset($gift['email_source'])?$gift['email_source']:1, array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_NOMINAL') ?></div>
						<div class="row_value span11" style="margin: 0; width: inherit;">
							<div><?= CHtml::dropDownList('gift[nominal]', $price[DiscountManager::BRUTTO_WORLD], $nominals, array('style'=>'width: 70px;', 'class'=>'periodic')); ?>&nbsp;<?= Currency::ToSign(Yii::app()->currency) ?></div>
							<div class="mb5 periodic_world" style="white-space:nowrap; ">
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
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php $this->renderPartial('/payment/_payment_only_online', array('pid'=>Yii::app()->getRequest()->getPost('payment_type_id', 25))) ?>
			<div><?= CHtml::submitButton(Yii::app()->ui->item('CERTIFICATE_BUTTON_PAY'), array('style'=>'width: 180px; background-color: #5bb75b; border-radius: 4px; border: 0; padding: 9px 0; text-align: center; font-size: 14px; color: #fff; font-weight: bold; float:right;')) ?></div>
			<?= CHtml::endForm(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$('#gift_maket').change(function () {
			$('#gift_preview').attr('src', '/new_img/gift' + this.value + '.jpg');
		});
	});

</script>

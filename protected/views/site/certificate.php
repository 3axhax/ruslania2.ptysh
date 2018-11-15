<?php /*Created by Кирилл (31.10.2018 22:54)*/ ?>
<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$breadcrumbs = $this->breadcrumbs;
$h1 = array_pop($breadcrumbs);
unset($breadcrumbs) ;
$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');

KnockoutForm::RegisterScripts();

$nominals = array();
for ($i=1;$i<=100;$i++) $nominals[$i] = $i;
$rates = Currency::GetRates();
$item = array(
	'brutto' => 50/$rates[Yii::app()->currency],
	'vat' => 24,
	'entity' => 0,
	'id' => 0,
);
$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
Debug::staticRun(array($price, Currency::GetRates()));

?>

<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?= $h1 ?></h1>
			<div class="text gift_certificate">
				<div class="sample"><img src="/new_img/gift1.jpg" id="gift_preview"></div>
				<div class="form">
					<?= CHtml::beginForm(Yii::app()->createUrl('site/certificate'), 'post'); ?>
					<div class="form_row">
						<div class="row_name">Вид сертификата</div>
						<div class="row_value"><?= CHtml::dropDownList('gift[maket]', 1, array(1=>1,2=>2,3=>3), array('style'=>'width: 50px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span>Имя получателя</div>
						<div class="row_value"><?= CHtml::textField('gift[fio_dest]', '', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span>E-mail получателя</div>
						<div class="row_value"><?= CHtml::textField('gift[email_dest]', '', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name">Текст сообщения</div>
						<div class="row_value"><?= CHtml::textArea('gift[txt_dest]', '', array('style'=>'height: 100px; width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span>Имя отправителя</div>
						<div class="row_value"><?= CHtml::textField('gift[fio_source]', '', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span>E-mail отправителя</div>
						<div class="row_value"><?= CHtml::textField('gift[email_source]', '', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span>Номинал</div>
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

					<div class="form_row">
						<div class="row_name"></div>
						<div class="row_value"><?= CHtml::submitButton('Купить', array('style'=>'width: 180px; background-color: #5bb75b; border-radius: 4px; border: 0; padding: 9px 0; text-align: center; font-size: 14px; color: #fff; font-weight: bold;')) ?></div>
					</div>
					<?= CHtml::endForm(); ?>
				</div>
				<?php $this->renderPartial('/payment/_payment_only_online', array()); ?>
			</div>
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

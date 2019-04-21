<?php /*Created by Кирилл (24.02.2019 11:08)*/ ?>
<div class="select_valut">
	<?php $arrVCalut = array(
		'1' => array('euro','Euro'),
		'2' => array('usd','USD'),
		'3' => array('gbp','GBP'),
	); ?>
	<div class="dd_select_valut">
		<div class="lable_empty" onclick="$('.dd_select_valut').toggle(); $('.label_valut.select').toggleClass('act')"></div>
		<div class="label_valut">
			<a href="<?= MyUrlManager::RewriteCurrency($this, Currency::EUR); ?>"><span style="width: 17px; display: inline-block; text-align: center">&euro;</span><span class="valut" style="margin-left: 10px;">Euro</span></a>
		</div>
		<div class="label_valut">
			<a href="<?= MyUrlManager::RewriteCurrency($this, Currency::USD); ?>"><span style="width: 17px; display: inline-block; text-align: center">$</span><span class="valut" style="margin-left: 10px;">USD</span></a>
		</div>
		<div class="label_valut">
			<a href="<?= MyUrlManager::RewriteCurrency($this, Currency::GBP); ?>"><span style="width: 17px; display: inline-block; text-align: center">£</span><span class="valut" style="margin-left: 10px;">GBP</span></a>
		</div>
	</div>
	<div style="padding-top: 10px;" class="label_valut select" onclick="$('.dd_select_valut').toggle();
                                                $(this).toggleClass('act')">
		<a href="javascript:;"><span class="valut <?= $arrVCalut[(string) Yii::app()->currency][0] ?>"><?= $arrVCalut[(string) Yii::app()->currency][1] ?><span class="fa fa-angle-down"></span></span></a>
	</div>
</div>

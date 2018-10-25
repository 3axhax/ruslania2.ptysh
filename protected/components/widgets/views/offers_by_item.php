<?php /*Created by Кирилл (04.07.2018 19:00) */ ?>
<?php //$this->widget('OffersByItem', array('entity'=>$entity, 'idItem'=>)); ?>
<div class="detail-prop">
	<div class="prop-name"><?= str_replace(':', '', Yii::app()->ui->item("RUSLANIA_RECOMEND")); ?></div>
	<div class="prop-value"><ul>
<?php $i = 0; foreach ($offers as $offer):
	if (empty($offer['is_special'])):
		$href = Yii::app()->createUrl('offers/view', array('title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($offer)), 'oid'=>$offer['id']));
	else:
		$mode = Offer::getMode($offer['id']);
		if (empty($mode)) $href = Yii::app()->createUrl('site/index');
		else $href = Yii::app()->createUrl('offers/special', array('mode'=>$mode));
	endif;
	$style = '';
	if ($i > 2) $style = ' style="display:none;"'; ?>
		<li<?= $style ?>><a href="<?= $href ?>"><?= ProductHelper::GetTitle($offer) ?></a></li>
<?php $i++; endforeach; ?>
	<?php if ($i > 3): ?>
		<li style="cursor: pointer;" onclick="$(this).hide().siblings().show();"><?= Yii::app()->ui->item("SHOW_ALL"); ?></li>
	<?php endif; ?>
	</ul></div>
	<div class="clearBoth"></div>
</div>


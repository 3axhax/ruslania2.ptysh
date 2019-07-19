<?php /*Created by Кирилл (19.07.2019 22:56)*/ ?>
<div class="cost">
<?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])) : ?>
	<span class="without_discount">
        <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
    </span>&nbsp;
	<span class="price with_discount"<?php if ($item['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>>
        <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
    </span>
<?php else : ?>
	<span class="price">
        <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
    </span>
<?php endif; ?>
</div>
<div class="nds"><?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $item['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>

<?php
$realPriceTitle = 'Price';
$realKeyBrutto = DiscountManager::BRUTTO_WORLD;
$realVatPrice = DiscountManager::WITH_VAT_WORLD;
$realWOVatPrice = DiscountManager::WITHOUT_VAT_WORLD;

if($key == 'PERIODIC_WORLD')
{
    $realPriceTitle = 'PERIODIC_DELIVERY_TO_WORLD';
}
else if($key == 'PERIODIC_FIN')
{
    $realPriceTitle = 'PERIODIC_DELIVERY_TO_FINLAND';
    $realKeyBrutto = DiscountManager::BRUTTO_FIN;
    $realVatPrice = DiscountManager::WITH_VAT_FIN;
    $realWOVatPrice = DiscountManager::WITHOUT_VAT_FIN;
}

if($item['entity'] == Entity::PERIODIC && $item['id'] == 319
    && $key == 'PERIODIC_WORLD' && isset($price[DiscountManager::DISCOUNT])
    && ($price[DiscountManager::DISCOUNT] > 14 && $price[DiscountManager::DISCOUNT] < 15)
)
{
    $price[DiscountManager::DISCOUNT] = 0;
}


?>
<div class="clearfix"></div>
<div class="mb5 <?=strtolower($key); ?>" style="margin-bottom: 16px; margin-top: 30px; float: left; min-width: 220px;">
    <?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
        <div class="price_h"><?= $ui->item($realPriceTitle); ?>:</div>
		<span class="without_discount"><?= ProductHelper::FormatPrice($price[$realKeyBrutto]); ?></span>
        <span class="price">
            <b class="pwvat"<?php if ($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>><?= ProductHelper::FormatPrice($price[$realVatPrice]); ?></b>
            <span class="notes">
                <span class="fa notes-circle"></span>
                <span class="notes-block"><?= $price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL?$ui->item('MSG_PERSNAL_DISCOUNT') . ' -':$ui->item('PRICE_DISCOUNT_FORMAT'); ?> <?= $price[DiscountManager::DISCOUNT] . '%'; ?></span>
            </span>
        </span>
        <div class="price">
        <?php /*if ($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL): ?>
        <?= $ui->item('MSG_PERSNAL_DISCOUNT'); ?> - <?= $price[DiscountManager::DISCOUNT] . '%'; ?>
       <?php else: ?>
        <?= $ui->item('PRICE_DISCOUNT_FORMAT'); ?> <?= $price[DiscountManager::DISCOUNT] . '%'; ?>
        <?php endif; */?>
            <span class="pwovat"><?= ProductHelper::FormatPrice($price[$realWOVatPrice]); ?> <?= $ui->item('WITHOUT_VAT'); ?></span>
        </div>
    <?php else : ?>
        <div class="price_h"><?= $ui->item($realPriceTitle); ?>:</div>
        <span class="price">
            <span class="pwvat"><?= ProductHelper::FormatPrice($price[$realVatPrice]); ?></span>
            <span class="pwovat"><?= ProductHelper::FormatPrice($price[$realWOVatPrice]); ?> <?= $ui->item('WITHOUT_VAT'); ?></span>
        </span>
    <?php endif; ?>
</div>

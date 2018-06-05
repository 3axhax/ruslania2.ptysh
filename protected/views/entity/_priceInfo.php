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

<div class="mb5 <?=strtolower($key); ?>" style="margin-bottom: 16px;">
    <?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
        <div style="font-size: 16px; color: #301c53;"><?= $ui->item($realPriceTitle); ?>:</div> 
		
		<span style="font-size: 16px; color: #ed1d24; margin-right: 13px; text-decoration: line-through; font-size: 18px; font-weight: bold;"><?= ProductHelper::FormatPrice($price[$realKeyBrutto]); ?></span>
        <br />
        <span class="price"  style="color: #301c53;font-size: 18px; font-weight: bold;">
            <span class="title__bold pwvat">
        <?php if ($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL): ?>
            <?= $ui->item('MSG_PERSNAL_DISCOUNT'); ?> - <?= $price[DiscountManager::DISCOUNT] . '%'; ?>:
        <?php else: ?>
            <?= $ui->item('PRICE_DISCOUNT_FORMAT'); ?> <?= $price[DiscountManager::DISCOUNT] . '%'; ?>:
        <?php endif; ?>
            <br /><?= ProductHelper::FormatPrice($price[$realVatPrice]); ?></span>
            
                <span class="pwovat" style="color: #747474; font-size: 14px;"><span><?= ProductHelper::FormatPrice($price[$realWOVatPrice]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
        </span>

    <?php else : ?>

        <div style="font-size: 16px; color: #301c53;"><?= $ui->item($realPriceTitle); ?>:</div> 
        <span class="price"  style="color: #301c53;font-size: 18px; font-weight: bold;"><?= ProductHelper::FormatPrice($price[$realVatPrice]); ?></span> 
        <span class="pwovat" style="color: #747474; font-size: 14px;"><span><?= ProductHelper::FormatPrice($price[$realWOVatPrice]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>

    <?php endif; ?>
</div>

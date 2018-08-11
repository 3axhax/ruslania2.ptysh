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

if (!empty($item['issues_year'])&&is_array($item['issues_year'])) {
    if (!empty($item['issues_year']['show3Months'])) {
        $price[$realKeyBrutto] = $price[$realKeyBrutto]/4;
        $price[$realVatPrice] = $price[$realVatPrice]/4;
        $price[$realWOVatPrice] = $price[$realWOVatPrice]/4;
    }
    elseif (!empty($item['issues_year']['show3Months'])) {
        $price[$realKeyBrutto] = $price[$realKeyBrutto]/2;
        $price[$realVatPrice] = $price[$realVatPrice]/2;
        $price[$realWOVatPrice] = $price[$realWOVatPrice]/2;
    }
}

if($item['entity'] == Entity::PERIODIC && $item['id'] == 319
    && $key == 'PERIODIC_WORLD' && isset($price[DiscountManager::DISCOUNT])
    && ($price[DiscountManager::DISCOUNT] > 14 && $price[DiscountManager::DISCOUNT] < 15)
)
{
    $price[DiscountManager::DISCOUNT] = 0;
}


?>

<div class="mb5 <?=strtolower($key); ?>" style="margin-bottom: 0;     margin-right: 46px;  float: left;">
    <?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
	
		<span style="font-size: 16px; color: #ed1d24; margin-right: 13px; text-decoration: line-through; font-size: 18px; font-weight: bold;"><?= ProductHelper::FormatPrice($price[$realKeyBrutto]); ?></span>
        
        <span class="price"  style="color: #301c53;font-size: 18px; font-weight: bold;">
                <b class="pwvat"><?= ProductHelper::FormatPrice($price[$realVatPrice]); ?></b>
            
                <span class="pwovat" style="color: #747474; font-size: 14px;"><br/><span><?= ProductHelper::FormatPrice($price[$realWOVatPrice]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
        </span>

    <?php else : ?>

        <span class="price"  style="color: #301c53;font-size: 18px; font-weight: bold;"><?= ProductHelper::FormatPrice($price[$realVatPrice]); ?></span> 
        <span class="pwovat" style="color: #747474; font-size: 14px;"><br/><span><?= ProductHelper::FormatPrice($price[$realWOVatPrice]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
        </span>

    <?php endif; ?>
</div>

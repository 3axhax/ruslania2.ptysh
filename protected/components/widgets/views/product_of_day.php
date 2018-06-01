<div id='day' style='text-align:center; width:220px; margin-top: 20px; margin-bottom: 20px;'>

        <p style="font-weight:bold; color:#1A3D69;"><?=sprintf($ui->item("PRODUCT_OF_DAY_INFO"), 10); ?></p>

    <p style='font-weight:bold; color:#1A3D69; font-size: 16px;'><?=$ui->item("PRODUCT_OF_DAY_TITLE_".$item['entity']);?></p>
<!--    <p style="font-weight:bold; color:#1A3D69;">--><?//=sprintf($ui->item("PRODUCT_OF_DAY_INFO"), 10); ?><!--</p>-->

    <a href="<?=ProductHelper::CreateUrl($item);?>" class="ctitle" style="font-size: 0.8em;">
        <img src="<?=Picture::Get($item, Picture::SMALL); ?>" style='border:none'/><br/>
        <?=ProductHelper::GetTitle($item);?></a>

    <div class="mt5">
        <a class="price">
            <?=$ui->item("Price");?>&nbsp;
            <?php $price = DiscountManager::GetPrice(Yii::app()->user->id, $item); ?>

            <b><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?></b><br/>
            (<?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]); ?> <?=$ui->item('WITHOUT_VAT'); ?>)
        </a>
    </div>
    <br /><br/>
</div>


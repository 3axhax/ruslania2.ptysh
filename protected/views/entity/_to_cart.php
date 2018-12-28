<div class="to_cart text">

    <?php $price = DiscountManager::GetPrice(Yii::app()->user->id, $item); ?>

    <div class="mb5">
    <?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
            <span style="font-size: 90%; color: #DD8888;"><?= $ui->item('Price'); ?>:
                <?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]); ?>
            </span><br/>

            <span class="price"><?=$ui->item('PRICE_DISCOUNT_FORMAT'); ?> <?=$price[DiscountManager::DISCOUNT].'%'; ?>:
                <b class="pwvat"<?php if ($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?></b><br/>
                (<span class="pwovat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]); ?></span> <?=$ui->item('WITHOUT_VAT'); ?>)
            </span>

    <?php else : ?>

        <span class="price"><?= $ui->item('Price'); ?>:&nbsp;
        <b class="pwvat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?></b><br/>
        (<span class="pwovat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]); ?></span> <?=$ui->item('WITHOUT_VAT'); ?>)
        </span>

    <?php endif; ?>
    </div>


    <?php $quantity = ($item['entity'] == Entity::PERIODIC) ? 12 : 1; ?>
    <?php if($item['entity'] != Entity::PERIODIC) : ?>
    <div class="mb5" style="color:#0A6C9D">
        <?=Availability::ToStr($item); ?>
    </div>
    <?php endif; ?>

    <?php if($hideButtons) { echo '</div>'; return; } ; ?>

    <?php if ($item['entity'] == Entity::PERIODIC) : ?>
        <select class="periodic">
            <option value="6">6 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?></option>
            <option value="12" selected="selected">12 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?></option>
        </select><br/><br/>
        <input type="hidden" value="<?=round($price[DiscountManager::WITH_VAT]/12, 2); ?>" class="monthpricevat"/>
        <input type="hidden" value="<?=round($price[DiscountManager::WITHOUT_VAT]/12, 2); ?>" class="monthpricevat0"/>
    <?php endif; ?>

    <?php if (ProductHelper::IsAvailableForOrder($item)) : ?>
        <a class="cart-action add" data-action="add" data-entity="<?= $item['entity']; ?>"
           data-quantity=<?= $quantity; ?> data-id="<?=$item['id']; ?>"  href="<?=Yii::app()->createUrl('cart/add', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><img
            src="/pic1/<?= $ui->item('ADD_TO_BASKET_PICTURE'); ?>"
            alt="<?= $ui->item('ADD_TO_BASKET_ALT'); ?>"/></a><br/>
    <?php else : ?>

        <?php if(Yii::app()->user->isGuest) : ?>

            <a href="<?=Yii::app()->createUrl('cart/dorequest',
                    array('entity' => Entity::GetUrlKey($item['entity']),
                          'iid' => $item['id'])); ?>"><img
                    src="/pic1/<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_PICTURE'); ?>"
                    alt="<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_ALT'); ?>"/></a>

        <?php else : ?>
        <a class="cart-action" data-action="request" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>"
           href="<?= Yii::app()->createUrl('cart/request', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><img
                src="/pic1/<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_PICTURE'); ?>"
                alt="<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_ALT'); ?>"/></a>

        <?php endif; ?>
    <?php endif; ?>
    <a class="cart-action" data-action="mark" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>"
       href="<?= Yii::app()->createUrl('cart/mark', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><img
            src="/pic1/<?= $ui->item('BTN_SHOPCART_ADD_SUSPEND_PICTURE'); ?>"
            alt="<?= $ui->item('BTN_SHOPCART_ADD_SUSPEND_ALT'); ?>"/></a><br/>
</div>

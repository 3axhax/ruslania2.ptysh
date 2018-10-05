<table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1">
    <thead>
    <tr>
        <th width="100%" valign="middle" class="cart1header1">Название</th>
        <th valign="middle" align="center" class="cart1header1">Цена</th>
        <th valign="middle" align="center" class="cart1header1">Количество</th>
        <th valign="middle" align="center" class="cart1header1">Всего</th>
        <th valign="middle" align="center" class="cart1header1">Общий&nbsp;вес</th>
    </tr>
    </thead>
    <?php $total = 0; ?>

    <tbody>
    <?php foreach($cart as $c) : ?>
        <?php $totalLine = $c['Quantity'] * $c['Price'];
              $total += $totalLine;
        ?>
    <tr>
        <td width="100%" valign="middle" class="cart1contents1">
            <span class="entity_icons"><i class="fa e<?= $c['Entity'] ?>"></i></span>
            <?php /*
            <img width="31" height="31" align="middle"
                                                                     style="vertical-align: middle"
                                                                     src="/pic1/cart_ibook.gif">
 */ ?>
            &nbsp;&nbsp;<a
                title="Получить дополнительную информацию об этом товаре"
                href="<?=$c['Url']; ?>" class="maintxt1"><?=CHtml::encode($c['Title']); ?></a></td>
        <td valign="middle" nowrap="" align="center" class="cart1contents1"><?=CHtml::encode($c['PriceStr']); ?></td>
        <td valign="middle" align="center" class="cart1contents1"><?=CHtml::encode($c['Quantity']); ?></td>
        <td valign="middle" nowrap="" align="center" class="cart1contents1"><?=$totalLine; ?> <?=ProductHelper::FormatCurrency(); ?></td>
        <td valign="middle" align="center" class="cart1contents1">
            <?=sprintf($ui->item("WEIGHT_FORMAT"), $c['UnitWeight']); ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td align="right" class="cart1header2">Итого, без учета стоимости доставки:</td>
        <td style="font-weight: bold;" colspan="4" class="cart1header2" id="total" data-total="<?=$total; ?>"><?=ProductHelper::FormatPrice(null, $total); ?></td>
    </tr>

    <tr data-bind="visible: DeliveryAddressID() > 0">
        <td align="right" class="cart1header3">Стоимость доставки заказа до:
         <span data-bind="text: DeliveryAddressText"></span>
         </td>
        <td style="font-weight: bold;" colspan="5" class="cart1header3"><span data-bind="text: DeliveryPrice"></span> <?=ProductHelper::FormatCurrency(); ?></td>
    </tr>
    <tr>
        <td align="right" class="cart1header3">Общая стоимость:</td>
        <td style="font-weight: bold;" colspan="5" class="cart1header3"><span data-bind="text: TotalPrice"></span> <?=ProductHelper::FormatCurrency(); ?></td>
    </tr>

    </tbody>
</table>
<?php KnockoutForm::RegisterScripts(); ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top" class="text">
<tr>
<td class="leftmnu" width="20%" valign="top">
    <?php $this->renderPartial('/site/_me_left'); ?>
</td>
<td valign="top" style="padding: 5px;" id="cart">

<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<br/>

<?php if(empty($requestItems)) : ?>

    <table border="0" cellspacing="5">
        <td valign=top><img src=/pic1/note1.gif width=18 height=18></td>
        <td class=maintxt><?=$ui->item("MSG_CART_ORDER_ERROR_EMPTY"); ?></td>
    </table>

<?php else : ?>

    <!-- cart -->
    <table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1">
        <thead>
        <tr>
            <th width="100%" valign="middle" class="cart1header1"><?=$ui->item("CART_COL_TITLE"); ?></th>
            <th valign="middle" align="center" class="cart1header1"><?=$ui->item("CART_COL_QUANTITY"); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($requestItems as $c) : ?>
            <tr>
                <td width="100%" valign="middle" class="cart1contents1">
                    <span class="entity_icons"><i class="fa e<?= $c['Entity'] ?>"></i></span>
                    <?php /*
                    <img width="31" height="31"
                                                                             align="middle"
                                                                             style="vertical-align: middle"
                                                                             src="/pic1/cart_ibook.gif">
               */ ?>
                    &nbsp;&nbsp;<a
                        title=""
                        href="<?= $c['Url']; ?>" class="maintxt1"><?= CHtml::encode($c['Title']); ?></a></td>
                <td valign="middle" align="center"
                    class="cart1contents1"><?= CHtml::encode($c['Quantity']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <!-- /cart -->

    <br/>

    <div class="accordion">
        <h3>Примечания и подтверждение заявки</h3>

        <div class="inner">
            <p>Напишите ваши пожелания к заявке:</p>
            <?=CHtml::beginForm('/order/createrequest'); ?>
                <textarea name="Notes" class="notesta"></textarea><br/><br/>
                <input type="submit" value="<?=$ui->item('BTN_SAVEPLACEORDER_ALT'); ?>" class="sort" />
            </form>
        </div>
    </div>

<?php endif; ?>

<!-- /content -->
</td>
</tr>

</table>








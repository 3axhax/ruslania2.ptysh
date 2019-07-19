<?php /*Created by Кирилл (19.07.2019 22:05)*/ ?>
<?php if ($sCount > 0): ?>
<a class="count<?= $sCount ?> cart-action cart_add_slider add_cart list_cart add_cart_plus cartMini<?= $id ?> green_cart" data-action="add" data-entity="<?= $eid ?>" data-id="<?= $id ?>" data-quantity="1" href="javascript:;" style="width: 177px; " onclick="searchTargets('add_cart_index_slider');">
	<span style="width: auto;"><?= sprintf(Yii::app()->ui->item('CARTNEW_IN_CART_BTN'), $sCount) ?></span>
</a>
<?php else: ?>
<a class="cart-action add_cart_plus cartMini<?= $id ?>" data-action="add" data-entity="<?= $eid ?>" data-id="<?= $id ?>" data-quantity="1" href="javascript:;" style="width: 135px;"  onclick="searchTargets('add_cart_index_slider');">
	<span><?= Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART') ?></span>
</a>
<?php endif;

<?php /*Created by Кирилл (19.07.2019 22:53)*/ ?>
<?php if ($sCount > 0): ?>
<a class="fa cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$id?> green_cart" data-action="add" data-entity="<?= $eid; ?>" data-id="<?= $id; ?>" data-quantity="1" href="javascript:;"  style="width: 132px;" onclick="searchTargets('add_cart_index');">
	<span><?=sprintf(Yii::app()->ui->item('CARTNEW_IN_CART_BTN'), $sCount)?></span>
</a>
<?php else : ?>
<a class="cart-action add_cart_plus list_cart cart<?=$id?>" data-action="add" data-entity="<?= $eid; ?>"
   data-id="<?= $id; ?>" data-quantity="1"
   href="javascript:;" style="width: 132px;" onclick="searchTargets('add_cart_index');"><span><?=Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span></a>
<?php endif; 
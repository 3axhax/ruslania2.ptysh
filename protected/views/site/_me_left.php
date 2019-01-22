<h2 class="cattitle me_left" style="margin-bottom: 20px;"><?=$ui->item('MENU'); ?></h2>

<ul class="left_list divider text">
    <li>
        <a href="<?=Yii::app()->createUrl('client/me'); ?>"><?=$ui->item('YM_CONTEXT_PERSONAL_MAIN'); ?></a>
    </li>
    <li>
        <a href="<?=Yii::app()->createUrl('cart/view'); ?>"><?=$ui->item("A_LEFT_PERSONAL_SHOPCART"); ?></a>
    </li>
    <li>
        <?php if(Yii::app()->user->isGuest) : ?>
            <span class="gray"><?=$ui->item("A_LEFT_PERSONAL_ORDERS"); ?></span>
        <?php else : ?>
            <a href="<?=Yii::app()->createUrl('my/orders'); ?>"><?=$ui->item("A_LEFT_PERSONAL_ORDERS"); ?></a>
        <?php endif; ?>
    </li>
    <li>
        <?php if(Yii::app()->user->isGuest) : ?>
            <span class="gray"><?=$ui->item("A_LEFT_PERSONAL_NOTAVAIBLE_ORDERS"); ?></span>
        <?php else : ?>
            <a href="<?=Yii::app()->createUrl('my/requests'); ?>"><?=$ui->item("A_LEFT_PERSONAL_NOTAVAIBLE_ORDERS"); ?></a>
        <?php endif; ?>
    </li>
    <li>
        <a href="<?=Yii::app()->createUrl('my/memo'); ?>"><?=$ui->item("MSG_SHOPCART_SUSPENDED_ITEMS"); ?></a>
    </li>

		<li>
        <?php if(Yii::app()->user->isGuest) : ?>
            <span class="gray"><?=Yii::app()->ui->item('A_NEW_SUBS_MENU_TTILE')?></span>
        <?php else : ?>
            <a href="<?=Yii::app()->createUrl('my/subscriptions'); ?>"><?=Yii::app()->ui->item('A_NEW_SUBS_MENU_TTILE')?></a>
        <?php endif; ?>
    </li>

    <li>
        <?php if(Yii::app()->user->isGuest) : ?>
            <span class="gray"><?=$ui->item("A_LEFT_PERSONAL_ADDRESSES"); ?></span>
        <?php else : ?>
            <a href="<?=Yii::app()->createUrl('my/addresses'); ?>"><?=$ui->item("A_LEFT_PERSONAL_ADDRESSES"); ?></a>
        <?php endif; ?>
    </li>
    <li>
        <?php if(Yii::app()->user->isGuest) : ?>
            <span class="gray"><?=$ui->item("A_LEFT_PERSONAL_USERDATA"); ?></span>
        <?php else : ?>
            <a href="<?=Yii::app()->createUrl('my/data'); ?>"><?=$ui->item("A_LEFT_PERSONAL_USERDATA"); ?></a>
        <?php endif; ?>
    </li>
    <li>
        <a href="<?=Yii::app()->createUrl('site/logout'); ?>"><?=$ui->item("A_LEFT_PERSONAL_LOGOUT"); ?></a>
    </li>
</ul>



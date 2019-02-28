<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<style>
    
    .orders-list li:first-child { margin-top: 0; }
    
    .orders-list li { margin: 20px 0; }
    
</style>

<div class="container cabinet">

<div class="row">
        <div class="span10" style="float: right">
            <!-- content -->

            <?php if(Yii::app()->user->isGuest) { $this->renderPartial('/site/login_form2', array('model' => new User, 'refresh' => true)); } else { ?>

            <div class="my-orders">

                <?=sprintf($ui->item('MSG_PERSNAL_INDEX_GREETEING_AUTH'), CHtml::encode($user['first_name']).' '.CHtml::encode($user['last_name'])); ?><br/>
                <?php if($user['discount'] > 0) : ?>
                    <?=sprintf($ui->item('MSG_PERSNAL_INDEX_DISCOUNT'), '<b>'.$user['discount'].'</b>'); ?>
                <?php endif; ?>
                <br/>
                <?php if(empty($orders)) : ?>
                    <div class="info-box information" style="padding-left: 0;">
                        
						<?
							
							$cart = new Cart;
							
							$cartGoods = $cart->GetCart($this->uid, $this->sid, $isMiniCart);
        $tmp = $cart->BeautifyCart($cartGoods, $this->uid, $isMiniCart);
        $inCart = array();
        $endedItems = array();
        foreach ($tmp as $item) {
            if ($item['IsAvailable'])
                $inCart[] = $item;
            else
                $endedItems[] = $item;
        }
							
							if ($inCart[0]) {
								
								?>
								
								<a href="<?=Yii::app()->createUrl('cart/view');?>" class="order_start" style="background-color: #5bb75b;width:  auto;padding: 9px;">
                            <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;">Продолжить оформление заказа</span>
                        </a>
								
								<?
								
							} else {
								
								?>
								<?=$ui->item('ORDER_MSG_NO_ORDERS'); ?>
								<?
								
							}
						
						?>
						
						
						
                    </div>

                <?php else : ?>
                    <?php

                    $open = array();
                    $notPay = array();

                    foreach($orders as $order)
                    {
                        if(!OrderState::IsClosed($order['States']))
                        {
                            $open[$order['id']] = $order;
                        }
                        if(!OrderState::IsPaid($order['States'])) $notPay[$order['id']] = $order;
                    }

                    ?>

<!--                    <div class="information info-box">-->
<!--                    У вас <a href="--><?//=Yii::app()->createUrl('my/orders'); ?><!--"><b>--><?//=count($orders); ?><!--</b></a> заказов в нашем магазине,-->
<!--                                                                                                        из которых <b>--><?//=count($open); ?><!--</b> в процессе выполнения и <b>--><?//=count($notPay); ?><!--</b> еще не оплаченных-->
<!---->
<!--                    </div>-->

                    <ul class="orders-list">
                        <?php foreach($orders as $order) : ?>
                            <?php
                            $id = $order['id'];
                            $first = OrderState::GetFirstState($order['States']);

                            $isClosed = OrderState::IsClosed($order['States']);
                            $isCancelled = OrderState::IsCancelled($order['States']);
                            $class =  $isClosed ? 'closed' : 'open';
                            ?>
                            <li class="<?=$class; ?>">
                                <a href="<?=Yii::app()->createUrl('order/view', array('oid' => $id)); ?>"><?=sprintf($ui->item('ORDER_MSG_NUMBER'), $id); ?></a>, <?=$first['date_string']; ?>, <?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?> <?=ProductHelper::FormatPrice($order['full_price'], $order['currency_id']); ?>

                                 <?php if(!$isClosed) : ?>
                                        <?php if(array_key_exists($id, $notPay)) : ?>
                                <a href="<?=Yii::app()->createUrl('cart/orderPay'); ?>?id=<?=$order['id']?>&ptype=<?=$order['payment_type_id']?>" class="order_start" style="float: right; background-color: #5bb75b; margin-right: 90px;"><?=$ui->item('ORDER_BTN_PAY_LUOTTOKUNTA'); ?></a>   
                                
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <div class="r">
                                    <?php if(!$isClosed) : ?>
                                        <?php if(array_key_exists($id, $notPay)) : ?>
                                            <span class="warning"><?=$ui->item('ORDER_NOT_PAID'); ?></span>
                                         
                                        <?php else : ?>
                                            <span class="success"><?=$ui->item('ORDER_PAID'); ?></span>
                                        <?php endif ; ?>
                                    <?php else : ?>
                                        <?php if($isCancelled) : ?>
                                            <?=$ui->item('ORDER_MSG_STATE_5'); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
			<?}?>
            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>

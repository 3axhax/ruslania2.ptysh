<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<script type="text/javascript">
	
	$(document).ready(function() {
		
		//ym(53579293, 'reachGoal', 'oplata_true');
	})
	
	</script>
<div class="container cabinet" style="margin-bottom: 50px;">
    <div class="row">
        <div class="span10" style="float: none;width: 100%;">
            <h1><?= $ui->item('PAY_ACCEPT_H1') ?></h1>
            <div class="info-box warning" style="border: none; background-color: rgba(85,180,86,0.4); color: #333333;">
                <h2><?=$ui->item('A_SAMPO_PAYMENT_ACCEPTED'); ?></h2>
                <?=$ui->item('MSG_PAYMENT_RESULTS_ACCEPTED_2'); ?><br/>
                <?=$ui->item('MSG_PAYMENT_RESULTS_ACCEPTED_3'); ?><br/>
            </div>
<div>
    <b><?=sprintf($ui->item("ORDER_MSG_NUMBER"), $order['id']); ?></b>
    <?php $this->widget('TradedoublerPixel', array(
                'orderValue' => Currency::ConvertToEUR($order['items_price'], $order['currency_id']),
                'orderNumber' => $order['id'],
            ));
    ?>
    <?php if($order['is_reserved']) : ?>
        <div class="mbt10">
            <?=$ui->item('IN_SHOP_NOT_READY'); ?>
            <br/><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>: <b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b>
        </div>
    <?php else : ?>
        <div class="mbt10 info_order">
            <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_DELIVERY_ADDRESS"); ?>:</span> <div class="span11"><?=CommonHelper::FormatAddress($order['DeliveryAddress']); ?></div></div>
            <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_DELIVERY_TYPE"); ?>:</span> <div class="span11"><?=CommonHelper::FormatDeliveryType($order['delivery_type_id']); ?></div></div>
            <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_BILLING_ADDRESS"); ?>:</span> <div class="span11"><?=CommonHelper::FormatAddress($order['BillingAddress']); ?></div></div>
            <div class="row"><span class="span1"><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:</span> <div class="span11"><b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b></div></div>
        </div>
        <?php if(!empty($order['notes'])) : ?>
            <div class="mbt10">
                <?=$ui->item('ORDER_MSG_USER_COMMENTS'); ?>: <?=nl2br(CHtml::encode($order['notes'])); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div style="margin-top: 20px;"><?= $ui->item('STATUS_IN_ME', Yii::app()->createUrl('client/me')) ?></div>
</div>
            <!-- /content -->
            <div class="clearBoth"></div>

        </div>

        <div class="span2" >

            <?php //$this->renderPartial('/site/_me_left'); ?>

        </div>


    </div>
</div>
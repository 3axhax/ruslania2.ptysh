<?php $onlyContent = isset($onlyContent) && $onlyContent; ?>
<?php $enableSlide = isset($enableSlide) && $enableSlide; ?>
<?php $class = empty($class) ? '' : 'class="'.$class.'"'; ?>
<div <?=$class;?>>
    <b><?=sprintf($ui->item("ORDER_MSG_NUMBER"), $order['id']); ?></b>

                <?php if($order['is_reserved']) : ?>

                    <div class="mbt10">
                        <?=$ui->item('IN_SHOP_NOT_READY'); ?>
<!--                        --><?//=$ui->item("MSG_PERSONAL_PAYMENT_INSHOP_COMMENTS"); ?>
                        <br/><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>: <b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b>
                    </div>

                <?php else : ?>

                <div class="mbt10 info_order">
                    <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_DELIVERY_ADDRESS"); ?>:</span> <div class="span11"><?=CommonHelper::FormatAddress($order['DeliveryAddress']); ?></div></div>
                    <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_DELIVERY_TYPE"); ?>:</span> <div class="span11"><?=CommonHelper::FormatDeliveryType($order['delivery_type_id']); ?></div></div>
                    <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_BILLING_ADDRESS"); ?>:</span> <div class="span11"><?=CommonHelper::FormatAddress($order['BillingAddress']); ?></div></div>
                    <?php if(!$onlyContent) : ?>
                        <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_PAYMENT_TYPE"); ?>:</span> <div class="span11"><?=CommonHelper::FormatPaymentType($order['payment_type_id']); ?></div></div>
                    <?php endif; ?>
                    <div class="row"><span class="span1"><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:</span> <div class="span11"><b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b></div></div>
                </div>

                <?php if(!empty($order['notes'])) : ?>
                    <div class="mbt10">
                        <?=$ui->item('ORDER_MSG_USER_COMMENTS'); ?>: <?=nl2br(CHtml::encode($order['notes'])); ?>
                    </div>
                <?php endif; ?>
                <?php endif; ?>



                <?php if($enableSlide) : ?>
                    <a href="#" onclick="slideContents(<?=$order['id']; ?>); return false;"><b><?=$ui->item("ORDER_MSG_CONTENTS"); ?></b></a>
                <?php else : ?>
                    
                <?php endif; ?>
                
                <table cellspacing="1" cellpadding="5" border="0" width="100%" class="cart1 items_orders" id="cnt<?=$order['id']; ?>" style="display: <?=$enableSlide ? 'none' : 'table'; ?>">
                    <tbody>
                    <tr>
                        <th width="70%" class="cart1header1"><?=$ui->item("CART_COL_TITLE"); ?></th>
                        <th width="10%" class="cart1header1 center"><?=$ui->item("CART_COL_QUANTITY"); ?></th>
                        <th width="20%" class="cart1header1 center"><?=$ui->item("CART_COL_SUBTOTAL_PRICE"); ?></th>
                    </tr>

                    <?php foreach($order['Items'] as $item) : ?>
                    <tr>
                        <td class="cart1contents1"><a class="maintxt"
                                                      href="<?=ProductHelper::CreateUrl($item); ?>"><?=ProductHelper::GetTitle($item); ?></a></td>
                        <td class="cart1contents1 center"><?=$item['quantity']; ?></td>
                        <td class="cart1contents1 center"><?=$item['items_price']; ?> <?=Currency::ToSign($order['currency_id']); ?></td>
                    </tr>

                    <?php endforeach; ?>

                    <tr class="footer">
						
						<td colspan="3">
							<div class="summa">
								
								<a style="float: left;" href="<?=Yii::app()->createUrl('client/printorder', array('oid' => $order['id'])); ?>" class="maintxt printed_btn"
                                     target="_new"><?=$ui->item('MSG_ACTION_PRINT_ORDER'); ?></a>
								
							<div class="itogo">
								<?=$ui->item("CART_COL_TOTAL_FULL_PRICE"); ?>: <b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?>
                            <?php if($order['currency_id'] != Currency::EUR) : ?>
                                (<?php $eur = Currency::ConvertToEUR($order['full_price'], $order['currency_id']);
                                        echo ProductHelper::FormatPrice($eur, true, Currency::EUR); ?>)
                            <?php endif; ?></b>
							</div><div class="clearfix"></div>
							
							</div>
						</td>
						
                        <td align="right" class="cart1contents1" colspan="2"></td>
                        <td class="cart1contents1 center" colspan="1">
                        </td>
                    </tr>
                    </tbody>
                </table>

				
                <?php if(!$onlyContent) : ?>
				<b><?=$ui->item("ORDER_MSG_HISTORY"); ?></b>
                <table cellspacing="1" cellpadding="5" style="margin-top: 10px" class="cart1 history_orders">
                    <tbody>
                     <tr>
                        <th class="cart1header1"><?=$ui->item("ORDER_MSG_HISTORY_DATE"); ?></th>
                        <th class="cart1header1"><?=$ui->item("ORDER_MSG_HISTORY_ACTION"); ?></th>
                    </tr>
                    <?php foreach($order['States'] as $state) : ?>
                    <tr>
                        <td class="cart1contents1"><?=$state['timestamp']; ?></td>
                        <td class="cart1contents1"><?=$ui->item("ORDER_MSG_STATE_".$state['state']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            
</div><?if ($co != $i){?><hr style="margin: 30px 0;" /><?}?>
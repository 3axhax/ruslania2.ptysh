<?php $onlyContent = isset($onlyContent) && $onlyContent; ?>
<?php $enableSlide = isset($enableSlide) && $enableSlide; ?>
<?php $class = empty($class) ? 'class="order_info_zakaz"' : 'class="order_info_zakaz '.$class.'"'; ?>

<style>

	.order_info_zakaz .info_order .row { margin: 5px 0; }
	.order_info_zakaz .info_order .row .span1 { margin-left: 0; width: 100%; }
	.order_info_zakaz .info_order .row .span11 { margin-left: 0; width: 100%; }
	table.history_orders { width: 100%; }
	.label_block { height: 22px; padding: 4px 6px; }
	.fa-check { margin-left: 10px; margin-top: 2px; float: right; }
	.row_addr .fa-pencil { float: right; }
	.redtext { color: #ed1d24;  }
	
	#pay_systems .selp { width: 85% !important; text-align: center; margin-left: 0}
	
</style>

<script>

	function editAddr(order_id, cont) {
		
		$('.addr_buyer_form').fadeIn();
		
		if ($('i', cont).hasClass('fa-close')) {
			
			$('.addr_buyer_form').fadeOut();
			$('i', cont).attr('class','fa fa-pencil');
		
		} else {
			$('i', cont).attr('class','fa fa-close');
		}
		
		
		
		
		
	}
	
	function saveDataAddr(t, cont) {
		
		var inp = $('input',cont.parent()).val();
		var inp2 = $('select',cont.parent()).val();
		var csrf = $('meta[name=csrf]').attr('content').split('=');
		
		if (t=='80' || t=='81') { inp = $('select',cont.parent()).val(); }
		
		if ($('i.fa', cont.parent()).hasClass('fa-pencil')) {
			
			$('input', cont.parent()).show();
			$('select', cont.parent()).show();
			
			$('.label_block', cont.parent()).hide();
			$('i.fa', cont.parent()).attr('class', 'fa fa-check');
			$('i.fa', cont.parent()).css('margin-left', '10px');
			
			
			
			
		} else {
		
		
		if (t=='1' || t=='2' || t=='4' || t=='5' || t=='6' || t=='7' || t=='80' || t=='81') {
			
			if (inp=='' || inp2 =='') {
				
				$('input, select',cont.parent()).css('border', '1px solid red');
				
			} else {
				
				$('input',cont.parent()).css('border', '');
				$('select',cont.parent()).css('border', '');
				
				$.post('<?=Yii::app()->createUrl('cart/editaddr')?>', { text : inp, text2 : inp2, ty : t, id : '<?=$order['billing_address_id']?>', YII_CSRF_TOKEN : csrf[1] }, function(data) {
			
			if (inp != '' || inp2 !='') {
			
				$('input', cont.parent()).hide();
				$('select', cont.parent()).hide();
				$('.label_block', cont.parent()).html(inp);
				$('.label_block', cont.parent()).css('display', 'inline-block');
				$('i.fa', cont.parent()).attr('class', 'fa fa-pencil');
				$('i.fa', cont.parent()).css('margin-left', '10px');
			
			}	
			
			if (data) {
				
				data = JSON.parse(data);
				
				
				$('.order_addr_buyer').html(data.addr_full);
				
				if (t==81) {
				
					$('.label_block', cont.parent()).html(data.state);
				
				}
				
				if (t==80) {
				
					$('.label_block', cont.parent()).html(data.country);
				
				}
				
			}
			
			
			
		})
				
				
			}
			
		}
		
		if (t == '3') {
		
		
		$.post('<?=Yii::app()->createUrl('cart/editaddr')?>', { text : inp, ty : t, id : '<?=$order['billing_address_id']?>', YII_CSRF_TOKEN : csrf[1] }, function(data) {
			
			if (inp != '') {
			
				$('input', cont.parent()).hide();
				$('.label_block', cont.parent()).html(inp);
				$('.label_block', cont.parent()).css('display', 'inline-block');
				$('i.fa', cont.parent()).attr('class', 'fa fa-pencil');
				$('i.fa', cont.parent()).css('margin-left', '10px');
			
			}	
			
			if (data) {
				
				data = JSON.parse(data);
				
				
				$('.order_addr_buyer').html(data.addr_full);
				
				if (t==81) {
				
					$('.label_block', cont.parent()).html(data.state);
				
				}
				
				if (t==80) {
				
					$('.label_block', cont.parent()).html(data.country);
				
				}
				
			}
			
			
			
		})
		
		}
		
		
		}
	}
	
	
	function select_city(cont) {
		
		var csrf = $('meta[name=csrf]').attr('content').split('=');
		
		if (cont.val() == 225 || cont.val() == 37 || cont.val() == 15) {

                $.post('<?= Yii::app()->createUrl('cart') ?>loadstates', {id: cont.val(), YII_CSRF_TOKEN: csrf[1]}, function (data) {
					
					$('.states_list').show();
					
                    $('.states_list select').html(data);

                    

                });

            } else {
				$('.states_list').hide();
                $('.states_list select').html('<select name="states"><option value="">Выберите штат</option></select>');

            }
		
	}

</script>

<div <?=$class;?>>
    <b><?=sprintf($ui->item("ORDER_MSG_NUMBER"), $order['id']); ?></b>

                <?php if($order['is_reserved'] == 9999) : ?>

                    <div class="mbt10">
                        <?=$ui->item('IN_SHOP_NOT_READY'); ?>
<!--                        --><?//=$ui->item("MSG_PERSONAL_PAYMENT_INSHOP_COMMENTS"); ?>
                        <br/><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>: <b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b>
                    </div>

                <?php else : ?>

                <div class="mbt10 info_order">
                    <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_DELIVERY_ADDRESS"); ?>:</span> <div class="span11">
					
					<? if ( $order['smartpost_address'] ) : echo $order['smartpost_address']; else : ?>
					
					<?=CommonHelper::FormatAddress($order['DeliveryAddress']); endif; ?></div></div>
                    <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_DELIVERY_TYPE"); ?>:</span> <div class="span11">
					
					<? if ( $order['smartpost_address'] ) : echo 'SmartPost'; else : ?>
					
					<?=CommonHelper::FormatDeliveryType($order['delivery_type_id']); endif; ?>
					
					
					</div></div>
                    <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_BILLING_ADDRESS"); ?>:</span> <div class="span11"><span class="order_addr_buyer"><?=CommonHelper::FormatAddress($order['BillingAddress']); ?></span> <? if ($order['hide_edit_order'] != '1') : ?><a href="javascript:;" style="margin-left: 20px;" title="Редактировать адрес плательщика" onclick="editAddr(<?=$order['id']; ?>, $(this));"><i class="fa fa-pencil"></i></a><? endif; ?>
					
					<? if ($order['hide_edit_order'] != '1') :
					$addrGet = CommonHelper::FormatAddress2($order['BillingAddress']);
					
					//var_dump($addrGet);
					
					?>
					<form class="addr_buyer_form" style="margin: 15px 0; display: none; width: 380px;">
					<div class="row_addr">
						
						<?//=(($addrGet['last_name']) ?'inline-block' : 'none' )?>
						
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Фамилия </div><span class="label_block" style="display: <?=(($addrGet['last_name']) ?'inline-block' : 'none' )?>;"><?=$addrGet['last_name']?></span><input type="text" style="margin: 0;<?=(($addrGet['last_name']) ?'display:none' : '' )?>" value="<?=$addrGet['last_name']?>" class="fam_addr_buyer" />
						
						<a href="javascript:;" onclick="saveDataAddr(1, $(this))"><i class="fa fa-<?=(($addrGet['last_name']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Имя </div><span class="label_block" style="display: <?=(($addrGet['first_name']) ?'inline-block' : 'none' )?>;"><?=$addrGet['first_name']?></span><input type="text" style="margin: 0;<?=(($addrGet['first_name']) ?'display:none' : '' )?>"  value="<?=$addrGet['first_name']?>" class="name_addr_buyer" /> <a href="javascript:;" onclick="saveDataAddr(2, $(this))"><i class="fa fa-<?=(($addrGet['first_name']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr">
						<div style="display: inline-block; width: 130px;">Отчество </div><span class="label_block" style="display: <?=(($addrGet['middle_name']) ?'inline-block' : 'none' )?>;"><?=$addrGet['middle_name']?></span><input type="text" style="margin: 0;<?=(($addrGet['middle_name']) ?'display:none' : '' )?>"  value="<?=$addrGet['middle_name']?>" class="middle_addr_buyer" /> <a href="javascript:;" onclick="saveDataAddr(3, $(this))"><i class="fa fa-<?=(($addrGet['middle_name']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr" style="margin: 5px 0">
					<?//=$addrGet['country_name']?>
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Страна </div><span class="label_block" style="display: none;"></span><?
						$list = CHtml::listData(Country::GetCountryList(), 'id', 'title_en');
						 ?><select style="margin: 0;     width: 220px;" onchange="select_city($(this))">
						<option value="">Выберите страну</option>
						<? foreach ($list as $id=>$name) :

						$sel='';
						if ($name == $addrGet['country_name']) { $sel = ' selected'; }

						?>
						
						<option value="<?=$id?>"<?=$sel?>><?=$name?></option>
						
						<? endforeach; ?>
						
						</select>

						<a href="javascript:;" onclick="saveDataAddr(80, $(this))"><i class="fa fa-check" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr states_list" style="margin: 5px 0; display: none">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Штат </div><span class="label_block" style="display: none;"><?=$addrGet['first_name']?></span><select style="margin: 0;     width: 220px;" class="states"><option value="">Выберите штат</option></select>

						<a href="javascript:;" onclick="saveDataAddr(81, $(this))"><i class="fa fa-check" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr" style="margin: 5px 0 0 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Город</div><span class="label_block" style="display: <?=(($addrGet['city']) ?'inline-block' : 'none' )?>;"><?=$addrGet['city']?></span><input type="text" style="margin: 0;<?=(($addrGet['city']) ?'display:none' : '' )?>" value="<?=$addrGet['city']?>" class="city_addr_buyer" /> <a href="javascript:;" onclick="saveDataAddr(4, $(this))"><i class="fa fa-<?=(($addrGet['city']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Индекс </div><span class="label_block" style="display: <?=(($addrGet['postindex']) ?'inline-block' : 'none' )?>;"><?=$addrGet['postindex']?></span><input type="text" style="margin: 0;<?=(($addrGet['postindex']) ?'display:none' : '' )?>"   value="<?=$addrGet['postindex']?>" class="index_addr_buyer" /> <a href="javascript:;" onclick="saveDataAddr(5, $(this))"><i class="fa fa-<?=(($addrGet['postindex']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Адрес</div><span class="label_block" style="display: <?=(($addrGet['streetaddress']) ?'inline-block' : 'none' )?>;"><?=$addrGet['streetaddress']?></span><input type="text" style="margin: 0; <?=(($addrGet['streetaddress']) ?'display:none' : '' )?>" value="<?=$addrGet['streetaddress']?>" class="addres_addr_buyer"/> <a href="javascript:;" onclick="saveDataAddr(6, $(this))"><i class="fa fa-<?=(($addrGet['streetaddress']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt" class="redtext">*</span>Телефон</div><span class="label_block" style="display: <?=(($addrGet['contact_phone']) ?'inline-block' : 'none' )?>;"><?=$addrGet['contact_phone']?></span><input type="text" style="margin: 0; <?=(($addrGet['contact_phone']) ?'display:none' : '' )?>" value="<?=$addrGet['contact_phone']?>" class="addres_addr_buyer"/> <a href="javascript:;" onclick="saveDataAddr(7, $(this))"><i class="fa fa-<?=(($addrGet['contact_phone']) ?'pencil' : 'check' )?>" style="font-size: 18px;"></i></a>
					</div>
					
					</form>
					<? endif; ?>
					
					</div></div>
                    <?php if(!$onlyContent) : ?>
                        <div class="row"><span class="span1"><?=$ui->item("ORDER_MSG_PAYMENT_TYPE"); ?>:</span> <div class="span11">
						
						<?
						if ($order['payment_type_id'] == '0'){
							$order['payment_type_id'] = '00';
						}
						?>
						
						<?=CommonHelper::FormatPaymentType($order['payment_type_id']); ?>
						
						<a href="javascript:;" class="order_start" style="background-color: #5bb75b; padding: 3px 9px" data-ptid="8" onclick="openPaySystems('dtype<?=$_GET['ptype']?>');">Изменить способ оплаты</a>
						
						<div id="pay_systems" class="row spay" style="display: none;">
		<?php $this->renderPartial('/site/pay_systems', array()); ?>
	</div>
						
						</div></div>
                    <?php endif; ?>
                    <div class="row"><span class="span1"><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:</span> <div class="span11"><b><?=ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b></div></div>
					
					
					
					
					
					
                </div>

                <?php if(!empty($order['notes']) AND $order['notes'] != '&nbsp;') : ?>
                    <div class="mbt10">
                        <?=$ui->item('ORDER_MSG_USER_COMMENTS'); ?>: <?=nl2br($order['notes']); ?>
                    </div>
                <?php endif; ?>
                <?php endif; ?>



                <?php if($enableSlide) : ?>
                    <a href="#" onclick="slideContents(<?=$order['id']; ?>); return false;"><b><?=$ui->item("ORDER_MSG_CONTENTS"); ?></b></a>
                <?php else : ?>
                    
                <?php endif; ?>
				<div style="height: 5px;"></div>
                Статус заказа: <br /><b><?php if(!$onlyContent) : ?><?=$ui->item("ORDER_MSG_STATE_".$order['States'][0]['state'])?></b><? endif; ?>
				
				
				
				<? if ($show_btn == '1' && $order['hide_edit_order'] != '1') : ?>
				<div style="height: 20px;"></div>
    <a href="<?= Yii::app()->createUrl('client/me')?>?order_id=<?=$order['id']?>" class="order_start" style="background-color: #5bb75b;  margin-top: -65px;">
                            <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_FINAL_BTN_VIEW_ORDER')?></span>
                        </a>
				
				<? endif; ?>
				
                <table cellspacing="1" cellpadding="5" border="0" width="100%" class="cart1 items_orders" id="cnt<?=$order['id']; ?>" style="display: <?=$enableSlide ? 'none' : 'table'; ?>">
                    <tbody>
                    <tr>
                        <th></th>
                        <th width="70%" class="cart1header1"><?=$ui->item("CART_COL_TITLE"); ?></th>
                        <th width="10%" class="cart1header1 center"><?=$ui->item("CART_COL_QUANTITY"); ?></th>
                        <th width="20%" class="cart1header1 center"><?=$ui->item("CART_COL_SUBTOTAL_PRICE"); ?></th>
                    </tr>

                    <?php foreach($order['Items'] as $item) : ?>
                    <tr>
                        <td class="cart1contents1">
                            <span class="entity_icons"><i class="fa e<?=$item['entity']?>"></i></span>

                        </td>
                        <td class="cart1contents1"><a class="maintxt" href="<?=ProductHelper::CreateUrl($item); ?>"><?=ProductHelper::GetTitle($item); ?></a></td>
                        <td class="cart1contents1 center"><?=$item['quantity']; ?></td>
                        <td class="cart1contents1 center"><?=$item['items_price']; ?> <?=Currency::ToSign($order['currency_id']); ?></td>
                    </tr>

                    <?php endforeach; ?>

                    <tr class="footer">
						
						<td colspan="4">
							<div class="summa">
								
								<a style="float: left;" href="<?=Yii::app()->createUrl('client/printorder', array('oid' => $order['id'])); ?>" class="maintxt printed_btn"
                                     target="_new"><span><?=$ui->item('MSG_ACTION_PRINT_ORDER'); ?></span></a>
								
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
            
			 
			
</div><?if ($co != $i){?><hr style="margin: 30px 0;" /><?}?>
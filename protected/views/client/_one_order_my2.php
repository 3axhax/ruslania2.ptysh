<?php $onlyContent = isset($onlyContent) && $onlyContent; ?>
<?php $enableSlide = isset($enableSlide) && $enableSlide; ?>
<?php $class = empty($class) ? 'class="order_info_zakaz"' : 'class="order_info_zakaz ' . $class . '"'; ?>

	<style>
		table.items_orders tr.footer td div.summa div.itogo {
			line-height: normal;
		}

		table.items_orders tr.footer td div.summa {

			text-align: left;

		}

		table.items_orders a.printed_btn:before {

			left: 13px;
			top: 14px;

		}

		.address_error {
			margin-left: -10px;
			padding: 10px;
			border: 1px solid #ff0000;
			border-radius: 4px;
		}

		.order_info_zakaz .info_order .row {
			margin: 22px 0;
		}

		.order_info_zakaz .info_order .row .span1 {
			margin-left: 0;
			width: 100%;
		}

		.order_info_zakaz .info_order .row .span11 {
			margin-left: 0;
			width: 100%;
		}

		table.history_orders {
			width: 100%;
		}

		.label_block {
			height: 22px;
			padding: 4px 6px;
		}

		.row_addr .fa-pencil {
			float: right;
		}

		.redtext {
			color: #ed1d24;
		}

		#pay_systems .selp {
			width: 85% !important;
			text-align: center;
			margin-left: 0
		}

		.cartorder h1 {
			margin-bottom: 37px;
		}

		a.disabled span {
			color: rgb(67, 67, 67) !important;
		}

		a.disabled {
			pointer-events: all !important;
		}

	</style>

	<script>

		function editAddr(order_id, cont) {

			$('.addr_delivery_form').fadeIn();

			if ($('i', cont).hasClass('fa-check')) {

				//сохраняем адрес
				var error = 0;

				for (var i = 1; i < 8; i++) {

					if (i != 3) {

						if ($('input[name=delivery_inp' + i + ']').val() == '') {

							$('input[name=delivery_inp' + i + ']').addClass('error');
							error = error + 1;
						} else {
							$('input[name=delivery_inp' + i + ']').removeClass('error');
						}

					}

				}


				if (error == 0) {

					$('.addr_delivery_form').fadeOut();
					$('i', cont).attr('class', 'fa fa-pencil');

				}
			} else {
				$('i', cont).attr('class', 'fa fa-check');
			}
		}

		function editAddr2(order_id, cont) {

			$('.addr_buyer_form').fadeIn();

			if ($('i', cont).hasClass('fa-check')) {

				$('.addr_buyer_form').fadeOut();
				$('i', cont).attr('class', 'fa fa-pencil');

			} else {
				$('i', cont).attr('class', 'fa fa-check');
			}
		}


		function saveAddrBlur(id, name, cont, tip) {

			var csrf = $('meta[name=csrf]').attr('content').split('=');

			if (cont.val() == '' && name != 'receiver_middle_name') {

				cont.addClass('error');

			} else {

				$.post('<?=Yii::app()->createUrl('cart/editaddr')?>', {
					id: id,
					name: name,
					val: cont.val(),
					tip: tip,
					orderId:<?= (int) $order['id'] ?>,
					YII_CSRF_TOKEN: csrf[1]
				}, function (data) {

					data = JSON.parse(data);

					$('span.' + tip).html(data.addr_full);

					cont.css('background-color', '#90EE90');
					setTimeout(function () {
						cont.css('background-color', '');
					}, 200);
					cont.removeClass('error');

					if (data.hide_btn_next == '0') {

						$('.hide_block_pay').show();
						$('.error_pay').hide();

						$('.paypalbtn').removeClass('disabled');
						$('.continuebtn').removeClass('disabled');
						$('.continuebtn').attr('onclick', '');
						$('.paypalbtn').attr('onclick', "$('form').submit()");
						$('.error_addr').hide();
						$('.address_error').removeClass('address_error');


					}


				})


			}


		}

<?php /* закомментировал, потому что не нашел где используется, если функция нужна, то ajax-ом передавать orderId
		function saveDataAddr(t, cont) {

			var inp = $('input', cont.parent()).val();
			var inp2 = $('select', cont.parent()).val();
			var csrf = $('meta[name=csrf]').attr('content').split('=');

			if (t == '80' || t == '81') {
				inp = $('select', cont.parent()).val();
			}

			if ($('i.fa', cont.parent()).hasClass('fa-pencil')) {

				$('input', cont.parent()).show();
				$('select', cont.parent()).show();

				$('.label_block', cont.parent()).hide();
				$('i.fa', cont.parent()).attr('class', 'fa fa-check');
				$('i.fa', cont.parent()).css('margin-left', '10px');


			} else {


				if (t == '1' || t == '2' || t == '4' || t == '5' || t == '6' || t == '7' || t == '80' || t == '81') {

					if (inp == '' || inp2 == '') {

						$('input, select', cont.parent()).css('border', '1px solid red');

					} else {

						$('input', cont.parent()).css('border', '');
						$('select', cont.parent()).css('border', '');

						$.post('<?=Yii::app()->createUrl('cart/editaddr')?>', {
							text: inp,
							text2: inp2,
							ty: t,
							id: '<?=$order['billing_address_id']?>',
							YII_CSRF_TOKEN: csrf[1]
						}, function (data) {

							if (inp != '' || inp2 != '') {

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

								if (t == 81) {

									$('.label_block', cont.parent()).html(data.state);

								}

								if (t == 80) {

									$('.label_block', cont.parent()).html(data.country);

								}

							}


						})


					}

				}

				if (t == '3') {


					$.post('<?=Yii::app()->createUrl('cart/editaddr')?>', {
						text: inp,
						ty: t,
						id: '<?=$order['billing_address_id']?>',
						YII_CSRF_TOKEN: csrf[1]
					}, function (data) {

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

							if (t == 81) {

								$('.label_block', cont.parent()).html(data.state);

							}

							if (t == 80) {

								$('.label_block', cont.parent()).html(data.country);

							}

						}


					})

				}


			}
		}
*/?>

		function select_city(cont) {

			var csrf = $('meta[name=csrf]').attr('content').split('=');

			if (cont.val() == 225 || cont.val() == 37 || cont.val() == 15) {

				$.post('<?= Yii::app()->createUrl('cart') ?>loadstates', {
					id: cont.val(),
					YII_CSRF_TOKEN: csrf[1]
				}, function (data) {

					$('.states_list').show();

					$('.states_list select').html(data);


				});

			} else {
				$('.states_list').hide();
				$('.states_list select').html('<select name="states"><option value="">Выберите штат</option></select>');

			}

		}

	</script>

<?
$addrGet = CommonHelper::FormatAddress2($order['DeliveryAddress']);

$hide_btn_next = 0;

if ($addrGet['streetaddress'] == '' OR $addrGet['postindex'] == '' OR $addrGet['city'] == '') {
	$hide_btn_next = 1;
}
?>


	<div <?= $class; ?>>
		<b><?= sprintf($ui->item("ORDER_MSG_NUMBER"), $order['id']); ?></b>

		<?php if ($order['is_reserved'] == 9999) : ?>

			<div class="mbt10">
				<?= $ui->item('IN_SHOP_NOT_READY'); ?>
				<!--                        --><? //=$ui->item("MSG_PERSONAL_PAYMENT_INSHOP_COMMENTS"); ?>
				<br/><?= $ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:
				<b><?= ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b>
			</div>

		<?php else : ?>

		<div class="mbt10 info_order">
			<div class="row">

				<div class="<? if ($hide_btn_next == '1') : ?>address_error<? endif; ?>">

					<span class="span1"><?= $ui->item("ORDER_MSG_DELIVERY_ADDRESS"); ?>:</span>

					<div class="span11">

						<? if ($order['smartpost_address']) : echo $order['smartpost_address'];
						else : ?>

							<? if ($order['delivery_type_id'] == '0') : ?>

								Bulevardi 7, FI-00120 Helsinki, Finland

							<? else : ?>

								<span
									class="deliveryAddress"><?= CommonHelper::FormatAddress($order['DeliveryAddress']); ?></span>

								<? if ($order['hide_edit_order'] != '1') : ?><span style="width: 5pt"
								                                                   class="redtext">*</span> <a
									href="javascript:;" style="margin-left: 20px;" title="Редактировать адрес доставки"
									onclick="editAddr(<?= $order['id']; ?>, $(this));"><i class="fa fa-pencil"></i>
									</a><? endif; ?>

							<? endif; endif; ?></div>

					<div class="clearfix"></div>

					<? if ($hide_btn_next == '1') : ?>
						<span class="redtext error_addr" style="margin-top: 5px; display: block; ">* Заполните адрес полностью</span>

					<? endif; ?>

				</div>

			</div>

			<? if ($order['hide_edit_order'] != '1') :
				$addrGet = CommonHelper::FormatAddress2($order['DeliveryAddress']);

				//var_dump($addrGet);

				?>
				<form class="addr_delivery_form" style=" margin: 22px 0px 0 0; display: none; width: 380px;" autocomplete="off">
					<div class="row_addr">

						<? //=(($addrGet['last_name']) ?'inline-block' : 'none' )
						?>

						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Фамилия
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['last_name'] ?>"
						       class="fam_addr_buyer" name="delivery_inp1"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'receiver_last_name', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Имя
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['first_name'] ?>"
						       class="name_addr_buyer" name="delivery_inp2"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'receiver_first_name', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr">
						<div style="display: inline-block; width: 130px;">Отчество</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['middle_name'] ?>"
						       class="middle_addr_buyer" name="delivery_inp3"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'receiver_middle_name', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<? //=$addrGet['country_name']
						?>
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Страна
						</div><?
						$list = CHtml::listData(Country::GetCountryList(), 'id', 'title_en');
						?><select style="margin: 0;     width: 220px;" onchange="select_city($(this))" disabled>
							<option value="">Выберите страну</option>
							<? foreach ($list as $id => $name) :

								$sel = '';
								if ($name == $addrGet['country_name']) {
									$sel = ' selected';
								}

								?>

								<option value="<?= $id ?>"<?= $sel ?>><?= $name ?></option>

							<? endforeach; ?>

						</select>
					</div>

					<div class="row_addr" style="margin: 5px 0 0 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Город
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['city'] ?>" class="city_addr_buyer"
						       name="delivery_inp4"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'city', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Индекс
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['postindex'] ?>"
						       class="index_addr_buyer" name="delivery_inp5"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'postindex', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Адрес
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['streetaddress'] ?>"
						       class="addres_addr_buyer" name="delivery_inp6"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'streetaddress', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Телефон
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['contact_phone'] ?>"
						       class="addres_addr_buyer" name="delivery_inp7"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'contact_phone', $(this), 'deliveryAddress')"/>
					</div>

				</form>
			<? endif; ?>


			<div class="row"><span class="span1"><?= $ui->item("ORDER_MSG_DELIVERY_TYPE"); ?>:</span>

				<div class="span11">

					<? if ($order['smartpost_address']) : echo 'SmartPost';
					else : ?>

						<?= CommonHelper::FormatDeliveryType($order['delivery_type_id']); endif; ?>


				</div>
			</div>
			<div class="row"><span class="span1"><?= $ui->item("ORDER_MSG_BILLING_ADDRESS"); ?>:</span>

				<div class="span11"><span
						class="order_addr_buyer"><?= CommonHelper::FormatAddress($order['BillingAddress']); ?></span> <? if ($order['hide_edit_order'] != '1') : ?>
						<a href="javascript:;" style="margin-left: 20px;" title="Редактировать адрес плательщика"
						   onclick="editAddr2(<?= $order['id']; ?>, $(this));"><i class="fa fa-pencil"></i>
						</a><? endif; ?>

					<? if ($order['hide_edit_order'] != '1') :
						$addrGet = CommonHelper::FormatAddress2($order['BillingAddress']);

						//var_dump($addrGet);

						?>
						<form class="addr_buyer_form" style=" margin: 22px 0px 0 0; display: none; width: 380px;">
							<div class="row_addr">

								<? //=(($addrGet['last_name']) ?'inline-block' : 'none' )
								?>

								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Фамилия
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['last_name'] ?>"
								       class="fam_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'receiver_last_name', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Имя
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['first_name'] ?>"
								       class="name_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'receiver_first_name', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr">
								<div style="display: inline-block; width: 130px;">Отчество</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['middle_name'] ?>"
								       class="middle_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'receiver_middle_name', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr" style="margin: 5px 0">
								<? //=$addrGet['country_name']
								?>
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Страна
								</div><?
								$list = CHtml::listData(Country::GetCountryList(), 'id', 'title_en');
								?><select style="margin: 0; width: 220px;"
								          onchange="select_city($(this));    saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'country', $(this), 'order_addr_buyer')">
									<option value="">Выберите страну</option>
									<? foreach ($list as $id => $name) :

										$sel = '';
										if ($name == $addrGet['country_name']) {
											$sel = ' selected';
										}

										?>

										<option value="<?= $id ?>"<?= $sel ?>><?= $name ?></option>

									<? endforeach; ?>

								</select>
							</div>

							<div class="row_addr states_list" style="margin: 5px 0; display: none">
								<div style="display: inline-block; width: 130px;">Штат</div>
								<select style="margin: 0;     width: 220px;" class="states"
								        onchange="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'state_id', $(this), 'order_addr_buyer')">
									<option value="">Выберите штат</option>
								</select>
							</div>

							<div class="row_addr" style="margin: 5px 0 0 0">
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Город
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['city'] ?>"
								       class="city_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'city', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Индекс
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['postindex'] ?>"
								       class="index_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'postindex', $(this), 'order_addr_buyer')"/>
							</div>


							<div class="row_addr">
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Адрес
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['streetaddress'] ?>"
								       class="addres_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'streetaddress', $(this), 'order_addr_buyer')"/>
							</div>
							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>E-mail
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['contact_email'] ?>"
								       class="addres_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'contact_email', $(this), 'order_addr_buyer')"/>
							</div>
							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 130px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Телефон
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['contact_phone'] ?>"
								       class="addres_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'contact_phone', $(this), 'order_addr_buyer')"/>
							</div>

						</form>
					<? endif; ?>

				</div>
			</div>
			<?php if (!$onlyContent) : ?>
				<div class="row"><span class="span1"><?= $ui->item("ORDER_MSG_PAYMENT_TYPE"); ?>:</span>

					<div class="span11">

						<?
						if ($order['payment_type_id'] == '0') {
							$order['payment_type_id'] = '00';
						}
						?>

						<?= CommonHelper::FormatPaymentType($order['payment_type_id']); ?>
						<? if ($order['hide_edit_payment'] != '1') : ?><a href="javascript:;" style="margin-left: 20px;"
						                                                  title="Изменить способ оплаты"
						                                                  onclick="openPaySystems('dtype<?= $_GET['ptype'] ?>');">
								<i class="fa fa-pencil"></i></a><? endif; ?>


						<div id="pay_systems" class="row spay" style="display: none;">
							<?php $this->renderPartial('/site/pay_systems', array()); ?>
						</div>

					</div>
				</div>
			<?php endif; ?>
			<div class="row"><span class="span1"><?= $ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:</span>

				<div class="span11">
					<b><?= ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?></b></div>
			</div>


			<?php if (!empty($order['notes']) AND $order['notes'] != '&nbsp;') : ?>
				<div class="mbt10 row">
					<span class="span1"><?= $ui->item('ORDER_MSG_USER_COMMENTS'); ?>
						: <?= nl2br($order['notes']); ?></span>
				</div>
			<?php endif; ?>
			<?php endif; ?>


		</div>


		<?php if ($enableSlide) : ?>
			<a href="#"
			   onclick="slideContents(<?= $order['id']; ?>); return false;"><b><?= $ui->item("ORDER_MSG_CONTENTS"); ?></b></a>
		<?php else : ?>

		<?php endif; ?>
		<div style="height: 5px;"></div>
		Статус заказа:
		<br/><b><?php if (!$onlyContent) : ?><?= $ui->item("ORDER_MSG_STATE_" . $order['States'][0]['state']) ?></b><? endif; ?>



		<? if ($show_btn == '1' && $order['hide_edit_order'] != '1') : ?>
			<div>
				<a href="<?= Yii::app()->createUrl('cart/orderPay') ?>?<?= Yii::app()->getRequest()->getQueryString() ?>&hide_edit=1"
				   class="order_start <?= (($hide_btn_next == '1') ? 'disabled ' : '') ?>continuebtn"
				   style="background-color: #5bb75b;  padding: 12px; margin: 22px 0 0 0"
				   onclick="<?= (($hide_btn_next) ? "$('.error_text_btn').removeClass('hide'); setTimeout(function() { $('.error_text_btn').addClass('hide'); }, 3000); return false" : '') ?>">
					<span
						style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?= $ui->item('CARTNEW_FINAL_BTN_VIEW_ORDER') ?></span>
				</a>

				<span class="error_text_btn paypalbtn_error redtext hide" style="margin-left: 20px; font-size: 14px;">Не все данные заполнены</span>


			</div>
		<? elseif ($order['hide_edit_order'] == '1') : ?>

			<div style="margin-top: 22px"><b>Данные подтверждены</b></div>

		<? endif; ?>

		<table cellspacing="1" cellpadding="5" border="0" width="100%" class="cart1 items_orders"
		       id="cnt<?= $order['id']; ?>" style="display: <?= $enableSlide ? 'none' : 'table'; ?>; margin-top: 22px;">
			<tbody>
			<tr>
				<th></th>
				<th width="70%" class="cart1header1"><?= $ui->item("CART_COL_TITLE"); ?></th>
				<th width="10%" class="cart1header1 center"><?= $ui->item("CART_COL_QUANTITY"); ?></th>
				<th width="20%" class="cart1header1 center"><?= $ui->item("CART_COL_SUBTOTAL_PRICE"); ?></th>
			</tr>

			<?php foreach ($order['Items'] as $item) : ?>
				<tr>
					<td class="cart1contents1">
						<span class="entity_icons"><i class="fa e<?= $item['entity'] ?>"></i></span>

					</td>
					<td class="cart1contents1"><a class="maintxt"
					                              href="<?= ProductHelper::CreateUrl($item); ?>"><?= ProductHelper::GetTitle($item); ?></a>
					</td>
					<td class="cart1contents1 center"><?= $item['quantity']; ?></td>
					<td class="cart1contents1 center"><?= $item['items_price']; ?> <?= Currency::ToSign($order['currency_id']); ?></td>
				</tr>

			<?php endforeach; ?>

			<tr class="footer">

				<td colspan="4">
					<div class="summa">

						<a style="float: left;     height: 43px;line-height: 43px; color: rgb(67, 67, 67)"
						   href="<?= Yii::app()->createUrl('client/printorder', array('oid' => $order['id'])); ?>"
						   class="maintxt"
						   target="_new"><i class="fa fa-print"></i> <?= $ui->item('MSG_ACTION_PRINT_ORDER'); ?></a>

						<div class="itogo" style="    margin-top: -3px;">
							Полная стоимость<br/> заказа с учетом доставки:
							<b><?= ProductHelper::FormatPrice($order['full_price'], true, $order['currency_id']); ?>
								<?php if ($order['currency_id'] != Currency::EUR) : ?>
									(<?php $eur = Currency::ConvertToEUR($order['full_price'], $order['currency_id']);
									echo ProductHelper::FormatPrice($eur, true, Currency::EUR); ?>)
								<?php endif; ?></b>
						</div>
						<div class="clearfix"></div>

					</div>
				</td>

				<td align="right" class="cart1contents1" colspan="2"></td>
				<td class="cart1contents1 center" colspan="1">
				</td>
			</tr>
			</tbody>
		</table>


	</div><? if ($co != $i) { ?>
	<hr style="margin: 30px 0;"/><? } ?>
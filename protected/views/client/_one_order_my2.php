<?php $onlyContent = isset($onlyContent) && $onlyContent; ?>
<?php $enableSlide = isset($enableSlide) && $enableSlide; ?>
<?php $class = empty($class) ? 'class="order_info_zakaz"' : 'class="order_info_zakaz ' . $class . '"'; ?>

	<style>
	
		.checkbox_custom {
			display: none;
		}

		.checkbox-custom {
			position: relative;
			width: 8px;
			height: 8px;
			padding: 3px;
			border: 1px solid #ccc;
			border-radius: 3px;
		}

		.checkbox-custom {
			display: inline-block;
			vertical-align: middle;
			margin-top: -3px;
		}

		.checkbox_custom:checked + .checkbox-custom::before {
			content: "";
			display: block;
			position: absolute;

			top: 3px;
			right: 3px;
			bottom: 3px;
			left: 3px;
			background: #413548;
			border-radius: 2px;

			background: #ed1d24;
			width: 8px;
			height: 8px;
			display: inline-block;
			font-size: 0;

		}
	
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
	
	function saveAddrSelect(id, t, tip) {
		
		var csrf = $('meta[name=csrf]').attr('content').split('=');
		
		$.post('<?=Yii::app()->createUrl('cart/editaddrselect')?>', {
					id: id,
					val: t.val(),
					tip: tip,
					YII_CSRF_TOKEN: csrf[1]
				}, function (data) {

					data = JSON.parse(data);
					
					if (tip == 1) {
						
						$('span.deliveryAddress').html(data.addr_full);
						
					} else {
						
						$('span.order_addr_buyer').html(data.addr_full);
						
					}
					
					if (tip != "2") {
										
					if (data.hide_btn_next == '0') {

						$('.hide_block_pay').show();
						$('.error_pay').hide();

						$('.paypalbtn').removeClass('disabled');
						$('.continuebtn').removeClass('disabled');
						$('.continuebtn').attr('onclick', "");
						$('.paypalbtn').attr('onclick', "$('form').submit()");
						$('.error_addr').hide();
						$('.address_error_box').removeClass('address_error');
						$('.error_pay_pt').addClass('hide');
						$('.hide_block_pay').show();
						

					} else {
						
						$('.hide_block_pay').hide();
						$('.error_pay').show();

						$('.paypalbtn').addClass('disabled');
						$('.continuebtn').addClass('disabled');
						$('.continuebtn').attr('onclick', "$('.error_text_btn').removeClass('hide'); setTimeout(function() { $('.error_text_btn').addClass('hide'); }, 3000); return false");
						$('.paypalbtn').attr('onclick', "$('.error_text_btn').removeClass('hide'); setTimeout(function() { $('.error_text_btn').addClass('hide'); }, 3000); return false;");
						$('.error_addr').show();
						$('.address_error_box').addClass('address_error');
						$('.error_pay_pt').removeClass('hide');
						$('.hide_block_pay').hide();
					}
					
					}
					
										
					t.css('background-color', '#90EE90');
					setTimeout(function () {
						t.css('background-color', '');
					}, 200);
					t.removeClass('error');
				})
		
	}
	
	function add_address(num_cont) {
        
        var cont = $('table.addr'+num_cont);
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        
        var query = '';
        
        query = 'YII_CSRF_TOKEN='+csrf[1]+'&'+$('table.addr'+num_cont+' input, table.addr'+num_cont+' select, table.addr'+num_cont+' textarea').serialize();
        //query = query + '&s1='.$('select[name=id_address]').val() + '&s2='.$('select[name=id_address_b]').val();
        
        var error = 0;

		$('.texterror', $('#Address'+num_cont+'_receiver_last_name', cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        
        if (!$('#Address'+num_cont+'_receiver_last_name',cont).val()) { $('#Address'+num_cont+'_receiver_last_name').addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_receiver_last_name',cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>'); } else {  $('#Address'+num_cont+'_receiver_last_name',cont).removeClass('error'); $('.texterror', $('#Address'+num_cont+'_receiver_last_name',cont).parent()).html(''); }
        
        if (error < 0) { error = 0; }
        
        
        if (!$('#Address'+num_cont+'_receiver_first_name',cont).val()) { $('#Address'+num_cont+'_receiver_first_name',cont).addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_receiver_first_name',cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');} else {  $('#Address'+num_cont+'_receiver_first_name',cont).removeClass('error');  $('.texterror', $('#Address'+num_cont+'_receiver_first_name',cont).parent()).html('');}
        
        if (!$('#Address'+num_cont+'_country',cont).val() && !$('.check_addressa').prop('checked')) { $('#Address'+num_cont+'_country',cont).addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_country',cont).parent()).html('<?=$ui->item('CARTNEW_SELECT_COUNTRY_ERROR')?>');} else {  $('#Address'+num_cont+'_country',cont).removeClass('error');  $('.texterror', $('#Address'+num_cont+'_country',cont).parent()).html(''); }
        
        if (!$('#Address'+num_cont+'_city',cont).val() && !$('.check_addressa').prop('checked')) { $('#Address'+num_cont+'_city',cont).addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_city',cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');} else {  $('#Address'+num_cont+'_city',cont).removeClass('error');  $('.texterror', $('#Address'+num_cont+'_city',cont).parent()).html('');}
        if (!$('#Address'+num_cont+'_postindex',cont).val() && !$('.check_addressa').prop('checked')) { $('#Address'+num_cont+'_postindex',cont).addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_postindex',cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');} else {  $('#Address'+num_cont+'_postindex',cont).removeClass('error');  $('.texterror', $('#Address'+num_cont+'_postindex',cont).parent()).html('');}
        if (!$('#Address'+num_cont+'_streetaddress',cont).val() && !$('.check_addressa').prop('checked')) { $('#Address'+num_cont+'_streetaddress',cont).addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_streetad192dress',cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');} else {  $('#Address'+num_cont+'_streetaddress',cont).removeClass('error');  $('.texterror', $('#Address'+num_cont+'_streetaddress',cont).parent()).html('');}
         var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
        
        if (!$('#Address'+num_cont+'_contact_email').val()) { $('#Address'+num_cont+'_contact_email').addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_contact_email').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');} else if(pattern.test($('#Address'+num_cont+'_contact_email').val())){  $('#Address'+num_cont+'_contact_email').removeClass('error');  $('.texterror', $('#Address'+num_cont+'_contact_email').parent()).html('');} else {
         
         $('#Address'+num_cont+'_contact_email').addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_contact_email').parent()).html('Неверно введен E-mail адрес');
            
        }
        if (!$('#Address'+num_cont+'_contact_phone',cont).val() && !$('.check_addressa').prop('checked')) { $('#Address'+num_cont+'_contact_phone',cont).addClass('error'); error = error + 1; $('.texterror', $('#Address'+num_cont+'_contact_phone',cont).parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');} else {  $('#Address'+num_cont+'_contact_phone',cont).removeClass('error');  $('.texterror', $('#Address'+num_cont+'_contact_phone',cont).parent()).html('');}
        
        
        if (error > 0) {
         
            $('input.error').slice(0,1).focus();
         
        }
        
        
        if (error == 0) {
        
        var s1 = $('select[name=id_address]').val();
        var s2 = $('select[name=id_address_b]').val();

        $.post('<?= Yii::app()->createUrl('cart/addaddress') ?>', query, function(data) {

            //alert(data);

            data = JSON.parse(data);
			
			if (num_cont == '1') {
			
            $('select[name=id_address]').html(data.items);
            $('select[name=id_address_b]').html(data.items);

            $('select[name=id_address]').val(data.ida);
            $('select[name=id_address_b]').val(s2);
            
            $('.div_box_tbl'+num_cont).hide('fade');
            } else {
				$('select[name=id_address]').html(data.items);
				$('select[name=id_address_b]').html(data.items);

				$('select[name=id_address]').val(s1);
				$('select[name=id_address_b]').val(data.ida);
				
				$('#Address'+num_cont+'_business_title, #Address'+num_cont+'_business_number1, #Address'+num_cont+'_receiver_last_name, #Address'+num_cont+'_receiver_first_name, #Address'+num_cont+'_receiver_middle_name, #Address'+num_cont+'_country, #Address_city, #Address'+num_cont+'_postindex, #Address'+num_cont+'_streetaddress, #Address'+num_cont+'_contact_phone, #Address'+num_cont+'_notes').val('');
			
			$('.states_list'+num_cont).hide();
				
				
            $('.div_box_tbl'+num_cont).hide('fade');
			}
        });
        
        }
        
        
    }
	
	function change_city2(cont, tbl) {

        var csrf = $('meta[name=csrf]').attr('content').split('=');

        if (cont.val() != '') {
			
			$.post('<?= Yii::app()->createUrl('cart') ?>getcodecity', {id_country: cont.val(), YII_CSRF_TOKEN: csrf[1]}, function (data) {
                if (data != '') {
                    $('#Address'+tbl + '_contact_phone').val('+' + data);
                } else {

                    $('#Address'+tbl + '_contact_phone').val('');

                }

               

            });
			
			if (cont.val() == 225 || cont.val() == 37 || cont.val() == 15) {

                $.post('<?= Yii::app()->createUrl('cart/loadstates') ?>', {id: cont.val(), YII_CSRF_TOKEN: csrf[1], tbl : 'tbl'}, function (data) {
					
					$('.addr'+tbl + ' .states_list'+tbl).show();
					
                    $('.addr'+tbl + ' .select_states'+tbl).html(data);

                  

                });

            } else {
				$('.addr'+tbl + ' .states_list'+tbl).hide();
                $('.addr'+tbl + ' .select_states'+tbl).html('<select name="Address'+tbl+'[state_id]" style="220px;"><option value="">---</option></select>');

            }

		
		
		}



    }
	
	
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
					
					<? if ($order['DeliveryAddress']['id'] == $order['BillingAddress']['id']) : ?>
					
					$('span.order_addr_buyer').html(data.addr_full);
					
					<? endif; ?>
					
					cont.css('background-color', '#90EE90');
					setTimeout(function () {
						cont.css('background-color', '');
					}, 200);
					cont.removeClass('error');

					if (data.hide_btn_next == '0' && tip !='order_addr_buyer') {

						$('.hide_block_pay').show();
						$('.error_pay').hide();

						$('.paypalbtn').removeClass('disabled');
						$('.continuebtn').removeClass('disabled');
						$('.continuebtn').attr('onclick', '');
						$('.paypalbtn').attr('onclick', "$('form').submit()");
						$('.error_addr').hide();
						$('.address_error').removeClass('address_error');
						$('.error_pay_pt').hide();

					}


				})


			}


		}

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

if (!$_GET['ptype']) {
	
	$_GET['ptype'] = $order['payment_type_id'];
	
}

$addrGet = CommonHelper::FormatAddress2($order['DeliveryAddress']);

$addr = new Address();
$addr_list = $addr->GetAddresses($this->uid);

$hide_btn_next = 0;

if ($addrGet['streetaddress'] == '' OR $addrGet['postindex'] == '' OR $addrGet['city'] == '') {
	$hide_btn_next = 1;
}

$cnt_orders = Order::GetCountOrders($this->uid);



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

				<div class="<? if ($hide_btn_next == '1' AND $order['delivery_type_id'] != '0') : ?>address_error<? endif; ?> address_error_box">

					<span class="span1"><?= $ui->item("ORDER_MSG_DELIVERY_ADDRESS"); ?>:</span>

					<div class="span11">

						<? if ($order['smartpost_address']) :
							if ($smartpostAddress = @unserialize($order['smartpost_address'])) {
								echo ''.
									$smartpostAddress['labelName']['fi'] . ': ' . $smartpostAddress['locationName']['fi'] . "<br>".
									$smartpostAddress['address']['fi']['address'] . ' ' . $smartpostAddress['address']['fi']['postalCode'] . ' ' . $smartpostAddress['address']['fi']['postalCodeName'] .
								'';
							}
							else echo $order['smartpost_address'];
						else : ?>

							<? if ($order['delivery_type_id'] == '0') : ?>

								Bulevardi 7, FI-00120 Helsinki, Finland

							<? else : ?>

								<span
									class="deliveryAddress"><?= CommonHelper::FormatAddress($order['DeliveryAddress']); ?></span>

								<? if ($order['hide_edit_order'] != '1' AND $order['delivery_type_id'] != '0') : ?> <a
									href="javascript:;" style="margin-left: 20px;" title="Редактировать адрес доставки"
									onclick="editAddr(<?= $order['id']; ?>, $(this));"><i class="fa fa-pencil"></i>
									</a><? endif; ?>

							<? endif; endif; ?></div>

					<div class="clearfix"></div>

					<? $class = ' none'; if ($hide_btn_next == '1' AND $order['delivery_type_id'] != '0') : ?>
						<? $class = ' block'; ?>
					<? endif; ?>
					<span class="redtext error_addr" style="margin-top: 5px; display: <?=$class?>; ">* Заполните адрес полностью</span>

					

				</div>

			</div>

			<? if ($order['hide_edit_order'] != '1') :
				$addrGet = CommonHelper::FormatAddress2($order['DeliveryAddress']);

				//var_dump($addrGet);
				
				?>
				<form class="addr_delivery_form" style=" margin: 22px 0px 0 0; display: none; <?=((count($addr_list) > 1) ? 'width: 580px;' : 'width: 410px;')?>" autocomplete="off">
					
					<?
					if ($cnt_orders > 1) {
    
					echo '<select name="id_address" style="margin-bottom: 0;margin-right: 8px; width: 60%" onchange="saveAddrSelect(\''.$order['id'].'\', $(this), \'1\')">'.((count($addr_list) > 1) ? '<option value="">'.$ui->item('CARTNEW_ERROR_SELECT_ADDR_DELIVERY').'</option>' : '' );

					$ch = new CommonHelper();

					foreach ($addr_list as $addr) {
						$select='';
						$adr_str = $ch->FormatAddress($addr);
						
						if ($order['DeliveryAddress']['id'] == $addr['address_id']) {
									$select = ' selected="selected" ';
								}
						
						echo '<option value="'.$addr['address_id'].'"'.$select.'>'.$adr_str.'</option>';

					}

					echo '</select><a href="javascript:;" onclick="$(\'select, input\').removeClass(\'error\'); $(\'span.texterror\').html(\'\'); $(\'.div_box_tbl1\').toggle();" class="order_start" style="margin-top: 0; padding: 6px 0; background-color: #28618E; width: 40px;">+</a>';
					
					?>
					
					<div class="div_box_tbl1" style="width: 444px; display: none">
					
					 <table class="address addr1" style=" margin-top: 10px; width: 450px ">
    <tbody>
        
    <tr>
        <td style="width: 200px;"><b>Получатель:</b></td>
        <td class="maintxt">
            <label style="float: left; margin-right: 20px;"><input value="1" onclick="$('.l1_1').show()" class="checkbox_custom" name="Address[type]" id="Address1_type" type="radio"><span class="checkbox-custom"></span>
            Организация</label>
            <label style="float: left; "><input value="2" onclick="$('.l1_1').hide()" class="checkbox_custom"  name="Address[type]" id="Address1_type" type="radio" checked="checked"><span class="checkbox-custom"></span>
            Частное лицо</label></td>
    </tr>
	<tr class="l1_1" style="display: none;">
        <td nowrap="" class="maintxt">Название организации        </td>
        <td class="maintxt-vat">
            <input name="Address[business_title]" id="Address1_business_title" type="text" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr class="l1_1" style="display: none;">
        <td nowrap="" class="maintxt">Номер VAT</td>
        <td class="maintxt-vat">
            <input name="Address[business_number1]" id="Address1_business_number1" type="text" value="" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span>Фамилия</td>
        <td class="maintxt-vat">
            <input name="Address[receiver_last_name]" id="Address1_receiver_last_name" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span>Имя</td>
        <td class="maintxt-vat">
            <input name="Address[receiver_first_name]" id="Address1_receiver_first_name" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt">Отчество</td>
        <td class="maintxt-vat">
            <input oninput="save_form()" name="Address[receiver_middle_name]" id="Address1_receiver_middle_name" type="text" value="" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt country_lbl">
            <span style="width: 5pt" class="redtext">*</span>Cтрана        </td>
        <td class="maintxt-vat">
            <select onchange="change_city2($(this), '1');" name="Address[country]" id="Address1_country" class="" style="width: 220px;"><option value="">---</option>
			
			<?
			
			$list = CHtml::listData(Country::GetCountryList(), 'id', 'title_en');
			
			
			foreach ($list as $k=>$v) {
			?>
			
			<option value="<?=$k?>"><?=$v?></option>
			
			<?
			}			
			?>
			
			
			</select>            <span class="texterror"></span>
        </td>
    </tr>
    
    <tr class="states_list1" style="display: none">
        <td nowrap="" class="maintxt">Штат</td>
        <td class="maintxt-vat select_states1"><select name="Address[state_id]" onclick="" style="width: 220px;"><option value="">---</option></select></td>
        
    </tr>
    
    
    <tr>
        <td nowrap="" class="maintxt city_lbl"><span style="width: 5pt" class="redtext">*</span>Город        </td>
        <td colspan="2" class="maintxt-vat">
            <input name="Address[city]" id="Address1_city" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt postindex_lbl"><span style="width: 5pt" class="redtext">*</span>Почтовый индекс</td>
        <td colspan="2" class="maintxt-vat">
            <input name="Address[postindex]" id="Address1_postindex" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt streetaddress_lbl"><span style="width: 5pt" class="redtext">*</span>Адрес</td>
        <td class="maintxt-vat">
            <input placeholder="Улица, дом, квартира, и т.д., в любом порядке" name="Address[streetaddress]" id="Address1_streetaddress" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span>Контактный e-mail        </td>
        <td class="maintxt-vat" colspan="2" style="position: relative;">
            <input name="Address[contact_email]" id="Address1_contact_email" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt contact_phone_lbl"><span style="width: 5pt" class="redtext">*</span>Контактный телефон</td>
        <td class="maintxt-vat">
            <input name="Address[contact_phone]" id="Address1_contact_phone" type="text" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1">
            
                    </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt">Примечания к адресу</td>
        <td class="maintxt-vat">
            <textarea name="Address[notes]" id="Address1_notes" class=""></textarea>        </td>
        <td class="smalltxt1"></td>
    </tr></tbody>
</table>	
		
		
					<a href="javascript:;" class="btn btn-success addr1" style="float: right; margin-right: 5px;" onclick="add_address(1)"><?=$ui->item('CARTNEW_BTN_ADD_ADDRESS')?></a>
<a href="javascript:;" onclick="$('.div_box_tbl1').hide();" class="cancel_add_adr btn btn-link" style="float: right;"><?=$ui->item('CARTNEW_BTN_CANCEL_ADDRESS')?></a>
		
		
					</div>
					
					<?
					
					
				} else {
					?>
					
					<div class="row_addr" style="margin-bottom: 5px;">

						<div style="display: inline-block; width: 160px; float: left;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Получатель
						</div>
						<div style="float: Left; width: 245px;">
							<label style="margin-right: 20px; display: inline; font-size: 13px"><input value="1" onclick="$('.l1_1').show(); saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'type', $(this), 'deliveryAddress')" class="checkbox_custom" name="Address[type]" id="Address1_type" type="radio" <?=(($addrGet['type'] == '1') ? 'checked="checked"' : '')?>><span class="checkbox-custom"></span>
							Организация</label>
							<label style=" display: inline; font-size: 13px"><input value="2" onclick="$('.l1_1').hide(); $('.l1_1 input').removeClass('error'); saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'type', $(this), 'deliveryAddress')" class="checkbox_custom"  name="Address[type]" id="Address1_type" type="radio" <?=(($addrGet['type'] == '2') ? 'checked="checked"' : '')?>><span class="checkbox-custom"></span>
							Частное лицо</label>
						</div>
						
						<div class="clearfix"></div>
						
					</div>
					
					<div class="row_addr l1_1" style=" display:  <?=(($addrGet['type'] == '1') ? ' block' : ' none')?>">
						
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Название организации	
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['business_title'] ?>"
						       class="nameorg_addr_buyer" name="delivery_inp12"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'business_title', $(this), 'deliveryAddress')"/>
					</div><div class="row_addr l1_1" style="margin: 5px 0; display: <?=(($addrGet['type'] == '1') ? ' block' : ' none')?>">

						<div style="display: inline-block; width: 160px"><span style="width: 5pt"
						                                                        class="redtext">*</span>Номер VAT	
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['business_number1'] ?>"
						       class="vatorg_addr_buyer" name="delivery_inp13"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'business_number1', $(this), 'deliveryAddress')"/>
					</div>
					
					
					<div class="row_addr">

						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Фамилия
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['last_name'] ?>"
						       class="fam_addr_buyer" name="delivery_inp1"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'receiver_last_name', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Имя
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['first_name'] ?>"
						       class="name_addr_buyer" name="delivery_inp2"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'receiver_first_name', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr">
						<div style="display: inline-block; width: 160px;">Отчество</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['middle_name'] ?>"
						       class="middle_addr_buyer" name="delivery_inp3"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'receiver_middle_name', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<? //=$addrGet['country_name']
						?>
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Страна
						</div> <?
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
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Город
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['city'] ?>" class="city_addr_buyer"
						       name="delivery_inp4"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'city', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Индекс
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['postindex'] ?>"
						       class="index_addr_buyer" name="delivery_inp5"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'postindex', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr">
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Адрес
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['streetaddress'] ?>"
						       class="addres_addr_buyer" name="delivery_inp6"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'streetaddress', $(this), 'deliveryAddress')"/>
					</div>

					<div class="row_addr" style="margin: 5px 0">
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Телефон
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['contact_phone'] ?>"
						       class="addres_addr_buyer" name="delivery_inp7"
						       onblur="saveAddrBlur(<?= $order['DeliveryAddress']['id'] ?>, 'contact_phone', $(this), 'deliveryAddress')"/>
					</div>
					<?  } ?>
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
						class="order_addr_buyer"><?= CommonHelper::FormatAddress($order['BillingAddress']); ?></span> <? if ($order['hide_edit_order'] != '1' AND $order['DeliveryAddress']['id'] != $order['BillingAddress']['id'] AND $cnt_orders == 1 ) : ?>
						<a href="javascript:;" style="margin-left: 20px;" title="Редактировать адрес плательщика"
						   onclick="editAddr2(<?= $order['id']; ?>, $(this));"><i class="fa fa-pencil"></i>
						</a>
						
						<? elseif ( $cnt_orders > 1 AND $order['hide_edit_order'] != '1') : ?>
						
						<a href="javascript:;" style="margin-left: 20px;" title="Редактировать адрес плательщика"
						   onclick="editAddr2(<?= $order['id']; ?>, $(this));"><i class="fa fa-pencil"></i>
						</a>
						
						<? endif; ?>

					<? if ($order['hide_edit_order'] != '1') :
						$addrGet = CommonHelper::FormatAddress2($order['BillingAddress']);

						//var_dump($addrGet);
						
						
						
						?>
						<form class="addr_buyer_form" style=" margin: 22px 0px 0 0; display: none; <?=((count($addr_list) > 1) ? 'width: 580px;' : 'width: 410px;')?>">
							
							<?
							
							if ($cnt_orders > 1) {
    
							echo '<div style="margin-toP: 22px;"><select name="id_address_b" style="margin-bottom: 0;margin-right: 8px; width: 60%" onchange="saveAddrSelect(\''.$order['id'].'\', $(this), \'2\')">'.((count($addr_list) > 1) ? '<option value="">'.$ui->item('CARTNEW_ERROR_SELECT_ADDR_BUYER').'</option>' : '' );

							$ch = new CommonHelper();

							foreach ($addr_list as $addr) {
								
								$select = '';
								
								$adr_str = $ch->FormatAddress($addr);
								
								if ($order['BillingAddress']['id'] == $addr['address_id']) {
									$select = ' selected="selected" ';
								}
								
								echo '<option value="'.$addr['address_id'].'"'.$select.'>'.$adr_str.'</option>';

							}

							echo '</select><a href="javascript:;" onclick="$(\'select, input\').removeClass(\'error\'); $(\'span.texterror\').html(\'\'); $(\'.div_box_tbl2\').toggle();" class="order_start" style="margin-top: 0; padding: 6px 0; background-color: #28618E; width: 40px;">+</a></div>';
							
							?>
							
							<div class="div_box_tbl2" style="width: 444px; display: none">
					
					 <table class="address addr2" style=" margin-top: 10px; width: 450px ">
    <tbody>
        
    <tr>
        <td style="width: 200px;"><b>Получатель:</b></td>
        <td class="maintxt">
            <label style="float: left; margin-right: 20px;"><input value="1" onclick="$('.l1_1').show()" class="checkbox_custom" name="Address[type]" id="Address2_type" type="radio"><span class="checkbox-custom"></span>
            Организация</label>
            <label style="float: left; "><input value="2" onclick="$('.l1_1').hide()" class="checkbox_custom"  name="Address[type]" id="Address2_type" type="radio" checked="checked"><span class="checkbox-custom"></span>
            Частное лицо</label></td>
    </tr>
	<tr class="l1_1" style="display: none;">
        <td nowrap="" class="maintxt">Название организации        </td>
        <td class="maintxt-vat">
            <input name="Address[business_title]" id="Address2_business_title" type="text" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr class="l1_1" style="display: none;">
        <td nowrap="" class="maintxt">Номер VAT</td>
        <td class="maintxt-vat">
            <input name="Address[business_number1]" id="Address2_business_number1" type="text" value="" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span>Фамилия</td>
        <td class="maintxt-vat">
            <input name="Address[receiver_last_name]" id="Address2_receiver_last_name" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt"><span style="width: 5pt" class="redtext">*</span>Имя</td>
        <td class="maintxt-vat">
            <input name="Address[receiver_first_name]" id="Address2_receiver_first_name" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td class="maintxt">Отчество</td>
        <td class="maintxt-vat">
            <input oninput="save_form()" name="Address[receiver_middle_name]" id="Address2_receiver_middle_name" type="text" value="" class="">        </td>
        <td class="smalltxt1"></td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt country_lbl">
            <span style="width: 5pt" class="redtext">*</span>Cтрана        </td>
        <td class="maintxt-vat">
            <select onchange="change_city2($(this), '2');" name="Address[country]" id="Address2_country" class="" style="width: 220px;"><option value="">---</option>
			
			<?
			
			$list = CHtml::listData(Country::GetCountryList(), 'id', 'title_en');
			
			
			foreach ($list as $k=>$v) {
			?>
			
			<option value="<?=$k?>"><?=$v?></option>
			
			<?
			}			
			?>
			
			
			</select>            <span class="texterror"></span>
        </td>
    </tr>
    
    <tr class="states_list2" style="display: none">
        <td nowrap="" class="maintxt">Штат</td>
        <td class="maintxt-vat select_states2"><select name="Address[state_id]" onclick="" style="width: 220px;"><option value="">---</option></select></td>
        
    </tr>
    
    
    <tr>
        <td nowrap="" class="maintxt city_lbl"><span style="width: 5pt" class="redtext">*</span>Город        </td>
        <td colspan="2" class="maintxt-vat">
            <input name="Address[city]" id="Address2_city" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt postindex_lbl"><span style="width: 5pt" class="redtext">*</span>Почтовый индекс</td>
        <td colspan="2" class="maintxt-vat">
            <input name="Address[postindex]" id="Address2_postindex" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt streetaddress_lbl"><span style="width: 5pt" class="redtext">*</span>Адрес</td>
        <td class="maintxt-vat">
            <input placeholder="Улица, дом, квартира, и т.д., в любом порядке" name="Address[streetaddress]" id="Address2_streetaddress" type="text" value="" class="">            <span class="texterror"></span>
        </td>
        
    </tr>
    <tr>
        <td nowrap="" class="maintxt"><span style="width: 5pt" class="redtext">*</span>Контактный e-mail        </td>
        <td class="maintxt-vat" colspan="2" style="position: relative;">
            <input name="Address[contact_email]" id="Address2_contact_email" type="text" value="" class="">            <span class="texterror"></span>
        </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt contact_phone_lbl"><span style="width: 5pt" class="redtext">*</span>Контактный телефон</td>
        <td class="maintxt-vat">
            <input name="Address[contact_phone]" id="Address2_contact_phone" type="text" class="">            <span class="texterror"></span>
        </td>
        <td class="smalltxt1">
            
                    </td>
    </tr>
    <tr>
        <td nowrap="" class="maintxt">Примечания к адресу</td>
        <td class="maintxt-vat">
            <textarea name="Address[notes]" id="Address2_notes" class=""></textarea>        </td>
        <td class="smalltxt1"></td>
    </tr></tbody>
</table>	
		
		
					<a href="javascript:;" class="btn btn-success addr1" style="float: right; margin-right: 5px;" onclick="add_address(2)"><?=$ui->item('CARTNEW_BTN_ADD_ADDRESS')?></a>
<a href="javascript:;" onclick="$('.div_box_tbl2').hide();" class="cancel_add_adr btn btn-link" style="float: right;"><?=$ui->item('CARTNEW_BTN_CANCEL_ADDRESS')?></a>
		
		
					</div>
					
							
							<?
							
							
						} else {
							
							?>
							
							<div class="row_addr" style="margin-bottom: 5px;">

						<div style="display: inline-block; width: 160px; float: left;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Получатель
						</div>
						<div style="float: Left; width: 245px;">
							<label style="margin-right: 20px; display: inline; font-size: 13px"><input value="1" onclick="$('.l1_2').show(); saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'type', $(this), 'order_addr_buyer')" class="checkbox_custom" name="Address[type]" id="Address1_type" type="radio" <?=(($addrGet['type'] == '1') ? 'checked="checked"' : '')?>><span class="checkbox-custom"></span>
							Организация</label>
							<label style=" display: inline; font-size: 13px"><input value="2" onclick="$('.l1_2').hide(); $('.l1_1 input').removeClass('error'); saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'type', $(this), 'order_addr_buyer')" class="checkbox_custom"  name="Address[type]" id="Address1_type" type="radio" <?=(($addrGet['type'] == '2') ? 'checked="checked"' : '')?>><span class="checkbox-custom"></span>
							Частное лицо</label>
						</div>
						
						<div class="clearfix"></div>
						
					</div>
					
					<div class="row_addr l1_2" style=" display:  <?=(($addrGet['type'] == '1') ? ' block' : ' none')?>">
						
						<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
						                                                        class="redtext">*</span>Название организации	
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['business_title'] ?>"
						       class="nameorg_addr_buyer" name="delivery_inp12"
						       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'business_title', $(this), 'order_addr_buyer')"/>
					</div><div class="row_addr l1_2" style="margin: 5px 0; display: <?=(($addrGet['type'] == '1') ? ' block' : ' none')?>">

						<div style="display: inline-block; width: 160px"><span style="width: 5pt"
						                                                        class="redtext">*</span>Номер VAT	
						</div>
						<input type="text" style="margin: 0;" value="<?= $addrGet['business_number1'] ?>"
						       class="vatorg_addr_buyer" name="delivery_inp13"
						       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'business_number1', $(this), 'order_addr_buyer')"/>
					</div>
							
							<div class="row_addr">

								<? //=(($addrGet['last_name']) ?'inline-block' : 'none' )
								?>

								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Фамилия
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['last_name'] ?>"
								       class="fam_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'receiver_last_name', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Имя
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['first_name'] ?>"
								       class="name_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'receiver_first_name', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr">
								<div style="display: inline-block; width: 160px;">Отчество</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['middle_name'] ?>"
								       class="middle_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'receiver_middle_name', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr" style="margin: 5px 0">
								<? //=$addrGet['country_name']
								?>
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Страна
								</div> <?
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
								<div style="display: inline-block; width: 160px;">Штат</div>
								<select style="margin: 0;     width: 220px;" class="states"
								        onchange="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'state_id', $(this), 'order_addr_buyer')">
									<option value="">Выберите штат</option>
								</select>
							</div>

							<div class="row_addr" style="margin: 5px 0 0 0">
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Город
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['city'] ?>"
								       class="city_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'city', $(this), 'order_addr_buyer')"/>
							</div>

							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Индекс
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['postindex'] ?>"
								       class="index_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'postindex', $(this), 'order_addr_buyer')"/>
							</div>


							<div class="row_addr">
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Адрес
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['streetaddress'] ?>"
								       class="addres_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'streetaddress', $(this), 'order_addr_buyer')"/>
							</div>
							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>E-mail
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['contact_email'] ?>"
								       class="addres_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'contact_email', $(this), 'order_addr_buyer')"/>
							</div>
							<div class="row_addr" style="margin: 5px 0">
								<div style="display: inline-block; width: 160px;"><span style="width: 5pt"
								                                                        class="redtext">*</span>Телефон
								</div>
								<input type="text" style="margin: 0;" value="<?= $addrGet['contact_phone'] ?>"
								       class="addres_addr_buyer"
								       onblur="saveAddrBlur(<?= $order['BillingAddress']['id'] ?>, 'contact_phone', $(this), 'order_addr_buyer')"/>
							</div>
							<?  } ?>
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
				   class="order_start <?= (($hide_btn_next == '1'AND $order['delivery_type_id'] != '0') ? 'disabled ' : '') ?>continuebtn"
				   style="background-color: #5bb75b;  padding: 12px; margin: 22px 0 0 0"
				   onclick="<?= (($hide_btn_next) ? "$('.error_text_btn').removeClass('hide'); setTimeout(function() { $('.error_text_btn').addClass('hide'); }, 3000); return false" : '') ?>">
					<span
						style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?= $ui->item('CARTNEW_FINAL_BTN_VIEW_ORDER') ?></span>
				</a>

				<span class="error_text_btn paypalbtn_error redtext hide" style="margin-left: 20px; font-size: 14px;">Не все данные заполнены</span>


			</div>
		<? elseif ($order['hide_edit_order'] == '1') : ?>

			<div style="margin-top: 0"><b>Данные подтверждены</b></div>

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
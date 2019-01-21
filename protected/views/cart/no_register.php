<hr />

<div class="lang_yesno_box alerthtml" style="display: none;margin-left: -192px; width: 300px; padding: 34px 42px;">

	<div style="text-align: center;" class="title"><?=$ui->item('ARE_YOU_SURE'); ?></div>
	<div class="box_btns">
		<a href="javascript:;" onclick="$('.alerthtml').hide();" class="btn_yes">Ок</a>
	</div>

</div>

<style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        border-radius: 2px;
        position: relative;
        padding-right: 55px;
		width: 188px;
        height: 80px !important;
    }

    label.seld .red_checkbox {
        position: absolute;
        right: 5px;
        top: 20px;

    }

    label.selt {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        border-radius: 2px;
        position: relative;
        width: 212px;
    }

    label.selt .red_checkbox {
        position: absolute;
        right: 5px;
        top: 20px;

    }
	
	
	
	
    label.selp {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        height: 70px;
        position: relative;
        padding-right: 55px;
        width: 188px;
        border-radius: 2px;
    }

    label.selp .red_checkbox {
        position: absolute;
        right: 5px;
        top: 20px;
    }

    .cartorder .p2, .cartorder .p1, .cartorder .p3 { font-size: 22px; }

    .cartorder .p1 { margin:0 0 15px 0;}
    .cartorder .p2 { margin: 15px 0;}
    .cartorder .p3 { margin: 15px 0;}


    .select_dd { position: relative; }
    .select_dd_popup { position: absolute; top: 29px; background: #fff; max-height: 400px; width: 340px; border: 1px solid #ccc; z-index: 9999; display: none; overflow-y: auto; }

    .select_dd_popup .item {
        padding: 5px 10px;
    }

    .select_dd_popup .item:hover {
        background-color: #ccc;
        cursor: pointer;
    }

    .cart_header {

        background: #f8f8f8;
        padding: 8px 10px;
        font-weight: bold;
        border-left: 1px solid #ececec;
        border-top: 1px solid #ececec;
        border-right: 1px solid #ececec;


    }

    table.cart {
        border: 1px solid #ececec;
    }

    .cart tbody tr td {

        padding: 10px;
        border-bottom: 1px solid #ececec;

    }

    .cart tbody tr td .minitext { color: #81807C; }
    .cart tbody tr td span.a {  }

    .cart_box {
        overflow: auto;
        max-height: 724px;
    }

    .cart_footer { float: right; }

    .footer3 { margin-bottom: 25px; border-bottom: 1px solid #ececec; }

    .footer1, .footer3, .footer_promocode {
        background: #f8f8f8;
        padding: 8px 10px;
        font-weight: bold;
        border-left: 1px solid #ececec;
        border-top: 1px solid #ececec;
        border-right: 1px solid #ececec;
    }

    .footer2 {

        padding: 8px 10px;
        color: #81807C;
        border-left: 1px solid #ececec;
        border-right: 1px solid #ececec;

    }

    a.order_start { background-color: #5bb75b; }

    .order_start.disabled {

        opacity: 0.5;
        cursor: default;

    }

    label.selp img {
        -webkit-filter: grayscale(100%);
        -moz-filter: grayscale(100%);
        -ms-filter: grayscale(100%);
        -o-filter: grayscale(100%);
        filter: grayscale(100%);
        filter: gray; /* IE 6-9 */
    }

    label.selp.act img {
        -webkit-filter: grayscale(0%);
        -moz-filter: grayscale(0%);
        -ms-filter: grayscale(0%);
        -o-filter: grayscale(0%);
        filter: grayscale(0%);
        filter: none; /* IE 6-9 */
    }


    input.error {
        border: 1px solid #ed1d24;
        margin-bottom: 0;
    }

    .texterror { color: #ed1d24; font-size: 11px; display: block; }

    td.maintxt-vat { padding: 5px 5px; }

    .redtext {

        color: #ed1d24;

    }

    .box_smartpost {
        display: none;
        width: 760px;
        margin-top: 20px;
    }

    .row_smartpost {
        padding: 15px 5px;
        border: 1px solid #ccc;
        margin: 5px 0;
    }

    .row_smartpost.act {
        background-color: #eaeaea;
    }






    /* Скрываем реальный чекбокс */
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

</style>
<div class="opacity alerthtml" onclick="$('.alerthtml').hide();"></div>	
<script src="/js/jquery.cookie.js"></script>

<script>
    var promocodeHandler = null;
	function alert_html(text) {
		
		$('.alerthtml .title').html(text);
		
		$('.alerthtml').show();
		
	}
	
    function check_desc_address(cont) {

        if (cont.prop('checked')) {

            $('.country_lbl span, .city_lbl span, .postindex_lbl span, .streetaddress_lbl span').hide();
			
			$('.seld02,.seld03,.seld04').addClass('disabled');
			
			$('.seld1').click();
			
			checked_sogl();
			
        } else {

            $('.country_lbl span, .city_lbl span, .postindex_lbl span, .streetaddress_lbl span').show();
			
			$('.seld02,.seld03,.seld04').removeClass('disabled');
			
			$('.seld02').click();
			
			checked_sogl();
        }
		
		

    }

    function checkForm() {



    }


    function select_smartpost_row(cont) {

        $('.row_smartpost').hide();
        $('.close_points').hide();
        $('.more_points').show();
        $('.row_smartpost').removeClass('act');
        $(cont).parent().addClass('act');
        $(cont).parent().show();

        $('.btn.btn-success', $(cont).parent()).html('Выбран');

        $('.sel_smartpost').val($('div.addr_name', $(cont).parent()).html());

    }


    function checkEmail(t) {
        var value = t.value;
        if (value != '') {
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            $.ajax({
                url: '<?= Yii::app()->createUrl('site/checkEmail') ?>',
                data: 'ha&email=' + encodeURIComponent(t.value) + '&' + csrf[0] + '=' + csrf[1],
                type: 'post',
                success: function (r) {
                    $('#js_forgot').remove();
                    if (r) {
                        $(t).after(r);
                        // $('.order_start').addClass('disabled');
                    }
                }
            });
        }
    }

    function forgotPassword(email) {
        document.getElementById('js_forgot').innerHTML = '<div style="font-weight: bold; z-index: 9999999;background: #fff;"><?=$ui->item('CARTNEW_SEND_PSW_EMAIL_OK')?>: ' + email + '</div><div style="font-weight: bold;"><?=$ui->item('CARTNEW_ALERT_SAVE_PRODUCTS')?></div>';
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        $.ajax({
            url: '<?= Yii::app()->createUrl('site/forgot') ?>',
            type: 'post',
            data: 'User[login]=' + email + '&' + csrf[0] + '=' + csrf[1],
            success: function () {

                window.setTimeout('document.location.href = "<?= Yii::app()->createUrl('cart/variants') ?>";', 1000);


            }
        });
    }

    function dontClick() {
        document.getElementById('js_forgot').innerHTML = '<div style="font-weight: bold;"><?=$ui->item('CARTNEW_CHANGE_EMAIL_OTHER')?></div>';

        window.setTimeout('$(document).ready(function() { $("#js_forgot").remove() })', 1200);

    }


    function cost_izmena(city_id) {

        var csrf = $('meta[name=csrf]').attr('content').split('=');

        $.post('<?= Yii::app()->createUrl('cart') ?>getcostizmena', {id_country: city_id, YII_CSRF_TOKEN: csrf[1]}, function (data) {

            var al = JSON.parse(data);

            //alert(al.fullpricehidden);

            $('.cart_header').html(al.cart_header);
            $('table.cart').html(al.cart);
            $('.footer2').html(al.footer2);
            $('.footer3').html(al.footer3);
            $('input.costall').val(al.fullpricehidden);

        });

    }

    var zamena1 = false;
    var zamena2 = true;

    function change_city(cont) {

        var csrf = $('meta[name=csrf]').attr('content').split('=');

        $('.check', $('.seld:visible')).removeClass('active');
        $('input[type=radio]', $('.seld #dtype2').parent()).attr('checked', 'true');
        $('.seld').css('border', '1px solid rgb(204, 204, 204)');
        $('.seld').removeClass('act');

        showALL();
        hide_oplata(1);
        $('.delivery_box_sp').hide();
        $('.rows_checkbox_delivery input').prop('checked', false);
        $('.delivery_box').show();

        sbros_delev();

        $('.seld #dtype2').parent().css('border', '1px solid rgb(100, 113, 127)');
        $('.check', $('.seld #dtype2').parent()).addClass('active');
        $('.seld #dtype2').parent().addClass('act');

        //$('.selp #dtype2').parent().click();

        $('input[type=radio]', $('.seld #dtype2').parent()).attr('checked', 'true');

$('.oplata3').click();

        //show_all();


        if (cont.val() != '') {
			
			$('.seld02, .seld03, .seld04').removeClass('disabled');
			
            if (cont.val() == 225 || cont.val() == 37 || cont.val() == 15) {

                $.post('<?= Yii::app()->createUrl('cart') ?>loadstates', {id: cont.val(), YII_CSRF_TOKEN: csrf[1]}, function (data) {

                    $('.select_states').html(data);

                    if ($.cookie('Address_country')) {

                        $('.select_states select').val($.cookie('Address_state_id'));

                    }

                });

            } else {

                $('.select_states').html('<select name="Address[state_id]" disabled><option value="">---</option></select>');

            }


            cost_izmena(cont.val());

            if (cont.val()==68 || cont.val()==62 || cont.val()=='') {

                $('.seld1').removeClass('act_city');

            } else {
                $('.seld1').addClass('act_city');
            }

            if (cont.val() == 68 || cont.val()==62) {
                // var b1 = $('.dtypes .seld1');
                // var b2 = $('.dtypes .seld2');
                //$('.dtypes').html('');
                // if (zamena1) {



                    // b2.replaceWith(b1.clone());
                    // b1.replaceWith(b2);

                    // zamena1 = false;
                    // zamena2 = true;

                // }



                $('.zabr_market').html('<?=$ui->item('CARTNEW_PICK_UP_STORE1')?>');

            } else {

                // var b1 = $('.dtypes .seld1');
                // var b2 = $('.dtypes .seld2');

                // if (zamena2) {

                    // b1.replaceWith(b2.clone());
                    // b2.replaceWith(b1);

                    // zamena1 = true;
                    // zamena2 = false;

                // }

                // $('.seld1').addClass('act_city');



                $('.zabr_market').html('<?=$ui->item('CARTNEW_PICK_UP_STORE2')?>');

            }


            $.post('<?= Yii::app()->createUrl('cart') ?>getdeliveryinfo2', {id_country: cont.val(), YII_CSRF_TOKEN: csrf[1]}, function (data) {
				
				
				
                data = JSON.parse(data);
					
					$('.row_del1').html(data['text1']);
					$('.row_del2').html(data['text2']);
					$('.row_del3').html(data['text3']);
					
					$('.delivery_box').html(data['smartpost']);
					
					
                //$('.box_opacity .op').hide();
                // $('.order_start').removeClass('disabled');
                sbros_delev();
                checked_sogl();
				
				$('.seld02').click();
				
            });

            //if ($('#Address_contact_phone').val() == '') {

            $.post('<?= Yii::app()->createUrl('cart') ?>getcodecity', {id_country: cont.val(), YII_CSRF_TOKEN: csrf[1]}, function (data) {
                if (data != '') {
                    $('#Address_contact_phone').val('+' + data);
                } else {

                    $('#Address_contact_phone').val('');

                }

                $('.delivery_name').html('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>');

            });

            // }


            $('.delivery_name').html('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>');

        } else {
            checked_sogl();
            //$('.order_start').addClass('disabled');
			$('.seld02, .seld03, .seld04').addClass('disabled');
			$('.seld1').click();

        }
		
		
		



    }

    function select_row(cont) {

        $('.select_dd_box input').val(cont.html());
        $('.select_dd_popup').html('');
        $('.select_dd_popup').hide();
    }


    var s = false;

    function clear_cook() {

        $.cookie('Address_business_title', '');
        $.cookie('Address_business_number1', '');
        $.cookie('Address_type', '');
        $.cookie('Address_receiver_title_name', '');
        $.cookie('Address_receiver_last_name', '');
        $.cookie('Address_receiver_first_name', '');
        $.cookie('Address_country', '');
        $.cookie('Address_state_id', '');
        $.cookie('Address_city', '');
        $.cookie('Address_postindex', '');
        $.cookie('Address_streetaddress', '');
        $.cookie('Address_contact_email', '');
        $.cookie('Address_contact_phone', '');
        $.cookie('Address_notes', '');

        // $.cookie('Address_notes');

    }

    function save_form() {

        if (s) {

            $.cookie('Address_business_title', $('#Address_business_title').val());
            $.cookie('Address_business_number1', $('#Address_business_number1').val());
            $.cookie('Address_type', $('#Address_type:checked').val());
            $.cookie('Address_receiver_title_name', $('#Address_receiver_title_name').val());
            $.cookie('Address_receiver_last_name', $('#Address_receiver_last_name').val());
            $.cookie('Address_receiver_first_name', $('#Address_receiver_first_name').val());
            $.cookie('Address_receiver_middle_name', $('#Address_receiver_middle_name').val());
            $.cookie('Address_country', $('#Address_country').val());
            $.cookie('Address_state_id', $('.select_states select').val());
            $.cookie('Address_city', $('#Address_city').val());
            $.cookie('Address_postindex', $('#Address_postindex').val());
            $.cookie('Address_streetaddress', $('#Address_streetaddress').val());
            $.cookie('Address_contact_email', $('#Address_contact_email').val());
            $.cookie('Address_contact_phone', $('#Address_contact_phone').val());
            $.cookie('Address_notes', $('#Address_notes').val());

            // $.cookie('Address_notes');

            $('title').html($.cookie('Address_state_id'));
        }
    }

    function load_form() {



        $('#Address_receiver_title_name').val($.cookie('Address_receiver_title_name'));
        $('#Address_receiver_last_name').val($.cookie('Address_receiver_last_name'));
        $('#Address_receiver_first_name').val($.cookie('Address_receiver_first_name'));
        $('#Address_receiver_middle_name').val($.cookie('Address_receiver_middle_name'));



        $('#Address_country').val($.cookie('Address_country'));
        $('#Address_country').change();
        $('.select_states select').val($.cookie('Address_state_id'));
        $('#Address_city').val($.cookie('Address_city'));
        $('#Address_postindex').val($.cookie('Address_postindex'));
        $('#Address_streetaddress').val($.cookie('Address_streetaddress'));
        $('#Address_contact_email').val($.cookie('Address_contact_email'));
        $('#Address_contact_phone').val($.cookie('Address_contact_phone'));
        $('#Address_notes').val($.cookie('Address_notes'));

        if ($.cookie('Address_type')) {
            $('#Address_type[value='+$.cookie('Address_type')+']').click();
            $('#Address_type[value='+$.cookie('Address_type')+']').change();
            $('#Address_business_title').val($.cookie('Address_business_title'));
            $('#Address_business_number1').val($.cookie('Address_business_number1'));

        }

        // alert($.cookie('Address_state_id'));

        s = true;

    }

    $(document).ready(function () {

        load_form();

        $(document).click(function (event) {
            if ($(event.target).closest(".qbtn, .info_box_smart").length)
                return;
            $('.info_box_smart').hide();
        event.stopPropagation();
        });

        $(document).click(function (event) {
            if ($(event.target).closest(".qbtn2,.info_box").length)
                return;
            $('.info_box').hide();
            event.stopPropagation();
        });


        $('.check', $('.selp')).removeClass('active');
        $('.selp').css('border', '1px solid #ccc');



        // $('input[type=radio]', $('.cartorder .row label.seld #dtype2').slice(0, 1)).attr('checked', 'true');
        // $('.check', $('.cartorder .row label.seld').slice(1, 2)).addClass('active');

        $('.selp #ptype2').parent().css('border', '1px solid #64717f');
        $('input[type=radio]', $('.selp #ptype2').parent()).attr('checked', 'true');
        $('.check', $('.selp #ptype2').parent()).addClass('active');
        $('.delivery_name').html('<?=$ui->item('CARTNEW_DELIVERY_POST_NAME')?>');
    })

    function checked_sogl() {


        //alert($('#confirm').prop('checked'));
		
		if ( !$('.check_addressa').prop('checked') ) {
			
			if (!$('#Address_country').val() || !$('#confirm').prop('checked')) {
			
			
				$('.box_opacity .op').show();
			
						
			} else {
			
				$('.box_opacity .op').hide();
			
			}
			
			
		} else {
			
			if (!$('#confirm').prop('checked')) {
			
			
				$('.box_opacity .op').show();
			
						
			} else {
			
				$('.box_opacity .op').hide();
			
			}
			
			
		}
		
		
		
		
		
        // if ((!$('#Address_country').val() || !$('#confirm').prop('checked'))  && !$('.check_addressa').prop('checked')) {

            //$('.order_start').addClass('disabled');
            // $('.box_opacity .op').show();
        // } else if ($('#Address_country').val() && $('#confirm').prop('checked') ) {

            // $('.box_opacity .op').hide();
            //$('.order_start').removeClass('disabled');
        // } else if ( $('.check_addressa').prop('checked') && $('#confirm').prop('checked') ) {
            // $('.box_opacity .op').hide();
		// } else if ((!$('#Address_country').val() || !$('#confirm').prop('checked'))  && !$('.check_addressa').prop('checked')) {
			// $('.box_opacity .op').show();
			
			
		// }


    }

    function check_cart_sel(cont, cont2, inputId) {
        $('label.seld').removeClass('act');
        $('.' + cont2 + ' .check').removeClass('active');
        $('.' + cont2 + ' input[type=radio]').removeAttr('checked');
        $('.' + cont2 + '').css('border', '1px solid #ccc');
        $('label.selp').removeClass('act');

        if ($('.check', cont).hasClass('active')) {
            $('.check', cont).removeClass('active');
            $(cont).removeClass('act');
            $(cont).css('border', '1px solid #ccc');
            $('#' + inputId, cont).removeAttr('checked');
        } else {
            $('.check', cont).addClass('active');
            $(cont).addClass('act');
            $(cont).css('border', '1px solid #64717f');
            $('#' + inputId, cont).attr('checked', 'true');
        }


        //$('.seld input#dtype3').parent().hide();
        //$('.oplata4, .oplata7').hide();

       /*  if ($('#Address_country').val() == 68) {

            $('.oplata4, .oplata7').show();

        } else if ($('#Address_country').val() == 62) {

            $('.oplata7').hide();
            $('.oplata4').show();

        } else if ($('#Address_country').val() == 62 && $('#Address_country').val() == 68) {

            $('.oplata4, .oplata7').hide();

        }

        if ($('#Address_country').val()==68 || $('#Address_country').val()==62) {

            $('.seld1').removeClass('act_city');

        } else {
            $('.seld1').addClass('act_city');
        } */




        $('.selp span.check.active').parent().parent().parent().addClass('act');



    }

    function search_smartpost() {

        var csrf = $('meta[name=csrf]').attr('content').split('=');

        $('.start-search-smartpost').html('Поиск...');

        var country = 'FI';

        if ($('#Address_country').val() == 62) {

            country = 'EE';

        }

        $('.box_smartpost').html('');
        $('.sel_smartpost').html('');
        $('.box_smartpost').hide();

        $.post('<?= Yii::app()->createUrl('cart') ?>loadsp2/', {ind: $('.smartpost_index').val(), YII_CSRF_TOKEN: csrf[1], country: country}, function (data) {

            if (data) {

                $('.box_smartpost').show();

                $('.box_smartpost').html(data);
            } else {
                $('.box_smartpost').html('');
                $('.box_smartpost').hide();
            }

            $('.start-search-smartpost').html('Найти');
        });

    }


    function  show_all() {

        $('.oplata1,.oplata2,.oplata3,.oplata4,.oplata5,.oplata6,.oplata7,.oplata8').show();

        $('.check', $('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');

		$('.check', $('.selp #ptype2').parent()).addClass('active');
        $('input[type=radio]', $('.selp #ptype2').parent()).attr('checked', 'true');        $('.selp #ptype2').parent().css('border', '1px solid #64717f');
        $('.selp #ptype2').parent().addClass('act');
    }

    function showALL() {
        $('.spay .selp').show();
    }

    function hide_oplata(oplata) {



        $('.oplata' + oplata).hide();

        $('.check', $('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');

        //$('.selp:visible').slice(0,1).hide();
        $('.selp #ptype2').parent().addClass('act');
        $('.check', $('.selp #ptype2').parent()).addClass('active');
        $('input[type=radio]', $('.selp #ptype2').parent()).attr('checked', 'true');
        $('.selp #ptype2').parent().css('border', '1px solid #64717f');

    }

    function sendforma() {

        var frm1 = $('form.no_register1').serialize();
        var frm2 = $('form.address.text').serialize();

        var frmall = frm1 + '&' + frm2;
        if (promocodeHandler) frmall += '&promocode=' + promocodeHandler.getValue();

        var error = 0;

        $('.texterror', $('#Address_receiver_last_name').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');

        if (!$('#Address_receiver_last_name').val()) {
            $('#Address_receiver_last_name').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_receiver_last_name').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        } else {
            $('#Address_receiver_last_name').removeClass('error');
            $('.texterror', $('#Address_receiver_last_name').parent()).html('');
        }

        if (error < 0) {
            error = 0;
        }

		
		

        if (!$('#Address_receiver_first_name').val()) {
            $('#Address_receiver_first_name').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_receiver_first_name').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        } else {
            $('#Address_receiver_first_name').removeClass('error');
            $('.texterror', $('#Address_receiver_first_name').parent()).html('');
        }

        if (!$('#Address_country').val()  && !$('.check_addressa').prop('checked')) {
            $('#Address_country').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_country').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        } else {
            $('#Address_country').removeClass('error');
            $('.texterror', $('#Address_country').parent()).html('');
        }

        if (!$('#Address_city').val() && !$('.check_addressa').prop('checked')) {
            $('#Address_city').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_city').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        } else {
            $('#Address_city').removeClass('error');
            $('.texterror', $('#Address_city').parent()).html('');
        }
        if (!$('#Address_postindex').val() && !$('.check_addressa').prop('checked')) {
                $('#Address_postindex').addClass('error');
                error = error + 1;
            } else {
                $('#Address_postindex').removeClass('error');
                $('.texterror', $('#Address_postindex').parent()).html('');
            }
            if ( !$('#Address_streetaddress').val() && !$('.check_addressa').prop('checked') ) {
                $('#Address_streetaddress').addClass('error');
                $('.texterror', $('#Address_streetaddress').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
                error = error + 1;
                $('.texterror', $('#Address_streetaddress').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
            } else {
                $('#Address_streetaddress').removeClass('error');
                $('.texterror', $('#Address_streetaddress').parent()).html('');
			}
			
			
			

        var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

        if (!$('#Address_contact_email').val()) {
            $('#Address_contact_email').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_contact_email').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        } else if (pattern.test($('#Address_contact_email').val())) {
            $('#Address_contact_email').removeClass('error');
            $('.texterror', $('#Address_contact_email').parent()).html('');
        } else {

            $('#Address_contact_email').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_contact_email').parent()).html('<?=$ui->item('CARTNEW_ERROR_WRONG_EMAIL')?>');

        }



        if ( (!$('#Address_contact_phone').val()) || ($('#Address_contact_phone').val().length < 6) ) {
            $('#Address_contact_phone').addClass('error');
            error = error + 1;
            $('.texterror', $('#Address_contact_phone').parent()).html('<?=$ui->item('CARTNEW_INPUT_ERROR')?>');
        } else {
            $('#Address_contact_phone').removeClass('error');
            $('.texterror', $('#Address_contact_phone').parent()).html('');
        }


        if (error > 0) {

            $('input.error').slice(0, 1).focus();

        }

        if ($('#confirm').is(':checked') == false) {

            $('.err_confirm').html('<?=$ui->item('CARTNEW_ERROR_AGREE_CONDITION')?>');

            $('label[for=confirm]').css('padding', '5px');
            $('label[for=confirm]').css('border', '1px solid rgb(237, 29, 36)');
            $('label[for=confirm]').css('border-radius', '6px');

            if (error == 0) {

                $('html, body').scrollTop($('#confirm').offset().top);

            }

            error = error + 1;

        } else {

            $('.err_confirm').html('');

            $('label[for=confirm]').css('padding', '');
            $('label[for=confirm]').css('border', '');
            $('label[for=confirm]').css('border-radius', '');

        }


        if ($('.seld input').is(':checked') == false) {

                $('.dtypes .texterror').css('display', 'inline-block');

                $('.dtypes .texterror').html('<?=$ui->item('CARTNEW_SELECT_TYPE_DELIVERY_ERROR')?>');
                error = error + 1;
            } else {
                $('.dtypes .texterror').html('');
                $('.dtypes .texterror').css('display', 'none');
            }





        if (error == 0) {

            //alert(frmall);

            $.post('<?= Yii::app()->createUrl('cart') ?>result/', frmall, function (data) {

                if (data != '') {

                    if (data == '9') {
                        alert_html('<?=$ui->item('CARTNEW_ERROR_MAIL_FIND_OK')?>');
                    } else {

                        clear_cook();

                        location.href = data;
                    }
                }


            });

        }

    }

    function check_delivery(cont) {

        var costall = $('input.costall').val();

        $('span.delivery_cost').html(cont.attr('rel') + '' + cont.attr('valute'));

        $('.itogo_cost').html((parseFloat($('input.costall').val()) + parseFloat(cont.attr('rel'))).toFixed(2) + '' + cont.attr('valute'));

        if (promocodeHandler&&promocodeHandler.active) promocodeHandler.recount(promocodeHandler.getValue());

    }

    function sbros_delev() {

        var costall = parseFloat($('input.costall').val()).toFixed(2);

        $('.itogo_cost').html(costall + ' ' + $('.valute').val());

        $('.delivery_cost').html('0 ' + $('.valute').val());


        $('.selt .check').removeClass('active');
        $('.selt input').removeAttr('checked');
        $('.select_dd_box').hide();
        $('.select_dd_box input').val('');
        $('.box_smartpost').html('');


        if (promocodeHandler&&promocodeHandler.active) promocodeHandler.recount(promocodeHandler.getValue());


    }


</script>

<div class="container cartorder" style="margin-bottom: 20px;">



    <?php
    $delivery = new PostCalculator();

    $r = $delivery->GetRates2(10, $this->uid, $this->sid);

    //var_dump($r);

    $cart = new Cart();

    $tmp = $cart->BeautifyCart($cart->GetCart($this->uid, $this->sid), $this->uid);

    $PH = new ProductHelper();

    $cart = CartController::actionGetAll(0);
		
	$cart = $cart['CartItems'];



    //echo '<pre>';
    //var_dump($tmp);
    //echo '</pre>';
    $cartInfo = '';
    $fullprice = 0;
    $fullweight = 0;
    $price = 0;
    $full_count = 0;
    $cartInfo['items'] = array();
    $t1 = false;
    $t2 = false;
    

    foreach ($cart as $item) {
			
			$withVat = $item['UseVAT'];
			
            $cartInfo['items'][$item['ID']]['title'] = 
			$item['Title'];
            $cartInfo['items'][$item['ID']]['weight'] = $item['UnitWeight'];
            if ($item['Entity'] == 30) {
                if ($item['type'] == '1') { //фины
                    $price = $item['PriceVATFin'];
                } else {
                    $price = $item['PriceVATWorld'];
                }
            } else {
                $price = $item['PriceVAT'];
            }
            if (!$withVat) {
                
				if ($item['Entity'] == 30) {
				
				if ($item['type'] == '1') { //фины
                    $price = $item['PriceVAT0Fin'];
                } else {
                    $price = $item['PriceVAT0World'];
                }
				} else {
					$price = $item['PriceVAT0'];
				}
				
            }
            $fullweight += $item['UnitWeight'];
            
			$cartInfo['items'][$item['ID']]['month_count'] = $item['Quantity'];
			
            if ($item['Entity'] == 30) {
                $fullprice += $price * $item['Quantity'];
				$cartInfo['items'][$item['ID']]['price'] = $price;
                $cartInfo['items'][$item['ID']]['quantity'] = 1;
				$item['Quantity'] = 1;
            } else {
                $fullprice += $price * $item['Quantity'];
                $cartInfo['items'][$item['ID']]['quantity'] = $item['Quantity'];
				
				$cartInfo['items'][$item['ID']]['price'] = $price;
            }
            $cartInfo['items'][$item['ID']]['entity'] = $item['Entity'];
            $full_count += $item['Quantity'];
        }

    $cartInfo['fullInfo']['count'] = $full_count;
    $cartInfo['fullInfo']['cost'] = $fullprice;
    $cartInfo['fullInfo']['weight'] = $fullweight;


    

    $user = Yii::app()->user->GetModel();
    $address = new Address;
    $address->receiver_title_name = $user['title_name'];
    $address->receiver_last_name = $user['last_name'];
    $address->receiver_first_name = $user['first_name'];
    $address->receiver_middle_name = $user['middle_name'];
    $address->contact_email = $user['login'];
    $address->type = 2;
    $this->renderPartial('/site/address_form2', array('model' => $address,
        'mode' => 'new',
        'afterAjax' => 'addrInserted', 'cart' => $cartInfo));
   

	if ($fullprice < 5){
		$fullprice = 5.0;
	}	
	
	echo '<input type="hidden" value="' . $cartInfo['fullInfo']['cost'] . '" name="costall" class="costall">';


   ?>

    <?php if ($t1 AND $t2) : ?>

        <script>

            $(document).ready(function () {

                $('.add_cost').show();

            })

        </script>

    <?php endif; ?>
	<div class="row" style="margin-left: 0;">
   
   <div class="span6" style="width: 49%; margin-left: 0; margin-right: 1%;">
   
	<textarea id="Notes" style="width: 100%; margin-bottom: 0; height: 245px; box-sizing: border-box;" placeholder="<?=str_replace('<br />', '', $ui->item("address_contact_notes")); ?>" name="Address[notes]"></textarea>
   
   </div>
	<div class="span6" style="width: 50%; margin: 0;">
	
    <div class="cart_footer  footer1" style="width: 553px;">
        <?=$ui->item('ORDER_MSG_DELIVERY_COST')?> <span class="delivery_cost">0 &euro;</span> <span class="add_cost" style="font-weight: bold; display: none;"><?=$ui->item('CARTNEW_OTHER_PRODUCTS_CART')?></span>
    </div>
    <div class="clearfix"></div>
    <div class="cart_footer footer2" style="width: 553px;">
        <?=$ui->item('CART_COL_SUBTOTAL_DELIVERY')?>: <span class="delivery_name"><?=$ui->item('MSG_DELIVERY_TYPE_0')?></span><span class="date" style="display: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: 05.07.2018 </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$ui->item('CARTNEW_TOTAL_WEIGHT_LABEL')?>: <?= $cartInfo['fullInfo']['weight'] ?> <?=$ui->item('CARTNEW_WEIGHT_NAME')?>
    </div>
    <div class="clearfix"></div>

    <div class="cart_footer footer_promocode" style="width: 553px;">
        <?php $this->renderPartial('/cart/_promocode', array('priceId'=>'itogo_cost')); ?>
    </div>
    <div class="clearfix"></div>
    <div class="cart_footer footer3" style="width: 553px;">
        <?=$ui->item('CARTNEW_TOTAL_COST_LABEL')?>: <span class="itogo_cost" id="itogo_cost"><?= $PH->FormatPrice($fullprice); ?></span>
    </div>
    <div class="clearfix"></div>

    <div style="float: right; width: 575px; margin-bottom: 10px;">
         <?=$ui->item('CARTNEW_SEND_INFO_LABEL')?>
    </div>
	
	</div>


    <div class="clearfix"></div>
	
    <a href="javascript:;" class="order_start" style="margin: 20px auto; width: 360px; display: block" onclick="sendforma();"><?=$ui->item('CARTNEW_SEND_ORDER_BTN')?></a>



</div>
</div>
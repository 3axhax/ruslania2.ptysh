<hr />

<style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        border-radius: 2px;
        position: relative;
        width: 212px;
    }
    
    label.seld .red_checkbox {
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
    .cart tbody tr td span.a { color: #005580; }
    
    .cart_box {
        overflow: auto;
        max-height: 565px;
    }
    
    .cart_footer { float: right; }
    
     .footer3 { margin-bottom: 25px; border-bottom: 1px solid #ececec; }
    
    .footer1, .footer3 {
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
    
</style>

<script>
    
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
                    if (r) $(t).after(r);
                }
            });
        }
    }

    function forgotPassword(email) {
        document.getElementById('js_forgot').innerHTML = '<div style="font-weight: bold;">Пароль отправлен на email: ' + email + '</div><div style="font-weight: bold;">Товары в корзине сохранены</div>';
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        $.ajax({
            url: '<?= Yii::app()->createUrl('site/forgot') ?>',
            type: 'post',
            data: 'User[login]=' + email + '&' + csrf[0] + '=' + csrf[1],
            success: function () {
                document.location.href = '<?= Yii::app()->createUrl('cart/variants') ?>';
            }
        });
    }

    function change_city(cont) {
        
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        
        $('.check',$('.seld:visible')).removeClass('active');
        $('input[type=radio]', $('.seld:visible')).attr('checked','true');
        $('.seld').css('border','1px solid rgb(204, 204, 204)');
        $('.seld').removeClass('act');
        $('.select_dd_box, .delivery_box_sp, .delivery_box').hide();
        
        $('.seld #dtype1').parent().css('border','1px solid rgb(100, 113, 127)');
        $('.check',$('.seld:visible').slice(0,1)).addClass('active');
        $('.selp #dtype2').parent().addClass('act');
        
        
        $('input[type=radio]', $('.seld:visible').slice(0,1)).attr('checked','true');
        
        if (cont.val() == 68) {
            
            
            
            $('.seld #dtype3').parent().hide();
            //$('.oplata4, .oplata7').hide();
            
            $('.seld #dtype3').parent().show();
            
            //$('.oplata4, .oplata7').show();
            
        } else if (cont.val() == 62) {
            
            $('.seld #dtype3').parent().hide();
            //$('.oplata4, .oplata7').hide();
            
            $('.seld #dtype3').parent().show();
            //$('.oplata4').show();
            
        } else {
            
            $('.seld #dtype3').parent().hide();
            $('.select_dd_box, .delivery_box_sp, .delivery_box').hide();
            
            show_all();
            
        }
        
        
        if (cont.val() != '') {
            
            $.post('<?=Yii::app()->createUrl('cart')?>getdeliveryinfo', { id_country: cont.val(), YII_CSRF_TOKEN: csrf[1] }, function(data) {
                
                $('.delivery_box, .delivery_box_sp').html(data);
                $('.box_opacity .op').hide();
                $('.order_start').removeClass('disabled');
                sbros_delev();
            })
            
            
            
            
        } else {
            $('.delivery_box').html('');
            $('.box_opacity .op').show();
            $('.order_start').addClass('disabled');
            
        }
        
    }
    
    function select_row(cont) {
        
        $('.select_dd_box input').val(cont.html());
        $('.select_dd_popup').html('');
        $('.select_dd_popup').hide();
    }
    
    function postindex_input(cont) {
        
        //alert(cont.val());
        
        if (cont.val().length >= 3) {
            
            var csrf = $('meta[name=csrf]').attr('content').split('=');
           
            
            $.post('<?=Yii::app()->createUrl('cart')?>loadsp/', { s : cont.val(), YII_CSRF_TOKEN: csrf[1] }, function(data) {
                
                if (data == '') {
                    $('.select_dd_popup', cont.parent().parent()).html('');
        $('.select_dd_popup', cont.parent().parent()).hide();
        
                } else {
                
                $('.select_dd_popup', cont.parent().parent()).html(data);
                $('.select_dd_popup', cont.parent().parent()).show();
                
        }
                
             });

        } else {
            $('.select_dd_popup', cont.parent().parent()).hide();
        }
    
    }
    
    $(document).ready(function() {
        
        $(document).click(function (event) {
				if ($(event.target).closest(".select_dd_box").length)
				return;
				$('.select_dd_popup').hide();
				event.stopPropagation();
			});
        $('.check',$('.selp')).removeClass('active');
        $('.selp').css('border', '1px solid #ccc');
        
        
        $('.cartorder .row label.seld').slice(2,3).css('border', '1px solid #64717f');
        $('input[type=radio]', $('.cartorder .row label.seld').slice(2,3)).attr('checked', 'true');
        $('.check', $('.cartorder .row label.seld').slice(2,3)).addClass('active');
        
        $('.selp #dtype2').parent().css('border', '1px solid #64717f');
        $('input[type=radio]', $('.selp #dtype2').parent()).attr('checked', 'true');
        $('.check', $('.selp #dtype2').parent()).addClass('active');
    })
    
    function check_cart_sel(cont,cont2,inputId) {
        
        $('.'+cont2+' .check').removeClass('active');
        $('.'+cont2+' input[type=radio]').removeAttr('checked');
        $('.'+cont2+'').css('border', '1px solid #ccc');
        $('label.selp').removeClass('act');
        
        if ($('.check', cont).hasClass('active')) {
            $('.check', cont).removeClass('active');
            $(cont).removeClass('act');
            $(cont).css('border', '1px solid #ccc');
            $('#'+inputId, cont).removeAttr('checked');
        } else {
            $('.check', cont).addClass('active');
            $(cont).addClass('act');
            $(cont).css('border', '1px solid #64717f');
            $('#'+inputId, cont).attr('checked', 'true');
        }
        
        
        //$('.seld input#dtype3').parent().hide();
        //$('.oplata4, .oplata7').hide();
        
        if ($('#Address_country').val() == 68) {
            
            $('.seld #dtype3').parent().show();
            $('.oplata4, .oplata7').show();
            
        } else if ($('#Address_country').val() == 62) {
            
            $('.oplata7').hide();
            $('.seld input#dtype3').parent().show();
            $('.oplata4').show();
            
        } else if ($('#Address_country').val() == 62 && $('#Address_country').val() == 68) {
            
            $('.seld input#dtype3').parent().hide();
            $('.oplata4, .oplata7').hide();
            
        }
        
        
    }
    
    function  show_all() {
        
        $('.oplata1,.oplata2,.oplata3,.oplata4,.oplata5,.oplata6,.oplata7,.oplata8').show();
        
        $('.check',$('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');
        
        
        
        
        
        $('.check',$('.selp #dtype2').parent()).addClass('active');
        $('input[type=radio]', $('.selp #dtype2').parent()).attr('checked','true');
        $('.selp #dtype2').parent().css('border', '1px solid #64717f');
        $('.selp #dtype2').parent().addClass('act');
    }
    
    function showALL() {
        $('.spay .selp').show();
    }
    
    function hide_oplata(oplata) {
        
        
        
        $('.oplata'+oplata).hide();
        
        $('.check',$('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');
        
        //$('.selp:visible').slice(0,1).hide();
        $('.selp #dtype2').parent().addClass('act');
        $('.check',$('.selp #dtype2').parent()).addClass('active');
        $('input[type=radio]', $('.selp #dtype2').parent()).attr('checked','true');
        $('.selp #dtype2').parent().css('border', '1px solid #64717f');
        
    }
    
    function sendforma() {
        
        var frm1 = $('form.no_register1').serialize();
        var frm2 = $('form.address.text').serialize();
        
        var frmall = frm1+'&'+frm2;
        
        //alert(frmall);
        
        $.post('<?=Yii::app()->createUrl('cart')?>valid/', frmall, function(data) {
            
            if (data == '1') {
                
                $('#add-address').submit();
                
            } else {
                alert(data);
            }
            
            
        });
        
        
        
    }
    
    function check_delivery(cont) {
     
     var costall = $('input.costall').val();
     
     $('span.delivery_cost').html(cont.attr('rel') + '' + cont.attr('valute'));
     
     $('.itogo_cost').html((parseFloat($('input.costall').val()) + parseFloat(cont.attr('rel'))).toFixed(2) + '' + cont.attr('valute'));
     
     
    }
    
    function sbros_delev() {
     
     var costall = $('input.costall').val();
     
      $('.itogo_cost').html(costall + $('.rows_checkbox_delivery label').attr('valute'));
     
     $('.delivery_cost').html('0'+$('.rows_checkbox_delivery label').attr('valute'));
     
     
    }
    
    
</script>

<div class="container cartorder" style="margin-bottom: 20px;">
    
    
        
        <?php
        
        $delivery = new PostCalculator();
        
        $r = $delivery->GetRates2(10,$this->uid, $this->sid);
        
        //var_dump($r);
        
        $cart = new Cart();
        
        $PH = new ProductHelper();
        
        $cart = $cart->GetCart($this->uid, $this->sid);
        
       //var_dump($cart);
       
        $cartInfo = '';
        $fullprice = 0;
        $fullweight = 0;
        $price = 0;
        $full_count = 0;
        $cartInfo['items'] = array();
        
        foreach ($cart as $item) {
            
            $cartInfo['items'][$item['id']]['title'] = $PH->GetTitle($item);
            $cartInfo['items'][$item['id']]['weight'] = $item['InCartUnitWeight'];
            
            
            if ($item['discount'] == '' OR $item['discount'] == '0.00')            {
            
                $price = $item['brutto'];
            
            } else {
                
                $price =  $item['discount'];
                
            }
            
            $fullweight += $item['InCartUnitWeight'];
            $fullprice += $price * $item['quantity'];
            $full_count += $item['quantity'];
            $cartInfo['items'][$item['id']]['price'] = $price;
            $cartInfo['items'][$item['id']]['quantity'] = $item['quantity'];
            
        }
        
        $cartInfo['fullInfo']['count'] = $full_count;
        $cartInfo['fullInfo']['cost'] = $fullprice;
        $cartInfo['fullInfo']['weight'] = $fullweight/1000;
        
        
        echo '<input type="hidden" value="'.$cartInfo['fullInfo']['cost'].'" name="costall" class="costall">';
        
    $user = Yii::app()->user->GetModel();
    $address = new Address;
    $address->receiver_title_name = $user['title_name'];
    $address->receiver_last_name = $user['last_name'];
    $address->receiver_first_name  = $user['first_name'];
    $address->receiver_middle_name = $user['middle_name'];
    $address->contact_email = $user['login'];
    $address->type = 2;
    $this->renderPartial('/site/address_form2', array('model' => $address,
                                                           'mode' => 'new',
                                                           'afterAjax' => 'addrInserted', 'cart'=>$cartInfo)); ?>
   
        <div class="cart_footer  footer1" style="width: 553px;">
            Стоимость доставки <span class="delivery_cost">0&euro;</span>
        </div>
    <div class="clearfix"></div>
        <div class="cart_footer footer2" style="width: 553px;">
            Доставка: <span class="delivery_name">Забрать в магазине</span><span class="date" style="display: none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата: 05.07.2018 </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Общий вес: <?=$cartInfo['fullInfo']['weight']?> кг
        </div>
        <div class="clearfix"></div>
        <div class="cart_footer footer3" style="width: 553px;">
            Итоговая стоимость: <span class="itogo_cost"><?=$PH->FormatPrice($fullprice);?></span>
        </div>
    <div class="clearfix"></div>
        <a href="javascript:;" class="order_start disabled" style="float: right; margin-left: 320px; display: block" onclick="if ($(this).hasClass('disabled') == false) { sendforma(); }">Оформить заказ</a>     
    
   
   
</div>


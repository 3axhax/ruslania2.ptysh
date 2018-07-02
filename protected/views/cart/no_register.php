<hr />

<style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        border-radius: 2px;
        position: relative;
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
        width: 187px;
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
    
</style>

<script>
    
    function select_row(cont) {
        
        $('.select_dd_box input').val(cont.html());
        $('.select_dd_popup').html('');
        $('.select_dd_popup').hide();
    }
    
    function postindex_input(cont) {
        
        if (cont.val().length >= 3) {
            
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            
            $.post('/cart/loadsp/', { s : cont.val(), YII_CSRF_TOKEN: csrf[1] }, function(data) {
                
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
        
        $('.cartorder .row label.seld').slice(0,1).css('border', '1px solid #64717f');
        $('input[type=radio]', $('.cartorder .row label.seld').slice(0,1)).attr('checked', 'true');
        $('.check', $('.cartorder .row label.seld').slice(0,1)).addClass('active');
        
        $('.cartorder .row label.selp').slice(0,1).css('border', '1px solid #64717f');
        $('input[type=radio]', $('.cartorder .row label.selp').slice(0,1)).attr('checked', 'true');
        $('.check', $('.cartorder .row label.selp').slice(0,1)).addClass('active');
    })
    
    function check_cart_sel(cont,cont2,inputId) {
        
        $('.'+cont2+' .check').removeClass('active');
        $('.'+cont2+' input[type=radio]').removeAttr('checked');
        $('.'+cont2+'').css('border', '1px solid #ccc');
        
        if ($('.check', cont).hasClass('active')) {
            $('.check', cont).removeClass('active');
            $(cont).css('border', '1px solid #ccc');
            if (inputId == undefined) $('.avail', cont).val('');
            else $('#'+inputId, cont).removeAttr('checked');
        } else {
            $('.check', cont).addClass('active');
            $(cont).css('border', '1px solid #64717f');
            if (inputId == undefined) $('.avail', cont).val('1');
            else $('#'+inputId, cont).attr('checked', 'true');
        }
        
    }
    
    function  show_all() {
        
        $('.oplata1,.oplata2,.oplata3,.oplata4,.oplata5,.oplata6,.oplata7,.oplata8').show();
        
        $('.check',$('.selp')).removeClass('active');
        $('input[type=radio]', $('.selp')).removeAttr('checked');
        $('.selp').css('border', '1px solid #ccc');
        
        $('.check',$('.selp:visible').slice(0,1)).addClass('active');
        $('input[type=radio]', $('.selp:visible').slice(0,1)).attr('checked','true');
        $('.selp:visible').slice(0,1).css('border', '1px solid #64717f');
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
        
        $('.check',$('.selp:visible').slice(0,1)).addClass('active');
        $('input[type=radio]', $('.selp:visible').slice(0,1)).attr('checked','true');
        $('.selp:visible').slice(0,1).css('border', '1px solid #64717f');
        
    }
    
    function sendforma() {
        
        var frm1 = $('form.no_register1').serialize();
        var frm2 = $('form.address.text').serialize();
        
        var frmall = frm1+'&'+frm2;
        
        $.post('/cart/valid/', frmall, function(data) {
            
            if (data != '0') {
                
                $('#add-address').submit();
                
            }
            
            
        });
        
        
        
    }
</script>

<div class="container cartorder" style="margin-bottom: 20px;">
    
    
        
        <?php

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
                                                           'afterAjax' => 'addrInserted')); ?>
   
        
        

        <a href="javascript:;" class="order_start" style="width: 248px; margin-left: 320px; display: block" onclick="sendforma()">Оформить заказ</a>     
    
   
   
</div>


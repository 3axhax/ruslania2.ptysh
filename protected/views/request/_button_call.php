<?php /*Created by Кирилл (18.03.2019 22:32)*/ ?>
<span class="telephone-circle js-slide-toggle call-request" data-slidetoggle=".send_call_form" data-slideeffect="fade"><a class="icons"><span class="fa phone"></span></a></span>
<div class="send_call_form"></div>
<script>
	$(function(){
		var call = false;
		$('.call-request a').on('click', function(){
			//if (!call) {
				call = true;
				$.ajax({
					url: '<?=Yii::app()->createUrl('request/callform') ?>',
					data: {},
					type: 'GET',
					success: function (r) {
						var $r = $(r);
						$r.find('.close').on('click', function(){ $(this).closest('.send_call_form').hide(); });
						$('.send_call_form').html($r);
					}
				});
			//}
		});
	});
	
	function sendCall() {
		
		var csrf = $('meta[name=csrf]').attr('content').split('=');
		
		var query = $('#send_call').serialize();
		
		$('.text-info').hide();
		$('.text-info').css('margin-top', '9px');
		$('.text-info').css('width', '');
		
		$.post('<?=Yii::app()->createUrl('site/callsend') ?>', query, function(data) {
			
			if (data == '1') {
$('.text-info').hide();				
				$('.text-info').fadeOut(1);
				$('.yes_ok_block').show();
				$('.yes_ok_block').html('<?=$ui->item('CALL_FORM_OK_ALERT')?>'); 
				$('#SendCalls_face').val('');
				$('#SendCalls_phone').val('');
				$('#SendCalls_code').val('');
				
				//$('.yes_ok_block').fadeOut(3000);
			}
			
			if (data == '10') { $('.text-info').show(); $('#SendCalls_face').addClass('error'); $('.text-info').html('Заполните имя'); }else{  $('#SendCalls_face').removeClass('error'); }
			
			if (data == '11') { $('.text-info').show(); $('#SendCalls_code').addClass('error'); $('.text-info').html('Заполните код страны'); }else{ 

			if (data == '14') { $('.text-info').show(); $('#SendCalls_code').addClass('error'); $('.text-info').html('<?=$ui->item('CALL_FORM_ERROR_PHONE');?>'); }else{  $('#SendCalls_code').removeClass('error'); }
			
			}
			
			if (data == '12') { $('.text-info').show(); $('#SendCalls_phone').addClass('error'); $('.text-info').html('Заполните номер телефона'); }else{  if (data == '13') { $('.text-info').show(); $('#SendCalls_phone').addClass('error'); $('.text-info').html('<?=$ui->item('CALL_FORM_ERROR_PHONE');?>'); }else{  $('#SendCalls_phone').removeClass('error');   }   }
			
			
			if (data == '56') { $('.text-info').css('margin-top', '0'); $('.text-info').css('width', '330px'); $('.text-info').show(); $('.text-info').html('Не отмечено согласие с пользованием виртуальным магазином'); }
			
			
		})
		
	}
	


$(document).mouseup(function (e) {
    var container = $("#send_call, .telephone-circle");
    if (container.has(e.target).length === 0){
        $('.close', container).click();
    }
});

	
</script>
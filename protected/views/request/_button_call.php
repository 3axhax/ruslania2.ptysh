<?php /*Created by Кирилл (18.03.2019 22:32)*/ ?>
<span class="telephone-circle js-slide-toggle call-request" data-slidetoggle=".send_call_form" data-slideeffect="fade"><a class="icons"><span class="fa phone"></span></a></span>
<div class="send_call_form"></div>
<script>
	$(function(){
		var call = false;
		$('.call-request a').on('click', function(){
			if (!call) {
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
			}
		});
	});
</script>
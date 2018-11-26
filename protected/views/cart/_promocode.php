<?php /*Created by Кирилл (26.11.2018 21:33)*/ ?>
<div class="cart_promocode">
	<div>
		<label>
			<input type="checkbox" name="usePromocode" onclick="checkPromocode();">
			<span><?= Yii::app()->ui->item('PROMOCODE') ?></span>
		</label>
		<div id="js_promocode" style="display: none;">
			<input type="text" id="js_promocode_value" name="promocode" value=""><span><a href="javascript:;" onclick="checkPromocode();"><?= Yii::app()->ui->item('A_NEW_APPLY') ?></a></span>
		</div>
	</div>
</div>

<script>
	function checkPromocode() {
		var promocode = document.getElementById('js_promocode_value').value;
		if ()
		$.ajax({
			url: '<?= Yii::app()->createUrl('site/checkEmail') ?>',
			data: 'promocode=' + encodeURIComponent(),
			type: 'post',
			success: function (r) {
				console.log(r);
			}
		});
	}
</script>
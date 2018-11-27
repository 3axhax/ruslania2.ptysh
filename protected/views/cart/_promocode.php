<?php /*Created by Кирилл (26.11.2018 21:33)*/ ?>
<div id="js_promocode" class="cart_promocode">
	<div>
		<label>
			<input class="checkbox_icon" type="checkbox">
			<span><?= Yii::app()->ui->item('PROMOCODE') ?></span>
		</label>
		<div style="display: none;">
			<input type="text" value="">
			<input type="button" value="<?= Yii::app()->ui->item('A_NEW_APPLY') ?>">
		</div>
	</div>
</div>

<script type="text/javascript">
(function() {
	promocodes = function() {
		return new _Promocodes();
	};

	function _Promocodes() {}
	_Promocodes.prototype = {
//		$input, $use, $submit,

		init: function(){
			this.setConst();
			this.setEvents();
		},
		setConst: function() {
			var $promocodeBlock = $('#js_promocode');
			this.$input = $promocodeBlock.find('input[type=text]');
			this.$use = $promocodeBlock.find('input[type=checkbox]');
			this.$submit = $promocodeBlock.find('input[type=button]');
		},
		setEvents: function() {
			var self = this;
			self.$use.on('change', function(){
				if (this.checked) {
					if (self.$input.val() != '') self._recount(self.$input.val());
					self.$input.closest('div').show();
				}
				else {
					self.$input.closest('div').hide();
					self._recount('');
				}

			});
			self.$submit.on('click', function() { self._recount(self.$input.val()); });
		},
		getValue: function() { return this.$input.val(); },
		_recount: function(value) {
			var csrf = $('meta[name=csrf]').attr('content').split('=');
			$.ajax({
				url: '<?= Yii::app()->createUrl('cart/checkPromocode') ?>',
				data: 'promocode=' + encodeURIComponent(value) + '&' + csrf[0] + '=' + csrf[1],
				type: 'post',
				success: function (r) {
					console.log(r);
				}
			});
		}
	}
}());
$(document).ready(function() {
	promocodes().init();
});
</script>
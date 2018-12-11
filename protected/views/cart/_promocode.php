<?php /*Created by Кирилл (26.11.2018 21:33)*/ ?>
<div id="js_promocode" class="cart_promocode">
	<div><?php /*
		<label>
			<input class="checkbox_icon" type="checkbox">
			<span><?= Yii::app()->ui->item('PROMOCODE') ?></span>
		</label>
		<div style="display: none;">
			<input type="text" value="">
			<input type="button" value="<?= Yii::app()->ui->item('A_NEW_APPLY') ?>">
		</div>*/ ?>
		<div>
			<input placeholder="<?= Yii::app()->ui->item('PROMOCODE'); ?>" type="text" id="promocode" value="">
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
		active: false,

		init: function(){
			this.setConst();
			this.setEvents();
			return this;
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
					if (self.$input.val() != '') self.recount(self.$input.val().trim());
					self.$input.closest('div').show();
				}
				else {
					self.$input.closest('div').hide();
					self.recount('');
				}

			});
			self.$submit.on('click', function() { self.recount(self.$input.val().trim()); });
		},
		getValue: function() {
			if (this.active) return this.$input.val();
			return '';
		},
		recount: function(value) {
			var self = this;
			var csrf = $('meta[name=csrf]').attr('content').split('=');
			var $form = $('form.address.text');
			var dtid = $form.find('input[name=dtid]:checked').val();
			var dtype = $form.find('input[name=dtype]:checked').val();
			var aid = 0;
			var $address = $form.find('select[name=id_address]');
			if ($address.length > 0) aid = $address.val();
			var cid = 0;
			var $country = $form.find('#Address_country');
			if ($country.length > 0) cid = $country.val();
			$.ajax({
				url: '<?= Yii::app()->createUrl('cart/checkPromocode') ?>',
				data: 'promocode=' + encodeURIComponent(value) +
					'&dtid=' + dtid +
					'&dtype=' + dtype +
					'&aid=' + aid +
					'&cid=' + cid +
					'&' + csrf[0] + '=' + csrf[1],
				type: 'post',
				dataType : 'json',
				success: function (r) {
					$('.<?= $priceId ?>').html(r.totalPrice + ' ' + r.currency);
					self.$input.closest('div').siblings().remove();
					if (value != '') {
						var $buf = self.$input.closest('div');
						var $elem = $('<div style="font-weight: normal;"></div>');
						if ('promocodeValue' in r.briefly) {
							$elem.append('<span style="margin-right: 20px;">' + r.briefly['promocodeValue'] + ' ' + r.briefly['promocodeUnit'] + '</span>');
							self.active = true;
						}
						else if ('message' in r.briefly) {
							$elem.append('<span style="margin-right: 20px;">' + r.briefly['message'] + '</span>');
							self.active = false;
						}
						$('<span style="color:#ed1d24; cursor: pointer;">&#10008;</span>').appendTo($elem).click(function(){ self.recount(''); });
						if ('name' in r.briefly) $elem.append(r.briefly['name']);
						$buf.after($elem);
					}
					else {
						self.active = false;
						self.$input.val('');
					}
				}
			});
		}
	}
}());
$(document).ready(function() {
	promocodeHandler = promocodes().init();
});
</script>
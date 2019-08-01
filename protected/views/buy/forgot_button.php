<?php /*Created by Кирилл (18.07.2018 22:28)*/ ?>
<div>
	<div><?= Yii::app()->ui->item('REGISTER_ERROR_LOGIN_IS_EXISTS') ?></div>
	<div style="font-size: 17px; font-weight: bold;"><?= Yii::app()->ui->item('CARTNEW_ALERT_PSW') ?></div>
	<input style="width: 50px; background-color: #0088cc; color: #fff" type="button" value="<?= Yii::app()->ui->item('A_NEW_BTN_YES') ?>" onclick="forgotPassword('<?= $email ?>')" />
	<input style="width: 50px; background-color: #0088cc; color: #fff" type="button" value="<?= Yii::app()->ui->item('A_NEW_BTN_NO') ?>" onclick="$('#Address_contact_email').val(''); $(this).closest('.info_box').html('');" />
</div>
<script>
	function forgotPassword(email) {
		var csrf = $('meta[name=csrf]').attr('content').split('=');
		$.ajax({
			url: '<?= Yii::app()->createUrl('site/forgot') ?>',
			type: 'post',
			data: 'User[login]=' + email + '&' + csrf[0] + '=' + csrf[1],
			success: function () {
				document.location.href = "<?= Yii::app()->createUrl('cart/variants') ?>";
			}
		});
	}
</script>
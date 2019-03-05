<?php /*Created by Кирилл (18.07.2018 22:28)*/ ?>
<div id="js_forgot" style="border: 1px solid #cccccc;padding: 10px;position: absolute;left: 100%;width: 300px;top: 0;border-radius: 4px; background-color: #fff;">
	<div><?= Yii::app()->ui->item('REGISTER_ERROR_LOGIN_IS_EXISTS') ?></div>
	<div style="font-size: 17px; font-weight: bold;"><?= Yii::app()->ui->item('CARTNEW_ALERT_PSW') ?></div>
	<input style="width: 50px; background-color: #0088cc; color: #fff" type="button" value="Да" onclick="forgotPassword('<?= $email ?>')" />
	<input style="width: 50px; background-color: #0088cc; color: #fff" type="button" value="Нет" onclick="$('#Address_contact_email').val(''); dontClick();" />
</div>
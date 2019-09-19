<!-- Russain -->
<? if (Yii::app()->language == 'ru') :  ?>
Уважаемый пользователь!
<br>На сайт <a href="https://www.ruslania.com/ru/">ruslania.com</a>поступил запрос о обнулении пароля.
<br>Нажмите на ссылку <a href="https://www.ruslania.com<?= $urlRestore ?>">Восстановить пароль</a>
<br>По этой ссылке вы перейдете на сайт Ruslania.com, где вы сможете обнулить пароль.
<br>С уважением, Руслания

<!-- Russain translit -->
<? elseif (Yii::app()->language == 'rut') : ?>
Uvazhayemyy pol'zovatel'!
<br>Na sayt <a href="https://www.ruslania.com/ru/">ruslania.com</a>postupil zapros o obnulenii parolya.
<br>Nazhmite na ssylku <a href="https://www.ruslania.com<?= $urlRestore ?>">Vosstanovit parol</a>
<br>Po etoy ssylke vy pereydete na sayt Ruslania.com, gde vy smozhete obnulit parol.
<br>S uvazheniyem, Ruslaniya

<!-- Deutsche -->
<? elseif (Yii::app()->language == 'de') : ?>
Sehr geehrter Kunde!
<br>Wir haben an <a href="https://www.ruslania.com/ru/">ruslania.com</a> eine Aufforderung zum Zurücksetzen Ihres Passworts erhalten.
<br>Klicken Sie auf den Link <a href="https://www.ruslania.com<?= $urlRestore ?>">Passwort Zurücksetzen</a>
<br>Der Link führt Sie zu der Seite bei Ruslania.com, auf der Sie Ihr Passwort zurücksetzen können.
<br>Beste Grüße, Ruslania

<!-- Suomi -->
<? elseif (Yii::app()->language == 'fi') : ?>
Arvoisa asiakas!
<br>Nettikauppaamme <a href="https://www.ruslania.com/ru/">ruslania.com</a> on saapunut pyyntö nollata salasanasi.
<br>Klikkaa linkkiä <a href="https://www.ruslania.com<?= $urlRestore ?>">Nollaa salasana</a>
<br>Linkistä pääset Ruslania.com-verkkokaupan sivulle, jolla voit keksiä itsellesi uuden salasanan.
<br>Terveisin, Ruslania

<!-- English -->
<? else/*if (Yii::app()->language == 'en')*/ : ?>
Dear customer!
<br>We have received to <a href="https://www.ruslania.com/ru/">ruslania.com</a> a request to reset your password.
<br>Click the link <a href="https://www.ruslania.com<?= $urlRestore ?>">Reset password</a>
<br>The link will take you to the page at Ruslania.com where you can reset your password.
<br>Best Regards, Ruslania
<?php /*
<!-- France -->
<? elseif (Yii::app()->language == 'fr') : ?>
Dear customer!<br>We have received to https://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<!-- Espanol -->
<? elseif (Yii::app()->language == 'es') : ?>
Dear customer!<br>We have received to https://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
<? else : ?>
Dear customer!<br>We have received to https://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
*/ ?>
<? endif; ?>

<br><br>


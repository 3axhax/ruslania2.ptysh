<!-- Russain -->

<? if (Yii::app()->language == 'ru') :  ?>

Уважаемый пользователь! <br>На сайт http://www.ruslania.com поступил запрос о восстановлении пароля. В соответствии с этим запросом высылаем Вам Ваш пароль по e-mail адресу, указанному Вами при регистрации.<br><br>Ваш логин: <?=$login; ?><br>Ваш пароль: <?=$pwd; ?>

<!-- Russain translit -->
<? elseif (Yii::app()->language == 'rut') : ?>


Uvazhaemyj polzovatel! <br>Na sajt http://www.ruslania.com postupil zapros o vosstanovlenii parolja. V sootvetstvii s etim zaprosom vysylaem Vam Vash parol po e-mail adresu, ukazannomu Vami pri registratsii.<br><br>Vash login: <?=$login; ?><br>Vash parol: <?=$pwd; ?>

<!-- Deutsche -->
<? elseif (Yii::app()->language == 'de') : ?>
Sehr geehrter Kunde! <br>Bei http://www.ruslania.com wurde Ihr  Wunsch angenommen, Ihr Kennwort zu liefern. Wir schicken das Kennwort an die von Ihnen bei Anmeldung angegebene E-Mail-Adresse. <br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<!-- Suomi -->
<? elseif (Yii::app()->language == 'fi') : ?>
Arvoisa asiakas! <br>Nettikauppaamme http://www.ruslania.com on saapunut pyynto toimittaa salasana. Lahetamme salasanan sahkopostiosoitteeseen, jonka olet antanut rekisteroityessasi.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<!-- English -->
<? elseif (Yii::app()->language == 'en') : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<!-- France -->
<? elseif (Yii::app()->language == 'fr') : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<!-- Espanol -->
<? elseif (Yii::app()->language == 'es') : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
<? else : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<? endif; ?>
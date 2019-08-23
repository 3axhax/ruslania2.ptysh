<!-- Russain -->

<? if (Yii::app()->language == 'ru') :  ?>

Уважаемый пользователь! <br>На сайт http://www.ruslania.com поступил запрос о восстановлении пароля. В соответствии с этим запросом высылаем Вам Ваш пароль по e-mail адресу, указанному Вами при регистрации.<br><br>Ваш логин: <?=$login; ?><br>Ваш пароль: <?=$pwd; ?>
<br>Это электронное письмо, отправленное автоматически с нашего сервера.
<br>Мы временно отправляем эти электронные письма с нашего адреса электронной почты  gmail.com из-за некоторых проблем с доставкой электронной почты с нашего собственного сервера ruslania.com
<br>Не волнуйтесь, никто не имеет доступа к вашему паролю. Если хотите, можете поменять его на Моей Руслании
<!-- Russain translit -->
<? elseif (Yii::app()->language == 'rut') : ?>


Uvazhaemyj polzovatel! <br>Na sajt http://www.ruslania.com postupil zapros o vosstanovlenii parolja. V sootvetstvii s etim zaprosom vysylaem Vam Vash parol po e-mail adresu, ukazannomu Vami pri registratsii.<br><br>Vash login: <?=$login; ?><br>Vash parol: <?=$pwd; ?>

<!-- Deutsche -->
<? elseif (Yii::app()->language == 'de') : ?>
Sehr geehrter Kunde! <br>Bei http://www.ruslania.com wurde Ihr  Wunsch angenommen, Ihr Kennwort zu liefern. Wir schicken das Kennwort an die von Ihnen bei Anmeldung angegebene E-Mail-Adresse. <br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
<br>Dies ist eine E-Mail, die automatisch von unserem Server gesendet wird.
<br>Wir senden diese E-Mails vorübergehend von unserer Google Mail-Adresse aufgrund einiger Probleme bei der E-Mail-Zustellung von unserem eigenen Server ruslania.com.
<br>Keine Sorge, niemand hat Zugriff auf Ihr Passwort. Wenn Sie möchten, können Sie dies bei Mein Ruslania ändern

<!-- Suomi -->
<? elseif (Yii::app()->language == 'fi') : ?>
Arvoisa asiakas! <br>Nettikauppaamme http://www.ruslania.com on saapunut pyynto toimittaa salasana. Lahetamme salasanan sahkopostiosoitteeseen, jonka olet antanut rekisteroityessasi.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
<br>Tämä sähköposti on automaattisesti lähetetty palvelimelta.
<br>Lähetämme väliaikaisesti nämä sähköpostit gmail-osoitteestamme tiettyjen ongelmien vuoksi sähköpostien toimittamisessa omalta palvelimeltamme ruslania.com.
<br>Älä huoli, kenelläkään ei ole pääsyä näkemään salasanaasi. Halutessasi voit muuttaa sen Minun Ruslaniani -sivulla

<!-- English -->
<? elseif (Yii::app()->language == 'en') : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
<br>This is an e-mail sent automatically from our server.
<br>We are temporarily sending these e-mails from our gmail address due to some problems in e-mail delivery from our own server ruslania.com
<br>Don't worry, no-one has access to your password. If you wish, you can change it at My Ruslania

<!-- France -->
<? elseif (Yii::app()->language == 'fr') : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<!-- Espanol -->
<? elseif (Yii::app()->language == 'es') : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>
<? else : ?>
Dear customer!<br>We have received to http://www.ruslania.com a request to deliver your password. The password has been sent to the e-mail address that you used upon registration.<br><br>Login: <?=$login; ?><br>Password: <?=$pwd; ?>

<? endif; ?>
<?php /*Created by Кирилл (30.05.2019 20:22)*/ ?>
Уважаемый(ая) <?= $formData['fio_source'] ?>!<br>
<br>
Вы купили сертификат на <?= ProductHelper::FormatPrice($formData['nominal'], true, 1); ?>: <?= $promocodeTxt ?><br>
<br>
<img src="<?= $promocodeUrl ?>">
<br>
Сертификат отправлен на <?= $formData['email_dest'] ?>
<br>
Сообщение для получателя:
<br>
<?= nl2br($formData['txt_dest']) ?>
<br>
<hr>
С уважением<br>
Ruslania Books Oy<br>
Bulevardi 7, 00120 HELSINKI, FINLAND<br>
Puhelin 09 272 70717Tel +358 9 272 717<br>
E-mail: ruslania@ruslania.com<br>
<a href="<?= Yii::app()->createUrl('site/index') ?>">ruslania.com</a><br>
Facebook.com/RuslaniaBooks
<br><br><hr width=75%><br>
<?php /*Created by Кирилл (30.05.2019 19:55)*/ ?>
Уважаемый(ая) <?= $formData['fio_dest'] ?>!<br>
<br>
Вам в подарок промокод на <?= ProductHelper::FormatPrice($formData['nominal'], true, 1); ?>: <?= $promocodeTxt ?><br>
<br>
<img src="<?= $promocodeUrl ?>">
<br>
Сообщение для получателя:
<br>
<?= $formData['txt_dest'] ?>
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
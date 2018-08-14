<?php
if (!empty($razds)) {
	if (count($razds) == 2) $razds = implode(' and ', $razds);
	else $razds = implode(', ', $razds);
}
?>
Dear <?= $user['last_name'] ?> <?= $user['first_name'] ?><?= trim(' ' . $user['middle_name']) ?>! Thanks for your registration at ruslania.com<br>
<br>
your login: <?= $user['login'] ?><br>
<br>
You can have the password here: <a href="https://ruslania.com/site/forgot?language=ru" target="_blank"><?= Yii::app()->ui->item('A_REMIND_PASS') ?></a><br>
<br>
You can change the password any time at My Ruslania.<br>
<br>
You can also change the language of the order progress notifications.<br>
<br>
You can also change the newsletter setting.<br>
<?php if (!empty($razds)): ?>
<br>
We have now subscribed you to the newsletter of: <?= $razds ?>.
<?php endif; ?>
<br>
Best regards<br>
Ruslania Books Oy<br>
Bulevardi 7, 00120 HELSINKI, FINLAND<br>
Puhelin 09 272 70717Tel +358 9 272 717<br>
E-mail: ruslania@ruslania.com<br>
<a href="https://ruslania.com/">ruslania.com</a><br>
Facebook.com/RuslaniaBooks
<br><br><hr width=75%><br>

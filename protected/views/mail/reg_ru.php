<?php /*Created by Кирилл (20.07.2018 23:47)*/
if (!empty($razds)) {
	if (count($razds) == 2) $razds = implode(' и ', $razds);
	else $razds = implode(', ', $razds);
}
?>
Уважаемый(ая) <?= $user['last_name'] ?> <?= $user['first_name'] ?><?= trim(' ' . $user['middle_name']) ?>! Благодарим за регистрацию на ruslania.com<br>
<br>
Ваш логин: <?= $user['login'] ?><br>
<br>
Пароль можно получить тут: <a href="https://ruslania.com/site/forgot?language=ru" target="_blank"><?= Yii::app()->ui->item('A_REMIND_PASS') ?></a><br>
<br>
Вы можете поменять пароль в любое время в ”Моей Руслании”.<br>
<br>
Также можете поменять язык на котором Вы бы хотели получать сообщения о ходе выполнения заказов.<br>
<br>
Кроме этого, можете  поменять подборку разделов, информация о новинках которых вам направляется.<br>
<?php if (!empty($razds)): ?>
<br>
На данный момент мы подписали вас на новости раздела(ов): <?= $razds ?>.<br>
<?php endif; ?>
<br>
С уважением<br>
Ruslania Books Oy<br>
Bulevardi 7, 00120 HELSINKI, FINLAND<br>
Puhelin 09 272 70717Tel +358 9 272 717<br>
E-mail: ruslania@ruslania.com<br>
<a href="https://ruslania.com/">ruslania.com</a><br>
Facebook.com/RuslaniaBooks
<br><br><hr width=75%><br>

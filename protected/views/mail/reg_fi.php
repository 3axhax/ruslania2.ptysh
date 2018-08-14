<?php
if (!empty($razds)) {
	if (count($razds) == 2) $razds = implode(' и ', $razds);
	else $razds = implode(', ', $razds);
}
?>
Hei <?= $user['last_name'] ?> <?= $user['first_name'] ?><?= trim(' ' . $user['middle_name']) ?>! Kiitos rekisteröitymisestä ruslania.com-nettikaupassa.<br>
<br>
käyttäjätunnuksesi on: <?= $user['login'] ?><br>
<br>
Tästä voit pyytää salasanasi sähköpostiisi: <a href="https://ruslania.com/site/forgot?language=ru" target="_blank"><?= Yii::app()->ui->item('A_REMIND_PASS') ?></a><br>
<br>
Voit vaihtaa salasanasi Minun Ruslaniassasi<br>
<br>
Voit myös vaihtaa kielen, jolla haluat saada tilausta koskevat ilmoitukset.<br>
<?php if (!empty($razds)): ?>
<br>
Voit myös vaihtaa uutiskirjeasetuksiasi. Nyt olemme merkinneet sinut uutiskirjeiden saajaksi: <?= $razds ?>.<br>
<?php endif; ?>
<br>
Tervehtien<br>
Ruslania Books Oy<br>
Bulevardi 7, 00120 HELSINKI, FINLAND<br>
Puhelin 09 272 70717Tel +358 9 272 717<br>
E-mail: ruslania@ruslania.com<br>
<a href="https://ruslania.com/">ruslania.com</a><br>
Facebook.com/RuslaniaBooks
<br><br><hr width=75%><br>

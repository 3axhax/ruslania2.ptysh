A call back request has arrived:<br /><br />
name: <?=$name?><br />
<? if ($id AND $email) : ?>
ID client: <?=$id?><br />
E-mail: <?=$email?><br />
<? endif;?>
country: <?=$city?><br />
country code: <?=$country_code?><br />
telephone number: <?=$phone?><br />
date: <?=date('d-m-Y')?><br />
time: <?=date('H:i:s')?>
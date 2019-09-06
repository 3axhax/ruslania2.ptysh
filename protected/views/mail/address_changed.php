<p>Пользователь сайта с ID: <?=$uid; ?> сменил адрес.</p>
<p>Старый адрес:</p>
<p>
<?php foreach ($old as $kName=>$v):
    switch ($kName):
        case 'type': ?><?= $kName ?>: <b><?= ($v == 1)?'Организация':'Частное лицо' ?></b><br/><?php break;
        case 'country': case 'state_id': case 'beta_id': case 'is_unloaded': break;
        default: ?><?= $kName ?>: <b><?= $v?:'N/A' ?></b><br/><?php break;
    endswitch;
endforeach; ?>
</p>

<p>Новый адрес:</p>
<p>
<?php foreach ($old as $kName=>$v):
    $v = $new[$kName];
    switch ($kName):
        case 'type': ?><?= $kName ?>: <b><?= ($v == 1)?'Организация':'Частное лицо' ?></b><br/><?php break;
        case 'country': case 'state_id': case 'beta_id': case 'is_unloaded': break;
        default: ?><?= $kName ?>: <b><?= $v?:'N/A' ?></b><br/><?php
    endswitch;
endforeach; ?>
</p>

<?php if(count($all) > 1) : ?>
<p>У клиента есть еще адреса доставки: </p>
<p>
    <ul>
    <?php foreach($all as $addr) : ?>
    <li>
        <?=$addr['receiver_first_name']; ?> <?=$addr['receiver_last_name']; ?><br/>
        <?=$addr['streetaddress']; ?><br/>
        <?=$addr['postindex']; ?> <?=$addr['city']; ?><br/>
        <?=$addr['country_name']; ?>
    </li>
    <?php endforeach; ?>
    </ul>
</p>

<?php endif; ?>
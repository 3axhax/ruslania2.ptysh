<p>Пользователь сайта с ID: <?=$uid; ?> сменил адрес.</p>
<p>Изменения:</p>
<table>
    <tr>
        <th style="border-top: 1px solid; border-bottom: 1px solid; border-right: 1px solid; padding: 2px 5px; width: 50%;">Старый адрес</th>
        <th style="border-top: 1px solid; border-bottom: 1px solid; padding: 2px 5px; width: 50%;">Новый адрес</th>
    </tr>
    <?php $change = false; foreach ($old as $kName=>$v):
        if ($v != $new[$kName]):
            switch ($kName):
                case 'type': $change = true; ?><tr>
                    <td style="border-bottom: 1px solid; border-right: 1px solid; padding: 2px 5px; width: 50%;"><?= ($v == 1)?'Организация':'Частное лицо' ?></td>
                    <td style="border-bottom: 1px solid; padding: 2px 5px; width: 50%;"><?= ($new[$kName] == 1)?'Организация':'Частное лицо' ?></td>
                </tr><?php break;
                case 'country': case 'state_id': case 'beta_id': case 'is_unloaded': case 'id': break;
                default: $change = true; ?><tr>
                    <td style="border-bottom: 1px solid; border-right: 1px solid; padding: 2px 5px; width: 50%;"><?= $v?:'N/A' ?></td>
                    <td style="border-bottom: 1px solid; padding: 2px 5px; width: 50%;"><?= $new[$kName]?:'N/A' ?></td>
                </tr><?php break;
            endswitch;
        elseif (!empty($v)):
            switch ($kName):
                case 'receiver_first_name': case 'receiver_last_name': case 'streetaddress': case 'postindex': case 'city': case 'country_name': ?><tr>
                    <td colspan="2" style="border-bottom: 1px solid; padding: 2px 5px; text-align: center"><?= $v ?></td>
                </tr><?php break;
            endswitch;
        endif;
    endforeach; ?>
</table>
<?php /*
if (!$change): ?>
    <p>Изменений не обнаружено</p>
<? endif;

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
 <?php */
<?php

$isClosed = RequestState::IsClosed($request['States']); ?>
<tr class="<?= (($isClosed) ? 'closed' : 'open'); ?>">
    <td class="center">
        <a href="<?= Yii::app()->createUrl('request/view', array('rid' => $request['RID'])); ?>"><?=$request['RID']; ?></a>
    </td>
    <td class="left">
        <?php if (Entity::IsValid($request['Item']['entity'])): ?><?= Entity::GetTitle($request['Item']['entity']); ?>:<?php endif; ?>
        <?php if (!empty($request['Item']['id'])) : ?>
            <a href="<?= ProductHelper::CreateUrl($request['Item']); ?>"><?= ProductHelper::GetTitle($request['Item']); ?></a>
        <?php else : ?>
            <?= ProductHelper::GetTitle($request['Item']); ?>
        <?php endif; ?>
    </td>
    <td class="left">
        <?php $last = RequestState::GetLastState($request['States']); ?>
        <?= $last['state_string']; ?>,<br/>
        <?= $last['date_string']; ?>
    </td>
</tr>

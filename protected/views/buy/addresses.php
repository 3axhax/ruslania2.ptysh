<?php /*Created by Кирилл (06.03.2019 22:00)*/ ?>
<select class="address_select" name="<?= $fieldName ?>" id="<?= $fieldName ?>">
	<?php if ($fieldName == 'delivery_address_id'): ?><option value="0"><?= $ui->item('TAKE_IN_THE_STORE') ?></option><?php endif; ?>
<?php
$selected = false;
foreach ($addrList as $addr):
	$addrStr = CommonHelper::FormatAddress($addr);
	if (!empty($addrStr)): ?>
	<option value="<?= $addr['address_id'] ?>"<?php if(!$selected&&!empty($addr['if_default'])): $selected = true; ?> selected<?php endif; ?>><?= $addrStr ?></option>
<?php endif; endforeach?>
</select>
<span class="address_add">+</span>
<span class="texterror" style="display: none;"><?= $ui->item(($fieldName == 'delivery_address_id')?'CARTNEW_ERROR_SELECT_ADDR_DELIVERY':'CARTNEW_ERROR_SELECT_ADDR_BUYER') ?></span>
<?php if ($fieldName == 'delivery_address_id'): ?><div style="display: none;" class="delivery_people"><?= $ui->item('DELIVERY_PEOPLE') ?></div><?php endif; ?>


<?php /*Created by Кирилл (21.06.2018 21:40)*/ ?>
<div class="row_item">
	<table>
		<tr>
			<td class="pic">
<?php
$url = ProductHelper::CreateUrl($item);
$urlPicture = ProductHelper::Link2Picture($item, true);
if (!empty($urlPicture)&&($urlPicture != 'http://ruslania.com/pictures/small/')): ?>
				<a href="<?= $url ?>"><img style="max-width: 100%;" height="86" src="<?= $urlPicture ?>" /></a>
<?php endif; ?>
			</td>
			<td class="name">
				<a href="<?= $url ?>"><?= ProductHelper::GetTitle($item) ?></a>
				<?php if (!empty($item['inDescription'])): ?>
					<div><?= Yii::app()->ui->item('IN_DESCRIPTION') ?>: <?= $item['inDescription'] ?></div>
				<?php else: ?>
				<div style="height: 18px;"></div>
				<?php endif; ?>
				<span class="price"><?= ProductHelper::FormatPrice($item['brutto']) ?></span>
			</td>
		</tr>
	</table>
</div>
<?php /*Created by Кирилл (21.06.2018 20:46)*/ ?>
<div class="row_item"><?= $ui->item('DID_YOU_MEAN') ?></div>
<?php foreach ($items as $item): ?>
<div class="row_item">
	<a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
</div>
<?php endforeach; ?>
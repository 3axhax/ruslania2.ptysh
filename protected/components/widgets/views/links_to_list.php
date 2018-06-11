<?php /*Created by Кирилл (05.06.2018 22:04)*/ ?>
<ul class="left_list divider">
	<?php foreach ($links as $link): ?>
    <li><a href="<?= $link['href'] ?>"><?= $link['name'] ?></a></li>
	<?php endforeach; ?>
</ul>
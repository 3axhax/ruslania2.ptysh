<?php /*Created by Кирилл (21.06.2018 21:40)*/ ?>
<div class="row_item">
	<table>
		<tr>
			<td class="pic">
<?php if (!empty($item['picture_url'])&&($item['picture_url'] != 'http://ruslania.com/pictures/small/')): ?>
				<a href="<?= $item['url'] ?>"><img style="max-width: 100%;" height="86" src="<?= $item['picture_url'] ?>" /></a>
<?php endif; ?>
			</td>
			<td class="name">
				<a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
				<div style="height: 18px;"></div>
				<span class="price"><?= $item['price'] ?></span>
			</td>
		</tr>
	</table>
</div>
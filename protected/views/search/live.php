<?php /*Created by Кирилл (15.09.2018 1:41)*/ if (empty($result)) $result = array(); ?>
<?php if (!empty($result['header'])): ?>
	<?= $result['header'] ?>
	<div class="clearBoth"></div>
<?php endif; ?>
<div>
<?php if (!empty($result['list'])): ?>
	<div style="float: left; width: 390px;">
		<?= implode('', $result['list']) ?>
	</div>
<?php endif; ?>
<?php if (!empty($result['entitys'])||!empty($result['did_you_mean'])): ?>
	<div style="padding-left: 400px;">
		<?php if (!empty($result['entitys'])): ?>
			<?= $result['entitys'] ?>
		<?php endif; ?>
		<?php if (!empty($result['did_you_mean'])): ?>
			<?= $result['did_you_mean'] ?>
		<?php endif; ?>
	</div>
<?php endif; ?>
</div>
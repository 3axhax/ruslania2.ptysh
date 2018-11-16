<?php /*Created by Кирилл (16.11.2018 21:35)*/ ?>
<hr />

<div class="container cartorder">
	Спасибо за заказ!<br /><br />

	<?php $this->widget($payName, array('order' => $order)); ?>

	<div class="clearBoth"></div>

</div>
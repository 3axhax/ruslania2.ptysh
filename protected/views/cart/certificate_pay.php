<?php /*Created by Кирилл (19.11.2018 19:49)*/ ?>
<hr />

<div class="container cartorder">
	Спасибо за заказ!<br /><br />
	<?php $this->widget($payName, array(
		'order' => $order,
		'acceptUrl'=>'/payment/acceptCertificate',
		'cancelUrl'=>'/payment/cancelCertificate',
		/*'notifyUrl'=>'/payment/notifyCertificate'*/)
	); ?>
	<div class="clearBoth"></div>
</div>

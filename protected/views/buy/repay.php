<?php /*Created by Кирилл (29.03.2019 21:12)*/
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
?>
<link rel="stylesheet" href="/new_style/order_buy.css?v=2908">
<div class="container cabinet">
	<div class="row">
		<div class="span10" style="float: right">
			<h1><?= $ui->item('ORDER_PAYMENT') ?></h1>
			<?php $this->renderPartial('/payment/_buy_choose', array('order' => $order)); ?>
			<!-- /content -->
			<div class="clearBoth"></div>
		</div>
		<div class="span2" >
			<?php //$this->renderPartial('/site/_me_left'); ?>
		</div>
	</div>
</div>
<?php /*Created by Кирилл (23.03.2019 17:54)*/
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<link rel="stylesheet" href="/new_style/order_buy.css">
<div class="container cabinet">
	<div class="row">
		<div class="span10" style="float: none;width: 100%;">
			<h1><?= $ui->item('PAY_ERROR_H1') ?></h1>
			<div class="info-box warning" style="border: none;     background-color: #edb421;     color: #333333;">
				<h2><?=$ui->item('A_SAMPO_PAYMENT_DECLINED'); ?></h2>
				<p><?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_2'); ?><br/>
					<?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_3'); ?><br/>
					<?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_PAYPAL'); ?>
				</p>
			</div>

			<p><?=$ui->item('ORDER_PAYMENT_TRY_AGAIN'); ?></p>

			<?php $this->renderPartial('/payment/_buy_choose', array('order' => $order)); ?>
			<!-- /content -->
			<div class="clearBoth"></div>
		</div>
		<div class="span2" >
			<?php //$this->renderPartial('/site/_me_left'); ?>
		</div>
	</div>
</div>
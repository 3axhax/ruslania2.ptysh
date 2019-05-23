<?php /*Created by Кирилл (19.11.2018 21:11)*/ ?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
<div class="span10" style="float: right">
			
				<div class="info-box information">
					<h2><?=$ui->item('A_SAMPO_PAYMENT_ACCEPTED'); ?></h2>
					<?=$ui->item('MSG_PAYMENT_RESULTS_ACCEPTED_2'); ?><br/>
					<?=$ui->item('MSG_PAYMENT_RESULTS_ACCEPTED_3'); ?><br/>
				</div>
				<div><?= $ui->item('PROMOCODE') ?>: <?= $code ?></div>
			<!-- /content -->
			</div>
            
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
    
    
            </div>
		</div>
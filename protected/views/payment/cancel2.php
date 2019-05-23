<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>



<div class="container cabinet">

<div class="row">
        <div class="span10" style="float: right">

            <?php if(empty($newOid)) : ?>

            <div class="info-box warning" style="border: none;     background-color: #edb421;     color: #333333;">
                <h2><?=$ui->item('A_SAMPO_PAYMENT_DECLINED'); ?></h2>
                <p><?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_2'); ?><br/>
                   <?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_3'); ?>
                </p>
            </div>

            <p><?=$ui->item('ORDER_PAYMENT_TRY_AGAIN'); ?></p>

            <?php $this->renderPartial('/payment/_payment_choose2', array('order' => $order)); ?>

            <?php else : ?>

            <div class="info-box warning">
                <h2><?=$ui->item('A_SAMPO_PAYMENT_DECLINED'); ?></h2>
                <p>
                    <?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_LUOTTOKUNTA'); ?>
                </p>

                <p>
                    <a href="<?=Yii::app()->createUrl('client/pay', array('oid' => $newOid)); ?>">New order# <?=$newOid; ?></a>
                </p>
            </div>

            <?php endif; ?>


            <!-- /content -->
        
            </div>
            
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
    
    
            </div>
            </div>
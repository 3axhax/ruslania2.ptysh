<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">
			<h2 class="cattitle me_left" style="margin-bottom: 25px;"><?= $ui->item('A_LEFT_PERSONAL_ORDERS') ?></h2>
            <?php if(count($list) == 0) : ?>

                <?=$ui->item("ORDER_MSG_NO_ORDERS"); ?>

            <?php else : ?>

                <ul style="list-style-type: none; margin:0; padding:0" class="text">
				
				<? $i = 1; ?>
				
                <?php foreach($list as $order) : ?>
                    <li>
                        <?php $this->renderPartial('_one_order', array('order' => $order, 'co'=>count($list), 'i'=>$i)); ?>
                        <div class=""></div>
						
                    </li>
                <?php $i++; endforeach; ?>
                </ul>

            <?php endif; ?>

            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>

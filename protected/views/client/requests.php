<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">
			
			<h1 class="title"><?=$ui->item('A_LEFT_PERSONAL_NOTAVAIBLE_ORDERS'); ?></h1>
			
            <?php if(count($list) == 0) : ?>

                <div class="info-box information">
                    <?=$ui->item('MSG_CART_ORDER_ERROR_EMPTY'); ?>
                </div>


            <?php else : ?>

                <?php
                $closed = array();
                $open = array();
                foreach($list as $item)
                {
                    if(RequestState::IsClosed($item['States'])) $closed[] = $item;
                    else $open[] = $item;
                }

                ?>

                <div class="information info-box">
                    <?=sprintf($ui->item('WAITING_LIST_SUMMARY'), count($list), count($open)); ?>
                </div>

                    <table class="request-list">
                        <tr>
                            <th style="width:60px;">№</th>
                            <th><?=$ui->item('CART_COL_TITLE'); ?></th>
                            <th>Last state</th>
                        </tr>
                        <?php foreach($list as $request) : ?>
                            <?php $this->renderPartial('_one_request2', array('request' => $request)); ?>
                        <?php endforeach; ?>

                    </table>

            <?php endif; ?>

            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>


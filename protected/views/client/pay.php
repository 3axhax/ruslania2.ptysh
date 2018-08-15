 <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>       
        
        <div class="container cabinet">

<div class="row">
        
        <div class="span10" style="float: right">
            <!-- content -->
           

            <?php if(Yii::app()->user->hasFlash('order')) : ?>
                <div class="info-box information">
                    <?=Yii::app()->user->getFlash('order'); ?>
                </div>
            <?php endif; ?>

            <?php if(!$order['is_reserved']) : ?>
                <?php $this->renderPartial('/payment/_payment_choose2', array('order' => $order, 'isPaid' => $isPaid)); ?>
            <?php else : ?>
                <div class="info-box information">
                    <?=$ui->item('IN_SHOP_NOT_READY'); ?>
                </div>
                <?php $this->renderPartial('/client/_one_order', array('order' => $order, 'onlyContent' => true,
                                                                       'class' => 'bordered',
                                                                       'enableSlide' => false)); ?>
            <?php endif; ?>
            <!-- /content -->
        
        </div>
            
        <div class="span2">

            <?php $this->renderPartial('/site/_me_left'); ?>

        </div>


        </div>
        </div>


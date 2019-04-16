<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">
            <!-- content -->

            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

            <?php $this->renderPartial('/client/_one_order_my', array('order' => $order)); ?>

            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>
<script type="text/javascript" src="/new_js/modules/print.js"></script>
<script type="text/javascript">
    print<?= $order['id'] ?> = function() { return new _Print(); };
    print<?= $order['id'] ?>().init({$button: $('.printed_btn'), $content: $('#cnt<?= $order['id'] ?>').closest('div')});
</script>

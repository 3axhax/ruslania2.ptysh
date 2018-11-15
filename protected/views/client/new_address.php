<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">

            <div class="information info-box">
                <?= $ui->item("MSG_PERSONAL_FORM_DESCRIPTION"); ?>
            </div>

            <?php $this->renderPartial('/site/address_form', array('model' => $model,
                                                                   'mode' => $mode,
                                                                   'afterAjax' => 'redirectToAddressList')); ?>


            <script type="text/javascript">
                function redirectToAddressList(json)
                {
                    window.location.href = '<?=Yii::app()->createUrl('client/addresses'); ?>';
                }
            </script>
            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>

<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">

            <div class="information info-box">
                <?= $ui->item("MSG_PERSONAL_FORM_DESCRIPTION"); ?>
            </div>

            <?php
            //if (isset($_GET['ha'])) {
                $this->renderPartial('/buy/address_form', array('alias'=>'Reg', 'addrModel'=>$model, 'onlyPereodic'=>0, 'existPereodic'=>0, 'showNotes'=>true));
                ?>
                <div class="address_action">
                    <a class="btn btn-success" id="send-forma" onclick="return false;"><?=$ui->item('CARTNEW_BTN_ADD_ADDRESS')?></a>
                </div>
                <link rel="stylesheet" href="/new_style/order_buy.css">
                <script>
                    $(function(){
                        scriptLoader('/new_js/modules/address.js').callFunction(function() {
                            address().init({
                                formId: 'Reg',
                                urlChangeCountry: '<?= Yii::app()->createUrl('buy/deliveryInfo') ?>',
                                urlGetCountry: '<?= Yii::app()->createUrl('buy/getCountry') ?>',
                                urlLoadStates: '<?= Yii::app()->createUrl('buy/loadstates') ?>',
                                urlRedirect: '<?= Yii::app()->createUrl('client/addresses') ?>'
                            });
                        });
                    });
                </script>

                <?php /*
            }
            else {
                $this->renderPartial('/site/address_form', array('model' => $model,
                    'mode' => $mode,
                    'afterAjax' => 'redirectToAddressList'));
            }
            */?>

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

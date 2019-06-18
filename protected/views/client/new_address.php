<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$user = User::model()->findByPk($this->uid);
?>

<div class="container cabinet">

<div class="row">
        <div class="span10">

            <div class="information info-box">
                <?= $ui->item("MSG_PERSONAL_FORM_DESCRIPTION"); ?>
            </div>

            <?php
            //if (isset($_GET['ha'])) {
                $this->renderPartial('/buy/address_form', array('alias'=>'Address', 'userType'=>'myAddr', 'addrModel'=>$model, 'onlyPereodic'=>0, 'existPereodic'=>0, 'showNotes'=>true, 'emailPlaceholder'=>$user->getAttribute('login')));
                ?>
                <div class="address_action" style="position: relative;">
                    <div class="pleasewait"><span class="fa fa-spinner fa-pulse" style="float: right; position: absolute; top: -5px;"></span></div>
                    <a class="btn btn-success" id="send-forma" onclick="return false;"><?=$ui->item(((empty($mode)||($mode != 'edit')))?'CARTNEW_BTN_ADD_ADDRESS':'ADDRESS_EDIT')?></a>
                </div>
                <link rel="stylesheet" href="/new_style/order_buy.css">
            <?php
            $userData = array();
            if (empty($mode)||($mode != 'edit')):
                $userData = array(
                    'id' => $this->uid,
                    'email'	=> $user->getAttribute('login'),
                );
            endif;
            ?>
            <script>
                    $(function(){
                        scriptLoader('/new_js/modules/address.js').callFunction(function() {
                            address().init({
                                userData: <?= json_encode($userData) ?>,
                                stateId: <?= ($model->getAttribute('state_id')?:0) ?>,
                                oldId: <?= ($model->getAttribute('id')?:0) ?>,
                                formId: 'Address',
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
<?php /*
            <script type="text/javascript">
                function redirectToAddressList(json)
                {
                    window.location.href = '<?=Yii::app()->createUrl('client/addresses'); ?>';
                }
            </script>
 */ ?>
            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>

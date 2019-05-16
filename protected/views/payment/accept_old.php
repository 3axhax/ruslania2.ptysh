<table width="100%" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top" class="text">
    <tr>
        <td class="leftmnu" width="300" valign="top" align="center">
            <div style="padding: 0 5px 0 5px;" >
                <?php $this->renderPartial('/site/login_form', array('model' => new User, 'class' => 'login2')); ?>
                <?php $this->renderPartial('/entity/_left_text'); ?>
        </td>
        <td valign="top" style="padding: 5px;">
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

            <?php if($checkResult) : ?>

                <div class="info-box information">
                    <h2><?=$ui->item('A_SAMPO_PAYMENT_ACCEPTED'); ?></h2>
                    <?=$ui->item('MSG_PAYMENT_RESULTS_ACCEPTED_2'); ?><br/>
                    <?=$ui->item('MSG_PAYMENT_RESULTS_ACCEPTED_3'); ?><br/>
                </div>

            <?php else : ?>

                <div class="info-box warning">

                    <h2><?=$ui->item('A_SAMPO_PAYMENT_DECLINED'); ?></h2>
                    <?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_2'); ?><br/>
                    <?=$ui->item('MSG_PAYMENT_RESULTS_DECLINED_3'); ?><br/>

                </div>

            <?php endif; ?>

            <!-- /content -->
            <div class="clearBoth"></div>
        </td>
        <td width="220" valign="top" style="padding-left: 10px; padding-right: 10px; padding-top: 10px;">
            <?php $this->widget('ProductOfDay'); ?>
            <?php $this->widget('Banners', array('entity' => 'index')); ?>
        </td>
    </tr>

</table>
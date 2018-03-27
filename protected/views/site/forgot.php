<div class="container">          

<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<h1 class="h1_registr">Восстановление пароля</h1>

            <?php if(isset($notFound) && $notFound) : ?>
                <div class="info-box error">
                    <h3><?=$ui->item('MSG_NO_DATA'); ?></h3>
                    <?=$ui->item('MSG_PERSONAL_REMIND_PASS_ERROR'); ?>
                </div>
            <?php endif; ?>

            <?php if(empty($user)) : ?>

                <div class="info-box information">
                    <?=sprintf($ui->item('MSG_PERSONAL_REMIND_PASS'), $ui->item('A_TITLE_REMIND_PASS')); ?>
                </div>
                <?php $this->renderPartial('_forgot_form', array('model' => $model, 'refresh' => false, 'class' => '')); ?>

            <?php else : ?>

                <div class="info-box information">
                    <?=$ui->item('MSG_PERSONAL_REMIND_PASS_SEND'); ?>
                </div>

            <?php endif; ?>
</div>
<?php /** Created by Кирилл rkv@dfaktor.ru 26.08.2019 22:41*/ ?>
<div class="container">

    <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

    <h1 class="h1_registr"><?= $ui->item('FORGOT_PASS_HEADER') ?></h1>

    <?php if(isset($notFound) && $notFound) : ?>
        <div class="info-box error">
            <h3><?=$ui->item('MSG_NO_DATA'); ?></h3>
            <?=$ui->item('MSG_PERSONAL_REMIND_PASS_ERROR'); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($isClosed) && $isClosed) : ?>
        <div class="info-box error">
            <?=$ui->item('USER_CLOSED'); ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($user)) : ?>
        <div class="info-box information">
            <div><?= $user->login ?></div>
            <div><?= $ui->item("NEW_PASSWORD_MESSAGE") ?></div>
        </div>
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'User',
//            'action'=>Yii::app()->createUrl('site/password', array('email'=>))
//            'enableAjaxValidation' => true,
//            'clientOptions' => array(
//                'validateOnChange' => false,
//                'validateOnSubmit' => true,
//            )
        )); ?>
        <?=$form->error($model, 'pwd'); ?>
        <?=$form->error($model, 'pwd2'); ?>
        <div><?= $form->passwordField($model, 'pwd', array('placeholder'=>$ui->item("regform_password"), 'autocomplete'=>'off')); ?></div>
        <div><?= $form->passwordField($model, 'pwd2', array('placeholder'=>$ui->item("regform_repeat_password"), 'autocomplete'=>'off')); ?></div>
        <div><input type="submit" class="sort order_start" value="<?=$ui->item('CHANGE'); ?>" style="background-color: #5bb75b;"/></div>

        <?php $this->endWidget(); ?>
    <?php endif; ?>
</div>

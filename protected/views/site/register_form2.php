<?php $refresh = isset($refresh) && $refresh; ?>
<? if (!$h1) { echo '<div style="height: 22px;"></div>'; } ?>
<?php if (!Yii::app()->user->isGuest): ?>

    Already registered

<?php else : ?>

<? if ($h1) { echo '<h1 class="h1_reg">Регистрация</h1>'; } ?>

    <?php $form = $this->beginWidget('KnockoutForm', array(
                                                          'model' => $model,
'class' =>'registr',                                                          'action' => Yii::app()->createUrl('site/register'),
                                                          'id' => 'user-register',
                                                          'viewModel' => 'registerVM',
                                                          'beforeAjaxSubmit' => 'beforeRegister',
                                                          'afterAjaxSubmit' => 'doRegister',
                                                     )); ?>
    <ul data-bind="foreach: errorStr">
        <li><span data-bind="text: $data"></span></li>
    </ul>
		
		
		<?= $form->textField('login', array('placeholder'=>$ui->item("regform_email"))); ?>
        <?= $form->passwordField('pwd', array('placeholder'=>$ui->item("regform_password"))); ?>
        <?= $form->passwordField('pwd2', array('placeholder'=>$ui->item("regform_repeat_password"))); ?>
        <?= $form->textField('first_name', array('placeholder'=>$ui->item("regform_firstname"))); ?>
        <?= $form->textField('last_name', array('placeholder'=>$ui->item("regform_lastname"))); ?>
            


        <div><?= $form->submitButton($ui->item('A_REGISTER'), array('class' => 'sort')); ?>
            </div>
    <script>

        function beforeRegister(vm)
        {
            $('#user-register input').change();
            return true;
        }

        function doRegister(json)
        {
            <?php if($refresh) : ?>
            window.location.reload();
            <?php else : ?>
            window.location.href = '/me';
            <?php endif; ?>
        }
    </script>
    <?php $this->endWidget(); ?>

<?php endif; ?>







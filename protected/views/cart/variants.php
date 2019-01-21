<hr /><?php $refresh = isset($refresh) && $refresh;
      $key = isset($uiKey) ? $uiKey : 'MSG_USER_LOGIN';

?>

<div class="container">
	
	<h1 style="margin-bottom: 50px; text-align: center;"><?=$ui->item('CARTNEW_TITLE_PAGE_VARIANT')?></h1>
	
    <div class="span6" style="margin-left: 50px;">
       <!--<h1 class="h1_reg" style="margin-top: 0px; margin-bottom: 25px;">Вход</h1>-->
       
        <?php
        $form = $this->beginWidget('KnockoutForm', array(
            'model' => new User,
            'action' => Yii::app()->createUrl('site/login'),
            'class' => 'registr',
            'id' => 'user-login',
            'viewModel' => 'loginVM',
            'afterAjaxSubmit' => 'doLogin',
            'beforeAjaxSubmit' => 'beforeAjax',
        ));
        ?>
       <h1 class="h1_reg" style="margin-top: 0px; margin-bottom: 25px;"><?=$ui->item('USER_LOGIN_ALT')?></h1>
        <?php
        $cls = 'login2';
        if (isset($class))
            $cls = $class;
        ?>

        <ul data-bind="foreach: errorStr" style='text-align: left'>
            <li><span data-bind="text: $data"></span></li>
        </ul>

<?= $form->textField('login', array('placeholder' => $ui->item('regform_email'))); ?>
<?= $form->passwordField('pwd', array('placeholder' => $ui->item('regform_password'))); ?><a href="<?= Yii::app()->createUrl('site/forgot'); ?>" title="<?= $ui->item('A_REMIND_PASS'); ?>"><?= $ui->item('A_REMIND_PASS'); ?></a>  <a href="<?= Yii::app()->createUrl('site/register'); ?>" title="Регистрация" style="float: right"><?= $ui->item('A_REGISTER') ?></a>

        <div style="margin-top: 10px;"><?= $form->submitButton($ui->item('A_SIGNIN'), array('class' => 'sort')); ?></div>

        <script>

            function beforeAjax(vm)
            {
                $('#user-login input').change();
                return true;
            }

            function doLogin(json)
            {
<?php if ($refresh) : ?>
                    window.location.reload();
<?php else : ?>
                    window.location.href = '<?=Yii::app()->createUrl('cart')?>doorder/';
        <?php endif; ?>
            }
        </script>
        
        
        
        
<?php $this->endWidget(); ?>
        <div class="divider"></div>
        
        
        
        
    </div>
    
    <div class="span6">
        <style> form.registr2  { width: auto; margin: 0 auto; padding: 30px 32px; background-color: #f8f8f8;} </style>
        <?php
        $form = $this->beginWidget('KnockoutForm', array(
            'model' => new User,
            'action' => Yii::app()->createUrl('site/login'),
            'class' => 'registr2',
            'id' => 'user-login',
            'viewModel' => 'loginVM',
            'afterAjaxSubmit' => 'doLogin',
            'beforeAjaxSubmit' => 'beforeAjax',
        ));
        ?>
        <h1 class="h1_reg" style="margin-top: 0px; margin-bottom: 25px;"><?=$ui->item('CARTNEW_LABEL_NO_ACCOUNT')?></h1>
        <a href="<?=Yii::app()->createUrl('cart/noregister/')?>" class="order_start" style="width: 248px; margin: 0 auto; display: block"><?=$ui->item('CARTNEW_LABEL_CONTINUE_WITHOUT_AUTHORIZATION')?></a>
        
        <?php $this->endWidget(); ?>
        
        <script src="//ulogin.ru/js/ulogin.js"></script>

        <div class="span6" style="margin-left: 0;">
    <div style="height: 20px;"></div>
    <center> <h1 class="h1_reg" style="margin-top: 0px; margin-bottom: 2px; font-size: 16px;"><?=$ui->item('CARTNEW_LOGIN_SOCIAL')?></h1></center>

    <div style="margin:0 auto; width: 260px;" id="uLogin" data-ulogin="display=panel;theme=classic;fields=first_name,last_name,email;providers=vkontakte,odnoklassniki,googleplus,facebook,twitter,instagram;redirect_uri=<?=urlencode('/ulogin.php')?>;mobilebuttons=0;"></div>
    </div>
        
    </div> <div class="clearfix"></div>
     
    <div class="clearfix"></div>





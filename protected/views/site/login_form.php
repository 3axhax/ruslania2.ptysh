<?php
$assets = Yii::getPathOfAlias('webroot') . '/protected/extensions/knockout-form/assets';
$baseUrl = Yii::app()->assetManager->publish($assets);

?>

<script type="text/javascript" src="<?= $baseUrl ?>/knockout.js"></script>
<script type="text/javascript" src="<?= $baseUrl ?>/knockout.mapping.js"></script>
<script type="text/javascript" src="<?= $baseUrl ?>/knockoutPostObject.js"></script>

<?php if (!Yii::app()->user->isGuest) return; ?>
<?php $refresh = isset($refresh) && $refresh;
      $key = isset($uiKey) ? $uiKey : 'MSG_USER_LOGIN';

?>


<div class="container">
        
<div style="text-align: center"><?=$ui->item($key); ?></div>
        
<h1 class="h1_reg"></h1>

<?php $form = $this->beginWidget('KnockoutForm', array(
                                                      'model' => $model,
                                                      'action' => Yii::app()->createUrl('site/login'),

'class' => 'registr',
                                                      'id' => 'user-login',
                                                      'viewModel' => 'loginVM',
                                                      'afterAjaxSubmit' => 'doLogin',
                                                      'beforeAjaxSubmit' => 'beforeAjax',
                                                 )); ?>
<?php $cls = 'login2';
      if(isset($class)) $cls = $class;
?>

<ul data-bind="foreach: errorStr" style='text-align: left'>
    <li><span data-bind="text: $data"></span></li>
</ul>

            <?= $form->textField('login', array('placeholder'=>$ui->item('regform_email'))); ?>
        <?= $form->passwordField('pwd', array('placeholder'=>$ui->item('regform_password'))); ?><a href="<?= Yii::app()->createUrl('site/forgot'); ?>" title="<?=$ui->item('A_REMIND_PASS'); ?>"><?=$ui->item('A_REMIND_PASS'); ?></a>
        
            <div style="margin-top: 10px;"><?= $form->submitButton($ui->item('A_SIGNIN'), array('class' => 'sort')); ?></div>
    
<script>

    function beforeAjax(vm)
    {
        $('#user-login input').change();
        return true;
    }

    function doLogin(json)
    {
        <?php if($refresh) : ?>
        window.location.reload();
        <?php else :
            $url = Yii::app()->createUrl('client/me');
            $referer = Yii::app()->getRequest()->getUrlReferrer();
            if (!empty($referer)) {
                $request = new MyRefererRequest();
                $request->setFreePath($referer);
                $refererRoute = Yii::app()->getUrlManager()->parseUrl($request);
                if (mb_strpos($refererRoute, 'passwordok', null, 'utf-8') === false) {
                    $url = $_SERVER['HTTP_REFERER'];
                }
            }
        ?>
        window.location.href = '<?= $url ?>';
        <?php endif; ?>
    }
</script>
<?php $this->endWidget(); ?>
<div class="divider"></div>

</div>







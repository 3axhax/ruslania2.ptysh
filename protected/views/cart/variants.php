<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container">

    <?php $form = $this->beginWidget('KnockoutForm', array(
                                                          'model' => $model,
'class' =>'registr',                                                          'action' => '/site/register',
                                                          'id' => 'user-register',
                                                          'viewModel' => 'registerVM',
                                                          'beforeAjaxSubmit' => 'beforeRegister',
                                                          'afterAjaxSubmit' => 'doRegister',
                                                     )); ?>

    <a href="/cart/register/" class="order_start" style="width: 248px; margin-bottom: 20px;">Я покупаю впервые</a>
    <a href="/cart/doorder/" class="order_start" style="width: 248px">Я уже покупал</a>

<?php $this->endWidget(); ?>


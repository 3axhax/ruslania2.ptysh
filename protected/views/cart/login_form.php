<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container">

            <?php $this->renderPartial('/site/login_form', array('model' => new User,
                                                                 'uiKey' => 'A_LEFT_PERSONAL_LOGIN',
                                                                 'class' => '',
                                                                 'refresh' => true)); ?>
        
		</div>


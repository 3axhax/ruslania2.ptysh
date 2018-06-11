<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container">

           

            <div class="center" >
                        <?= Yii::app()->ui->item('A_LEFT_PERSONAL_REGISTRATION'); ?>
                   </div>
            <?php $this->renderPartial('/site/register_form3', array('model' => new User, 'refresh' => true, 'h1'=>true)); ?>
        
		</div>


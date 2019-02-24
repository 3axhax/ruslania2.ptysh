<?php $ui = Yii::app()->ui;
?><!DOCTYPE html><html>
<head><?php $this->renderPartial('/layouts/head'); ?></head>
<body>
<div class="header_logo_search_cart">
    <div class="container">
        <div class="row">
            <div class="span1 logo">
                <a href="<?= Yii::app()->createUrl('site/index') ?>"><img src="/new_img/logo.png" alt=""/></a>
            </div>
            <a href="<?= Yii::app()->createUrl('cart/view') ?>" style="float: right; margin-top: 50px;"><?=$ui->item('CARTNEW_BACK_TO_CART')?></a>
            <div class="span10" style="margin-top: 40px;">
                <div class="pult">
                    <ul>
                       <li class="langs"><?php $this->renderPartial('/layouts/select_langs'); ?></li>
                       <li class="valuts"><?php $this->renderPartial('/layouts/select_valuts'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 10px;"></div>
</div>
<?= $content; ?>
</body>
</html>
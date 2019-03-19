<?php $ui = Yii::app()->ui;
    $returnButton = $this->returnButton();
?><!DOCTYPE html><html>
<head><?php $this->renderPartial('/layouts/head'); ?></head>
<body>
<div class="header_logo_search_cart">
    <div class="container">
        <div class="row">
            <div class="span1 logo">
                <a href="<?= Yii::app()->createUrl('site/index') ?>"><img src="/new_img/logo.png" alt=""/></a>
            </div>
            <a href="<?= $returnButton['href'] ?>" style="float: right; margin-top: 50px;"><?= $returnButton['name'] ?></a>
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
<script type="text/javascript" src="/js/common.js"></script>
</body>
</html>
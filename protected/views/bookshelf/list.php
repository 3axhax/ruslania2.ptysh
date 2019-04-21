<table width="100%" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top">
    <tr>
        <td class="leftmnu" id="js_leftmnu" width="20%" valign="top">
            <?php //$this->renderPartial('/entity/_left_text'); ?>
            <?php //$this->renderPartial('/site/login_form', array('model' => new User)); ?>
        </td>
        <td valign="top" style="padding: 5px;">
            <!-- content -->
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
            /*?>
            <!-- /content -->

            <h3 style="font-size: 18px; margin-left: 10px;"><?=$ui->item('BOOKSHELF_LIST'); ?>:</h3>
            */ ?>
            <h1 class="titlename poht" style="margin-bottom: 20px;"><?php
                $breadcrumbs = $this->breadcrumbs;
                $h1 = array_pop($breadcrumbs);
                unset($breadcrumbs) ;
                $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
                if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . Yii::app()->ui->item('PAGES_N', $page);
                ?><?= $h1 ?></h1>
            <?php

            $models = $dp->getData();
            if(!empty($models)) : ?>
                <table class="table table-triped">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($models as $model) : ?>
                        <tr>
                        <td><?=Yii::app()->dateFormatter->format('dd MMM yyyy', $model['date_of']); ?></td>
                        <td><a title="<?=CHtml::encode($model['title']); ?>" href="<?=Yii::app()->createUrl('/bookshelf/view', array('id' => $model['bookshelf_id'])); ?>"><?=CHtml::encode($model['title']); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php $this->widget('MyLinkPager', array('pages' => $dp->getPagination(),
                    'header' => sprintf(Yii::app()->ui->item('PAGES'), ''),
                    'nextPageLabel' => '',
                    'prevPageLabel' => '',
                    'firstPageLabel' => '',
                    'lastPageLabel' => '',
                    'separator' => '<span class="mainpg2"> | </span>',
                    'htmlOptions' => array('class' => 'pager')));

                ?>

            <?php else : ?>

            <p>No data</p>

            <?php endif; ?>
        </td>
        <td width="220" valign="top" style="padding-left: 10px; padding-right: 10px; padding-top: 10px;">
            <?php $this->widget('Banners', array('entity' => 'index')); ?>
        </td>
    </tr>
</table>
<?php $this->renderPartial('/entity/_social_widgets'); ?>
<?php $this->renderPartial('/entity/_we_recommend'); ?>
<?php $this->renderPartial('/site/login_form', array('model' => new User)); ?>
<?php /*
<div style="display: none">
    <div id="js_for-leftmnu">
        <?php $this->renderPartial('/entity/_left_text'); ?>
        <?php $this->renderPartial('/site/login_form', array('model' => new User)); ?>
    </div>
    <script>(function(d) {
        var $leftContent = $('#js_for-leftmnu');
        var $leftContainer = $('#js_leftmnu');
        $leftContainer.prepend($leftContent.html())
        $leftContent.parent().remove();
    }(document));</script>
</div>
 */ ?>
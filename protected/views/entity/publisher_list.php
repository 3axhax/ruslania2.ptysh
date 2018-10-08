<?php Yii::app()->clientScript->registerScriptFile('/js/jquery.autocolumnlist.js'); ?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top">
    <tr>
        <td class="leftmnu" width="20%" valign="top">
            <div style="padding: 0 5px 0 5px;">

                <?php $this->renderPartial('/entity/_left_menu', array('entity' => $entity)); ?>
                <?php $this->renderPartial('/site/login_form', array('model' => new User)); ?>

            </div>
        </td>
        <td valign="top" style="padding: 5px;">
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

            <h1 class="titlename poht" style="margin-bottom: 20px;"><?php
                $breadcrumbs = $this->breadcrumbs;
                $h1 = array_pop($breadcrumbs);
                unset($breadcrumbs) ;
                $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
            ?><?= $h1 ?></h1>
            <div class="text">
                Страницы:
                <?php foreach($abc as $item) : ?>
                <a href="<?=Yii::app()->createUrl('entity/publisherlist',
                    array('entity' => Entity::GetUrlKey($entity), 'char' => $item['first_'.$lang])); ?>"
                   ><?=$item['first_'.$lang]; ?></a> |
                <?php endforeach; ?>
            </div>

            <div class="text">
                <ul id="al" class="list">
                    <?php foreach($list as $item) : ?>
                        <?php $title = $item['title_'.$lang]; ?>
                        <li style="margin-bottom: 10px;"><a href="<?=Yii::app()->createUrl('entity/bypublisher',
                            array('entity' => Entity::GetUrlKey($entity),
                                  'pid' => $item['id'],
                                  'title' => $title
                            )); ?>" title="<?=$title; ?>"><?=$title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- /content -->
        </td>
        <td width="220" valign="top" style="padding-left: 10px; padding-right: 10px; padding-top: 10px;">
            <?php $this->widget('Banners', array('entity' => $entity)); ?>
        </td>
    </tr>

</table>
<script type="text/javascript">

    $(document).ready(function()
    {
        $('#al').autocolumnlist({ columns: 3});
    });

</script>
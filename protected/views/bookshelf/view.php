<table width="100%" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top">
    <tr>
        <td class="leftmnu" id="js_leftmnu" width="20%" valign="top">
            <?php //$this->renderPartial('/entity/_left_text'); ?>
            <?php //$this->renderPartial('/site/login_form', array('model' => new User)); ?>
        </td>
        <td valign="top" style="padding: 5px;" width="80%">
            <!-- content -->
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
            <!-- /content -->

            <table width="100%" cellspacing="0" cellpadding="0" style="vertical-align: top;">
                <tr>
                    <td width="35%" valign="top" style="padding-right: 20px;">
                        <h2><?=CHtml::encode($model['title']); ?></h2>
                        <p><?=nl2br(CHtml::encode($model['small_desc'])); ?></p>

                        <audio controls>
                            <source src="http://ruslania.com/download/<?=$model['file_name']; ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>

                        <p><?=nl2br(CHtml::encode($model['info'])); ?></p>
                    </td>
                    <td width="65%" valign="top">

                        <?php foreach($groups as $group=>$data): ?>
                            <table height="30" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top" class="text">
                                <tr>
                                    <td colspan="3">
                                        <div class="itemsep1">&nbsp;</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="31" class="maintxt" style="padding-top: 2px;padding-bottom: 2px;padding-left: 2px;padding-right: 5px;">
                                        <span class="entity_icons"><i class="fa e<?= $data['entity'] ?>"></i></span>
                                        <?php /*<img width="31" height="31" border="0" src="/pic1/cart_ibook.gif"> */ ?>
                                    </td>
                                    <td width="100%" class="maintxt" style="padding: 2px;"><a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($data['entity']))); ?>" class="ctitle"><?=Entity::GetTitle($data['entity']); ?></a></td>
                                </tr>
                            </table>
                            <div class="listgoods bookshelf">
                            <ul class="items">
                                <?php foreach($data['items'] as $item) : ?>
                                    <?php $item['entity'] = $data['entity']; ?>
                                    <li>
                                        <?php $this->renderPartial(/*'_bookshelf_item'*/'/entity/_common_item_2',
                                            array('item' => $item,
                                                'entity' => $data['entity'], 'isList' => true)); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            </div>

                        <?php endforeach; ?>



                    </td>
                </tr>
            </table>

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
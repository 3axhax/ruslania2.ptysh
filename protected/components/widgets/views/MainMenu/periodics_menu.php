<?php /*Created by Кирилл (02.10.2018 23:30)*/ ?>
<div class="click_arrow"></div>
<a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))); ?>"><?= Yii::app()->ui->item("A_GOTOPEREODICALS"); ?></a>

<div class="dd_box_bg list_subcategs" style="left: -280px;">

    <div class="span10 mainmenu-periodics">
        <div style="float:left;width:250px;">
            <ul>
                <li style="margin-bottom: 10px;">
                    <a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'type' => 2)) ?>">
                        <span class="title__bold"><?= Yii::app()->ui->item('PERIODIC_TYPE_PLURAL_2') ?></span>
                    </a>
                </li>
                <?php foreach ($availCategory2 as $id=>$name):
                    if (!empty($availCategory[$id])&&!empty($availCategory[$id]['avail_items_type_2'])):
                        if (empty($name)) $name = ProductHelper::GetTitle($availCategory[$id]);
                        else $name = Yii::app()->ui->item($name);
                        ?>
                        <li>
                            <a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'cid' => $id, 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($availCategory[$id])), 'binding' => array(2))); ?>"><?= $name ?></a>
                        </li>
                    <?php endif; endforeach; ?>
                <li>
                    <a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $printed['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($printed)))) ?>"><?= ProductHelper::GetTitle($printed) ?></a>
                </li>
            </ul>
        </div>
        <div style="margin-left: 250px">
            <ul>
                <li style="margin-bottom: 10px;">
                    <a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'type' => 1)) ?>">
                        <span class="title__bold"><?= Yii::app()->ui->item('PERIODIC_TYPE_PLURAL_1') ?></span>
                    </a>
                </li>
                <?php foreach ($availCategory1 as $id=>$name):
                    if (!empty($availCategory[$id])&&!empty($availCategory[$id]['avail_items_type_1'])):
                        if (empty($name)) $name = ProductHelper::GetTitle($availCategory[$id]);
                        else $name = Yii::app()->ui->item($name);
                        ?>
                        <li>
                            <a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'cid' => $id, 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($availCategory[$id])), 'binding' => array(1))); ?>"><?= $name ?></a>
                        </li>
                    <?php endif; endforeach; ?>
                <li style="margin-bottom: 10px;margin-top: 15px;">
                    <a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'type' => 3)) ?>">
                        <span class="title__bold"><?= Yii::app()->ui->item('PERIODIC_TYPE_PLURAL_3') ?></span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
        <div style="margin-top: 15px;">
            <ul>
                <li id="periodic_category">
                    <a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))) ?>"><?= Yii::app()->ui->item('A_NEW_ALL_CATEGORIES_PERIODICS'); ?></a>
                </li>
                <li>
                    <a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'cid' => 100, 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($availCategory[100])))); ?>"><?= Yii::app()->ui->item($availCategorySale[100]) ?></a>
                </li>
                <li>
                    <a href="<?= Yii::app()->createUrl('entity/gift', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))) ?>"><?= Yii::app()->ui->item('A_NEW_PERIODIC_FOR_GIFT'); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>



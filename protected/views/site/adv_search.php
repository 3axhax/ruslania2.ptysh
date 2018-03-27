
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container content_books">
            <?php $this->renderPartial('_adv_search_form'); ?>

            <div class="text" style="margin-top: 7px;">
                <?= sprintf($ui->item("X items found"), $paginatorInfo->getItemCount()); ?>
            </div>

            <?php $this->widget('MyLinkPager', array('pages' => $paginatorInfo,
                                                     'header' => sprintf(Yii::app()->ui->item('PAGES'), ''),
                                                     'nextPageLabel' => '',
                                                     'prevPageLabel' => '',
                                                     'firstPageLabel' => '',
                                                     'lastPageLabel' => '',
                                                     'separator' => '<span class="mainpg2"> | </span>',
                                                     'htmlOptions' => array('class' => 'pager')));

            ?>
<div class="listgoods span10" style="margin-left: 0;">
            <ul class="items">
                <?php foreach ($items as $i) : ?>
                    <li>
                        <?php $this->renderPartial('/entity/_common_item_2', array('item' => $i,
                                                                                 'isList' => true,
                                                                                 'entity' => $i['entity'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
</div>
            


      </div>
           
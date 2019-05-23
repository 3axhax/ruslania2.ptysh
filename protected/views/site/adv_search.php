
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container content_books">
    <h1 class="titlename poht" style="margin-bottom: 20px;"><?php
        $breadcrumbs = $this->breadcrumbs;
        $h1 = array_pop($breadcrumbs);
        unset($breadcrumbs) ;
        $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
        if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . $ui->item('PAGES_N', $page);
        ?><?= $h1 ?><?php
        $isHide = false;
        if (!empty($items)):
            $isHide = true; ?>
        <div class="fa fa-angle-down" onclick="$('#advsearch').show(); $(this).hide();" style="cursor: pointer; font-size: 26px; color: #ccc; opacity: 1;"></div>
        <?php endif; ?>
    </h1>
    <?php ?>
            <?php $this->renderPartial('_adv_search_form', array('isHide'=>$isHide)); ?>

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
           
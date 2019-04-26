<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container content_books">
<div class="row">
		<div class="listgoods span10">
            <div class="text" style="margin-top: 7px;">
                <?= sprintf($ui->item("X items found"), $paginatorInfo->getItemCount()); ?>
                <div class="red_checkbox" onclick="check_search($(this), 'js_avail'); $('#srch').append('<input type=\'hidden\' name=\'e\' value=\'<?= $eid ?>\'>').submit(); " style="float: right;">
                    <span class="checkbox">
                        <span class="check<?= ($this->GetAvail(1))?' active':'' ?>"></span>
                    </span>
                    <?= $ui->item('A_NEW_SEARCH_AVAIL'); ?>
                </div>
            </div>
            <ul class="items">
                <?php foreach ($products as $i) : ?>
                    <li>
                        <?php $this->renderPartial('/entity/_common_item_2', array('item' => $i, 'isList' => true, 'entity' => $i['entity'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (count($products) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
        </div>
    <div class="span2" id="js_rightSidebar">
        <?php if (!empty($abstractInfo)): ?>
            <div class="js_fixedScroll" style="margin-bottom: 20px;">
                <?php $dataForSearch = array('q'=>$q,'avail'=>$this->GetAvail(1));
                foreach ($abstractInfo as $eNum=>$counts): $dataForSearch['e'] = $eNum; ?>
                    <div class="row_category"><?= $q ?> <span><?= $ui->item('A_NEW_SEARCH_IN_CAT') ?> <?= Entity::GetTitle($eNum) ?></span> <a href="<?= Yii::app()->createUrl('search/general') ?>?<?= http_build_query($dataForSearch) ?>" class="result_search_count"><?= $counts ?></a></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($items)) : ?>
        <div class="js_fixedScroll">
            <p><?=$ui->item('DID_YOU_MEAN'); ?></p>
            <ul class="items">
                <?php foreach ($items as $i) : ?>
                    <li>
                        <a href="<?= $i['url']; ?>"><?= $i['title']; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php $this->widget('YouView', array()); ?>
    </div>
</div>
</div>

<script type="text/javascript">
    $(function(){
        scriptLoader('/new_js/modules/fixedScroll.js').callFunction(function(){
            var contentBlock = document.getElementById('js_rightSidebar');
            fixedScroll().init({
                block: $('.js_fixedScroll:first').get(0),
                $blocks: $('.js_fixedScroll'),
                $otherBlocks: $(contentBlock).children(':not(.js_fixedScroll)'),
                contentBlock: contentBlock,
                stopBlock: $('.footer').get(0)
            });
        });
    });
</script>
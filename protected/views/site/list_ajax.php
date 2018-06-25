<h1 class="titlename"><?=((!$cid) ? $ui->item('A_NEW_GOODS_RAZD_TITLE') . ': ' . Entity::GetTitle($entity) : $ui->item('A_NEW_GOODS_CAT_TITLE') . ': ' . $title_cat); ?></h1>

<?php
$lang = Yii::app()->language;
?>

<div class="top-filters">
    <?php $this->widget('TopFilters', array(
        'lang' => $lang,
        'entity' => $entity,
        'cid' => $cid)); ?>
</div>
<div class="sortbox" style="float: right;">
    <?=$ui->item('A_NEW_FILTER_SORT_FOR')?>
    <?php
    $value = SortOptions::GetDefaultSort(Yii::app()->getRequest()->getParam('sort'));
    $this->widget('SelectSimulator', array('items'=>SortOptions::GetSortData(), 'paramName'=>'sort', 'selected'=>$value, 'style'=>'float:right; margin-left:10px;'));
    ?>
            </div>
			
			<div class="sortbox langsel">
                <?php $this->widget('SelectSimulator', array('items'=>ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id), 'paramName'=>'lang')); ?>
            </div>

<ul class="items">
    <?php foreach ($items as $item) : ?>
        <?php
        $item['entity'] = $entity;
        $key = 'itemlist_' . $entity . '_' . $item['id'];
        ?>
        <li>
            <?php $this->renderPartial('/entity/_common_item_2', array('item' => $item, 'entity' => $entity, 'isList' => true)); ?>
        </li>
    <?php endforeach; ?>
</ul>


<?php if (count($items) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
<!--</div>-->
<!--</div>-->

<!--<h1 class="titlename"><?/*=((!$cid) ? $ui->item('A_NEW_GOODS_RAZD_TITLE') . ': ' . Entity::GetTitle($entity) : $ui->item('A_NEW_GOODS_CAT_TITLE') . ': ' . $title_cat); */?></h1>

<?php
/*$lang = Yii::app()->language;
*/?>

<div class="top-filters">
    <?php /*$this->widget('TopFilters', array(
        'filters' => $filters,
        'lang' => $lang,
        'entity' => $entity,
        'title_cat' => $title_cat,
        'filter_data' => $filter_data,
        'cid' => $cid)); */?>
</div>
<div class="sortbox" style="float: right;">
    <?/*=$ui->item('A_NEW_FILTER_SORT_FOR')*/?>
    <?php
/*    $value = SortOptions::GetDefaultSort(Yii::app()->getRequest()->getParam('sort'));
    $dataParam = ['entity' => Entity::GetUrlKey($_GET['entity'])];
    $this->widget('SelectSimulator', array(
            'items'=>SortOptions::GetSortData(),
            'paramName'=>'sort',
            'selected'=>$value,
            'dataParam'=>$dataParam,
            'route'=>'refererPage',
            'style'=>'float:right; margin-left:10px;'));
    */?>
            </div>
			
			<div class="sortbox langsel">
                <?php /*$dataParam = ['entity' => Entity::GetUrlKey($_GET['entity'])];
                $this->widget('SelectSimulator', array(
                        'items'=>ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id),
                        'paramName'=>'lang',
                        'dataParam'=>$dataParam,
                        'route'=>'refererPage'));*/?>
            </div>
-->
    <?php foreach ($items as $item) : ?>
        <?php
        $item['entity'] = $entity;
        $key = 'itemlist_' . $entity . '_' . $item['id'];
        ?>
        <li>
            <?php $this->renderPartial('/entity/_common_item_2', array('item' => $item, 'entity' => $entity, 'isList' => true)); ?>
        </li>
    <?php endforeach; ?>


<?php if (count($items) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
<script>
    initAAddCart();
</script>
<?php if ($entity == 30):?>
<script>
    initPeriodicPriceSelect();
</script>
<?php endif;?>
<!--</div>-->
<!--</div>-->

<?php  /**@var $this MyController*/
$siteLang = (isset(Yii::app()->language) && Yii::app()->language != '') ? Yii::app()->language : 'ru';
$lang = Yii::app()->language;
?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?><div class="container content_books">
    <div class="row">
        <div class="span10 listgoods" style="float: right;">

            <h1 class="titlename"><?=((!$cid) ? $ui->item('A_NEW_GOODS_RAZD_TITLE') . ': ' . Entity::GetTitle($entity) : $ui->item('A_NEW_GOODS_CAT_TITLE') . ': ' . $title_cat); ?><?php if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1): ?> &ndash; <?= $ui->item('PAGES_N', $page) ?> <?php endif; ?></h1>
			
			<? if ($entity == 100) : ?>
			Ведётся оптимизация раздела...
			<? else : ?>

            <div class="top-filters">
                <?php $this->widget('TopFilters', array(
                        'filters' => $filters,
                        'lang' => $lang,
                        'entity' => $entity,
                        'title_cat' => $title_cat,
                        'filter_data' => $filter_data,
                        'cid' => $cid)); ?>
            </div>

            <div class="sortbox" style="float: right;">
                <?php //if (isset($_GET['ha'])): ?>
                    <?=$ui->item('A_NEW_FILTER_SORT_FOR')?>
                    <?php
                    $value = SortOptions::GetDefaultSort(Yii::app()->getRequest()->getParam('sort'));
                    $this->widget('SelectSimulator', array(
                            'items'=>SortOptions::GetSortData(),
                            'paramName'=>'sort',
                            'selected'=>$value,
                            'dataParam'=>$_GET,
                            'route'=>'curPage',
                            'style'=>'float:right; margin-left:10px;'));
                    ?>
                <?php /*else: ?>
                <form method="GET">
                    <?=$ui->item('A_NEW_FILTER_SORT_FOR')?> <?php $value = SortOptions::GetDefaultSort(@$_GET['sort']) ?>
                    <?= CHtml::dropDownList('sort', $value, SortOptions::GetSortData(), array('onchange' => '$(this).parent().submit()', 'style'=>'width: auto;')); ?>
					
					<? if (Yii::app()->getRequest()->getParam('lang')) : ?>
					
					<input type="hidden" name="lang" value="<?=Yii::app()->getRequest()->getParam('lang')?>"/>
					
					<? endif; ?>
					
                </form>
                <?php endif; */?>
            </div>
			<div class="sortbox langsel">
                <?php $this->widget('SelectSimulator', array(
                    'items'=>ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id),
                    'paramName'=>'lang',
                    'dataParam'=>$_GET,
                    'route'=>'curPage')
                ); ?>
            </div>
			<div style="margin: 5px 0 ;">
			<?//=sprintf($ui->item('X items here'), $total)?>
			</div>

            <?php
                if (isset($presentation)) {
                    preg_match('/([\w,\s-]+)\.[A-Za-z]{3}/', $presentation, $f);
                    $fileName = $f[1];
                    if (file_exists(__DIR__.'/authors/'.$fileName.'.php')) {
                        $this->renderPartial('/entity/authors/' . $fileName);
                    }
                }
                ?>

            <ul class="items">
                <?php $i=0; foreach ($items as $item) : $i++;?>
                    <?php

                    $item['entity'] = $entity;
                    $key = 'itemlist_' . $entity . '_' . $item['id'];
                    ?>
                    <li>
                        <?php $this->renderPartial('/entity/_common_item_2', array('item' => $item, 'entity' => $entity, 'isList' => true)); ?>
                    </li>
                    <?php if ($i == 2): ?>
                        <li class="list-banner-content"><?php $this->widget('Banners', array('location'=>'topInList')) ?></li>
                    <?php elseif ($i == 20): ?>
                        <li class="list-banner-content"><?php $this->widget('Banners', array('location'=>'centerInList')) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>

		

            <?php if (count($items) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
            </ul>
			<? endif; ?>
			
		</div>
        <div class="span2">
<?php $this->widget('LinksToList', array('entity'=>$entity)); ?>
<?php $this->widget('LeftCategories', array('entity'=>$entity, 'cid'=>$cid, 'catTitle'=>$title_cat)); ?>
		</div>
    </div>
</div>
<script>
    $(document).ready(function () {
        //это для случая, когда нет js
        $('.js_without').toggle();
    });
    initAAddCart();
</script>
<?php if ($entity == 30):?>
    <script>
        initPeriodicPriceSelect();
    </script>
<?php endif;?>
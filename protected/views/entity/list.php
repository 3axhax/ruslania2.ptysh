<?php  /**@var $this MyController*/
$siteLang = (isset(Yii::app()->language) && Yii::app()->language != '') ? Yii::app()->language : 'ru';
$lang = Yii::app()->language;
$productModel = new Product();
?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?><div class="container content_books">
    <div class="row">
        <div class="span10 listgoods" style="float: right;">
<?php $h1 = Seo_settings::get()->getH1();
if (empty($h1)): ?>
    <h1 class="titlename"><?=((!$cid) ? '' . Entity::GetTitle($entity) : $title_cat); ?><?php if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1): ?> &ndash; <?= $ui->item('PAGES_N', $page) ?> <?php endif; ?></h1>
<?php else: ?>
    <h1 class="titlename"><?= $h1 ?></h1>
<?php endif; ?>
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
                        'total' => $total,
                        'cid' => $cid)); ?>
            </div>

            <div class="sortbox sortfield" style="float: right;">
                <?php //if (isset($_GET['ha'])): ?>
                <div class="sort_lable"><?=$ui->item('A_NEW_FILTER_SORT_FOR')?></div>
                    <?php
                    $filterData = FilterHelper::getFiltersData($entity, $cid);
                    $value = SortOptions::GetDefaultSort(Yii::app()->getRequest()->getParam('sort', isset($filterData['sort'])?$filterData['sort']:null));
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
                <?php if ($entity != Entity::SHEETMUSIC /*&& $entity != Entity::PERIODIC*/
                    && $entity != Entity::MUSIC && $entity != Entity::VIDEO
                    /*&& $entity != Entity::PRINTED*/ && $entity != Entity::SOFT):?>
                <?php $this->widget('SelectSimulator', array(
                    'items'=>ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id),
                    'paramName'=>'lang',
                    'dataParam'=>$_GET,
                    'route'=>'curPage')
                ); ?>
                <?php endif;?>
            </div>

			<div style="margin: 5px 0 ;">
			<?//=sprintf($ui->item('X items here'), $total)?>
			</div>
			
			<?//=$entity?>
			
            <ul class="items">
                <?php //$start = microtime_float();
                $i=0; foreach ($items as $item) : $i++;?>
                    <?php

                    $item['entity'] = $entity;
                    $key = 'itemlist_' . $entity . '_' . $item['id'];
                    ?>
                    <li>
                        <?php
                        if ($entity == Entity::PERIODIC) $this->renderPartial('/entity/list/periodics', array('item' => $item, 'entity' => $entity, 'isList' => true, 'productModel' => $productModel));
                        else { 
						
						switch ($entity) {
							
							case 40 : $prefix = '_40'; break;
							
							default: $prefix = ''; break;
							
							
						}
						
						//$prefix = '';
						
						$this->renderPartial('/entity/_common_item_2'.$prefix, array('item' => $item, 'entity' => $entity, 'isList' => true)); }
                        ?>
                    </li>
                    <?php if ($i == 2): ?>
                        <?php $this->widget('Banners', array('location'=>'topInList', 'entity' => $entity, 'page'=>(int) Yii::app()->getRequest()->getParam('page'))) ?>
                    <?php elseif ($i == 20): ?>
                        <?php $this->widget('Banners', array('location'=>'centerInList', 'entity' => $entity, 'page'=>(int) Yii::app()->getRequest()->getParam('page'))) ?>
                    <?php endif; ?>
                <?php endforeach;
                //var_dump(array(number_format((microtime_float() - $start), 4) . ' сек'));
                ?>

		

            <?php if (count($items) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
            </ul>
			<? endif; ?>
            <?php if (!empty($presentation)&&file_exists($presentation)):
                $fileText = str_replace('ruslania.com/templates-html/', 'ruslania.com/pictures/templates-html/', file_get_contents($presentation));
                ?>
                <div class="description_container"><?= $fileText ?></div>
            <?php endif; ?>
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
</script>
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
                    $this->widget('SelectSimulator', array('items'=>SortOptions::GetSortData(), 'paramName'=>'sort', 'selected'=>$value, 'dataParam'=>$_GET, 'route'=>'entity/list', 'style'=>'float:right; margin-left:10px;'));
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
                <?php if (!isset($_GET['ha'])): ?>
                    <?php $this->widget('SelectSimulator', array('items'=>ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id), 'paramName'=>'lang', 'dataParam'=>$_GET, 'route'=>'entity/list')); ?>
                <?php else: ?>
                <form method="GET">
                    <?= CHtml::dropDownList('lang', (int) Yii::app()->getRequest()->getParam('lang'), ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id), array('onchange' => '$(this).closest(\'form\').submit()', 'style'=>'width: auto;')); ?>
					<?php if ($sort = Yii::app()->getRequest()->getParam('sort')) : ?>
					<input type="hidden" name="sort" value="<?=$sort?>"/>
					<?php endif; ?>
                </form>
                <?php endif; ?>
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
            </ul>
		

            <?php if (count($items) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
			
			<? endif; ?>
			
		</div>
        <div class="span2">
<?php $this->widget('LinksToList', array('entity'=>$entity)); ?>
<?php $this->widget('LeftCategories', array('entity'=>$entity, 'cid'=>$cid, 'catTitle'=>$title_cat)); ?>

			<?php /*if (!empty($categoryList)) : ?>
                <h2 class="cattitle">Категории:</h2>
                <ul class="left_list divider">

                    <li><a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cid)) ?>">
                            <span class="title__bold"><?=((!$cid) ? Entity::GetTitle($entity) : $title_cat); ?></span>
                        </a>
                    </li>
                    <?php

                    function getSubCategoryes($entity, $cid, $lvl = 1) {

                        $rows = Category::exists_subcategoryes($entity, $cid);

                        if (count($rows)) {

                            echo '<ul style="margin-right: 20px;" class="subcat sc' . $cid . ' lvlcat'.$lvl.'" rel="' . $cid . '">';
                            foreach ($rows as $cat) :
                                echo '<li>';
                                if (count(Category::exists_subcategoryes($entity, $cat['id']))) {
                                    echo '<a href="javascript:;" class="open_subcat subcatlvl'.($lvl+1).'" onclick="show_sc($(\'ul.sc' . $cat['id'] . '\'), $(this), '.($lvl+1).')"></a>';
                                }
                                echo '<a href="' . Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)))) . '">' . ProductHelper::GetTitle($cat) . '</a>';


								getSubCategoryes($entity, $cat['id'], $lvl + 1);
                                echo '</li>';

                            endforeach;

                            echo '</ul>';
                        }
                    }

                    foreach ($categoryList as $cat) :
                        ?>
                        <li>
                            <? if (count(Category::exists_subcategoryes($entity, $cat['id']))) {?>
                            <a href="javascript:;" class="open_subcat subcatlvl1" onclick="show_sc($('ul.sc<?= $cat['id'] ?>'), $(this), 1)"></a>
                            <?} ?>
                            <a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($cat)))); ?>"><?= ProductHelper::GetTitle($cat); ?></a>



                            <!--                        (--><?//=$cat['items_count']; ?><!-- / --><?//=$cat['avail_items_count']; ?><!--)-->
							<?getSubCategoryes($entity, $cat['id'], 1);?>
                        </li>

                    <?php endforeach; ?>
                </ul>

				<a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey($entity))); ?>" class="order_start" style="width: 100%"><?=$ui->item('A_NEW_VIEW_ALL_CATEGORY'); ?></a>

                <div style="height: 47px"></div>
            <?php endif; */?>

            <h2 class="filter"><?=$ui->item('A_NEW_SETTINGS_FILTER'); ?>:</h2>

            <form method="get" action="" class="filter-old">

                <input type="hidden" name="lang" class="lang" value="<?= Yii::app()->getRequest()->getParam('lang'); ?>"/>
                <input type="hidden" name="entity_val" class="entity_val" value="<?= $entity ?>"/>
                <input type="hidden" name="cid_val" class="cid_val" value="<?= $cid ?>"/>
                <input type="hidden" name="sort" class="sort" value="<?= (Yii::app()->getRequest()->getParam('sort')) ? Yii::app()->getRequest()->getParam('sort') : 12 ?>"/>
                <div class="form-row">
                    <?php
                        $title_search = $ui->item('A_NEW_SEARCH_CAT').': "'.$title_cat.'"';
                        if($cid == 0) {
                            $title_search =  $ui->item('A_NEW_SEARCH_ENT').': "'.Entity::GetTitle($entity).'"';
                        }
                    ?>
                    <!--<label class="title"><?/*=$title_search; */?></label>
                    <input type="text" class="search inp" placeholder="<?/*=$ui->item('A_NEW_NAME_ISBN'); */?>" name="name_search" onkeyup="if ($(this).val().length > 2) { show_result_count($(this)); } else { $('.box_select_result_count').hide(1); }"/>
                    <div class="box_select_result_count">
                        <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div> <?/*=$ui->item('A_NEW_FILTER_SELECT')*/?>:
                        <span class="res_count"></span>
                        <a  href="javascript:;" onclick="show_items()"><?/*=$ui->item('A_NEW_FILTER_VIEW')*/?></a>
                    </div>-->
                </div>


                <input type="submit" value="<?= $ui->item('BTN_SEARCH_ALT') ?>" class="js_without">
            </form>



		</div>
    </div>
</div>
<script>
    $(document).ready(function () {
        //это для случая, когда нет js
        $('.js_without').toggle();
    });

</script>
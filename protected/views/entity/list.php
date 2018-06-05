<?php
$siteLang = (isset(Yii::app()->language) && Yii::app()->language != '') ? Yii::app()->language : 'ru';
?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?><div class="container content_books">
    <div class="row">
        <div class="span10 listgoods" style="float: right;">

            <h1 class="titlename"><?=((!$cid) ? $ui->item('A_NEW_GOODS_RAZD_TITLE') . ': ' . Entity::GetTitle($entity) : $ui->item('A_NEW_GOODS_CAT_TITLE') . ': ' . $title_cat); ?></h1>
			
			<? if ($entity == 100) : ?>
			Ведётся оптимизация раздела...
			<? else : ?>
			
            <div class="sortbox" style="float: right;">
                <?php //if (isset($_GET['ha'])): ?>
                    <?=$ui->item('A_NEW_FILTER_SORT_FOR')?>
                    <?php
                    $value = SortOptions::GetDefaultSort(Yii::app()->getRequest()->getParam('sort'));
                    $this->widget('SelectSimulator', array('items'=>SortOptions::GetSortData(), 'paramName'=>'sort', 'selected'=>$value, 'style'=>'float:right; margin-left:10px;'));
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
                    <?php $this->widget('SelectSimulator', array('items'=>ProductLang::getLangs($entity, empty($cat_id)?null:$cat_id), 'paramName'=>'lang')); ?>
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
                <?php foreach ($items as $item) : ?>
                    <?php

                    $item['entity'] = $entity;
                    $key = 'itemlist_' . $entity . '_' . $item['id'];
                    ?>
                    <li>
                        <?php /*echo '<pre>';
                        print_r($item['DeliveryTime']);
                        echo '</pre>';*/?>
                        <?php $this->renderPartial('_common_item_2', array('item' => $item, 'entity' => $entity, 'isList' => true)); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
		

            <?php if (count($items) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
			
			<? endif; ?>
			
		</div>
        <div class="span2">

			<?php if (!empty($categoryList)) : ?>
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
                                echo '<a href="' . Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii($cat['title_en']))) . '">' . ProductHelper::GetTitle($cat) . '</a>';


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
                            <a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title' => ProductHelper::ToAscii($cat['title_en']))); ?>"><?= ProductHelper::GetTitle($cat); ?></a>



                            <!--                        (--><?//=$cat['items_count']; ?><!-- / --><?//=$cat['avail_items_count']; ?><!--)-->
							<?getSubCategoryes($entity, $cat['id'], 1);?>
                        </li>

                    <?php endforeach; ?>
                </ul>

				<a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey($entity))); ?>" class="order_start" style="width: 100%"><?=$ui->item('A_NEW_VIEW_ALL_CATEGORY'); ?></a>

                <div style="height: 47px"></div>
            <?php endif; ?>

            <?php $lang = Yii::app()->language;?>

            <h2 class="filter"><?=$ui->item('A_NEW_SETTINGS_FILTER'); ?>:</h2>

            <form method="get" action="" class="filter">

                <input type="hidden" name="lang" class="lang" value="<?= Yii::app()->getRequest()->getParam('lang'); ?>"/>
                <input type="hidden" name="entity_val" class="entity_val" value="<?= $entity ?>"/>
                <input type="hidden" name="cid_val" class="cid_val" value="<?= $cid ?>"/>
                <input type="hidden" name="sort" class="sort" value="<?= (Yii::app()->getRequest()->getParam('sort')) ? Yii::app()->getRequest()->getParam('sort') : 12 ?>"/>
                <div class="form-row">
                    <label class="title"><?=$ui->item('A_NEW_SEARCH_CAT'); ?></label>
                    <input type="text" class="search inp" placeholder="<?=$ui->item('A_NEW_NAME_ISBN'); ?>" name="name_search" onkeyup="if ($(this).val().length > 2) { show_result_count($(this)); } else { $('.box_select_result_count').hide(1); }"/>
                    <div class="box_select_result_count">
                        <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div> <?=$ui->item('A_NEW_FILTER_SELECT')?>:
                        <span class="res_count"></span>
                        <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                    </div>
                </div>
                <?php if (!empty($authors)) { ?>
                    <div class="form-row">
                        <div class="box_select_result_count">
                            <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                            <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                            <span class="res_count"></span>
                            <a href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                        </div>
                        <label class="title"><?=$ui->item('A_NEW_FILTER_AUTHOR'); ?></label>
                            <div class="dd_box_select dd_box_select--botspace interactive_search" style="z-index: 40">
                                <div class="text">
                                    <input type="hidden" name="author" oninput="console.log(1)" value="0">
                                    <input type="text" name="new_author" class="find_author interactive_find" autocomplete="off" disabled
                                           value="Загрузка..." placeholder="Поиск автора">
                                </div>
                                <ul class="search_result search_result_author"></ul>
                            </div>
                        <script>
                            var author_search = [];
                            console.log('lang=<?=$lang?>');
                            $.ajax({
                                url: '/entity/getauthordata',
                                data: {entity: '<?=$entity?>', lang: '<?=$lang?>', cid: '<?=$cid?>'},
                                type: 'GET',
                                beforeSend: function () {
                                    $(".find_author").attr('disabled', true);
                                    $(".find_author").val('Загрузка...');
                                },
                                success: function (data) {
                                    author_search = JSON.parse(data);
                                    var search_auth = [];
                                    $.each(author_search, function(index, value) {
                                        if ((value != '') && (value != null) ) search_auth[index] = value;
                                    });
                                    interactiveSearch('.find_author', search_auth, 'author', '.search_result_author');
                                    $(".find_author").attr('disabled', false);
                                    $(".find_author").val('');
                                },
                                error: function (data) {
                                    console.log("Error response");
                                },
                            });
                        </script>

                        <!--Старый селект-->
                        <!--<div class="dd_box_select" style="z-index: 20">

                            <div class="arrow_d" onclick="$('.list_dd', $(this).parent()).toggle()"></div>
                            <input type="hidden" name="author" value="0">

                            <div class="text" onclick="$('.list_dd', $(this).parent()).toggle()">
                                <span><?/*if ($filter_data['author'] == '' OR $filter_data['author'] == '0') { echo $ui->item('A_NEW_FILTER_ALL'); } else { $row = CommonAuthor::GetById($filter_data['author']); echo $row['title_' . Yii::app()->language]; }*/?></span>
                            </div>
                            <div class="list_dd authors_dd">
                                <div class="items">
                                    <div class="rows">
                                        <div class="item" rel="0" onclick="select_item($(this), 'author')"><?/*=$ui->item('A_NEW_FILTER_ALL'); */?></div>
                                        <?php
/*                                        foreach ($authors as $author => $binfo) {
                                            $row = CommonAuthor::GetById($binfo['author_id']);
                                            if (!$row['id'] OR $row['id'] == '0')
                                                continue;
                                            $name_publ = $row['title_' . Yii::app()->language];
                                            if (!trim($name_publ))
                                                continue;

											$selact = ' selact';

											if ($row['id'] != $filter_data['author']) {
												$selact = '';
											}

                                            echo '<div class="item'.$selact.'" rel="' . $row['id'] . '" onclick="select_item($(this), \'author\')">' . $name_publ . '</div>';
                                        }
                                        */?>

                                    </div>
                                    <div class="load_items"></div>
                                </div>
                            </div>
                        </div>-->

                    </div> <?php } ?>

                <div class="form-row">
                    <div class="box_select_result_count">
                        <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                        <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                        <span class="res_count"></span>
                        <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                    </div>
                    <label class="title"><?=$ui->item('CART_COL_ITEM_AVAIBILITY')?></label>
                    <div class="dd_box_select" style="z-index: 19">

                        <div class="arrow_d" onclick="$('.list_dd', $(this).parent()).toggle()"></div>
                        <input type="hidden" name="avail" value="1">
                        <div class="text" onclick="$('.list_dd', $(this).parent()).toggle()"><span><?=(($filter_data['avail'] == 0) ? $ui->item('A_NEW_FILTER_ALL') : $ui->item('A_NEW_FILTER_AVAIL') ); ?></span></div>
                        <div class="list_dd">
                            <div class="items">
                                <div class="rows">

                                    <div class="item<?=(($filter_data['avail'] == 0) ? ' select' : '' ); ?>" rel="0" onclick="select_item($(this), 'avail')"><?=$ui->item('A_NEW_FILTER_ALL'); ?></div>
                                    <div class="item<?=(($filter_data['avail'] == 1) ? ' select' : '' ); ?>" rel="1" onclick="select_item($(this), 'avail')"><?=$ui->item('A_NEW_FILTER_AVAIL')?></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

				<? if ($langVideo) {?>

				<div class="form-row"><div class="box_select_result_count">
                        <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                        <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                        <span class="res_count"></span>
                        <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                    </div>
                    <label class="title">Язык звуковой дорожки</label>
                    <div class="dd_box_select" style="z-index: 18">

                        <div class="arrow_d" onclick="$('.list_dd', $(this).parent()).toggle()"></div>
                        <input type="hidden" name="langVideo" value="<?=(isset($filter_data['langVideo']) && ($filter_data['langVideo'] != '')) ? $filter_data['langVideo'] : 0?>" id="langVideo">
                        <div class="text" onclick="$('.list_dd', $(this).parent()).toggle()">
                            <span><?= (isset($filter_data['langVideo']) && ($filter_data['langVideo'] != false)) ? VideoAudioStream::model()->findByPk($filter_data['langVideo'])['title_'.$siteLang] : 'Язык звуковой дорожки'?></span>
                        </div>
                        <div class="list_dd">
                            <div class="items">
                                <div class="rows">
									<div class="item" rel="0" onclick="select_item($(this), 'langVideo')"><?=$ui->item('A_NEW_FILTER_ALL'); ?></div>
									 <? foreach ($langVideo as $k => $lang) { ?>
									
                                    <div class="item <?= ((isset($filter_data['langVideo'])) && ($lang['id'] == (int)$filter_data['langVideo'])) ? 'selact' : ''?>" rel="<?=$lang['id']?>" onclick="select_item($(this), 'langVideo')"><?=ProductHelper::GetTitle($lang);?></div>
									
									 <? } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

				<? } ?>

                <? if ($langSubtitles) {?>

                    <div class="form-row"><div class="box_select_result_count">
                            <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                            <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                            <span class="res_count"></span>
                            <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                        </div>
                        <label class="title">Язык субтитров</label>
                        <div class="dd_box_select" style="z-index: 17">

                            <div class="arrow_d" onclick="$('.list_dd', $(this).parent()).toggle()"></div>
                            <input type="hidden" name="langSubtitles" value="<?=(isset($filter_data['subtitlesVideo']) && ($filter_data['subtitlesVideo'] != '')) ? $filter_data['subtitlesVideo'] : 0?>" id="subtitlesVideo">
                            <div class="text" onclick="$('.list_dd', $(this).parent()).toggle()">
                                <span><?= (isset($filter_data['subtitlesVideo']) && ($filter_data['subtitlesVideo'] != false)) ? VideoSubtitle::model()->findByPk($filter_data['subtitlesVideo'])['title_'.$siteLang] : 'Язык субтитров'?></span>
                            </div>
                            <div class="list_dd">
                                <div class="items">
                                    <div class="rows">
                                        <div class="item" rel="0" onclick="select_item($(this), 'langSubtitles')"><?=$ui->item('A_NEW_FILTER_ALL'); ?></div>
                                        <? foreach ($langSubtitles as $k => $lang) { ?>

                                            <div class="item <?= ((isset($filter_data['subtitlesVideo'])) && ($lang['id'] == (int)$filter_data['subtitlesVideo'])) ? 'selact' : ''?>" rel="<?=$lang['id']?>" onclick="select_item($(this), 'langSubtitles')"><?=ProductHelper::GetTitle($lang);?></div>

                                        <? } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <? } ?>

                <? if ($formatVideo) {?>

                    <div class="form-row"><div class="box_select_result_count">
                            <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                            <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                            <span class="res_count"></span>
                            <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                        </div>
                        <label class="title">Формат Видео</label>
                        <div class="dd_box_select" style="z-index: 16">

                            <div class="arrow_d" onclick="$('.list_dd', $(this).parent()).toggle()"></div>
                            <input type="hidden" name="formatVideo" value="<?=(isset($filter_data['formatVideo']) && ($filter_data['formatVideo'] != '')) ? $filter_data['formatVideo'] : 0?>" id="formatVideo">
                            <div class="text" onclick="$('.list_dd', $(this).parent()).toggle()">
                                <?php $m = new Media()?>
                                <span><?= (isset($filter_data['formatVideo']) && ($filter_data['formatVideo'] != false)) ? $m->GetMedia(Entity::VIDEO, $filter_data['formatVideo'])['title'] : 'Формат видео'?></span>
                            </div>
                            <div class="list_dd">
                                <div class="items">
                                    <div class="rows">
                                        <div class="item" rel="0" onclick="select_item($(this), 'formatVideo')"><?=$ui->item('A_NEW_FILTER_ALL'); ?></div>
                                        <? foreach ($formatVideo as $k => $lang) { ?>

                                            <div class="item <?= ((isset($filter_data['formatVideo'])) && ($lang['id'] == (int)$filter_data['formatVideo'])) ? 'selact' : ''?>" rel="<?=$lang['id']?>" onclick="select_item($(this), 'formatVideo')"><?=$lang['title'];?></div>

                                        <? } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <? } ?>


				<? if ($filter_year[1]) : ?>
                <div class="form-row"><div class="box_select_result_count">
                        <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                        <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                        <span class="res_count"></span>
                        <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                    </div>

                    <label class="title"><?=$ui->item('A_NEW_FILTER_YEAR')?></label>

					<?
						$max_year = $filter_year[1];
						$min_year = $filter_year[0];



						if ($filter_data['ymin'] != '') {

							$filter_year[0] = $filter_data['ymin'];

						}


						if ($filter_data['ymax'] != '') {

							$filter_year[1] = $filter_data['ymax'];

						}
					?>

                    <input type="text" value="<?=($filter_year[1]-1)?>" class="inp_mini year_inp_mini inp" name="year_min"  onblur="show_result_count($(this))"/>-<input type="text" value="<?=$filter_year[1]?>" name="year_max" class="inp_max year_inp_max inp" onblur="show_result_count($(this))" />

                    <div id="slider_year"></div>



                </div>
				<? endif;?>

                <?php if (!empty($pubs)) { ?>
                    <div class="form-row"><div class="box_select_result_count">
                            <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                            <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                            <span class="res_count"></span>
                            <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                        </div>
                        <label class="title"><?=$ui->item('A_NEW_FILTER_PUBLISHER')?></label>

                        <div class="dd_box_select dd_box_select--botspace interactive_search" style="z-index: 15">
                            <div class="text">
                                <input type="hidden" name="izda" value="0">
                                <input type="text" name="new_izda" class="find_izda interactive_find" autocomplete="off" disabled
                                       value="Загрузка..." placeholder="Поиск издания">
                            </div>
                            <ul class="search_result search_result_izda"></ul>
                        </div>
                        <script>
                            var izda_search = [];
                            $.ajax({
                                url: '/entity/getizdadata',
                                data: 'entity=<?=$entity?>&lang=<?=$lang?>&cid=<?=$cid?>',
                                type: 'GET',
                                beforeSend: function () {
                                    $(".find_izda").attr('disabled', true);
                                    $(".find_izda").val('Загрузка...');
                                },
                                success: function (data) {
                                    izda_search = JSON.parse(data);
                                    var search_izd = [];
                                    $.each(izda_search, function(index, value) {
                                        if ((value != '') && (value != null) ) search_izd[index] = value;
                                    });
                                    interactiveSearch('.find_izda', search_izd, 'izda', '.search_result_izda');
                                    $(".find_izda").attr('disabled', false);
                                    $(".find_izda").val('');
                                },
                                error: function () {
                                    console.log("Error response");
                                },
                            });
                        </script>
                    </div> <?php }

                if (!empty($series)) { ?>

                    <div class="form-row"><div class="box_select_result_count">
                            <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                            <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                            <span class="res_count"></span>
                            <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                        </div>
                        <label class="title"><?=$ui->item('A_NEW_FILTER_SERIES')?></label>

                        <div class="dd_box_select dd_box_select--botspace interactive_search" style="z-index: 14">
                            <div class="text">
                                <input type="hidden" name="seria" value="0">
                                <input type="text" name="new_series" class="find_series interactive_find" autocomplete="off" disabled
                                       value="Загрузка..." placeholder="Поиск серии">
                            </div>
                            <ul class="search_result search_result_series"></ul>
                        </div>
                        <script>
                            var series_search = [];
                            $.ajax({
                                url: '/entity/getseriesdata',
                                data: 'entity=<?=$entity?>&lang=<?=$lang?>&cid=<?=$cid?>',
                                type: 'GET',
                                beforeSend: function () {
                                    $(".find_series").attr('disabled', true);
                                    $(".find_series").val('Загрузка...');
                                },
                                success: function (data) {
                                    series_search = JSON.parse(data);
                                    var search_series = [];
                                    $.each(series_search, function(index, value) {
                                        if ((value != '') && (value != null) ) search_series[index] = value;
                                    });
                                    interactiveSearch('.find_series', search_series, 'seria', '.search_result_series');
                                    $(".find_series").attr('disabled', false);
                                    $(".find_series").val('');
                                },
                                error: function () {
                                    console.log("Error response");
                                },
                            });
                        </script>

                    </div> <?php } ?>

					<? if ($filter_year[3]) : ?>


                <div class="form-row"><div class="box_select_result_count">
                        <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                        <div class="close" onclick="$(this).parent().hide()">x</div><?=$ui->item('A_NEW_FILTER_SELECT')?>:
                        <span class="res_count"></span>
                        <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                    </div>
                    <label class="title"><?=$ui->item('CART_COL_PRICE');?></label>
                    <input type="text" value="<?=$filter_year[2]?>" class="inp_mini cost_inp_mini inp" name="min_cost" onblur="show_result_count($(this))"/>-<input type="text" value="<?=$filter_year[3]?>" class="inp_max cost_inp_max inp" name="max_cost" onblur="show_result_count($(this))"/>
                    <div id="slider_cost"></div>

                   </div>
				<? endif; ?>
                <?php if (!empty($bgs)) { ?>
                    <div class="form-row bindings">
                        <div class="box_select_result_count">
                            <div class="arrow"><img src="/new_img/arrow_select.png" alt=""></div>
                            <div class="close" onclick="$(this).parent().hide()">x</div> <?=$ui->item('A_NEW_FILTER_SELECT')?>:
                            <span class="res_count"></span>
                            <a  href="javascript:;" onclick="show_items()"><?=$ui->item('A_NEW_FILTER_VIEW')?></a>
                        </div>
                        <label class="title"><?
						if ($entity == 10 OR $entity == 15) {
							 echo $ui->item('A_NEW_FILTER_TYPE1');
						} else {
							echo $ui->item('A_NEW_FILTER_TYPE2');
						}

						?></label>


                    <label><input type="checkbox" class="" name="binding_id[]" value="0" onchange="change_all_binding(event, true);show_result_count($(this));" <?= ($filter_data['binding_id']) ? '' : 'checked'?>/> Все</label>

                        <?php
                        foreach ($bgs as $bg => $binfo) {
                            $row = Binding::GetBinding($entity, $binfo['binding_id']);
                            $title = 'title_' . Yii::app()->language;
                            if ($entity == 22 OR $entity == 24) {
                                $row = Media::GetMedia($entity, $binfo['media_id']);
                                $title = 'title';
                            }
                            if (!$row['id'])
                                continue;

							$sel = '';

							if (isset($filter_data['binding_id']) && in_array($row['id'], $filter_data['binding_id'])) {
								$sel = 'checked="checked"';
							}

                            echo '<label><input '.$sel.' type="checkbox" class="" name="binding_id[]" value="' . $row['id'] . '" onchange="show_result_count($(this));change_all_binding(event)"/> ' . str_replace('/', ' / ', $row[$title]) . '</label>';
                        }
                        ?>


                    </div><?php } ?>
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
    function interactiveSearch(classInput, data, inp_name, result) {
        $(classInput).bind("change keyup input click", function () {
            if (this.value.length >= 2) {
                $(result).html(findEqual(this.value, data)).fadeIn();
            }
            else {
                $(classInput).prev().val(0);
                select_item($(this), inp_name);
                $(result).fadeOut();
            }
        });

        $(result).hover(function () {
            $(classInput).blur();
        });

        $(result).on("click", "li", function () {
            $(classInput).val($(this).text());
            $(result).fadeOut();
        });

        function findEqual(value, availableValue) {
            result_value = '';
            availableValue.forEach(function (item, index) {
               if (item.toLowerCase().indexOf(value.toLowerCase()) != -1) result_value += '<li rel="' + index + '" onclick="select_item($(this), \''+inp_name+'\')">' + item + '</li>';
            });
            return result_value;
        }
    }
</script>
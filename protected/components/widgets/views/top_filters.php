<form class="prod-filter filter" method="get" action="">

    <input type="hidden" name="langsel" class="lang" value="<?= Yii::app()->getRequest()->getParam('lang'); ?>"/>
    <input type="hidden" name="entity_val" class="entity_val" value="<?= $entity ?>"/>
    <input type="hidden" name="cid_val" class="cid_val" value="<?= $cid ?>"/>
    <input type="hidden" name="sort" class="sort" value="<?= (Yii::app()->getRequest()->getParam('sort')) ? Yii::app()->getRequest()->getParam('sort') : 12 ?>"/>

    <!--Основной блок фильтров-->
    <div class="prod-filter__row">

        <!--Фильтр по авторам-->
        <?php if (isset($filters['author']) && $filters['author'] == true):?>
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_AUTHOR'); ?>:</label>
            <div class="text">
                <input type="hidden" name="author" value="<?=($author = (isset($filter_data['author']) && $filter_data['author'] != 0)) ? $filter_data['author'] : 0?>">
                <input type="text" class="find_author prod-filter__input prod-filter__input--m clearable <?= ($author) ? 'x' : ''?>"
                       placeholder="По автору" autocomplete="off" name="new_author"
                       <?= ($author) ? 'value="'.ProductHelper::GetAuthorTitle($filter_data['author'], Yii::app()->language).'"' : '' ?>/>
            </div>
            <script>
                liveFindAuthorMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_authors')?>', <?=$cid?>);
            </script>
        </div>
        <?php endif;?>

        <?php if (isset($filters['avail']) && $filters['avail'] == true):?>
        <!--Фильтр по наличию-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_ITEM_AVAIBILITY')?>:</label>
            <select class="prod-filter__input prod-filter__input__select--m" name="avail" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')">
                <option value="0" <?= ($avail = (isset($filter_data['avail']) && $filter_data['avail'] == 0)) ? 'selected' : ''?>>Всё</option>
                <option value="1" <?= (!$avail) ? 'selected' : ''?>>В наличии</option>
            </select>
        </div>
        <?php endif;?>

        <!--Фильтр по цене-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_PRICE');?>:</label>
            <div class="prod-filter__row">
                <input type="text" value="<?= ($min_p = (isset($filter_data['cost_min']) && $filter_data['cost_min'] != '')) ? $filter_data['cost_min'] : '' ?>"
                       class="prod-filter__input prod-filter__input--s cost_inp_mini clearable <?= ($min_p) ? 'x' : '' ?>"
                       placeholder="<?= (isset($filters['max-min'][2]) && $filters['max-min'][2] != '') ? $filters['max-min'][2] : ''?>"
                       name="min_cost" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                <span class="prod-filter__inp-separator">&ndash;</span>
                <input type="text" value="<?= ($max_p = (isset($filter_data['cost_max']) && $filter_data['cost_max'] != '')) ? $filter_data['cost_max'] : '' ?>"
                       class="prod-filter__input prod-filter__input--s cost_inp_max clearable <?= ($max_p) ? 'x' : '' ?>"
                       placeholder="<?= (isset($filters['max-min'][3]) && $filters['max-min'][3] != '') ? $filters['max-min'][3] : ''?>"
                       name="max_cost" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
            </div>
        </div>

        <?php if (isset($filters['years']) && $filters['years'] == true):?>
            <!--Фильтр по году-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_YEAR')?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_y = (isset($filter_data['year_min']) && $filter_data['year_min'] != '')) ? $filter_data['year_min'] : '' ?>"
                           name="ymin" class="prod-filter__input prod-filter__input--s year_inp_mini clearable <?= ($min_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min'][0]) && $filters['max-min'][0] != '') ? $filters['max-min'][0] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_y = (isset($filter_data['year_max']) && $filter_data['year_max'] != '')) ? $filter_data['year_max'] : '' ?>"
                           name="ymax" class="prod-filter__input prod-filter__input--s year_inp_max clearable <?= ($max_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min'][1]) && $filters['max-min'][1] != '') ? $filters['max-min'][1] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                </div>
            </div>
        <?php endif;?>

        <!--Кнопки управления-->
        <button class="prod-filter__button" type="button" id="filter_apply" onclick="show_items('<?=Yii::app()->createUrl('/site/ggfilter/')?>', <?= ($_GET['page'])?>)">
            <?= $ui->item('A_NEW_APPLY'); ?> <span class="prod-filter__button-icon" id="loader-filter">&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)</span>
        </button>
    </div>

    <!--"Второй" блок фильтров-->
    <div class="prod-filter__row" id="more-filter-block">

        <?php if (isset($filters['publisher']) && $filters['publisher'] == true):?>
            <!--Фильтр по издательству-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_PUBLISHER')?>:</label>
            <div class="text">
                <input type="hidden" name="izda" value="<?=($publisher = (isset($filter_data['publisher']) && $filter_data['publisher'] != 0)) ? $filter_data['publisher'] : 0?>">
                <input type="text" name="new_izda" class="find_izda prod-filter__input prod-filter__input--m clearable <?= ($publisher) ? 'x' : ''?>"
                       placeholder="Все" autocomplete="off"
                       <?= ($publisher) ? 'value="'.ProductHelper::GetPublisherTitle($filter_data['publisher'], Yii::app()->language).'"' : '' ?>/>
            </div>
            <ul class="search_result search_result_izda"></ul>
            <script>
                liveFindPublisherMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_publishers')?>', <?=$cid?>);
            </script>
        </div>
        <?php endif;?>

        <?php if (isset($filters['series']) && $filters['series'] == true):?>
            <!--Фильтр по серии-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_SERIES')?>:</label>
            <div class="text">
                <input type="hidden" name="seria" value="<?=($seria = (isset($filter_data['series']) && $filter_data['series'] != 0)) ? $filter_data['seria'] : 0?>">
                <input type="text" name="new_series" class="find_series prod-filter__input prod-filter__input--m clearable <?= ($seria) ? 'x' : ''?>"
                       autocomplete="off" placeholder="Все"
                    <?= ($seria) ? 'value="'.ProductHelper::GetSeriesTitle($filter_data['series'], $entity, Yii::app()->language).'"' : '' ?> />
            </div>
            <ul class="search_result search_result_series"></ul>
            <script>
                liveFindSeriesMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_series')?>', <?=$cid?>);
            </script>
        </div>
        <?php endif;?>

        <?php if (isset($filters['langVideo']) && $filters['langVideo'] == true):?>
            <!--Фильтр по языку звуковой дорожки-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_LANG_VIDEO')?>:</label>
            <select class="prod-filter__input prod-filter__input__select--m"
                    name="langVideo" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="langVideo">
                <option value="0"><?=$ui->item('A_NEW_FILTER_ALL'); ?></option>
                <?php foreach ($filters['langVideo'] as $k => $lang) :?>
                    <option value="<?=$lang['id']?>" <?= (isset($filter_data['lang_video']) && ($lang['id'] == (int)$filter_data['lang_video'])) ? 'selected' : ''?>>
                        <?=ProductHelper::GetTitle($lang);?>
                    </option>
                <?php endforeach;?>
            </select>
        </div>
        <?php endif; ?>

        <?php if (isset($filters['langSubtitles']) && $filters['langSubtitles'] == true):?>
            <!--Фильтр по языку субтитров-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_LANG_SUBTITLES')?>:</label>
                <select class="prod-filter__input prod-filter__input__select--m"
                        name="subtitlesVideo" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="subtitlesVideo">
                    <option value="0"><?=$ui->item('A_NEW_FILTER_ALL'); ?></option>
                    <?php foreach ($filters['langSubtitles'] as $k => $lang) :?>
                        <option value="<?=$lang['id']?>" <?= ((isset($filter_data['subtitles_video'])) && ($lang['id'] == (int)$filter_data['subtitles_video'])) ? 'selected' : ''?>>
                            <?=ProductHelper::GetTitle($lang);?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        <?php endif; ?>

        <?php if (isset($filters['formatVideo']) && $filters['formatVideo'] == true):?>
            <!--Фильтр формату видео-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_FORMAT_VIDEO')?>:</label>
                <select class="prod-filter__input prod-filter__input__select--m"
                        name="formatVideo" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="formatVideo">
                    <option value="0"><?=$ui->item('A_NEW_FILTER_ALL'); ?></option>
                    <?php foreach ($filters['formatVideo'] as $k => $lang) :?>
                        <option value="<?=$lang['id']?>" <?= ((isset($filter_data['format_video'])) && ($lang['id'] == (int)$filter_data['format_video'])) ? 'selected' : ''?>>
                            <?=$lang['title'];?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        <?php endif; ?>

        <?php if (isset($filters['binding']) && !empty($filters['binding'])):?>
            <!--Фильтр по типу/переплету-->
        <div class="prod-filter__col--grow" id="binding_div">
            <label class="prod-filter__label" for="">
                <?php if ($entity == 10 OR $entity == 15) $label_binding = $ui->item('A_NEW_FILTER_TYPE1');
                else $label_binding = $ui->item('A_NEW_FILTER_TYPE2'); ?>
                <?=$label_binding?>:</label>

            <select id="binding_select" multiple="multiple" name="binding_id[]" onchange="show_result_count()">
                <?php
                foreach ($filters['binding'] as $bg => $binfo) {
                if ($entity == 22 OR $entity == 24) {
                    $row = Media::GetMedia($entity, $binfo['media_id']);
                    $title = 'title';
                }
                elseif ($entity == 30) {
                    $row = TypeRetriever::GetType($entity, $binfo['type']);
                    $title = 'title_' . Yii::app()->language;
                }
                else {
                    $row = Binding::GetBinding($entity, $binfo['binding_id']);
                    $title = 'title_' . Yii::app()->language;
                }
                if (!$row['id'])
                    continue;
                $sel = '';
                if (isset($filter_data['binding']) && !empty($filter_data['binding']) && in_array($row['id'], $filter_data['binding'])) {
                    $sel = 'selected="selected"';
                }
                ?>
                <option value="<?=$row['id']?>" <?=$sel?>><?= str_replace('/', ' / ', $row[$title])?></option>
                <?php } ?>
            </select>
            <script>
                $('#binding_select').multipleSelect({
                    selectAllText: '<?= $ui->item('A_NEW_FILTER_ALL')?>',
                    placeholder: '<?= $label_binding ?>',
                    width: '161px',
                });
            </script>
            <?php if ($entity == 30):?>
                <script>
                    typeDiv = $('#binding_div').detach();
                    typeDiv.insertBefore($('#filter_apply'));
                </script>
            <?php endif;?>
        </div>
        <?php endif;?>
    </div>
</form>
<script>
    show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>');
    $(document).ready(function () {
        //show_items('<?=Yii::app()->createUrl('/site/ggfilter/')?>', <?= ($_GET['page'])?>);
    });

</script>

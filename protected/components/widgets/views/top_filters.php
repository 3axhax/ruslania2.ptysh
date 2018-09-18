<form class="prod-filter filter" method="get" action="">

    <input type="hidden" name="lang" class="lang" value="<?= Yii::app()->getRequest()->getParam('lang'); ?>"/>
    <input type="hidden" name="entity_val" class="entity_val" value="<?= $entity ?>"/>
    <input type="hidden" name="cid_val" class="cid_val" value="<?= $cid ?>"/>
    <input type="hidden" name="sort" class="sort" value="<?= (Yii::app()->getRequest()->getParam('sort')) ? Yii::app()->getRequest()->getParam('sort') : 12 ?>"/>

    <!--Основной блок фильтров-->
    <div class="prod-filter__row">

        <?php if (isset($filters['author']) && $filters['author'] == true):?>
            <!--Фильтр по авторам-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_AUTHOR'); ?>:</label>
            <div class="text">
                <input type="hidden" name="author" value="<?=($author = (isset($filter_data['author']) && $filter_data['author'] != 0)) ? $filter_data['author'] : 0?>">
                <input type="text" class="find_author prod-filter__input prod-filter__input--m clearable <?= ($author) ? 'x' : ''?>"
                       placeholder="<?=$ui->item('A_NEW_FILTER_ALL'); ?>" autocomplete="off" name="new_author"
                       <?= ($author) ? 'value="'.ProductHelper::GetAuthorTitle($filter_data['author'], Yii::app()->language).'"' : '' ?>/>
            </div>
            <script>
                liveFindAuthorMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_authors')?>', <?=$cid?>);
            </script>
        </div>
        <?php endif;?>

        <?php if (isset($filters['directors']) && $filters['directors'] == true):?>
            <!--Фильтр по режисерам-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_DIRECTORS'); ?>:</label>
                <div class="text">
                    <input type="hidden" name="directors" value="<?=($directors = (isset($filter_data['directors']) && $filter_data['directors'] != 0)) ? $filter_data['directors'] : 0?>">
                    <input type="text" class="find_directors prod-filter__input prod-filter__input--m clearable <?= ($directors) ? 'x' : ''?>"
                           placeholder="<?=$ui->item('A_NEW_FILTER_ALL'); ?>" autocomplete="off" name="new_directors"
                        <?= ($directors) ? 'value="'.ProductHelper::GetAuthorTitle($filter_data['directors'], Yii::app()->language).'"' : '' ?>/>
                </div>
                <script>
                    liveFindDirectorsMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_directors')?>', <?=$cid?>);
                </script>
            </div>
        <?php endif;?>

        <?php if (isset($filters['actors']) && $filters['actors'] == true):?>
            <!--Фильтр по авторам-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_ACTORS'); ?>:</label>
                <div class="text">
                    <input type="hidden" name="actors" value="<?=($actors = (isset($filter_data['actors']) && $filter_data['actors'] != 0)) ? $filter_data['actors'] : 0?>">
                    <input type="text" class="find_actors prod-filter__input prod-filter__input--m clearable <?= ($actors) ? 'x' : ''?>"
                           placeholder="<?=$ui->item('A_NEW_FILTER_ALL'); ?>" autocomplete="off" name="new_actors"
                        <?= ($actors) ? 'value="'.ProductHelper::GetAuthorTitle($filter_data['actors'], Yii::app()->language).'"' : '' ?>/>
                </div>
                <script>
                    liveFindActorsMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_actors')?>', <?=$cid?>);
                </script>
            </div>
        <?php endif;?>

        <?php if (isset($filters['avail']) && $filters['avail'] == true):?>
        <!--Фильтр по наличию-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_ITEM_AVAIBILITY')?>:</label>
            <select class="prod-filter__input prod-filter__input__select--m" name="avail" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')">
                <option value="0" <?= ($avail = (isset($filter_data['avail']) && $filter_data['avail'] == 0)) ? 'selected' : ''?>><?=$ui->item('A_NEW_FILTER_ALL'); ?></option>
                <option value="1" <?= (!$avail) ? 'selected' : ''?>>В наличии</option>
            </select>
        </div>
        <?php endif;?>

        <?php if ($entity != Entity::VIDEO && isset($filters['price']) && $filters['price'] == true):?>
        <!--Фильтр по цене-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_PRICE');?>:</label>
            <div class="prod-filter__row">
                <input type="text" value="<?= ($min_p = (isset($filter_data['cost_min']) && $filter_data['cost_min'] != '')) ? $filter_data['cost_min'] : '' ?>"
                       class="prod-filter__input prod-filter__input--s cost_inp_mini clearable <?= ($min_p) ? 'x' : '' ?>"
                       placeholder="<?= (isset($filters['max-min']['cost_min']) && $filters['max-min']['cost_min'] != '') ? round($filters['max-min']['cost_min'], 2) : ''?>"
                       name="cost_min" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                <span class="prod-filter__inp-separator">&ndash;</span>
                <input type="text" value="<?= ($max_p = (isset($filter_data['cost_max']) && $filter_data['cost_max'] != '')) ? $filter_data['cost_max'] : '' ?>"
                       class="prod-filter__input prod-filter__input--s cost_inp_max clearable <?= ($max_p) ? 'x' : '' ?>"
                       placeholder="<?= (isset($filters['max-min']['cost_max']) && $filters['max-min']['cost_max'] != '') ? round($filters['max-min']['cost_max'], 2) : ''?>"
                       name="cost_max" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
            </div>
        </div>
        <?php endif;?>

        <?php if (isset($filters['years']) && $filters['years'] == true):?>
            <!--Фильтр по году/году выхода-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=($entity != Entity::VIDEO) ? $ui->item('A_NEW_FILTER_YEAR') : $ui->item('A_NEW_YEAR')?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_y = (isset($filter_data['year_min']) && $filter_data['year_min'] != '')) ? $filter_data['year_min'] : '' ?>"
                           name="year_min" class="prod-filter__input prod-filter__input--s year_inp_mini clearable <?= ($min_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min']['year_min']) && $filters['max-min']['year_min'] != '') ? $filters['max-min']['year_min'] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_y = (isset($filter_data['year_max']) && $filter_data['year_max'] != '')) ? $filter_data['year_max'] : '' ?>"
                           name="year_max" class="prod-filter__input prod-filter__input--s year_inp_max clearable <?= ($max_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min']['year_max']) && $filters['max-min']['year_max'] != '') ? $filters['max-min']['year_max'] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                </div>
            </div>
        <?php endif;?>

        <?php if (isset($filters['release_years']) && $filters['release_years'] == true):?>
            <!--Фильтр по году издания-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_YEAR_REAL')?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_y = (isset($filter_data['release_year_min']) && $filter_data['release_year_min'] != '')) ? $filter_data['release_year_min'] : '' ?>"
                           name="release_year_min" class="prod-filter__input prod-filter__input--s release_year_inp_mini clearable <?= ($min_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min']['rel_year_min']) && $filters['max-min']['rel_year_min'] != '') ? $filters['max-min']['rel_year_min'] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_y = (isset($filter_data['release_year_max']) && $filter_data['release_year_max'] != '')) ? $filter_data['release_year_max'] : '' ?>"
                           name="release_year_max" class="prod-filter__input prod-filter__input--s release_year_inp_max clearable <?= ($max_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min']['rel_year_max']) && $filters['max-min']['rel_year_max'] != '') ? $filters['max-min']['rel_year_max'] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                </div>
            </div>
        <?php endif;?>

        <?php if (isset($filters['country']) && $filters['country'] == true):?>
            <!--Фильтр по стране-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_PERIODIC_COUNTRY')?>:</label>
                <select class="prod-filter__input prod-filter__input__select--m"
                        name="country" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="country">
                    <option value="0"><?=$ui->item('A_NEW_FILTER_ALL'); ?></option>
                    <?php foreach ($filters['country'] as $k => $lang) :?>
                        <option value="<?=$lang['id']?>" <?= ((isset($filter_data['country'])) && ($lang['id'] == (int)$filter_data['country'])) ? 'selected' : ''?>>
                            <?=ProductHelper::GetTitle($lang);?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($entity != Entity::VIDEO):?>
        <!--Кнопки управления-->
        <button class="prod-filter__button" type="button" id="filter_apply"
                onclick="show_items('<?=Yii::app()->createUrl('/site/ggfilter/')?>', <?= ($_GET['page'])?>)">
            <?= $ui->item('A_NEW_APPLY'); ?> <span class="prod-filter__button-icon" id="loader-filter">&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)</span>
        </button>
        <?php endif;?>
    </div>

    <!--"Второй" блок фильтров-->
    <div class="prod-filter__row" id="more-filter-block">

        <?php if ($entity == Entity::VIDEO):?>
            <!--Фильтр по цене-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_PRICE');?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_p = (isset($filter_data['cost_min']) && $filter_data['cost_min'] != '')) ? $filter_data['cost_min'] : '' ?>"
                           class="prod-filter__input prod-filter__input--s cost_inp_mini clearable <?= ($min_p) ? 'x' : '' ?>"
                           placeholder="<?= (isset($filters['max-min']['cost_min']) && $filters['max-min']['cost_min'] != '') ? round($filters['max-min']['cost_min'], 2) : ''?>"
                           name="cost_min" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_p = (isset($filter_data['cost_max']) && $filter_data['cost_max'] != '')) ? $filter_data['cost_max'] : '' ?>"
                           class="prod-filter__input prod-filter__input--s cost_inp_max clearable <?= ($max_p) ? 'x' : '' ?>"
                           placeholder="<?= (isset($filters['max-min']['cost_max']) && $filters['max-min']['cost_max'] != '') ? round($filters['max-min']['cost_max'], 2) : ''?>"
                           name="cost_max" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                </div>
            </div>
        <?php endif;?>

        <?php if (isset($filters['pre_sale']) && $filters['pre_sale'] == true):?>
            <!--Фильтр по предпродажам-->
            <?php
                $pre_sale = $filter_data['pre_sale'] ?: false;
            ?>
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_PRE_SALE_LABLE')?>:</label>
                <select class="prod-filter__input prod-filter__input__select--m" name="pre_sale" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')">
                    <option value="0" ><?=$ui->item('A_NEW_FILTER_PRE_SALE_1')?></option>
                    <option value="1" <?= ($pre_sale == 1) ? 'selected' : ''?>><?=$ui->item('A_NEW_FILTER_PRE_SALE_2')?></option>
                    <option value="2" <?= ($pre_sale == 2) ? 'selected' : ''?>><?=$ui->item('A_NEW_FILTER_PRE_SALE_3')?></option>
                </select>
            </div>
        <?php endif;?>

        <?php if (isset($filters['performers']) && $filters['performers'] == true):?>
            <!--Фильтр по исполнителю-->
            <div class="prod-filter__col">
                    <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_PERFORMER')?>:</label>
                <div class="text">
                    <input type="hidden" name="performer" value="<?=($performer = (isset($filter_data['performer']) && $filter_data['performer'] != 0)) ? $filter_data['performer'] : 0?>">
                    <input type="text" name="new_performer" class="find_performer prod-filter__input prod-filter__input--m clearable <?= ($performer) ? 'x' : ''?>"
                           placeholder="<?=$ui->item('A_NEW_FILTER_ALL'); ?>" autocomplete="off"
                        <?= ($performer) ? 'value="'.ProductHelper::GetPerformerTitle($filter_data['performer'], Yii::app()->language).'"' : '' ?>/>
                </div>
                <ul class="search_result search_result_performer"></ul>
                <script>
                    liveFindPerformerMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_performers')?>', <?=$cid?>);
                </script>
            </div>
        <?php endif;?>

        <?php if (isset($filters['publisher']) && $filters['publisher'] == true):?>
            <!--Фильтр по издательству-->
        <div class="prod-filter__col" id="publisher_div">
            <?php if ($entity == Entity::MUSIC):?>
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_LABEL')?>:</label>
            <?php else:?>
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_PUBLISHER')?>:</label>
            <?php endif;?>
            <div class="text">
                <input type="hidden" name="publisher" value="<?=($publisher = (isset($filter_data['publisher']) && $filter_data['publisher'] != 0)) ? $filter_data['publisher'] : 0?>">
                <input type="text" name="new_publisher" class="find_publisher prod-filter__input prod-filter__input--m clearable <?= ($publisher) ? 'x' : ''?>"
                       placeholder="<?=$ui->item('A_NEW_FILTER_ALL'); ?>" autocomplete="off"
                       <?= ($publisher) ? 'value="'.ProductHelper::GetPublisherTitle($filter_data['publisher'], Yii::app()->language).'"' : '' ?>/>
            </div>
            <ul class="search_result search_result_publisher"></ul>
            <script>
                liveFindPublisherMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_publishers')?>', <?=$cid?>);
            </script>
            <?php if ($entity == Entity::MAPS || $entity == Entity::SOFT || $entity == Entity::PRINTED):?>
                <script>
                    typeDiv = $('#publisher_div').detach();
                    typeDiv.insertBefore($('#filter_apply'));
                </script>
            <?php endif;?>
        </div>
        <?php endif;?>

        <?php if (isset($filters['series']) && $filters['series'] == true):?>
            <!--Фильтр по серии-->
            <?php if ($entity == Entity::BOOKS && $cid == 0):?>
                <div class="prod-filter__col">
                    <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_SERIES')?>:</label>
                    <div class="text">
                        <input type="hidden" name="seria" value="<?=($seria = (isset($filter_data['series']) && $filter_data['series'] != 0)) ? $filter_data['series'] : 0?>">
                        <input type="text" name="new_series" class="find_series prod-filter__input prod-filter__input--m clearable <?= ($seria) ? 'x' : ''?>"
                               autocomplete="off" placeholder="<?=$ui->item('A_NEW_FILTER_ALL'); ?>"
                            <?= ($seria) ? 'value="'.htmlspecialchars(ProductHelper::GetSeriesTitle($filter_data['series'], $entity, Yii::app()->language)).'"' : '' ?> />
                    </div>
                    <ul class="search_result search_result_series"></ul>
                    <script>
                        liveFindSeriesMP(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/filter_series')?>', <?=$cid?>);
                    </script>
                </div>
            <?php else:?>
                <div class="prod-filter__col">
                    <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_SERIES')?>:</label>
                    <!--<input type="hidden" name="series" value="<?/*=($seria = (isset($filter_data['series']) && $filter_data['series'] != 0)) ? $filter_data['series'] : 0*/?>">-->
                    <select class="select2_series prod-filter__input prod-filter__input prod-filter__input__select--m" name="seria"
                    onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')">
                        <option value=""><?=$ui->item('A_NEW_FILTER_ALL'); ?></option>
                    </select>
                    <script>
                        getSeries(<?=$entity?>, '<?=Yii::app()->createUrl('/liveSearch/select_filter_series')?>', <?=$cid?>,
                            '<?= (isset($filter_data['series']) && $filter_data['series'] != 0) ?
                            htmlspecialchars(ProductHelper::GetSeriesTitle($filter_data['series'], $entity, Yii::app()->language)) : ''?>');
                    </script>
                </div>
            <?php endif;?>
        <?php endif;?>

        <?php if (isset($filters['langVideo']) && $filters['langVideo'] == true):?>
            <!--Фильтр по языку звуковой дорожки-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_LANG_VIDEO')?>:</label>
            <select class="prod-filter__input prod-filter__input__select--m"
                    name="lang_video" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="lang_video">
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
                        name="subtitles_video" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="subtitles_video">
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
            <!--Фильтр по формату видео-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_FORMAT_VIDEO')?>:</label>
                <select class="prod-filter__input prod-filter__input__select--m"
                        name="format_video" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')" id="format_video">
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
                <?php if ($entity == Entity::BOOKS OR $entity == Entity::SHEETMUSIC) $label_binding = $ui->item('A_NEW_FILTER_TYPE1');
                elseif ($entity == Entity::MUSIC) $label_binding = $ui->item('A_NEW_FILTER_TYPE3');
                else $label_binding = $ui->item('A_NEW_FILTER_TYPE2'); ?>
                <?=$label_binding?>:</label>

            <select id="binding_select" multiple="multiple" name="binding[]" onchange="show_result_count()">
                <?php
                foreach ($filters['binding'] as $bg => $binfo) {
                if ($entity == Entity::MUSIC OR $entity == Entity::SOFT) {
                    $row = Media::GetMedia($entity, $binfo['media_id']);
                    $title = 'title';
                }
                elseif ($entity == Entity::PERIODIC) {
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
            <?php if ($entity == Entity::PERIODIC || $entity == Entity::SOFT):?>
                <script>
                    typeDiv = $('#binding_div').detach();
                    typeDiv.insertBefore($('#filter_apply'));
                </script>
            <?php endif;?>
        </div>
        <?php endif;?>

        <?php if ($entity == Entity::VIDEO):?>
            <!--Кнопки управления-->
            <button class="prod-filter__button" type="button" id="filter_apply" onclick="show_items('<?=Yii::app()->createUrl('/site/ggfilter/')?>', <?= ($_GET['page'])?>)">
                <?= $ui->item('A_NEW_APPLY'); ?> <span class="prod-filter__button-icon" id="loader-filter">&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)</span>
            </button>
        <?php endif;?>

    </div>
</form>
<script>
    show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>');
</script>
<style>
    .text .mp_list {
        width: 210px;
    }
</style>

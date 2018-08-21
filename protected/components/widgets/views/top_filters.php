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

        <?php if ($entity != 40):?>
        <!--Фильтр по цене-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_PRICE');?>:</label>
            <div class="prod-filter__row">
                <input type="text" value="<?= ($min_p = (isset($filter_data['cost_min']) && $filter_data['cost_min'] != '')) ? $filter_data['cost_min'] : '' ?>"
                       class="prod-filter__input prod-filter__input--s cost_inp_mini clearable <?= ($min_p) ? 'x' : '' ?>"
                       placeholder="<?= (isset($filters['max-min'][2]) && $filters['max-min'][2] != '') ? round($filters['max-min'][2], 2) : ''?>"
                       name="cost_min" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                <span class="prod-filter__inp-separator">&ndash;</span>
                <input type="text" value="<?= ($max_p = (isset($filter_data['cost_max']) && $filter_data['cost_max'] != '')) ? $filter_data['cost_max'] : '' ?>"
                       class="prod-filter__input prod-filter__input--s cost_inp_max clearable <?= ($max_p) ? 'x' : '' ?>"
                       placeholder="<?= (isset($filters['max-min'][3]) && $filters['max-min'][3] != '') ? round($filters['max-min'][3], 2) : ''?>"
                       name="cost_max" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
            </div>
        </div>
        <?php endif;?>

        <?php if (isset($filters['years']) && $filters['years'] == true):?>
            <!--Фильтр по году-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_YEAR')?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_y = (isset($filter_data['year_min']) && $filter_data['year_min'] != '')) ? $filter_data['year_min'] : '' ?>"
                           name="year_min" class="prod-filter__input prod-filter__input--s year_inp_mini clearable <?= ($min_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min'][0]) && $filters['max-min'][0] != '') ? $filters['max-min'][0] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_y = (isset($filter_data['year_max']) && $filter_data['year_max'] != '')) ? $filter_data['year_max'] : '' ?>"
                           name="year_max" class="prod-filter__input prod-filter__input--s year_inp_max clearable <?= ($max_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min'][1]) && $filters['max-min'][1] != '') ? $filters['max-min'][1] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                </div>
            </div>
        <?php endif;?>

        <?php if (isset($filters['years']) && $filters['years'] == true):?>
            <!--Фильтр по году-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('A_NEW_FILTER_YEAR')?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_y = (isset($filter_data['year_min']) && $filter_data['year_min'] != '')) ? $filter_data['year_min'] : '' ?>"
                           name="year_min" class="prod-filter__input prod-filter__input--s year_inp_mini clearable <?= ($min_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min'][0]) && $filters['max-min'][0] != '') ? $filters['max-min'][0] : ''?>"
                           onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_y = (isset($filter_data['year_max']) && $filter_data['year_max'] != '')) ? $filter_data['year_max'] : '' ?>"
                           name="year_max" class="prod-filter__input prod-filter__input--s year_inp_max clearable <?= ($max_y) ? 'x' : ''?>"
                           placeholder="<?= (isset($filters['max-min'][1]) && $filters['max-min'][1] != '') ? $filters['max-min'][1] : ''?>"
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

        <?php if ($entity != 40):?>
        <!--Кнопки управления-->
        <button class="prod-filter__button" type="button" id="filter_apply" onclick="show_items('<?=Yii::app()->createUrl('/site/ggfilter/')?>', <?= ($_GET['page'])?>)">
            <?= $ui->item('A_NEW_APPLY'); ?> <span class="prod-filter__button-icon" id="loader-filter">&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)</span>
        </button>
        <?php endif;?>
    </div>

    <!--"Второй" блок фильтров-->
    <div class="prod-filter__row" id="more-filter-block">

        <?php if ($entity == 40):?>
            <!--Фильтр по цене-->
            <div class="prod-filter__col">
                <label class="prod-filter__label" for=""><?=$ui->item('CART_COL_PRICE');?>:</label>
                <div class="prod-filter__row">
                    <input type="text" value="<?= ($min_p = (isset($filter_data['cost_min']) && $filter_data['cost_min'] != '')) ? $filter_data['cost_min'] : '' ?>"
                           class="prod-filter__input prod-filter__input--s cost_inp_mini clearable <?= ($min_p) ? 'x' : '' ?>"
                           placeholder="<?= (isset($filters['max-min'][2]) && $filters['max-min'][2] != '') ? round($filters['max-min'][2], 2) : ''?>"
                           name="cost_min" onchange="show_result_count('<?=Yii::app()->createUrl('/site/gtfilter/')?>')"/>
                    <span class="prod-filter__inp-separator">&ndash;</span>
                    <input type="text" value="<?= ($max_p = (isset($filter_data['cost_max']) && $filter_data['cost_max'] != '')) ? $filter_data['cost_max'] : '' ?>"
                           class="prod-filter__input prod-filter__input--s cost_inp_max clearable <?= ($max_p) ? 'x' : '' ?>"
                           placeholder="<?= (isset($filters['max-min'][3]) && $filters['max-min'][3] != '') ? round($filters['max-min'][3], 2) : ''?>"
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
        <div class="prod-filter__col">
            <?php if ($entity == 22):?>
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
        </div>
        <?php endif;?>

        <?php if (isset($filters['series']) && $filters['series'] == true):?>
            <!--Фильтр по серии-->
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
            <!--Фильтр формату видео-->
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
                <?php if ($entity == 10 OR $entity == 15) $label_binding = $ui->item('A_NEW_FILTER_TYPE1');
                else $label_binding = $ui->item('A_NEW_FILTER_TYPE2'); ?>
                <?=$label_binding?>:</label>

            <select id="binding_select" multiple="multiple" name="binding[]" onchange="show_result_count()">
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

        <?php if ($entity == 40):?>
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

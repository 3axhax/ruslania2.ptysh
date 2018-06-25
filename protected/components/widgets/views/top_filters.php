<form class="prod-filter filter" method="get" action="">

    <input type="hidden" name="lang" class="lang" value="<?= Yii::app()->getRequest()->getParam('lang'); ?>"/>
    <input type="hidden" name="entity_val" class="entity_val" value="<?= $entity ?>"/>
    <input type="hidden" name="cid_val" class="cid_val" value="<?= $cid ?>"/>
    <input type="hidden" name="sort" class="sort" value="<?= (Yii::app()->getRequest()->getParam('sort')) ? Yii::app()->getRequest()->getParam('sort') : 12 ?>"/>

    <!--Основной блок фильтров-->
    <div class="prod-filter__row">
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Поиск по категории:</label>
            <input type="text" class="prod-filter__input search" placeholder="По названию,ISBN" name="name_search" />
        </div>

        <!--Фильтр по авторам-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Автор:</label>
            <div class="text">
                <input type="hidden" name="author" value="0">
                <input type="text" class="find_author prod-filter__input prod-filter__input--m clearable"
                       placeholder="По автору" autocomplete="off" name="new_author"
                       disabled value="Загрузка..."/>
            </div>
            <ul class="search_result search_result_author"></ul>
            <script>
                liveFindAuthor(<?=$entity?>, '<?=$lang?>', <?=$cid?>);
            </script>
        </div>

        <!--Фильтр по наличию-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Наличие:</label>
            <input type="hidden" name="avail" value="1">
            <select class="prod-filter__input prod-filter__input--m" name="avail" onchange="show_result_count()">
                <option value="0">Всё</option>
                <option value="1" selected>В наличии</option>
            </select>
        </div>

        <!--Фильтр по цене-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Цена:</label>
            <div class="prod-filter__row">
                <input type="text" value="" class="prod-filter__input prod-filter__input--s cost_inp_mini clearable" placeholder="5.0" name="min_cost" onchange="show_result_count()"/>
                <span class="prod-filter__inp-separator">&ndash;</span>
                <input type="text" value="" class="prod-filter__input prod-filter__input--s cost_inp_max clearable" placeholder="500.0" name="max_cost" onchange="show_result_count()"/>
            </div>
        </div>

        <!--Кнопки управления-->
        <div class="prod-filter__col prod-filter__col--grow">
            <span class="prod-filter__more" id="more-filter-toggle">Ещё</span>
        </div>
        <button class="prod-filter__button" type="button" id="filter_apply" onclick="show_items()">
            Применить <span class="prod-filter__button-icon" id="loader-filter">&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)</span>
        </button>
    </div>

    <!--"Скрытый" блок фильтров-->
    <div class="prod-filter__row" id="more-filter-block">

        <!--Фильтр по году-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Год:</label>
            <div class="prod-filter__row">
                <input type="text" value="" name="year_min" class="prod-filter__input prod-filter__input--s year_inp_mini clearable" placeholder="1900" onchange="show_result_count()"/>
                <span class="prod-filter__inp-separator">&ndash;</span>
                <input type="text" value="" name="year_max" class="prod-filter__input prod-filter__input--s year_inp_max clearable" placeholder="2018" onchange="show_result_count()"/>
            </div>
        </div>

        <!--Фильтр по издательству-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Издательство:</label>
            <div class="text">
                <input type="hidden" name="izda" value="0">
                <input type="text" name="new_izda" class="find_izda prod-filter__input prod-filter__input--m clearable"
                       placeholder="Все" name="name_publish"
                       autocomplete="off" disabled value="Загрузка..."/>
            </div>
            <ul class="search_result search_result_izda"></ul>
            <script>
                liveFindIzda(<?=$entity?>, '<?=$lang?>', <?=$cid?>);
            </script>
        </div>

        <!--Фильтр по серии-->
        <div class="prod-filter__col">
            <label class="prod-filter__label" for="">Серия:</label>
            <div class="text">
                <input type="hidden" name="seria" value="0">
                <input type="text" name="new_series" class="find_series prod-filter__input prod-filter__input--m clearable"
                       autocomplete="off" disabled
                       value="Загрузка..." placeholder="Все">
            </div>
            <ul class="search_result search_result_series"></ul>
            <script>
                liveFindSeries(<?=$entity?>, '<?=$lang?>', <?=$cid?>);
            </script>
        </div>


        <div class="prod-filter__col--grow">
            <label class="prod-filter__label" for="">Формат:</label>

            <label class="prod-filter__checkbox"><input type="checkbox" class="" name="binding_id[]" value="0" onchange="change_all_binding(event, true);show_result_count($(this));" checked=""> Все</label>
            <label class="prod-filter__checkbox"><input type="checkbox" class="" name="binding_id[]" value="1" onchange="show_result_count($(this));change_all_binding(event)"> Переплет</label>
            <label class="prod-filter__checkbox"><input type="checkbox" class="" name="binding_id[]" value="2" onchange="show_result_count($(this));change_all_binding(event)"> Мягкая обложка</label>
        </div>
    </div>
</form>
<script>
    initMoreFilterButton();
    show_result_count();
</script>

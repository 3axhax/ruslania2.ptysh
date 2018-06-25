$(document).ready(function(){
    $('.slider_recomend').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 5,
        speed: 800,
        prevArrow:"<div class=\"btn_left slick-arrow\" style=\"display: block;\"><img src=\"/new_img/btn_left_news.png\"></div>",
        nextArrow:"<div class=\"btn_right slick-arrow\" style=\"display: block;\"><img src=\"/new_img/btn_right_news.png\"></div>"
    });
});

function initMoreFilterButton () {
    $('#more-filter-block').hide();
    $('#more-filter-toggle').click(function() {
        filterBlock = $('#more-filter-block');
        filterBlock.fadeToggle('fast');
    });
}

// "X" в input-ах фильтра
function tog(v){return v?'addClass':'removeClass';}
$(document).on('input', '.clearable', function(){
    $(this)[tog(this.value)]('x');
}).on('mousemove', '.x', function( e ){
    $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');
}).on('touchstart click', '.onX', function( ev ){
    ev.preventDefault();
    $(this).removeClass('x onX').val('').change();
});

//Функция живого поиска
function interactiveSearch(classInput, data, inp_name, result) {
    $(classInput).bind("change keyup input click", function () {
        if (this.value.length >= 2) {
            if ((val = findEqual(this.value, data)) != '') $(result).html(val).fadeIn();
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

// Живой поиск для Author
function liveFindAuthor(entity, lang, cid) {
    var author_search = [];
    $.ajax({
        url: '/entity/getauthordata',
        data: {entity: entity, lang: lang, cid: cid},
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
}

// Живой поиск для Izda
function liveFindIzda(entity, lang, cid) {
    var izda_search = [];
    $.ajax({
        url: '/entity/getizdadata',
        data: {entity: entity, lang: lang, cid: cid},
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
}

function liveFindSeries(entity, lang, cid) {
    var series_search = [];
    $.ajax({
        url: '/entity/getseriesdata',
        data: {entity: entity, lang: lang, cid: cid},
        type: 'GET',
        beforeSend: function () {
            $(".find_series").attr('disabled', true);
            $(".find_series").val('Загрузка...');
        },
        success: function (data) {
            series_search = JSON.parse(data);
            var search_series = [];
            $.each(series_search, function (index, value) {
                if ((value != '') && (value != null)) search_series[index] = value;
            });
            interactiveSearch('.find_series', search_series, 'seria', '.search_result_series');
            $(".find_series").attr('disabled', false);
            $(".find_series").val('');
        },
        error: function () {
            console.log("Error response");
        },
    });
}

// Выбор фильтра
function select_item(item, inp_name) {
    var id = item.attr('rel');

    if (!$(item).parent().parent().is('div.interactive_search')) $('.text span', item.parent().parent().parent().parent()).html(item.html());
    $('.list_dd', item.parent().parent().parent().parent()).hide();
    $('input[name=' + inp_name + ']', item.parent().parent().parent().parent()).val(id);

    show_result_count(item);
}

// Подсчёт результатов фильтра
function show_result_count() {

    var frm = $('form.filter').serialize();
    var csrf = $('meta[name=csrf]').attr('content').split('=');

    frm = frm + '&' + csrf[0] + '=' + csrf[1];

    $.ajax({
        url: '/site/gtfilter/',
        type: "POST",
        data: frm,
        beforeSend: function(){
            $('#loader-filter').html('&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)');
        },
        success: function (data) {
            $('#loader-filter').html('&nbsp;('+data+')');
        }
    });
}

//Вывод результата фильтра
function show_items() {

    var create_url;

    create_url = '/site/ggfilter' +
        '/entity/'+(( entity = $('form.filter input.entity_val').val()) ? entity : '100')+
        '/cid/'+(( cid = $('form.filter input.cid_val').val()) ? cid : '0')+
        '/author/'+(( author = $('form.filter input[name=author]').val()) ? author : '')+
        '/avail/'+(( avail = $('form.filter select[name=avail]').val()) ? avail : '1')+
        '/ymin/'+(( ymin = $('form.filter input.year_inp_mini').val()) ? ymin : '0')+
        '/ymax/'+(( ymax = $('form.filter input.year_inp_max').val()) ? ymax : '3000')+
        '/izda/'+(( izda = $('form.filter input[name=izda]').val()) ? izda : '0')+
        '/seria/'+(( seria = $('form.filter input[name=seria]').val()) ? seria : '0')+
        '/cmin/'+(( cmin = $('form.filter input.cost_inp_mini').val()) ? cmin : '0')+
        '/cmax/'+(( cmax = $('form.filter input.cost_inp_max').val()) ? cmax : '10000')+
        '/langsel/'+(( langsel = $('form.filter input.lang').val()) ? langsel : '');
console.log(create_url);
    var bindings = [];
    var i = 0;
    $('.bindings input[type=checkbox]:checked').each(function() {
        bindings[i] = $(this).val();
        i++;
    });

    var csrf = $('meta[name=csrf]').attr('content').split('=');

    $('.span10.listgoods').html('Загрузка...');
    $.post(create_url, { YII_CSRF_TOKEN: csrf[1], 'binding_id[]' : bindings,
        search_name : $('form.filter .search.inp').val(), sort : $('form.filter .sort').val(),
        formatVideo : $('#formatVideo').val(),
        langVideo : $('#langVideo').val(),
        subtitlesVideo : $('#subtitlesVideo').val(),
    }, function(data) {
        $('.span10.listgoods').html(data);
        $('.box_select_result_count').hide(1);
        $(window).scrollTop(0);
    })
}


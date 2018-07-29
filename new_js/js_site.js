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

// "X" в input-ах фильтра
function tog(v){return v?'addClass':'removeClass';}
$(document).on('input', '.clearable', function(){
    $(this)[tog(this.value)]('x');
}).on('mousemove', '.x', function( e ){
    $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');
}).on('touchstart click', '.onX', function( ev ){
    ev.preventDefault();
    $(this).removeClass('x onX').val('').change();
    $(this).prev().val('');
    show_result_count();
});

//Функция живого поиска
function interactiveSearch(classInput, data, inp_name, result) {
    $(classInput).bind("change keyup input click", function () {
        if (this.value.length >= 2) {
            if ((val = findEqual(this.value, data)) != '') {
                $(result).html(val).fadeIn();
            }
        }
        else {
            $(classInput).prev().val(0);
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
            i = index;
            $.each(item, function (index, val) {
                if (val.toLowerCase().indexOf(value.toLowerCase()) != -1) result_value += '<li rel="' + i + '" onclick="select_item($(this), \''+inp_name+'\')">' + val + '</li>';
            });

        });
        return result_value;
    }
}

// Живой поиск для Izda
function liveFindIzda(entity, lang, cid) {
    var izda_search = [];
    let find_izda = $(".find_izda");
    $.ajax({
        url: '/entity/getizdadata',
        data: {entity: entity, lang: lang, cid: cid},
        type: 'GET',
        beforeSend: function () {
            if (find_izda.val() == '') {
                find_izda.attr('disabled', true);
                find_izda.val('Загрузка...');
            }
        },
        success: function (data) {
            izda_search = JSON.parse(data);
            var search_izd = [];
            $.each(izda_search, function(index, value) {
                if ((value != '') && (value != null) ) search_izd[index] = value;
            });
            interactiveSearch('.find_izda', search_izd, 'izda', '.search_result_izda');
            if (find_izda.attr('disabled') == 'disabled') {
                find_izda.attr('disabled', false);
                find_izda.val('');
            }
        },
        error: function () {
            console.log("Error response");
        },
    });
}

function liveFindSeries(entity, lang, cid) {
    var series_search = [];
    find_series = $(".find_series");
    $.ajax({
        url: '/entity/getseriesdata',
        data: {entity: entity, lang: lang, cid: cid},
        type: 'GET',
        beforeSend: function () {
            if (find_series.val() == '') {
                find_series.attr('disabled', true);
                find_series.val('Загрузка...');
            }
        },
        success: function (data) {
            series_search = JSON.parse(data);
            var search_series = [];
            $.each(series_search, function (index, value) {
                if ((value != '') && (value != null)) search_series[index] = value;
            });
            interactiveSearch('.find_series', search_series, 'seria', '.search_result_series');
            if (find_series.attr('disabled') == 'disabled') {
                find_series.attr('disabled', false);
                find_series.val('');
            }
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

    show_result_count();
}

//Выбор элемента MP
function select_item_mp(id, inp_name, title, show_inp_name) {
    $('input[name=' + inp_name + ']').val(id);
    $('input[name=' + show_inp_name + ']').val(title);
    show_result_count();
}

// Подсчёт результатов фильтра
function show_result_count(url = '/ru/site/gtfilter/') {

    var frm = $('form.filter').serialize();
    var csrf = $('meta[name=csrf]').attr('content').split('=');
    frm = frm + '&' + csrf[0] + '=' + csrf[1];
    $.ajax({
        url: url,
        type: "POST",
        data: frm,
        beforeSend: function(){
            $('#loader-filter').html('&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)');
        },
        success: function (data) {
            $('#loader-filter').html('&nbsp;('+data+')');
        },
        error: function (data) {
            console.log('Error response');
        }
    });
}

//Вывод результата фильтра
function show_items(url = '/ru/site/ggfilter/') {

    var create_url;
    create_url = url +
        'entity/'+(( entity = $('form.filter input.entity_val').val()) ? entity : '100')+
        '/cid/'+(( cid = $('form.filter input.cid_val').val()) ? cid : '0')+
        '/author/'+(( author = $('form.filter input[name=author]').val()) ? author : '')+
        '/avail/'+(( avail = $('form.filter select[name=avail]').val()) ? avail : '1')+
        '/ymin/'+(( ymin = $('form.filter input.year_inp_mini').val()) ? ymin : '0')+
        '/ymax/'+(( ymax = $('form.filter input.year_inp_max').val()) ? ymax : '3000')+
        '/izda/'+(( izda = $('form.filter input[name=izda]').val()) ? izda : '0')+
        '/seria/'+(( seria = $('form.filter input[name=seria]').val()) ? seria : '0')+
        '/min_cost/'+(( cmin = $('form.filter input.cost_inp_mini').val()) ? cmin : '0')+
        '/max_cost/'+(( cmax = $('form.filter input.cost_inp_max').val()) ? cmax : '10000')+
        '/langsel/'+(( langsel = $('form.filter input[name=langsel]').val()) ? langsel : '');
    var bindings = [];
    var i = 0;

    bindings = $('#binding_select').val();
    var csrf = $('meta[name=csrf]').attr('content').split('=');

    console.log(url);

    $.ajax({
        url: create_url,
        type: "POST",
        data: { YII_CSRF_TOKEN: csrf[1],
            'binding_id[]' : bindings,
            name_search : $('#name_search').val(),
            sort : $('form.filter .sort').val(),
            formatVideo : $('#formatVideo').val(),
            langVideo : $('#langVideo').val(),
            subtitlesVideo : $('#subtitlesVideo').val(),
        },
        beforeSend: function(){
            $('.span10.listgoods').html('Загрузка...');
        },
        success: function (data) {
            $('.span10.listgoods').html(data);
            $('.box_select_result_count').hide(1);
            $(window).scrollTop(0);
        },
        error: function (msg) {
            console.log (msg);
        }
    });
}

// Управление фильтром по типу/переплету
function change_all_binding(event, binding_all = false) {
    if (binding_all) {
        if (event.target.checked) {
            otherBinding = event.target.parentElement.nextElementSibling;
            do
            {
                otherBinding.firstElementChild.checked = false;
            }
            while (otherBinding = otherBinding.nextElementSibling)
        }
    }
    else {
        event.target.parentElement.parentElement.children[1].firstElementChild.checked = false;
    }
}

function liveFindAuthorMP(entity, url, cid) {
    find_author = $('.find_author');
    var dataPost = {entity: entity, cid: cid};
    find_author.marcoPolo({
        minChars:3,
        cache : false,
        hideOnSelect: true,
        delay: 50,
        url: url,
        data:dataPost,
        formatMinChars: false,
        formatItem:function (data, $item, q) {
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'author\', \'' + data.title + '\', \'new_author\')">' + data.title + '</li>';
        },
    });
}

function liveFindPublisherMP(entity, url, cid) {
    find_pub = $('.find_izda');
    var dataPost = {entity: entity, cid: cid};
    find_pub.marcoPolo({
        minChars:3,
        cache : false,
        hideOnSelect: true,
        delay: 50,
        url: url,
        data:dataPost,
        formatMinChars: false,
        formatItem:function (data, $item, q) {
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'izda\', \'' + data.title + '\', \'new_izda\')">' + data.title + '</li>';
        },
    });
}

function liveFindSeriesMP(entity, url, cid) {
    find_series = $('.find_series');
    var dataPost = {entity: entity, cid: cid};
    find_series.marcoPolo({
        minChars:3,
        cache : false,
        hideOnSelect: true,
        delay: 50,
        url: url,
        data:dataPost,
        formatMinChars: false,
        formatItem:function (data, $item, q) {
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'seria\', \'' + data.title + '\', \'new_series\')">' + data.title + '</li>';
        },
    });
}
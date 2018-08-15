$(document).ready(function(){
    initAAddCart();
    $(document).click(function (event) {
        if ($(event.target).closest('.subcat').length) return;
        if ($(event.target).closest('.open_subcat').length) return;
        $('.subcat').hide();
        $('.open_subcat').removeClass('open');
        event.stopPropagation();
    });
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
function show_result_count(url) {
    if (url === undefined) {
        url = '/ru/site/gtfilter/';
    }
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
            console.log(data);
        }
    });
}

//Вывод результата фильтра
function show_items(url, page) {
    if (url === undefined) {
        url = '/ru/site/ggfilter/';
    }
    if (page === undefined) {
        page = 0;
    }
    var create_url;
    create_url = url +
        'entity_val/'+(( entity = $('form.filter input.entity_val').val()) ? entity : '100')+
        '/cid_val/'+(( cid = $('form.filter input.cid_val').val()) ? cid : '0')+
        '/author/'+(( author = $('form.filter input[name=author]').val()) ? author : '')+
        '/avail/'+(( avail = $('form.filter select[name=avail]').val()) ? avail : '1')+
        '/ymin/'+(( ymin = $('form.filter input.year_inp_mini').val()) ? ymin : '0')+
        '/ymax/'+(( ymax = $('form.filter input.year_inp_max').val()) ? ymax : '0')+
        '/publisher/'+(( publisher = $('form.filter input[name=publisher]').val()) ? publisher : '0')+
        '/seria/'+(( seria = $('form.filter input[name=seria]').val()) ? seria : '0')+
        '/min_cost/'+(( cmin = $('form.filter input.cost_inp_mini').val()) ? cmin : '0')+
        '/max_cost/'+(( cmax = $('form.filter input.cost_inp_max').val()) ? cmax : '0')+
        '/lang/'+(( lang = $('form.filter input[name=lang]').val()) ? lang : '') +
        '/page/' + page;
    var bindings = [];
    var i = 0;

    bindings = $('#binding_select').val();
    var csrf = $('meta[name=csrf]').attr('content').split('=');

    items_content = $('.span10 .items');
    //items_content = $('.span10.listgoods');

    $.ajax({
        url: create_url,
        type: "POST",
        data: { YII_CSRF_TOKEN: csrf[1],
            'binding[]' : bindings,
            name_search : $('#name_search').val(),
            sort : $('form.filter .sort').val(),
            format_video : $('#format_video').val(),
            lang_video : $('#lang_video').val(),
            subtitles_video : $('#subtitles_video').val(),
        },
        beforeSend: function(){
            items_content.html('Загрузка...');
        },
        success: function (data) {
            items_content.html(data);
            $('.box_select_result_count').hide(1);
            //$(window).scrollTop(0);
        },
        error: function (msg) {
            console.log (msg);
        }
    });
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
    find_pub = $('.find_publisher');
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
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'publisher\', \'' + data.title + '\', \'new_publisher\')">' + data.title + '</li>';
        },
    });
}

function liveFindPerformerMP(entity, url, cid) {
    find_pub = $('.find_performer');
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
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'performer\', \'' + data.title + '\', \'new_performer\')">' + data.title + '</li>';
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
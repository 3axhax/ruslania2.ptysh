var mini_map_isOn = 0;
var TimerId;

$(document).ready(function(){
    initAAddCart();
    initPeriodicPriceSelect();
    $(".trigger_keyboard").click(function (event) {
        $(".keyboard_on").toggle();
        $(".keyboard_off").toggle();
        $(".trigger_keyboard img").toggleClass('keyboard_off_img');
        if ($(".keyboard_off").is(':visible')) {
            $("#virtual_keyboard").hide();
        }
        else {
            input_search = $("input.search_text");
            keyboard = $("#virtual_keyboard");
            temp_keyboard = keyboard.remove();
            input_search.after(temp_keyboard);
            temp_keyboard.show();
            temp_keyboard.jkeyboard({
                layout: "russian",
                input: input_search,
            });
        }
    });
    $("input.enable_virtual_keyboard").on('focus', function (event) {
        var keyboard_visible = false;
        if ($(".keyboard_on").is(':visible')) {
            keyboard_visible = true;
        }
        if (keyboard_visible) {
            if (window.old_target === undefined || window.old_target !== event.target || !$("#virtual_keyboard").is(':visible')) {
                keyboard = $("#virtual_keyboard");
                temp_keyboard = keyboard.remove();
                $(event.target).after(temp_keyboard);
                temp_keyboard.show();
                temp_keyboard.jkeyboard({
                    layout: "russian",
                    input: $(event.target),
                });
                window.old_target = event.target;
                /*$(".close_keyboard").click(function (event) {
                    keyboard.hide();
                });*/
            }
        }
    });
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

    $('.select2_series').select2();
    $('.select2_publishers').select2();
    $('.select2_periodic').select2({minimumResultsForSearch: Infinity});

    $('li.dd_box .click_arrow').click(function () {
        var $t = $(this);
        if ($t.closest('li').hasClass('show_dd')) {
            $('.dd_box').removeClass('show_dd');
            if ($t.parents().is('li.more_menu') && !$t.parent().is('li.more_menu')) {
                $t.parents('li.more_menu').addClass('show_dd');
            }
        } else {
            $('.dd_box').removeClass('show_dd');
            $t.closest('li').addClass('show_dd');
            $t.closest('li.more_menu').addClass('show_dd');
        }
        return false;
    });

    $('li.dd_box.more_menu').click(function () {
        var $t = $(this);
        if ($t.hasClass('show_dd')) {
            $('.dd_box').removeClass('show_dd');
        }
        else {
            $('.dd_box').removeClass('show_dd');
            $t.addClass('show_dd');
        }
    });

    $(document).click(function (event) {
        if ($(event.target).closest("li.dd_box").length)
            return;
        $('li.dd_box').removeClass('show_dd');
        event.stopPropagation();
    });

    $('.search_text').on('keydown', function (a) {
        if (a.keyCode == 13) $('#srch').submit();
    });

    $('.cart_box').click(function () {
        $('#cart_renderpartial').toggle(100);
        mini_map_isOn = 1 - mini_map_isOn;
        if (mini_map_isOn)
            TimerId = setTimeout(mini_cart_off, 10000);
        else
            clearTimeout(TimerId);
    });

    $(window).scroll(function() {
        var minicart = $('.header_logo_search_cart .span1.cart');
        if ($(window).scrollTop() > 310) {
            minicart.css('position', 'fixed');
            minicart.css('background', '#fff');
            minicart.css('top', '-37px');
            //$('.span1', minicart).css('display', 'none');
            minicart.css('width', 'auto');
            minicart.css('right', '0');
            minicart.css('z-index', '999999');
            minicart.css('border-radius', '4px 0 0 4px');
            minicart.css('box-shadow', '0 0 10px rgba(0,0,0,0.3)');
            minicart.css('padding-left', '20px');
        }
        else {
            minicart.css('position', '');
            minicart.css('background', '');
            minicart.css('top', '');
            $('.span1', minicart).css('display', '');
            minicart.css('width', '');
            minicart.css('right', '');
            minicart.css('z-index', '');
            minicart.css('border-radius', '');
            minicart.css('box-shadow', '');
            minicart.css('padding-left', '');
        }
    });

    $(document).click(function (event) {
        if ($(event.target).closest(".select_lang").length)
            return;
        $('.select_lang .dd_select_lang').hide();
        $('.select_lang').removeClass('act');
        $('.select_lang .label_lang').removeClass('act');
        event.stopPropagation();
    });

    $.fn.prettyPhoto({social_tools: false});
    $('a.read_book').click(function () {
        var $this = $(this);
        var images = [];
        if ($this.attr('data-images') != '')
        {
            images = $this.attr('data-images').split('|');
            if (images.length > 0)
                $.prettyPhoto.open(images, [], []);
        }
    });
    $(document).click(function (event) {
        if ($(event.target).closest(".span1.cart, .b-basket-list").length)
            return;
        $('.b-basket-list').fadeOut();
        event.stopPropagation();
    });

    $(document).click(function (event) {
        if ($(event.target).closest(".select_valut").length)
            return;
        $('.select_valut .dd_select_valut').hide();
        $('.label_valut').removeClass('act');
        event.stopPropagation();
    });

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

    $('.red_checkbox').on('click', function(){
        var cont = $(this);
        var inputId = cont.data('connect');
        check_search(cont, inputId);
    });

    $('.open_subcat').on('click', function(){
        var c = $(this);
        var lvl = c.data('lvl');
        var cont = c.siblings('ul');
        show_sc(cont, c, lvl);
        return false;
    });

});

function check_search(cont, inputId) {
    if ($('.check', cont).hasClass('active')) {
        $('.check', cont).removeClass('active');
        if (inputId == undefined)
            $('.avail', cont).val('');
        else
            $('#' + inputId).val('');
    }
    else {
        $('.check', cont).addClass('active');
        if (inputId == undefined)
            $('.avail', cont).val('1');
        else
            $('#' + inputId).val('1');
    }
    if (inputId != undefined) $('#Search').marcoPolo('search');
}

// "X" в input-ах фильтра
function tog(v){return v?'addClass':'removeClass';}

// Выбор фильтра
/*
function select_item(item, inp_name) {
    var id = item.attr('rel');

    if (!$(item).parent().parent().is('div.interactive_search')) $('.text span', item.parent().parent().parent().parent()).html(item.html());
    $('.list_dd', item.parent().parent().parent().parent()).hide();
    $('input[name=' + inp_name + ']', item.parent().parent().parent().parent()).val(id);

    show_result_count();
}
*/

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
        '/publisher/'+(( publisher = $('form.filter input[name=publisher]').val()) ? publisher :
            ((publisher_s = $('form.filter select[name=publisher]').val()) ? publisher_s : '0'))+
        '/seria/'+(( seria = $('form.filter input[name=seria]').val()) ? seria :
            ((seria_s = $('form.filter select[name=seria]').val()) ? seria_s : '0'))+
        '/min_cost/'+(( cmin = $('form.filter input.cost_inp_mini').val()) ? cmin : '0')+
        '/max_cost/'+(( cmax = $('form.filter input.cost_inp_max').val()) ? cmax : '0')+
        '/lang/'+(( lang = $('form.filter input[name=lang]').val()) ? lang : '') +
        '/pre_sale/'+(( pre_sale = $('form.filter select[name=pre_sale]').val()) ? pre_sale : '') +
        '/performer/'+(( performer = $('form.filter input[name=performer]').val()) ? performer : '') +
        '/page/' + page;
    var bindings = [];
    var i = 0;

    bindings = $('#binding_select').val();
    var csrf = $('meta[name=csrf]').attr('content').split('=');

    items_content = $('.span10 .items');

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
            country : $('#country').val(),
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
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'seria\', \'' + (data.title) + '\', \'new_series\')">' + (data.title) + '</li>';
        },
    });
}

function liveFindDirectorsMP(entity, url, cid) {
    find_series = $('.find_directors');
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
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'directors\', \'' + data.title + '\', \'new_directors\')">' + data.title + '</li>';
        },
    });
}

function liveFindActorsMP(entity, url, cid) {
    find_series = $('.find_actors');
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
            return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'actors\', \'' + data.title + '\', \'new_actors\')">' + data.title + '</li>';
        },
    });
}

function getSeries(entity, url, cid, selected_item) {
    $(document).ready(function () {
        if (cid == undefined) cid = 0;
        select_series = $('.select2_series');
        select_series_visible = select_series.next("span").children("span").children("span");
        var frm = 'entity='+entity+'&cid='+cid;
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        frm = frm + '&' + csrf[0] + '=' + csrf[1];
        $.ajax({
            url: url,
            type: "POST",
            data: frm,
            beforeSend: function(){
                select_series_visible.addClass('disabled');
            },
            success: function (data) {
                titles = JSON.parse(data);
                for (id in titles) {
                    for (tittle in titles[id]) {
                        if (selected_item == titles[id][tittle]) {
                            select_series
                                .append($("<option></option>")
                                    .attr("value", id)
                                    .attr("selected", true)
                                    .text(titles[id][tittle]));
                        }
                        else {
                            select_series
                                .append($("<option></option>")
                                    .attr("value", id)
                                    .text(titles[id][tittle]));
                        }
                    }
                }
                select_series_visible.removeClass('disabled');
                show_result_count();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

}

function getPublishers(entity, url, cid, selected_item) {
    $(document).ready(function () {
        if (cid == undefined) cid = 0;
        select_publishers = $('.select2_publishers');
        select_publishers_visible = select_publishers.next("span").children("span").children("span");
        var frm = 'entity=' + entity + '&cid=' + cid;
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        frm = frm + '&' + csrf[0] + '=' + csrf[1];
        $.ajax({
            url: url,
            type: "POST",
            data: frm,
            beforeSend: function () {
                select_publishers_visible.addClass('disabled');
            },
            success: function (data) {
                titles = JSON.parse(data);
                for (id in titles) {
                    for (tittle in titles[id]) {
                        if (selected_item == titles[id][tittle]) {
                            select_publishers
                                .append($("<option></option>")
                                    .attr("value", id)
                                    .attr("selected", true)
                                    .text(titles[id][tittle]));
                        }
                        else {
                            select_publishers
                                .append($("<option></option>")
                                    .attr("value", id)
                                    .text(titles[id][tittle]));
                        }
                    }
                }
                select_publishers_visible.removeClass("disabled");
                show_result_count();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
}

function mini_cart_off() {
    if (mini_map_isOn == 1) $('#cart_renderpartial').toggle(100);
    mini_map_isOn = 0;
}

function show_sc(cont, c, lvl) {
    if (cont.css('display') == 'none') {
        $('ul.lvlcat' + lvl).hide();
        $('a.subcatlvl' + lvl).removeClass('open');
        cont.show();
        c.addClass('open');
    }
    else {
        $('ul.lvlcat' + lvl + ' ul').hide();
        $('ul.lvlcat' + lvl + ' a.open_subcat').removeClass('open');
        cont.hide();
        c.removeClass('open');
    }

    var liW = c.parent().width();

    cont.css('position', 'absolute');
    cont.css('left', (liW + 20) + 'px');
    cont.css('top', '0');
    cont.css('z-index', '999999');
    cont.css('background', '#fff');
    cont.css('width', '249px');
    cont.css('box-shadow', '0px 3px 10px 3px rgba(0, 0, 0, 0.15)');
    cont.css('padding', '0px 10px');
}

jQuery.cachedScript = function(url, options) {
    options = $.extend(options || {}, {
        dataType: "script",
        cache: true,
        url: url
    });
    return jQuery.ajax(options);
};
//$.cachedScript("ajax/test.js").done(function(script, textStatus) {
//    console.log( textStatus );
//});

var mini_map_isOn = 0;
var TimerId;

$(document).ready(function(){
    initAAddCart();
    initPeriodicPriceSelect();
    scriptLoader('/new_js/modules/jkeyboard-master/lib/js/jkeyboard.js').callFunction(function(){
        $(".trigger_keyboard").click(function (event) {
            var keyboard = $("#virtual_keyboard");
            $(".keyboard_on").toggle();
            $(".keyboard_off").toggle();
            $(".trigger_keyboard img").toggleClass('keyboard_off_img');
            if ($(".keyboard_off").is(':visible')) {
                keyboard.hide();
            }
            else {
                input_search = $("td.search_text input.enable_virtual_keyboard");
                temp_keyboard = keyboard.remove();
                input_search.after(temp_keyboard);
                temp_keyboard.show();
                temp_keyboard.jkeyboard({
                    layout: "russian",
                    input: input_search
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
                    var keyboard = $("#virtual_keyboard");
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
    });

    $(document).click(function (event) {
        if ($(event.target).closest('.subcat').length) return;
        if ($(event.target).closest('.open_subcat').length) return;
        $('.subcat').hide();
        $('.open_subcat').removeClass('open');
        event.stopPropagation();
    });
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

    /* $('.cart_box').click(function () {
        $('#cart_renderpartial').toggle(100);
        mini_map_isOn = 1 - mini_map_isOn;
        if (mini_map_isOn)
            TimerId = setTimeout(mini_cart_off, 10000);
        else
            clearTimeout(TimerId);
    }); */

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

    scriptLoader('/js/jquery.prettyPhoto.js').callFunction(function() {
        $.fn.prettyPhoto({social_tools: false});
        $('a.read_book').click(function () {
            var $this = $(this);
            var images = [];
            if ($this.attr('data-images') != '') {
                images = $this.attr('data-images').split('|');
                if (images.length > 0)
                    $.prettyPhoto.open(images, [], []);
            }
        });
        $("a[id^='img']").each(function (i, el) {
            $(el).prettyPhoto({social_tools: false});
        });
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

    /*$(document).on('input', '.clearable', function(){
        $(this)[tog(this.value)]('x');
    }).on('mousemove', '.x', function( e ){
        $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');
    }).on('touchstart click', '.onX', function( ev ){
        ev.preventDefault();
        $(this).removeClass('x onX').val('').change();
        $(this).prev().val('');
        //show_result_count();
    });*/

    $('.open_subcat').on('click', function(){
        var c = $(this);
        var lvl = c.data('lvl');
        var cont = c.siblings('ul');
        show_sc(cont, c, lvl);
        return false;
    });

    lazyImageLoader.onDomReady();
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
    //if (inputId != undefined) $('#Search').marcoPolo('search');
}

// "X" в input-ах фильтра
function tog(v){return v?'addClass':'removeClass';}

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

lazyImageLoader = function(){
    var data;

    function onDomReady(){
        refreshImgList();
        $(window).on('scroll', onScroll);
    }

    function onScroll(){
        var countScroll = getScrollTop() + getWindowHeight();
        data = data.filter(function(elemData){
            if (countScroll > elemData.offsetTop){
                startLoad(elemData.elem);
                return false;
            }
            return true;
        });
    }

    function startLoad(img){
        var newImg = document.createElement('img');
        newImg.alt = img.alt;
        //newImg.title = img.title;

        var className = img.className;
        if (img.getAttribute('lazyClass')) className += (className ? ' ' : '') + img.getAttribute('lazyClass');

        if (className != '') newImg.className = className;
        newImg.onload = function() {replace(newImg, img);};
        newImg.onerror = function() {
            img.src = '/pic1/nophoto.gif';
        };
        newImg.src = img.getAttribute('lazySrc');
    }

    function getWindowHeight(){
        if (self.innerHeight) return self.innerHeight;
        if (document.documentElement && document.documentElement.clientHeight) return document.documentElement.clientHeight;
        if (document.body) return document.body.clientHeight;
    }

    function refreshImgList(){
        data = [];
        addToImgList($('img[lazySrc]'));
    }

    function addToImgList(imgs){
        var countScroll = getScrollTop() + getWindowHeight();
        for (var len = imgs.length, offset; len--;) {
            if (imgs[len].getAttribute('lazySrc')){
                offset = getOffsetTop(imgs[len]);
                if (countScroll + 100 > offset) startLoad(imgs[len]);
                else data.push({
                    offsetTop: offset,
                    elem: imgs[len]
                });
            }
        }
    }

    /**@returns {Number|number} количество Y прокрутки страницы*/
    function getScrollTop() {
        return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
    }

    /**@returns {Number|number} позиция Y верхнего левого угла относительно верхнеей угла страницы*/
    function getOffsetTop(elem) {
        if (elem.getBoundingClientRect) {
            return Math.round(elem.getBoundingClientRect().top + getScrollTop() - (document.documentElement.clientTop || document.body.clientTop || 0));
        } else {
            var top = 0;
            while (elem) {
                top += parseFloat(elem.offsetTop);
                elem = elem.offsetParent;
            }
            return top;
        }
    }

    /**
     * @param newElem что заменяем
     * @param oldElem на что заменяем
     */
    function replace(newElem, oldElem){
        if (oldElem) {
            if (oldElem.parentNode) oldElem.parentNode.insertBefore(newElem, oldElem);
        }
        if (oldElem.parentNode) oldElem.parentNode.removeChild(oldElem);
    }

    return {
        onDomReady: onDomReady,
        refreshImgList: refreshImgList,
        addToImgList: addToImgList
    };
}();

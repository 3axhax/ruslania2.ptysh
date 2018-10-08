<?php
// session_start();
// if(!isset($_SESSION['ert']))
// {
// echo 'no4';
// $_SESSION['ert'] = '456';
// }
// else
// echo 'yes4';
// echo $_SESSION['ert'];
// $session = Yii::app()->session;
// echo  $session['shopcartkey'];

$url = explode('?', $_SERVER['REQUEST_URI']);
$url = trim($url[0], '/');

   

//if (isset($_GET['langsel'])) {
//
//Yii::app()->getRequest()->cookies['langsel'] = new CHttpCookie('langsel', $_GET['langsel']);
//
//}

$entity = Entity::ParseFromString($url);

//if (Yii::app()->getRequest()->cookies['showSelLang']->value != '1') {
//
//Yii::app()->language = $this->getPreferLanguage();
//}

$url = explode('?', $_SERVER['REQUEST_URI']);
$url = trim($url[0], '/');

$ex = explode('?', $url);

$ex = explode('/', $ex[0]);

$url = $ex;

$ctrl = Yii::app()->getController()->id;

$ui = Yii::app()->ui;
?><!DOCTYPE html><html>
    <head>
        <title><?= $this->pageTitle; ?></title>
        <meta name="keywords" content="<?= $this->pageKeywords ?>">
        <?php if ($canonicalPath = $this->getCanonicalPath()): ?>
        <link rel="canonical" href="<?= $canonicalPath ?>"/>
        <?php endif; ?>
        <?php foreach ($this->getNextPrevPath() as $relName => $path): ?>
        <link rel="<?= $relName ?>" href="<?= $path ?>" />
        <?php endforeach; ?>
        <meta name="description" content="<?= $this->pageDescription ?>">
        <META name="verify-v1" content="eiaXbp3vim/5ltWb5FBQR1t3zz5xo7+PG7RIErXIb/M="/>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
        <?php if (mb_strpos(Yii::app()->getRequest()->getPathInfo(), 'request-books', null, 'utf-8') !== false): ?>
        <meta name="robots" content="noindex">
        <?php endif; ?>
        <?php foreach ($this->getOtherLangPaths() as $lang=>$path): ?>
        <link rel="alternate" href="<?= $path ?>" hreflang="<?= $lang ?>">
        <?php endforeach; ?>
        <link href="/new_style/jscrollpane.css" rel="stylesheet" type="text/css"/>
        <link href="/new_style/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="/new_js/modules/jkeyboard-master/lib/css/jkeyboard.css" rel="stylesheet" type="text/css"/>
        <link href="/new_style/select2.css" rel="stylesheet" type="text/css"/>

        <link rel="stylesheet" href="/new_style/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="/css/template_styles.css" />
        <link rel="stylesheet" href="/css/jquery.bootstrap-touchspin.min.css">
        <link rel="stylesheet" href="/css/opentip.css">
        <link rel="stylesheet" type="text/css" href="/css/jquery-bubble-popup-v3.css"/>
        <link href="/new_style/style_site.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="/css/prettyPhoto.css"/>
        <script src="/new_js/jquery.js" type="text/javascript"></script>
        <script src="/new_js/jquery.mousewheel.min.js" type="text/javascript"></script>
        <script src="/new_js/js_site.js" type="text/javascript"></script>
        <script src="/new_js/multiple-select.js" type="text/javascript"></script>

        <meta name="csrf" content="<?= MyHTML::csrf(); ?>"/>
        <script src="/new_js/jScrollPane.js" type="text/javascript"></script>
        <script src="/new_js/slick.js" type="text/javascript" charset="utf-8"></script>
        <script src="/new_js/nouislider.js" type="text/javascript" charset="utf-8"></script>
        <link href="/new_js/nouislider.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>
        <script src="/js/common.js"></script>
        <script src="/new_js/jquery.bootstrap-touchspin.min.js"></script>
        <script src="/js/opentip.js"></script>
        <script type="text/javascript" src="/js/marcopolo.js"></script>
        <script type="text/javascript" src="/new_js/modules/jkeyboard-master/lib/js/jkeyboard.js"></script>
        <script type="text/javascript" src="/new_js/modules/select2.full.js"></script>
        <!--[if lt IE 9]>
<script src="libs/html5shiv/es5-shim.min.js"></script>
<script src="libs/html5shiv/html5shiv.min.js"></script>
<script src="libs/html5shiv/html5shiv-printshiv.min.js"></script>
<script src="libs/respond/respond.min.js"></script>
<![endif]-->
        <script type="text/javascript" src="/js/magnific-popup.js"></script>
        <script>

            function show_subs(uid, sid, subsid) {
                var csrf = $('meta[name=csrf]').attr('content').split('=');

                $.post('/site/loadhistorysubs', {uid: uid, sid: sid, YII_CSRF_TOKEN: csrf[1], subsid: subsid}, function (data) {

                    $('.history_subs_box').css('top', $(window).scrollTop() + 50);

                    $('.history_subs_box .table_box').html(data);

                    $('.history_subs_box, .opacity').show();

                });

            }

            $(document).ready(function () {

                $('li.dd_box .click_arrow').click(function () {

                    if ($(this).closest('li').hasClass('show_dd')) {

                        $('.dd_box').removeClass('show_dd');
                        if ($(this).parents().is('li.more_menu') && !$(this).parent().is('li.more_menu')) {
                            $(this).parents('li.more_menu').addClass('show_dd');
                        }

                    } else {

                        $('.dd_box').removeClass('show_dd');

                        $(this).closest('li').addClass('show_dd');
                        $(this).closest('li.more_menu').addClass('show_dd');

                    }

                    return false;
                })


                $('li.dd_box.more_menu').click(function () {

                    if ($(this).hasClass('show_dd')) {

                        $('.dd_box').removeClass('show_dd');

                    } else {

                        $('.dd_box').removeClass('show_dd');

                        $(this).addClass('show_dd');

                    }


                })

                $(document).click(function (event) {
                    if ($(event.target).closest("li.dd_box").length)
                        return;
                    $('li.dd_box').removeClass('show_dd');
                    event.stopPropagation();
                });


                $(document).ready(function ()
                {
                    $('.search_text').on('keydown', function (a)
                    {
                        if (a.keyCode == 13)
                        {
                            $('#srch').submit();
                        }
                    });

                    function decline_days(num) {
                        var count = num;

                        num = num % 100;

                        if (num > 19) {
                            num = num % 10;
                        }

                        switch (num) {

                            case 1:
                            {
                                return count + ' <?= $ui->item('A_NEW_SEARCH_RES_COUNT3'); ?>';
                            }

                            case 2:
                            case 3:
                            case 4:
                            {
                                return count + ' <?= $ui->item('A_NEW_SEARCH_RES_COUNT2'); ?>';
                            }

                            default:
                            {
                                return count + ' <?= $ui->item('A_NEW_SEARCH_RES_COUNT1'); ?>';
                            }
                        }
                    }

<?php
$act = array();
$act = array(1, ' active');
if (isset($_GET['avail'])) {
if ($_GET['avail'] == '1') $act = array(1, ' active');
else $act = array('', '');
}
?>
                    $('#Search').marcoPolo({
                        url: '<?= Yii::app()->createUrl('liveSearch/general') ?>',
                        cache: false,
                        minChars: 3,
                        formatMinChars: function (minChars, $item) {
                            return '<em><?= $ui->item('SEARCH_TIP2') ?></em>';
                        },
                        formatNoResults: function (q, $item) {
                            return '<em><?= $ui->item('MSG_SEARCH_ERROR_NOTHING_FOUND') ?></em>';
                        },
                        hideOnSelect: false,
                        dynamicData: {avail: function () {
                                return $('#js_avail').val();
                            }},
                        formatItem: function (data, $item, q)
                        {
                            var ret = '';
                            ret += data;
                            return ret;
                        }

                    });
                });



                $.ajax({
                    url: '/cart/getcount',
                    data: 'id=1',
                    type: 'GET',
                    success: function (data) {
                        var d = JSON.parse(data);
                        //alert(data);
                        $('div.cart_count').html(d.countcart)
                        $('div.span1.cart .cost').html(d.totalPrice)
                    }
                });
                initPeriodicPriceSelect();
                //initAAddCart();
            });

            $(document).ready(function () {

                $(document).click(function (event) {
                    if ($(event.target).closest(".dd_box_select").length)
                        return;
                    $('.dd_box_select .list_dd').hide();
                    event.stopPropagation();
                })

                var blockScroll1 = false;
                var blockScroll2 = false;
                var blockScroll3 = false;
                var page_authors = 1;
                var page_izda = 1;
                var page_seria = 1;
                $('.dd_box_select .list_dd.authors_dd').scroll(function () {

                    if (($(this).height() + $(this).scrollTop()) >= $('.items', $(this)).height() && !blockScroll1) {

                        blockScroll1 = true;
                        page_authors++;
                        var tthis = $(this);
                        $('.load_items', $(this)).show();
                        $('.load_items', $(this)).html('<?= $ui->item('A_NEW_LOAD'); ?>');
                        var csrf = $('meta[name=csrf]').attr('content').split('=');

                        var url = '/site/loaditemsauthors/page/' + page_authors + '/entity/' + $('.entity_val').val() + '/cid/' + $('.cid_val').val();

                        $.post(url, {YII_CSRF_TOKEN: csrf[1]}, function (data) {
                            //alert(data);
                            $('.items .rows', tthis).append(data);
                            blockScroll1 = false;
                            $('.load_items', tthis).html('');
                            $('.load_items', tthis).hide();
                        })

                    }

                })
                $('.dd_box_select .list_dd.izda_dd').scroll(function () {

                    if (($(this).height() + $(this).scrollTop()) >= $('.items', $(this)).height() && !blockScroll2) {

                        blockScroll2 = true;
                        page_izda++;
                        var tthis = $(this);
                        $('.load_items', $(this)).show();
                        $('.load_items', $(this)).html('<?= $ui->item('A_NEW_LOAD'); ?>');
                        var csrf = $('meta[name=csrf]').attr('content').split('=');

                        var url = '/site/loaditemsizda/page/' + page_izda + '/entity/' + $('.entity_val').val() + '/cid/' + $('.cid_val').val();

                        $.post(url, {YII_CSRF_TOKEN: csrf[1]}, function (data) {
                            //alert(data);
                            $('.items .rows', tthis).append(data);
                            blockScroll2 = false;
                            $('.load_items', tthis).html('');
                            $('.load_items', tthis).hide();
                        })

                    }

                })
                $('.dd_box_select .list_dd.seria_dd').scroll(function () {

                    if (($(this).height() + $(this).scrollTop()) >= $('.items', $(this)).height() && !blockScroll3) {

                        blockScroll3 = true;
                        page_seria++;
                        var tthis = $(this);
                        $('.load_items', $(this)).show();
                        $('.load_items', $(this)).html('<?= $ui->item('A_NEW_LOAD'); ?>');
                        var csrf = $('meta[name=csrf]').attr('content').split('=');

                        var url = '/site/loaditemsseria/page/' + page_seria + '/entity/' + $('.entity_val').val() + '/cid/' + $('.cid_val').val();

                        $.post(url, {YII_CSRF_TOKEN: csrf[1]}, function (data) {
                            //alert(data);
                            $('.items .rows', tthis).append(data);
                            blockScroll3 = false;
                            $('.load_items', tthis).html('');
                            $('.load_items', tthis).hide();
                        })

                    }

                })

            })

            var mini_map_isOn = 0;
            var TimerId;

            function mini_cart_off() {
                if (mini_map_isOn == 1)
                {
                    $('#cart_renderpartial').toggle(100);
                }
                mini_map_isOn = 0;
            }
            $(document).ready(function () {
                $('.cart_box').click(function () {
                    $('#cart_renderpartial').toggle(100);
                    mini_map_isOn = 1 - mini_map_isOn;
                    if (mini_map_isOn)
                        TimerId = setTimeout(mini_cart_off, 10000);
                    else
                        clearTimeout(TimerId);
                })
            })

            function show_sc(cont, c, lvl) {

                if (cont.css('display') == 'none') {
                    $('ul.lvlcat' + lvl).hide();
                    $('a.subcatlvl' + lvl).removeClass('open');
                    cont.show();
                    c.addClass('open');
                } else {
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

            function add2Cart(action, eid, iid, qty, type, $el)
            {

                var csrf = $('meta[name=csrf]').attr('content').split('=');
                var post =
                        {
                            entity: eid,
                            id: iid,
                            quantity: qty,
                            type: type
                        };
                post[csrf[0]] = csrf[1];

                var seconds_to_wait = 10;


                var opentip = new Opentip($el, '', {target: true, tipJoint: "bottom", group: "group-example", showOn: "click", hideOn: 'ondblclick', background: '#fff', borderColor: '#fff'});

                opentip.deactivate();

                $.post('<?=Yii::app()->createUrl('cart/')?>' + action, post, function (json)
                {

                    var json = JSON.parse(json);
                    var opentip = new Opentip($el, '<div style="padding-right: 17px;">' + json.msg +
                            '</div><div style="height: 6px;"></div><span class="timer_popup"></span> <span class="countdown">00: 10</span><a href="javascript:;" class="close_popup" onclick="$(this).parent().parent().parent().remove()"><img src="/new_img/close_popup.png" alt="" /></a>', {target: true, tipJoint: "bottom", group: "group-example", showOn: "click", hideOn: 'ondblclick', background: '#fff', borderColor: '#fff'});


                    opentip.show();

                    function doCountdown()
                    {

                        var str = '';

                        var timer = setTimeout(function ()
                        {
                            seconds_to_wait--;

                            if (seconds_to_wait < 10) {
                                str = '00:0' + seconds_to_wait;
                            } else {
                                str = '00:' + seconds_to_wait;
                            }

                            if ($('#opentip-' + opentip.id + ' span.countdown').length > 0)
                                $('#opentip-' + opentip.id + ' span.countdown').html(str);
                            if (seconds_to_wait > 0)
                                doCountdown();
                            else
                                opentip.deactivate();
                        }, 1000);
                    }

                    if (json.already)
                    {
                        $('div.already-in-cart', $el.parent()).html(json.already);
                    }



                    doCountdown();

                    <?php if ($ctrl != 'cart') : ?>

                    update_header_cart();

                    <? endif; ?>

                    <?php if ($ctrl == 'cart') : ?>

                   // var cvm = new cartVM();

                    //ko.applyBindings(cvm, $('#cart')[0]);

                    //cvm.AjaxCall(true);

                    location.href = location.href;

                    <?php endif; ?>

                })
            }


            $(document).ready(function () {
//                sortCategoryMenu('#books_menu', '#books_category', '#books_sale', true);
//                sortCategoryMenu('#sheet_music_menu', '#sheet_music_category', '#sheet_music_sale', true);
//                sortCategoryMenu('#music_menu', '#music_category', '#music_sale', true);
                //sortCategoryMenu('#periodic_menu', '#periodic_category', '#periodic_sale', false);
                $(document).click(function (event) {
                    if ($(event.target).closest(".select_lang").length)
                        return;
                    $('.select_lang .dd_select_lang').hide();
                    $('.select_lang').removeClass('act');
                    $('.select_lang .label_lang').removeClass('act');
                    event.stopPropagation();
                });

                $.fn.prettyPhoto({social_tools: false});

                $('a.read_book').click(function ()
                {


                    var $this = $(this);
                    var images = [];
                    if ($this.attr('data-images') != '')
                    {
                        images = $this.attr('data-images').split('|');
                        if (images.length > 0)
                            $.prettyPhoto.open(images, [], []);
                    }

                    //            var pdf = $this.attr('data-pdf').split('|');
                    //            if(pdf.length > 0)
                    //            {
                    //                var iid = $this.attr('data-iid');
                    //                $('#staticfiles'+iid).fadeIn();
                    //            }
                });

                /* $('.tabs_container .tabs li').click(function() {
                 
                 var $clas = $(this).attr('class').split(' ')[0];
                 
                 //alert($clas);
                 
                 $('.tabs_container .tabcontent, .tabs_container .tabs li').removeClass('active');
                 
                 $('.tabs_container .tabcontent.'+$clas).addClass('active');
                 $('.tabs_container .tabs li.'+$clas).addClass('active');
                 
                 }) */


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


            })

            function check_search(cont, inputId) {
                if ($('.check', cont).hasClass('active')) {
                    $('.check', cont).removeClass('active');
                    if (inputId == undefined)
                        $('.avail', cont).val('');
                    else
                        $('#' + inputId).val('');
                } else {
                    $('.check', cont).addClass('active');
                    if (inputId == undefined)
                        $('.avail', cont).val('1');
                    else
                        $('#' + inputId).val('1');
                }

            }

            function show_tab(cont, url) {

                if (cont.parent().hasClass('active')) {

                    location.href = cont.attr('href');


                } else {

                    $('.dd_box_bg .tabs li').removeClass('active');
                    cont.parent().addClass('active');
                    var csrf = $('meta[name=csrf]').attr('content').split('=');
                    $('.dd_box_bg .content .list').html('');

                    $.post('/site/mload' + cont.attr('href'), {YII_CSRF_TOKEN: csrf[1], id: 1}, function (data) {
                        $('.dd_box_bg .content .list').html(data);
                    })

                }
            }

            function update_header_cart() {
                $.ajax({
                    url: '/cart/getcount',
                    data: 'id=1',
                    type: 'GET',
                    success: function (data) {
                        var d = JSON.parse(data);

                        var data = {language: '<?= Yii::app()->language; ?>', is_MiniCart: 1};
                        $.getJSON('/cart/getall', data, function (json)
                        {
                            ko.mapping.fromJS(json, {}, cvm_1);

                            cvm_1.FirstLoad(false);

                        });

                        $('div.cart_count').html(d.countcart)
                        $('div.span1.cart .cost').html(d.totalPrice)
                    }
                });
            }

            function addComment() {
                var csrf = $('meta[name=csrf]').attr('content').split('=');
                var ser = $('form.addcomment').serialize() + '&' + csrf[0] + '=' + csrf[1];



                $.post('/site/addcomments/', ser, function (data) {

                    if (data) {
                        //$('.comments_block').html(data);
                        $('form span.info').html('<?= $ui->item('A_NEW_REVIEW_SENT1'); ?>');
                        $('form span.info').delay(1).show(0);

                        $('form span.info').delay(2000).hide(0);

                        $('.review form textarea').val('');
                    } else {
                        $('form span.info').html('<span style="color: #ff0000;"><?= $ui->item('A_NEW_REVIEW_SENT2'); ?></span>');
                        $('form span.info').delay(1).show(0);

                        $('form span.info').delay(2000).hide(0);

                        $('.review form textarea').val('');
                    }
                })

            }

            /*function sortCategoryMenu(id_category, id_category_item = false, id_sale_item = false, clearfix) {
                clearfix = clearfix || false;
                var mylist = $(id_category);
                var listitems = mylist.children().get();
                var category_item;
                var sale_item;
                listitems.sort(function (a, b) {
                    var compA = $(a).children('a').text().toUpperCase();
                    var compB = $(b).children('a').text().toUpperCase();
                    return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
                });
                $.each(listitems, function (idx, itm) {
                    if (('#' + itm.id) == id_category_item)
                        category_item = itm;
                    else if (('#' + itm.id) == id_sale_item)
                        sale_item = itm;
                    else {
                        mylist.append(itm);
                        if (clearfix) mylist.append('<div class="clearfix"></div>');
                    }
                });
                mylist.append(sale_item);
                if (clearfix) mylist.append('<div class="clearfix"></div>');
                mylist.append(category_item);
            }*/

            function initPeriodicPriceSelect() {
                $('select.periodic').change(function ()
                {

                    var $el = $(this);
                    var cart = $el.closest('.span11, .span1.cart');

                    var worldpmonthVat0 = cart.find('input.worldmonthpricevat0').val();
                    var worldpmonthVat = cart.find('input.worldmonthpricevat').val();
                    var finpmonthVat0 = cart.find('input.finmonthpricevat0').val();
                    var finpmonthVat = cart.find('input.finmonthpricevat').val();

                    var worldpmonthOrig = cart.find('input.worldmonthpriceoriginal').val();
                    var finpmonthOrig = cart.find('input.finmonthpriceoriginal').val();

                    var nPriceVat = (worldpmonthVat * $el.val()).toFixed(2);
                    var nPriceVat0 = (worldpmonthVat0 * $el.val()).toFixed(2);

                    var nPriceFinVat = (finpmonthVat * $el.val()).toFixed(2);
                    var nPriceFinVat0 = (finpmonthVat0 * $el.val()).toFixed(2);

                    var nPriceOrigW = (worldpmonthOrig * $el.val()).toFixed(2);
                    var nPriceOrigF = (finpmonthOrig * $el.val()).toFixed(2);

                    cart.find('.periodic_world .price').html(nPriceVat + ' <?= Currency::ToSign(); ?>');
                    cart.find('.periodic_world .pwovat span').html(nPriceVat0 + ' <?= Currency::ToSign(); ?>');

                    cart.find('.periodic_fin .price').html(nPriceFinVat + ' <?= Currency::ToSign(); ?>');
                    cart.find('.periodic_fin .pwovat span').html(nPriceFinVat0 + ' <?= Currency::ToSign(); ?>');

                    cart.find('.periodic_world .without_discount').html(nPriceOrigW + ' <?= Currency::ToSign(); ?>');
                    cart.find('.periodic_fin .without_discount').html(nPriceOrigF + ' <?= Currency::ToSign(); ?>');

                    cart.find('a.add').attr('data-quantity', $el.val());
                });
            }

            function initAAddCart() {

                var elems = $('a.cart-action');
                var $finSubButton = $('#finSubscription');
                var $worldSubButton = $('#worldSubscription');
                var $formDiv = $('#periodic-price-form');
                var $formEid = $formDiv.find('input[name="eid"]');
                var $formIid = $formDiv.find('input[name="iid"]');
                var $formQty = $formDiv.find('input[name="qty"]');


                $finSubButton.click(function ()
                {
                    $.magnificPopup.close();
                    add2Cart('add', $formEid.val(), $formIid.val(), $formQty.val(), 1, $finSubButton.data());
                });

                $worldSubButton.click(function ()
                {
                    $.magnificPopup.close();
                    add2Cart('add', $formEid.val(), $formIid.val(), $formQty.val(), 2, $worldSubButton.data());
                });

                $(elems).click(function () {
                    //alert(1);


                    var $el = $(this);
                    var $parent = $el.closest('.to_cart');

                    var entity = $el.attr('data-entity');

                    if (entity == <?= Entity::PERIODIC; ?>) {
                        var $finPrice = $('#finPrice');
                        var $worldPrice = $('#worldPrice');

                        var $itemFinBlock = $parent.find('.periodic_fin');
                        var $itemWorldBlock = $parent.find('.periodic_world');

                        if ($itemWorldBlock.length && $itemFinBlock.length) {
                            $formEid.val($el.attr('data-entity'));
                            $formIid.val($el.attr('data-id'));
                            $formQty.val($el.attr('data-quantity'));
                            $finSubButton.data($el);
                            $worldSubButton.data($el);

                            // show dialog only if we have different prices
                            var $formTitle = $('#formTitle');
                            var $formMonths = $('#formMonths');
                            var $title = $parent.closest('.to_cart').find('h1.title');
                            $formTitle.html($title.html());

                            var $select = $parent.find('select.periodic');
                            $formMonths.html($(':selected', $select).text());

                            var finHtml = $itemFinBlock.html();
                            $finPrice.html(finHtml);
                            var worldHtml = $itemWorldBlock.html();
                            $worldPrice.html(worldHtml);
                            $.magnificPopup.open({
                                items: {
                                    src: '#periodic-price-form', // can be a HTML string, jQuery object, or CSS selector
                                    type: 'inline'
                                }
                            });
                            return false;
                        }
                    }

                    add2Cart($el.attr('data-action'),
                        $el.attr('data-entity'),
                        $el.attr('data-id'),
                        $el.attr('data-quantity'),
                        null,
                        $el
                    );



                    return false;
                });
            }


        </script>

    </head>

    <body>
        <?

        if (!Yii::app()->getRequest()->cookies['showSelLang']->value) {

        ?>

        <div class="opacity_box" style="display: block;"></div>

        <div class="lang_yesno_box">

            <div class="box_title box_title_ru"><?= $ui->item('A_NEW_RUS_POPUP'); ?></div>

            <? if (Yii::app()->language != 'en') : ?>

            <div class="box_title box_title_en">Is your language <?= $ui->item('LANG_IN_EN'); ?>?</div>

            <? endif; ?>

            <div class="box_btns">
                <a href="<?= MyUrlManager::RewriteCurrent($this, Yii::app()->language, 1); ?>" class="btn_yes"><?= $ui->item('A_NEW_BTN_YES'); ?> <? if (Yii::app()->language != 'en') : ?>(Yes)<? endif; ?></a>
                <a href="javascript:;" onclick="$('.lang_yesno_box').hide();
                                        $('.lang_yesno_box.select_lang').show();" class="btn_no"><?= $ui->item('A_NEW_BTN_NO'); ?> <? if (Yii::app()->language != 'en') : ?>(No)<? endif; ?></a>
            </div>

        </div>

        <div class="lang_yesno_box select_lang">

            <div class="box_title box_title_ru"><?= $ui->item('A_NEW_SELECT_LANG_TITLE'); ?>:</div>
            <? if (Yii::app()->language != 'en') : ?>

            <div class="box_title box_title_en">Choose your language</div>

            <? endif; ?>
            <div class="row">
                <ul class="list_languages">
                    <li class="ru span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'ru', 1); ?>"><?= $ui->item('A_LANG_RUSSIAN') ?></a></li>
                    <li class="fi span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'fi', 1); ?>"><?= $ui->item('A_LANG_FINNISH') ?></a></li>
                    <li class="en span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'en', 1); ?>"><?= $ui->item('A_LANG_ENGLISH') ?></a></li>
                    <li class="de span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'de', 1); ?>"><?= $ui->item('A_LANG_GERMAN') ?></a></li>
                    <li class="fr span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'fr', 1); ?>"><?= $ui->item('A_LANG_FRENCH') ?></a></li>
                    <li class="es span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'es', 1); ?>"><?= $ui->item('A_LANG_ESPANIOL') ?></a></li>
                    <li class="se span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'se', 1); ?>"><?= $ui->item('A_LANG_SWEDISH') ?></a></li>

                </ul>
            </div>
        </div>

        <?

        }
        ?>


        <div class="header_logo_search_cart">

            <?php $this->widget('InfoText', array('isFrame'=>1)); ?>
            <? if ($ctrl != 'cart') : ?>

            <div class="light_gray_menu">
                <div class="container">
                    <ul>
                                    <!--<li style="padding-right: 0px;"><img src="/new_img/flag.png" /></li>-->
                        <li style="border: 0;padding-left: 10px;"><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>" class=""><?= $ui->item('A_NEW_ABOUTUS'); ?></a></li>
                        <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'contact')); ?>"><?= $ui->item('YM_CONTEXT_CONTACTUS') ?></a></li>
                        <li><span class="telephone2"><a href="whatsapp://send?phone=+358503889439"><img src="/new_img/telephone2.png" alt="" /></a></span><a href="tel:+35892727070"><span class="telephone">+358 92727070</span></a></li>
                        <li><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank"><span class="adrs">Bulevardi 7, FI-00120 Helsinki, Finland</span></a></li>
                        <li><?= $ui->item('A_NEW_TITLE_TOP'); ?></li>

                        <?php if (Yii::app()->user->isGuest) : ?>

                        <li class="menu_right none_right_padding"><a href="<?= Yii::app()->createUrl('site/login'); ?>"><?= $ui->item('A_SIGNIN') ?></a></li>
                        <li class="menu_right none_border"><a href="<?= Yii::app()->createUrl('site/register'); ?>"><?= $ui->item('A_REGISTER') ?></a></li>
                        <? else :?>
                        <li class="menu_right none_right_padding"><a href="<?= Yii::app()->createUrl('site/logout'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_LOGOUT'); ?></a></li>
                        <li class="menu_right none_border "><a href="<?= Yii::app()->createUrl('client/me'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_MAIN'); ?></a></li>

                        <?endif;?>
                    </ul>
                </div>
            </div>

            <? endif; ?>

            <div class="container">
                <div class="row">
                    <div class="span1 logo">
                        <a href="<?= Yii::app()->createUrl('site/index') ?>"><img src="/new_img/logo.png" alt=""/></a>
                    </div>

                   <?
                   
                   
                   if (($ctrl == 'cart' AND (!in_array('orderPay',$url)))) : ?>

                    <a href="/cart/" style="float: right; margin-top: 50px;">Вернуться в корзину</a>

                    <? elseif ($ctrl == 'cart') : ?>
                    
                     <a href="/" style="float: right; margin-top: 50px; color: #ff0000;">Продолжить покупки</a>
                    
                    <? endif; ?>

                    <? if ($ctrl != 'cart') : ?>

                    <div class="span10">
                        <form method="get" action="<?= Yii::app()->createUrl('search/general') ?>" id="srch" onsubmit="if (document.getElementById('Search').value.length < 3) { alert('<?= strip_tags($ui->item('SEARCH_TIP2')) ?>'); return false; } return true; ">
                            <div class="search_box">
                                <div class="loading"><?= $ui->item('A_NEW_SEARCHING_RUR'); ?></div>
                                <input type="text" name="q" class="search_text enable_virtual_keyboard" placeholder="<?= $ui->item('A_NEW_PLACEHOLDER_SEARCH'); ?>" id="Search" value="<?= $_GET['q'] ?>"/>
                                <input type="submit" class="search_run" value=""><!--<img src="/new_img/btn_search.png" class="search_run" alt="" onclick="$('#srch').submit()"/>-->
                                <div class="trigger_keyboard">
                                    <img src="/new_img/keyboard.png" width="20px" class="keyboard_off_img"/>
                                    <span class="keyboard_on" hidden><?= $ui->item('A_NEW_KEYBOARD_ON')?></span>
                                    <span class="keyboard_off"><?= $ui->item('A_NEW_KEYBOARD_OFF')?></span>
                                </div>
                            </div>

                            <div class="pult">

                                <ul>
                                    <li class="sm"><a href="<?= Yii::app()->createUrl('site/advsearch') ?><? if ($entity) { echo '?e='.$entity; } elseif ($_GET['e']) { echo '?e='.$_GET['e']; }?>" class="search_more"> <?= $ui->item('Advanced search') ?></a></li>
                                    <input type="hidden" name="avail" id="js_avail" value="<?= $act[0] ?>" class="avail">
                                    <?php /*
                                      <li class="chb">
                                      <div class="checkbox_box" onclick="check_search($(this))">
                                      <?

                                      $act = array();

                                      $act = array(1, ' active');

                                      if (isset($_GET['avail'])) {

                                      if ($_GET['avail'] == '1') {
                                      $act = array(1, ' active');
                                      } else {
                                      $act = array('', '');
                                      }

                                      }
                                      ?>

                                      <span class="checkbox">
                                      <span class="check<?=$act[1]?>"></span>
                                      </span> <input type="hidden" name="avail" value="<?=$act[0]?>" class="avail"><?= $ui->item('A_NEW_SEARCH_AVAIL'); ?>
                                      </div>
                                      </li>
                                     */ ?>
                                    <li class="langs">
                                        <div class="select_lang">
                                            <?
                                            $arrLangsTitle = array(
                                            'ru' => $ui->item('A_LANG_RUSSIAN'),
                                            'rut' => $ui->item('A_LANG_TRANSLIT'),
                                            'fi' => $ui->item('A_LANG_FINNISH'),
                                            'en' => $ui->item('A_LANG_ENGLISH'),
                                            'de' => $ui->item('A_LANG_GERMAN'),
                                            'fr' => $ui->item('A_LANG_FRENCH'),
                                            'es' => $ui->item('A_LANG_ESPANIOL'),
                                            'se' => $ui->item('A_LANG_SWEDISH')
                                            );
                                            ?>
                                            <div class="dd_select_lang">
                                                <div class="lable_empty" onclick="$('.dd_select_lang').toggle(); $('.label_lang.view_lang').toggleClass('act').parent().toggleClass('act')"></div>
                                                <?php foreach ($arrLangsTitle as $k => $v): ?>
                                                <div class="label_lang">
                                                    <span class="lang <?= $k ?>"><a href="<?= MyUrlManager::RewriteCurrent($this, $k); ?>"><?= $v ?></a></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="label_lang view_lang" onclick="$('.dd_select_lang').toggle(); $(this).toggleClass('act'); $(this).parent().toggleClass('act')">
                                                <span class="lang <?= Yii::app()->language; ?>"><a href="javascript:;"><?= $arrLangsTitle[Yii::app()->language]; ?></a> <span class="dd"></span></span>
                                            </div>

                                        </div>
                                    </li>
                                    <li class="valuts">

                                        <div class="select_valut">
                                            <? $arrVCalut = array(

                                            '1' => array('euro','Euro'),
                                            '2' => array('usd','USD'),
                                            '3' => array('gbp','GBP'),

                                            ); ?>
                                            <div class="dd_select_valut">
                                                <div class="lable_empty" onclick="$('.dd_select_valut').toggle(); $('.label_valut.select').toggleClass('act')"></div>
                                                <div class="label_valut">
                                                    <a href="<?= MyUrlManager::RewriteCurrency($this, Currency::EUR); ?>"><span style="width: 17px; display: inline-block; text-align: center">&euro;</span><span class="valut" style="margin-left: 10px;">Euro</span></a>
                                                </div>
                                                <div class="label_valut">
                                                    <a href="<?= MyUrlManager::RewriteCurrency($this, Currency::USD); ?>"><span style="width: 17px; display: inline-block; text-align: center">$</span><span class="valut" style="margin-left: 10px;">USD</span></a>
                                                </div>
                                                <div class="label_valut">
                                                    <a href="<?= MyUrlManager::RewriteCurrency($this, Currency::GBP); ?>"><span style="width: 17px; display: inline-block; text-align: center">£</span><span class="valut" style="margin-left: 10px;">GBP</span></a>
                                                </div>
                                            </div>
                                            <div class="label_valut select" onclick="$('.dd_select_valut').toggle();
                                                    $(this).toggleClass('act')">
                                                <a href="javascript:;"><span class="valut <?= $arrVCalut[(string) Yii::app()->currency][0] ?>"><?= $arrVCalut[(string) Yii::app()->currency][1] ?><span class="dd"></span></span></a>
                                            </div>
                                        </div>

                                    </li>
                                    <!--<li class="keyboard">
                                        <div class="trigger_keyboard" style="margin-left: 30px; cursor: pointer">
                                            <img src="/new_img/keyboard.png" width="20px" class="keyboard_off_img"/>
                                            <span class="keyboard_on" hidden><?/*= $ui->item('A_NEW_KEYBOARD_ON')*/?></span>
                                            <span class="keyboard_off"><?/*= $ui->item('A_NEW_KEYBOARD_OFF')*/?></span>
                                        </div>
                                    </li>-->
                                </ul>

                            </div>
                        </form>
                    </div>
                    <div class="span1 cart" >


                        <div class="span1">

                            <?= $ui->item('A_NEW_CART'); ?>:
                            <div class="cost"></div>

                        </div>
                        <div class="span2 js-slide-toggle" data-slidetoggle=".b-basket-list" data-slideeffect="fade" data-slidecontext=".span1.cart" >

                            <div class="cart_box" ><img src="/new_img/cart.png" alt=""/></div>
                            <div class="cart_count"></div>

                        </div>


                        <?php $this->renderPartial('/cart/header_cart'); ?>   




                    </div>

                    <? endif; ?>

                </div>



            </div>
            <div style="height: 10px;"></div>
            <script>
                $(document).ready(function () {
                    //$('a', $('.dd_box .tabs li')[0]).click();
                    // $('li.dd_box .content').jScrollPane({scrollbarWidth:18, showArrows:true});
                    $('.dd_box').removeClass('show_dd');
                })
            </script>

                <?php $this->widget('MainMenu'); ?>
         </div>


        <?= $content; ?>


        <? if ($ctrl != 'cart') : ?>

        <div class="footer">

            <div class="container">

                <div class="row">
                    <div class="span1">
                        <a href="" title="Ruslania"><img src="/new_img/logo_footer.png" alt="Ruslania" /></a>
                        <div class="text">
                            <?= $ui->item('A_NEW_DESC_FOOTER'); ?>
                            <a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?= $ui->item('A_NEW_MORE_ABOUTUS'); ?></a>
                        </div>
                        <div class="contacts">

                            <div class="maps_ico"><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank">Ruslania Books Corp. Bulevardi 7, FI-00120 Helsinki, Finland</a></div>
                            <div class="phone_ico"><a href="tel:+35892727070">+358 9 2727070</a></div>
                            <div class="mail_ico">generalsupports@ruslania.com</div>

                        </div>
                        <div class="social_icons">

                            <a href="https://vk.com/ruslaniabooks"><img src="/new_img/vk.png" alt="" /></a>
                            <a href="https://www.facebook.com/RuslaniaBooks/"><img src="/new_img/fb.png" alt="" /></a>
                            <a href="https://twitter.com/RuslaniaKnigi"><img src="/new_img/tw.png" alt="" /></a>
                            <!--<a href=""><img src="/new_img/gp.png" alt="" /></a>-->

                        </div>
                    </div>
                    <div class="span2">
                        <div class="span1">
                            <ul>
                                <li class="title"><?= $ui->item('A_NEW_ABOUTUS'); ?></li>

                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?= $ui->item("A_ABOUTUS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'csr')); ?>"><?= $ui->item("A_CSR"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions')); ?>"><?= $ui->item("MSG_CONDITIONS_OF_USE"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions_order')); ?>"><?= $ui->item("YM_CONTEXT_CONDITIONS_ORDER_ALL"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions_subscription')); ?>"><?= $ui->item("YM_CONTEXT_CONDITIONS_ORDER_PRD"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'contact')); ?>"><?= $ui->item("YM_CONTEXT_CONTACTUS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'legal_notice')); ?>"><?= $ui->item("YM_CONTEXT_LEGAL_NOTICE"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'faq')); ?>"><?= $ui->item("A_FAQ"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'sitemap')); ?>"><?= $ui->item("A_SITEMAP"); ?></a></li>
                            </ul>
                        </div><div class="span1">
                            <ul>
                                <li class="title"><?= $ui->item('A_NEW_OURPREDL'); ?></li>

                                <li><a href="<?= Yii::app()->createUrl('site/sale'); ?>"><?= $ui->item("MENU_SALE"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('offers/list'); ?>"><?= $ui->item("RUSLANIA_RECOMMENDS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'offers_partners')); ?>"><?= $ui->item("A_OFFERS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'offers_partners')); ?>">– <?= $ui->item("A_OFFERS_PARTNERS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => 'uni')); ?>">– <?= $ui->item("A_OFFERS_UNIVERCITY"); ?></a></li>
                            </ul>
                        </div><div class="span1">
                            <ul>
                                <li class="title"><?= $ui->item('A_NEW_USERS'); ?></li>

                                <?php if (Yii::app()->user->isGuest) : ?>

                                <li><a href="<?= Yii::app()->createUrl('site/register'); ?>"><?= $ui->item('A_REGISTER'); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/login'); ?>"><?= $ui->item('A_SIGNIN'); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('cart/view'); ?>"><?= $ui->item('A_SHOPCART'); ?></a></li>

                                <!-- <li><a href="">Выход</a></li>-->
                                <?php else : ?>
                                <li><a href="<?= Yii::app()->createUrl('client/me'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_MAIN'); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('cart/view'); ?>"><?= $ui->item('A_SHOPCART'); ?></a></li>
                                <li><a href="/my/memo"><?= $ui->item('A_NEW_MY_FAVORITE'); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/logout'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_LOGOUT'); ?></a></li>
                                <?endif;?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row payment">

                    <div class="span1">
                        <img src="https://img.paytrail.com/?id=34135&type=horizontal&cols=18&text=0&auth=b6c2c7566147a60e" width="770" alt="Ruslania, PayTrail" />
                    </div>
                    <div class="span2">
<?php /*
                        <img src="/new_img/payment2.png" alt="secures by thawte" />
                                                <!-- <img src="https://seal.thawte.com/getthawteseal?at=0&sealid=1&dn=RUSLANIA.COM&lang=en&gmtoff=-180" alt="" /> -->
*/ ?>
                        <img src="data:image/png;base64,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" />
                        <img src="/new_img/payment3.png" alt="PayPal verified" />
                        <img src="/new_img/buyer_protection.jpg" alt="PayPal buyer protection" />
                        <img src="/new_img/payment4.png" alt="verified by visa MasterCard" />

                    </div>

                </div>

                <div class="copyright">

                    2017 © <span class="title__bold">Ruslania</span> - All rights Reserved

                </div>

            </div>

        </div>
        <? endif; ?>

        <link rel="stylesheet" href="/css/magnific-popup.css" >
        <div id="periodic-price-form" class="white-popup-block mfp-hide white-popup">
            <div class="box_title box_title_ru"><?= $ui->item('PERIODIC_POPUP_HEADER'); ?></div>
            <div class="box_title box_title_en" id="formTitle"></div>
            <div class="box_title box_title_en" id="formMonths"></div>
            <input type="hidden" name="eid" value=""/>
            <input type="hidden" name="iid" value=""/>
            <input type="hidden" name="qty" value=""/>

            <div class="periodic_choice box_btns">
                <div id="finPrice"></div>
                <button class="btn_yes" id="finSubscription"><?= $ui->item('PERIODIC_POPUP_FINLAND_BUTTON'); ?></button>
            </div>
            <div class="periodic_choice box_btns">
                <div id="worldPrice"></div>
                <button class="btn_yes" id="worldSubscription"><?= $ui->item('PERIODIC_POPUP_WORLD_BUTTON'); ?></button>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php $this->widget('InfoText', array('isFrame'=>0)); ?>
        <div id="virtual_keyboard" style="display: none"></div>
    </body>
</html>

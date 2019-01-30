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
$act = array(1, ' active');
if (isset($_GET['avail'])) {
    if ($_GET['avail'] == '1') $act = array(1, ' active');
    else $act = array('', '');
}
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
    <?php if ((mb_strpos(Yii::app()->getRequest()->getPathInfo(), 'request-books', null, 'utf-8') !== false)||Yii::app()->getRequest()->getParam('lang')): ?>
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

    <meta name="csrf" content="<?= MyHTML::csrf(); ?>"/>
    <?php /*
    <script src="/new_js/nouislider.js" type="text/javascript" charset="utf-8"></script>
    <link href="/new_js/nouislider.css" rel="stylesheet" type="text/css"/>*/?>
    <script type="text/javascript" src="/new_js/modules/scriptLoader.js"></script>
    <!--[if lt IE 9]>
    <script src="libs/html5shiv/es5-shim.min.js"></script>
    <script src="libs/html5shiv/html5shiv.min.js"></script>
    <script src="libs/html5shiv/html5shiv-printshiv.min.js"></script>
    <script src="libs/respond/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/js/magnific-popup.js"></script>

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
                    <?php /*
                    <li><span class="telephone2"><a href="whatsapp://send?phone=+358503889439"><img src="/new_img/telephone2.png" alt="" /></a></span><a href="tel:+35892727070"><span class="telephone">+358 92727070</span></a></li>
*/ ?>
                    <li>
                        <span class="telephone2"><a href="whatsapp://send?phone=+358503889439" class="icons"><span class="fa whatsapp"></span></a></span>
                        <span class="telephone-circle"><a href="tel:+35892727070" class="icons"><span class="fa phone"></span></a></span>
                        <a href="tel:+35892727070"><span>+358 92727070</span></a>
                    </li>
                    <?php /*
                    <li><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank"><span class="adrs">Bulevardi 7, FI-00120 Helsinki, Finland</span></a></li>
*/ ?>
                    <li>
                        <span class="telephone-circle"><a href="tel:+35892727070" class="icons"><span class="fa location"></span></a></span>
                        <a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank"><span>Bulevardi 7, FI-00120 Helsinki, Finland</span></a>
                    </li>
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


            if (($ctrl == 'cart' AND (!in_array('orderPay',$url))) AND count($url) > 2) : ?>

                <a href="<?= Yii::app()->createUrl('cart/view') ?>" style="float: right; margin-top: 50px;"><?=$ui->item('CARTNEW_BACK_TO_CART')?></a>

            <? elseif ($ctrl == 'cart' AND (!in_array('doorder',$url))) : ?>

                <?

                $url_ref = end(explode('/', trim($_SERVER['HTTP_REFERER'], '/')));

                $arr_cart_url = array('variants', 'noregister', 'doorder');

                if (in_array($url_ref, $arr_cart_url)) {

                    $_SERVER['HTTP_REFERER'] = '/';

                }

                ?>

                <a href="<?=$_SERVER['HTTP_REFERER']?>" style="float: right; margin-top: 50px; color: #ff0000;"><?=$ui->item('CARTNEW_CONTINUE_SHOPPING')?></a>

            <? elseif ($ctrl == 'cart' AND (in_array('doorder',$url))) :?>

                <a href="<?=Yii::app()->createUrl('cart/view')?>" style="float: right; margin-top: 50px;"><?=$ui->item('CARTNEW_BACK_TO_CART')?></a>

            <? endif; ?>

            <div class="span10"<? if ($ctrl == 'cart') : echo 'style="margin-top: 40px;"'; else : echo ''; endif; ?>>

                    <? if ($ctrl != 'cart') : ?>
                <form method="get" action="<?= Yii::app()->createUrl('search/general') ?>" id="srch" onsubmit="if (document.getElementById('Search').value.length < 3) { alert('<?= strip_tags($ui->item('SEARCH_TIP2')) ?>'); return false; } return true; ">
                        <div class="search_box">
                            <div class="loading"><?= $ui->item('A_NEW_SEARCHING_RUR'); ?></div>
                            <input type="text" name="q" class="search_text enable_virtual_keyboard" placeholder="<?= $ui->item('A_NEW_PLACEHOLDER_SEARCH'); ?>" id="Search" value="<?= $_GET['q'] ?>"/>
                            <span class="search_run fa"><input type="submit" value=""></span>
                            <div class="trigger_keyboard">
                                <img src="/new_img/keyboard.png" width="20px" class="keyboard_off_img"/>
                                <span class="keyboard_on" hidden><?= $ui->item('A_NEW_KEYBOARD_ON')?></span>
                                <span class="keyboard_off"><?= $ui->item('A_NEW_KEYBOARD_OFF')?></span>
                            </div>
                        </div>

                    <? endif; ?>

                    <div class="pult">

                        <ul>

                            <? if ($ctrl != 'cart') : ?>

                                <li class="sm">
                                    <a href="<?= Yii::app()->createUrl('site/advsearch') ?><? if ($entity) { echo '?e='.$entity; } elseif ($_GET['e']) { echo '?e='.$_GET['e']; }?>" class="search_more">
                                        <span class="fa fa-rotate-90"></span>
                                        <?= $ui->item('Advanced search') ?>
                                    </a></li>

                            <? endif; ?>


                            <input type="hidden" name="avail" id="js_avail" value="<?= $act[0] ?>" class="avail">
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
                                        <span class="lang <?= Yii::app()->language; ?>"><a href="javascript:;"><?= $arrLangsTitle[Yii::app()->language]; ?></a> <span class="fa fa-angle-down"></span></span>
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
                                    <div style="padding-top: 10px;" class="label_valut select" onclick="$('.dd_select_valut').toggle();
                                                    $(this).toggleClass('act')">
                                        <a href="javascript:;"><span class="valut <?= $arrVCalut[(string) Yii::app()->currency][0] ?>"><?= $arrVCalut[(string) Yii::app()->currency][1] ?><span class="fa fa-angle-down"></span></span></a>
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

                    <? //endif; ?>

                    <? if ($ctrl != 'cart') : ?></form><? endif; ?>
            </div>

            <? if ($ctrl != 'cart') : ?>

                <div class="span1 cart">


                    <div class="span1 js-slide-toggle" data-slidetoggle=".b-basket-list" data-slideeffect="fade" data-slidecontext=".span1.cart">

                        <?= $ui->item('A_NEW_CART'); ?>:
                        <div class="cost"></div>

                    </div>
                    <div class="span2 js-slide-toggle" data-slidetoggle=".b-basket-list" data-slideeffect="fade" data-slidecontext=".span1.cart">

                        <div class="cart_box" ><img src="/new_img/cart.png" alt=""/></div>
                        <div class="cart_count"></div>

                    </div>

                    <div class="b-basket-list" id="cart_renderpartial"></div>
                    <?php //$this->renderPartial('/cart/header_cart'); ?>




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

                        <div class="ico-circle"><span class="icons"><span class="fa location"></span></span><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank">Ruslania Books Corp. Bulevardi 7, FI-00120 Helsinki, Finland</a></div>
                        <div class="ico-circle"><span class="icons"><span class="fa phone"></span></span><a href="tel:+35892727070">+358 9 2727070</a></div>
                        <div class="ico-circle"><span class="icons"><span class="fa email"></span></span>generalsupports@ruslania.com</div>
                        <?php /*
                        <div class="maps_ico"><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank">Ruslania Books Corp. Bulevardi 7, FI-00120 Helsinki, Finland</a></div>
                        <div class="phone_ico"><a href="tel:+35892727070">+358 9 2727070</a></div>
                        <div class="mail_ico">generalsupports@ruslania.com</div>
*/ ?>
                    </div>
                    <div class="social_icons">

                        <a target="_blank" href="https://vk.com/ruslaniabooks" class="icons"><span class="fa vk"></a>
                        <a target="_blank" href="https://www.facebook.com/RuslaniaBooks/" class="icons"><span class="fa facebook"></span></a>
                        <a target="_blank" href="https://www.twitter.com/RuslaniaKnigi/" class="icons"><span class="fa twitter"></span></a>
                        <a target="_blank" href="https://www.instagram.com/ruslaniabooks/" class="icons"><span class="fa instagram"></span></a>
                        <?php /*
                        <a href="https://vk.com/ruslaniabooks"><img src="/new_img/vk.png" alt="" /></a>
                        <a href="https://www.facebook.com/RuslaniaBooks/"><img src="/new_img/fb.png" alt="" /></a>
                        <a href="https://twitter.com/RuslaniaKnigi"><img src="/new_img/tw.png" alt="" /></a>
                        <!--<a href=""><img src="/new_img/gp.png" alt="" /></a>-->
*/ ?>
                    </div>
                </div>
                <div class="span2">
                    <div class="span1">
                        <ul>
                            <li class="title"><?= $ui->item('A_NEW_ABOUTUS'); ?></li>

                            <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?= $ui->item("A_ABOUTUS"); ?></a></li>
                            <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'news')); ?>"><?= $ui->item("NEWS"); ?></a></li>
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
                            <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'offers_partners')); ?>"><?= $ui->item("A_OFFERS_PARTNERS"); ?></a></li>
                            <li><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => 'uni')); ?>"> <?= $ui->item("A_OFFERS_UNIVERCITY"); ?></a></li>
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
                                <li><a href="/my/memo"><?= $ui->item('MSG_SHOPCART_SUSPENDED_ITEMS'); ?></a></li>
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
                    <img src="/new_img/gls_square.png" alt="FlexDeliveryService" style="width: 80px;" />
                    <a href="<?= Yii::app()->createUrl('site/static', array('page'=>'paypal')) ?>"><img src="/new_img/payment3.png" alt="PayPal verified" /></a>
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

<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/new_js/jScrollPane.js"></script>
<script type="text/javascript" src="/new_js/js_site.js"></script>
<script type="text/javascript" src="/js/opentip.js"></script>
<script>

    $(document).ready(function () {
        scriptLoader('/js/marcopolo.js').callFunction(function(){
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
                formatItem: function (data, $item, q) {
                    var ret = '';
                    ret += data;
                    return ret;
                }

            });
        });
        <?php if ($ctrl != 'cart'): ?>
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        $.ajax({
            url: '<?= Yii::app()->createUrl('cart/loadheader') ?>',
            type: "POST",
            data: csrf[0] + '=' + csrf[1],
            success: function (r) { $('.b-basket-list').html(r); }
        });
        <?php endif; ?>
    });

    function add2Cart(action, eid, iid, qty, type, $el) {
        var csrf = $('meta[name=csrf]').attr('content').split('=');

        var post_mark = 0;

        if (action == 'mark ') {

            if ($el.hasClass('active')) {

                post_mark = 0;

                $('span.tooltip').html('<span class="arrow"></span><?=$ui->item('BTN_SHOPCART_ADD_SUSPEND_ALT')?></span>')



            } else {

                post_mark = 1;

                $('span.tooltip').html('<span class="arrow"></span><?=$ui->item('BTN_SHOPCART_DELETE_SUSPEND_ALT')?></span>')

            }

        }



        var post =
            {
                entity: eid,
                id: iid,
                quantity: qty,
                type: type,
                mark : post_mark
            };
        post[csrf[0]] = csrf[1];
        post['hidecount'] = $el.data('hidecount');

        var seconds_to_wait = 10;


        var opentip = new Opentip($el, '', {target: true, tipJoint: "bottom", group: "group-example", showOn: "click", hideOn: 'ondblclick', background: '#fff', borderColor: '#fff'});

        opentip.deactivate();

        $.post('<?=Yii::app()->createUrl('cart/')?>' + action, post, function (json)
        {

            //if ($el.hasClass('add_cart_view')) {

                $el.addClass('green_cart');

            //}


            var json = JSON.parse(json);
            var opentip = new Opentip($el, '<div style="padding-right: 17px;">' + json.msg +
                '</div><div style="height: 6px;"></div><a href="javascript:;" class="close_popup" onclick="$(this).parent().parent().parent().remove()"><img src="/new_img/close_popup.png" alt="" /></a>', {target: true, tipJoint: "top", group: "group-example", showOn: "click", hideOn: 'ondblclick', background: '#fff', borderColor: '#fff'});


            opentip.show();
            if ('buttonName' in json) $el.find('span').html(json['buttonName']);

            function doCountdown()
            {

                var str = '';

                var timer = setTimeout(function ()
                {
                    seconds_to_wait--;
                    if (seconds_to_wait > 0)
                        doCountdown();
                    else
                        opentip.deactivate();
                }, 1000);
            }

            if (json.already)
            {


                if ($el.hasClass('add_cart_view')) {

                    $('span', $el).html(json.already2);

                }


            }




            if (action == 'mark ') {

                $el.toggleClass('active');

            }


            doCountdown();

            <?php if ($ctrl != 'cart') : ?>

            update_header_cart();

            <?php else: ?>
            location.href = location.href;
            <?php endif; ?>
        })
    }

    function show_tab(cont, url) {
        if (cont.parent().hasClass('active')) {
            location.href = cont.attr('href');
        } else {

            $('.dd_box_bg .tabs li').removeClass('active');
            cont.parent().addClass('active');
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            $('.dd_box_bg .content .list').html('');

            $.post('<?= Yii::app()->createUrl('site/mload') ?>' + cont.attr('href'), {YII_CSRF_TOKEN: csrf[1], id: 1}, function (data) {
                $('.dd_box_bg .content .list').html(data);
            })

        }
    }

    function update_header_cart() {
        $.ajax({
            url: '<?=Yii::app()->createUrl('cart/getcount') ?>',
            data: 'id=1',
            type: 'GET',
            success: function (data) {
                var d = JSON.parse(data);
                $.getJSON('<?=Yii::app()->createUrl('cart/getall') ?>', {is_MiniCart: 1}, function (json) {
                    ko.mapping.fromJS(json, {}, cvm_1);
                    cvm_1.FirstLoad(false);
                });
                $('div.cart_count').html(d.countcart);
                $('div.span1.cart .cost').html(d.totalPrice);
            }
        });
    }

    function addComment() {
        var csrf = $('meta[name=csrf]').attr('content').split('=');
        var ser = $('form.addcomment').serialize() + '&' + csrf[0] + '=' + csrf[1];
        $.post('<?=Yii::app()->createUrl('site/addcomments') ?>', ser, function (data) {
            var $form = $('form span.info');
            if (data) {
                //$('.comments_block').html(data);
                $form.html('<?= str_replace("'", "\'", $ui->item('A_NEW_REVIEW_SENT1')); ?>');
                $form.delay(1).show(0);
                $form.delay(2000).hide(0);
                $('.review form textarea').val('');
            } else {
                $form.html('<span style="color: #ff0000;"><?= str_replace("'", "\'", $ui->item('A_NEW_REVIEW_SENT2')); ?></span>');
                $form.delay(1).show(0);
                $form.delay(2000).hide(0);
                $('.review form textarea').val('');
            }
        })

    }

    function initPeriodicPriceSelect() {
        $('select.periodic').change(function () {

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

            cart.find('.periodic_world .price .pwvat').html(nPriceVat + ' <?= Currency::ToSign(); ?>');
            cart.find('.periodic_world .price .pwovat span').html(nPriceVat0 + ' <?= Currency::ToSign(); ?>');

            cart.find('.periodic_fin .price .pwvat').html(nPriceFinVat + ' <?= Currency::ToSign(); ?>');
            cart.find('.periodic_fin .price .pwovat span').html(nPriceFinVat0 + ' <?= Currency::ToSign(); ?>');

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

            if (entity == <?= Entity::PERIODIC; ?> && $el.attr('data-action') != 'mark ') {
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

</body>
</html>
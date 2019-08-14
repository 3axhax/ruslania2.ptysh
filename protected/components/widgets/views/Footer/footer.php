<?php /** Created by Кирилл rkv@dfaktor.ru 14.08.2019 19:37*/ ?>
<div style="float: left; width: 470px;">
    <div class="span1" style="margin: 0;">
        <a href="" title="Ruslania"><img src="/new_img/logo_footer.png" alt="Ruslania" /></a>
        <div class="text">
            <?= Yii::app()->ui->item('A_NEW_DESC_FOOTER'); ?>
            <a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?= Yii::app()->ui->item('A_NEW_MORE_ABOUTUS'); ?></a>
        </div>
        <div class="contacts" style="margin-top: 65px;">

            <div class="ico-circle"><span class="icons"><span class="fa location"></span></span><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank">Ruslania Books Corp. Bulevardi 7, FI-00120 Helsinki, Finland</a></div>
            <div class="ico-circle"><span class="icons"><span class="fa phone"></span></span><a href="tel:+35892727070">+358 9 2727070</a></div>
            <div class="ico-circle"><span class="icons"><span class="fa email"></span></span><a href="mailto:info@ruslania.com">info@ruslania.com</a></div>
        </div>
        <div class="social_icons" style="margin-top: 50px;">

            <a target="_blank" href="https://vk.com/ruslaniabooks" class="icons"><span class="fa vk"></a>
            <a target="_blank" href="https://www.facebook.com/RuslaniaBooks/" class="icons"><span class="fa facebook"></span></a>
            <a target="_blank" href="https://www.instagram.com/ruslaniabooks/" class="icons"><span class="fa instagram"></span></a>

            <a target="_blank" href="https://www.tripadvisor.com/Attraction_Review-g189934-d15003860-Reviews-Ruslania-Helsinki_Uusimaa.html" class="icons"><span class="fa tripadvisor"></span></a>
            <span class="notes">
                <a target="_blank" href="https://www.twitter.com/RuslaniaKnigi/" class="icons"><span class="fa twitter"></span></a>
                <span class="notes-block">
                    <div><a target="_blank" href="https://www.twitter.com/RuslaniaKirjat/" class="icons"><span class="fa twitter"></span> Kirjat</a></div>
                    <div><a target="_blank" href="https://www.twitter.com/RuslaniaKnigi/" class="icons"><span class="fa twitter"></span> Книги</a></div>
                    <div><a target="_blank" href="https://www.twitter.com/RuslaniaMovies/" class="icons"><span class="fa twitter"></span> Music & films</a></div>
                    <div><a target="_blank" href="https://www.twitter.com/RuslaniaMusic/" class="icons"><span class="fa twitter"></span> Sheetmusic / Ноты</a></div>
                </span>
            </span>
        </div>
        <?php /*
        <div class="mailing_form">
            <script>
                $(function(){
                    $.ajax({
                        url: '<?=Yii::app()->createUrl('request/mailingform') ?>',
                        data: {},
                        type: 'GET',
                        success: function (r) { $('.mailing_form').html(r); }
                    });
                });
            </script>
        </div>
 */?>
    </div>
</div>
<div style="width: 700px; margin-left: 470px;">
    <div class="span2" style="margin:0; padding-left: 30px;">
        <div class="span1" style="margin:0;width: 33%">
            <ul>
                <li class="title" style="margin-bottom: 33px;"><?= Yii::app()->ui->item('A_NEW_ABOUTUS'); ?></li>

                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?= Yii::app()->ui->item("A_ABOUTUS"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'news')); ?>"><?= Yii::app()->ui->item("NEWS"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'csr')); ?>"><?= Yii::app()->ui->item("A_CSR"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions')); ?>"><?= Yii::app()->ui->item("MSG_CONDITIONS_OF_USE"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions_order')); ?>"><?= Yii::app()->ui->item("YM_CONTEXT_CONDITIONS_ORDER_ALL"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions_subscription')); ?>"><?= Yii::app()->ui->item("YM_CONTEXT_CONDITIONS_ORDER_PRD"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'contact')); ?>"><?= Yii::app()->ui->item("YM_CONTEXT_CONTACTUS"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'legal_notice')); ?>"><?= Yii::app()->ui->item("YM_CONTEXT_LEGAL_NOTICE"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'faq')); ?>"><?= Yii::app()->ui->item("A_FAQ"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'sitemap')); ?>"><?= Yii::app()->ui->item("A_SITEMAP"); ?></a></li>
            </ul>
        </div>
        <div class="span1" style="margin:0;width: 33%">
            <?php
            $o = Offer::model();
            $fs = $o->GetOffer(Offer::FREE_SHIPPING, true, true);
            ?>
            <ul>
                <li class="title" style="margin-bottom: 33px;"><?= Yii::app()->ui->item('A_NEW_OURPREDL'); ?></li>
                <li><a href="<?= Yii::app()->createUrl('site/sale'); ?>"><?= Yii::app()->ui->item("MENU_SALE"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('offers/list'); ?>"><?= Yii::app()->ui->item("RUSLANIA_RECOMMENDS"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'offers_partners')); ?>"><?= Yii::app()->ui->item("A_OFFERS_PARTNERS"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => 'uni')); ?>"> <?= Yii::app()->ui->item("A_OFFERS_UNIVERCITY"); ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => Offer::getMode(Offer::FREE_SHIPPING))); ?>"> <?= Yii::app()->ui->item("FREE_SHIPPING_OFFER") ?></a></li>
                <li><a href="<?= Yii::app()->createUrl('site/certificate', array()); ?>"> <?= Yii::app()->ui->item("GIFT_CERTIFICATE"); ?></a></li>
            </ul>
        </div>
        <div class="span1" style="margin:0;width: 33%">
            <ul>
                <li class="title" style="margin-bottom: 33px;"><?= Yii::app()->ui->item('A_NEW_USERS'); ?></li>

                <?php if (Yii::app()->user->isGuest) : ?>
                    <li><a href="<?= Yii::app()->createUrl('site/register'); ?>"><?= Yii::app()->ui->item('A_REGISTER'); ?></a></li>
                    <li><a href="<?= Yii::app()->createUrl('site/login'); ?>"><?= Yii::app()->ui->item('A_SIGNIN'); ?></a></li>
                    <li><a href="<?= Yii::app()->createUrl('cart/view'); ?>"><?= Yii::app()->ui->item('A_SHOPCART'); ?></a></li>
                <?php else : ?>
                    <li><a href="<?= Yii::app()->createUrl('client/me'); ?>"><?= Yii::app()->ui->item('YM_CONTEXT_PERSONAL_MAIN'); ?></a></li>
                    <li><a href="<?= Yii::app()->createUrl('cart/view'); ?>"><?= Yii::app()->ui->item('A_SHOPCART'); ?></a></li>
                    <li><a href="<?=Yii::app()->createUrl('my/memo'); ?>"><?= Yii::app()->ui->item('MSG_SHOPCART_SUSPENDED_ITEMS'); ?></a></li>
                    <li><a href="<?= Yii::app()->createUrl('site/logout'); ?>"><?= Yii::app()->ui->item('YM_CONTEXT_PERSONAL_LOGOUT'); ?></a></li>
                <?endif;?>
            </ul>
        </div>
    </div>
    <div class="span2" style="margin:0; padding-left: 30px; padding-top: 30px;">
        <div style="float: left; margin: 0 35px 34px 0;"><picture>
            <source srcset="/new_img/pay/paytrail.webp" type="image/webp">
            <img src="/new_img/pay/paytrail.jpg">
        </picture></div>
        <div style="float: left; margin: 0 65px 35px 0;"><picture>
                <source srcset="/new_img/pay/american.webp" type="image/webp">
                <img src="/new_img/pay/american.jpg">
            </picture></div>
        <div style="float: left; margin: 0 35px 35px 0;"><a href="<?= Yii::app()->createUrl('site/static', array('page'=>'paypal')) ?>"><picture>
                <source srcset="/new_img/pay/paypal.webp" type="image/webp">
                <img src="/new_img/pay/paypal.jpg">
            </picture></a></div>
        <div style="float: left; margin: 0 35px 35px 0;"><picture>
                <source srcset="/new_img/pay/mobile.webp" type="image/webp">
                <img src="/new_img/pay/mobile.jpg">
            </picture></div>
        <div style="float: left; margin: 0 35px 35px 0; visibility: hidden;"><picture>
                <source srcset="/new_img/pay/diners.webp" type="image/webp">
                <img src="/new_img/pay/diners.jpg">
            </picture></div>
        <div style="float: left; margin: 0 35px 35px 0;"><picture>
                <source srcset="/new_img/pay/cache.webp" type="image/webp">
                <img src="/new_img/pay/cache.jpg">
            </picture></div>
        <div style="float: left; margin: 0 35px 35px 0;"><picture>
                <source srcset="/new_img/pay/gls.webp" type="image/webp">
                <img src="/new_img/pay/gls.jpg">
            </picture></div>
        <div style="float: left; margin: 0 50px 35px 17px;"><picture>
                <source srcset="/new_img/pay/visael.webp" type="image/webp">
                <img src="/new_img/pay/visael.jpg">
            </picture></div>
        <div style="float: left; margin: 0 40px 35px 0;"><picture>
                <source srcset="/new_img/pay/visa.webp" type="image/webp">
                <img src="/new_img/pay/visa.jpg">
            </picture></div>
        <div style="float: left; margin: 0 35px 35px 0;"><picture>
                <source srcset="/new_img/pay/mastercart.webp" type="image/webp">
                <img src="/new_img/pay/mastercart.jpg">
            </picture></div>
        <div style="float: left; margin: 0 35px 35px 0;"><picture>
                <source srcset="/new_img/pay/posti.webp" type="image/webp">
                <img src="/new_img/pay/posti.jpg">
            </picture></div>
    </div>
</div>

<div class="copyright">
    <?=date('Y')?> © <span class="title__bold">Ruslania</span> - All rights Reserved
</div>


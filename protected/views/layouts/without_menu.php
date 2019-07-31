<?php $ui = Yii::app()->ui;
    $returnButton = $this->returnButton();
?><!DOCTYPE html><html>
<head><?php $this->renderPartial('/layouts/head'); ?> <script type="text/javascript" src="https://js.stripe.com/v2/"></script></head>
<body>
<div class="header_logo_search_cart">
    <div class="container">
        <div class="row">
            <div class="span1 logo">
                <a href="<?= Yii::app()->createUrl('site/index') ?>"><img src="/new_img/logo.png" alt=""/></a>
            </div>
            <a href="<?= $returnButton['href'] ?>" style="float: right; margin-top: 50px; color: #ff0000;" onclick="searchTargets('back_cart');"><?= $returnButton['name'] ?></a>
            <div class="span10" style="margin-top: 40px;">
                <div class="pult">
                    <ul>
                       <li class="langs"><?php $this->renderPartial('/layouts/select_langs'); ?></li>
                       <li class="valuts"><?php $this->renderPartial('/layouts/select_valuts'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 10px;"></div>
</div>
<?= $content; ?>
<script type="text/javascript" src="/js/common.js"></script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(48174647, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/48174647" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-27359361-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-27359361-1');
</script>

<script type="text/javascript" >
    function searchTargets(name) {
        if (typeof (yaCounter48174647.reachGoal) === "function") {
            yaCounter48174647.reachGoal(name);
        }
    }
</script>

</body>
</html>
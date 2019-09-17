<?php /*Created by Кирилл (24.02.2019 11:39)*/ ?>
<title><?= $this->pageTitle; ?></title>
<meta name="google-site-verification" content="KVL1M7Tp8F9rDwfNKDWb7rPvBQ1JDqp82BxalICYkwM" />
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
<link href="/new_style/style_site.css?v=0209" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/css/prettyPhoto.css"/>
<script src="/new_js/jquery.js" type="text/javascript"></script>
<script src="/new_js/jquery.mousewheel.min.js" type="text/javascript"></script>
<meta name="csrf" content="<?= MyHTML::csrf(); ?>"/>
<script type="text/javascript" src="/new_js/modules/scriptLoader.js"></script>
<!--[if lt IE 9]>
<script src="libs/html5shiv/es5-shim.min.js"></script>
<script src="libs/html5shiv/html5shiv.min.js"></script>
<script src="libs/html5shiv/html5shiv-printshiv.min.js"></script>
<script src="libs/respond/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="/js/magnific-popup.js"></script>

<script>


	function gometrika(ident) {
	
		if (typeof (ym) === "function") {
			
			//ym(53579293, 'reachGoal', ident);
							
		}
	
	}
</script>
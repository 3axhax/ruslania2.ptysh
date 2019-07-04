<?php

$entities = 'books|audio|periodics|printed|video|maps|soft|music|sheetmusic';
$ValidLanguages = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
$languages = implode('|', $ValidLanguages);
$ValidLanguages[] = 'rut';

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Ruslania',
    'sourceLanguage' => 'ru',
    'language' => 'ru',

    // preloading 'log' component
    'preload' => array('log', 'urlManager'),

    // autoloading model and component classes
    'import' => array(
        'application.controllers.*',
        'application.components.frameworkext.*',
        'application.components.widgets.*',
        'application.models.*',
        'application.components.*',
        'zii.widgets.CMenu',
        'application.extensions.*',
        'application.extensions.knockout-form.*',
        'application.extensions.dgsphinxsearch.*',
        'application.models.postcalc.*',
        'application.extensions.yii-mail.*',
        'application.extensions.excel.*',
        'application.models.postcalc.shippingtypes.*',
        'application.models.postcalc.shippingtypes.envelope.*',
        'application.models.postcalc.shippingtypes.minipacket.*',
        'application.models.postcalc.shippingtypes.packet.*',
    ),

    'onBeginRequest' => array('OldUrlManager', 'CheckRequest'),

    'components' => array(

        'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),

 		'mail' => array(
 			'class' => 'ext.yii-mail.YiiMail',
 			'transportType' => 'php',
 			'viewPath' => 'application.views.mail',
 			'logging' => true,
// 			'dryRun' => true
 		),
		
        'sessionCache' => array(
            'class' => 'MyCache',
            'folder' => 'sessions',
        ),
		
		'session' => array(
            'class' => 'MyCacheHttpSession',
            'autoStart' => true,
            'cacheID' => 'sessionCache',
			'cookieMode' => 'only',
            'cookieParams' => array('lifetime' => 9999999, 'httponly' => true, 'timeout' => 9999999)
        ),

        'image' => array(
            'class' => 'application.extensions.image.CImageComponent',
            'driver' => 'GD',
        ),

       'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'autoRenewCookie' => true,
            'class' => 'WebUser',
            'loginUrl' => '/login',
            'identityCookie' => array('httpOnly' => true),
        ),

        'search' => array(
            'class' => 'application.extensions.DGSphinxSearch.DGSphinxSearch',
            'server' => 'localhost',
            'port' => 9312,
            'maxQueryTime' => 3000,
            'enableProfiling' => true,
            'enableResultTrace' => true,
        ),

      'request' => array('class' => 'MyRequest',
                           'dontCheckCsrf' => array('payment/accept', 'payment/cancel', 'payment/acceptCertificate', 'payment/cancelCertificate'),
                           'enableCsrfValidation' => true,
                           'enableCookieValidation' => true,
                           'csrfCookie' => array('httpOnly' => true)),

        // Кеш для сложных запросов из БД
        'urlManager' => array(
            'class' => 'MyUrlManager',
            'urlFormat' => 'path',
            'cacheID' => 'schemaCache', // туда же закешируем, куда и схему
            'showScriptName' => false,
//            'suffix' => '/',
            'urlSuffix'=>'/',
            'rules' => array(
                '__entityUrlRule' => array('class' => 'EntityUrlRule'),
                '__staticUrlRule' => array('class' => 'StaticUrlRule'),
                '/' => 'site/index',
                'landingpage' => 'site/landingpage',
                'login' => 'site/login',
                'register' => 'site/register',

                /*'doorder'*/'placeorder' => 'cart/doorder',
                'request-<entity:(' . $entities . ')>-<iid:(\d+)>' => 'cart/dorequest',
                'print/<oid:(\d+)>' => 'client/printorder',

                'my/orders' => 'client/orders',
                'my/requests' => 'client/requests',
                'my/memo' => 'client/memo',
                'my/subscriptions' => 'client/subscriptions',
                'my/changememo' => 'client/changememo',
                'my/addresses' => 'client/addresses',
                'my/newaddress' => 'client/newaddress',
                'my/deleteaddress/<aid:(\d+)>' => 'client/deleteaddress',
                'my/editaddress/<aid:(\d+)>' => 'client/editaddress',
                'my/data' => 'client/data',

                'pay/<oid:(\d+)>' => 'client/pay',
                'view/<oid:(\d+)>' => 'order/view',

                'offer/download/<oid:(\d+)>' => 'offers/download',
                'offer/download/<oid:(\d+)>/<title:(\w+)>' => 'offers/download',
//                'offer/<oid:(\d+)>/<title:(.+)>' => 'offers/view',
//                'offer/<oid:(\d+)>' => 'offers/view',
            ),
        ),

        'email' => array(
            'class' => 'application.extensions.email.Email',
            'delivery' => 'debug',
        ),
        'db' => array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',

            'connectionString' => 'mysql:host=localhost;port=3306;dbname=ruslania',
            'username' => 'ruslania',
            'password' => 'A7d4Z3j5',

//            'connectionString' => 'mysql:host=83.145.232.161;port=3306;dbname=ruslania_unicode',
//            'username' => 'ruslania_www',
//            'password' => 'qXsDa',

            'charset' => 'utf8',
            'enableProfiling' => true,
            'enableParamLogging' => true,
            'emulatePrepare' => true,
            'schemaCachingDuration' => 3600,
            'schemaCacheID' => 'schemaCache',
        ),

        'siteDb' => array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',
            'connectionString' => 'mysql:host=localhost;port=3306;dbname=ruslania',
            'username' => 'ruslania',
            'password' => 'A7d4Z3j5',
            'charset' => 'utf8',
            'enableProfiling' => true,
            'enableParamLogging' => true,
            'emulatePrepare' => true,
            'schemaCachingDuration' => 3600,
            'schemaCacheID' => 'schemaCache',
        ),

        'spx' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;port=3306',
        ),

//        // Office server (Econet, Prinetti)
//        'mssql' => array(
//            'class' => 'CDbConnection',
//            'connectionString' => 'sqlsrv:Server=SERVER2\R2SERVERSQL;Database=baza_internetSQL;MultipleActiveResultSets=false;',
//            'username' => 'RuslaniaManager',
//            'password' => 'Zse45tgb',
//            'charset' => 'utf8',
//            'enableProfiling' => true,
//            'enableParamLogging' => true,
//        ),


        'ui' => array('class' => 'RuslaniaUI'),
    ),

    'params' => array(
		'developerMode'=>0,
        'DefaultLanguage' => 'ru',
        'ValidLanguages' => $ValidLanguages,
        'ItemsPerPage' => 40,
        'LoginDuration' => 2592000, // 30 days
        'MerchantAuthHash' => '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ',
        'MerchantID' => 13466,
        'LangDir' => '',
        'DbCacheTime' => (60 * 60 * 4), // 4 hours,
        'OrderMinPrice' => 5, // min order 5,-
        'PicDomain' => 'https://ruslania.com',///'https://ruslania.com',
        'Base' => 'ruslania.com',
        'DataProviderCacheID' => 'queryCache', // cacheQuery
        'DataProviderCacheTimeout' => (60 * 60 * 4), // 4 hours
        //'PAYMENT_ENVIRONMENT' => 'prod',
        'PAYMENT_ENVIRONMENT' => 'test',
        'listMemcacheTime' => (60 * 60 * 4), // 4 hours
    ),
);


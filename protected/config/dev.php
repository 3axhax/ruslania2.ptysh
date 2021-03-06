<?php

ini_set("session.cookie_domain", ".ruslania.tw1.ru");
ini_set("session.cookie_lifetime", 0);

return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array(
         'components' => array(

             'image' => array(
                 'class' => 'application.extensions.image.CImageComponent',
                 'driver' => 'GD',
             ),
             // Кеш для схемы
             'schemaCache' => array(
                 'class' => 'MyCache',
                 'folder' => 'schema',
             ),

             'dbCache' => array(
                 'class' => 'CDummyCache',//'CMemCache  CDummyCache',
             ),

             'queryCache' => array(
                 'class' => 'CDummyCache',//'CMemCache',//'CMemCache',MyDataProvider почему то не правильно работает когда включен кеш
                 //например: для детальной страницы для блока "Вы смотрели" условие для запроса берется из кеша и дообавляется к нему новое
                 // WHERE ((t.id=1502473) AND (t.id in (963495,1503652))),
                 // а должно быть только (t.id=1502473)
             ),

             'memcache' => array(
                 'class' => 'CMemCache',
             ),

             'log' => array(
                 'class' => 'CLogRouter',
                 'routes' => array(
                     array('class' => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                           'enableOnlyByCookie' => true,
                           'cookieName' => 'YiiDebug',
                           'ipFilters' => array('127.0.0.1', '::1', '83.145.211.92'),
                     ),
                     array('class' => 'CFileLogRoute',
                           'levels' => CLogger::LEVEL_INFO,
                           'categories' => 'ext.yii-mail.YiiMail',
                           'logFile' => 'mail.log',
                     ),
                     array(
                         'class' => 'ExtendedLogger',
                         'logFile' => 'db.log',
                         'categories' => 'system.db.*',
                     ),
                     array(
                         'class' => 'ExtendedLogger',
                         'logFile' => 'sphinx.log',
                         'categories' => 'CEXT.DGSphinxSearch.*',
                     ),
                     array(
                         'class' => 'ExtendedLogger',
                         'logFile' => 'myerrors.log',
                         'categories' => 'mycategory.*',
                     ),
                     array(
                         'class' => 'ExtendedLogger',
                         'logFile' => 'profile.log',
                         'categories' => 'application.*',
                     ),
                     array(
                         'class' => 'ExtendedLogger',
                         'logFile' => 'warnings.log',
                         'categories' => 'mywarnings.*',
                     ),
                     array( // -- CWebLogRoute --------------------------- внизу страницы показывает логи
                         'class'=>isset($_GET['ha'])?'CWebLogRoute':'SlowSqlLogRoute',
//                         'levels'=>'error, warning, trace, profile, info',
                         'levels'=>'profile',
                         'enabled'=>true,
                     ),
                   /* array( // -- CWebLogRoute --------------------------- медленные запросы в файл /test/slow_sql.log
                        'class'=>'SlowSqlLogRoute',
                        'levels'=>'profile',
                        'enabled'=>true,
                    ),*/
                 )),
         ),


         'params' => array(
             'LangDir' => '/pictures/langv2/',
         )
    )
);



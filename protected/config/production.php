<?php

ini_set("session.cookie_domain", ".ruslania2.ptysh.ru");
ini_set("session.cookie_lifetime", 0);

return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array(
         'defaultController' => 'site',
         'components' => array(

             'mail' => array(
                 'dryRun' => false,
                 'transportType'=>'smtp',
                 'transportOptions'=>array(
                     'host'=>'smtp.nebula.fi',
                     'username'=>'ruslaniacom',
                     'password'=>'XxwFhFQr',
                     'port'=>'25',
                 ),
             ),

             'dbCache' => array(
                 'class'=>'MyMemCache',
				 'servers'=>array(
					array(
						'host'=>'localhost',
						'port'=>11211,
					),
				),
             ),

             'schemaCache' => array(
//                 'class'=>'system.caching.CMemCache',
                 'class'=>'MyMemCache',
				 array(
						'host'=>'localhost',
						'port'=>11211,
					),
				),
             ),

             'queryCache' => array(
//                 'class'=>'system.caching.CMemCache',
                 'class'=>'MyMemCache',
				 array(
						'host'=>'localhost',
						'port'=>11211,
					),
				),
             ),


             'search' => array(
                 'class' => 'application.extensions.DGSphinxSearch.DGSphinxSearch',
                 'server' => 'localhost',
                 'port' => 9306,
                 'maxQueryTime' => 3000,
                 'enableProfiling' => false,
                 'enableResultTrace' => false,
             ),

             'db' => array(
                 'class'=>'CDbConnection',
                 'pdoClass' => 'NestedPDO',
                 'connectionString' => 'mysql:host=localhost;port=3306;dbname=ruslania',
                 'username' => 'ruslania',
                 'password' => 'K7h9E6r2',
                 'charset' => 'utf8',
                 'enableProfiling' => true,
                 'enableParamLogging' => true,
                 'emulatePrepare' => true,
                 'schemaCachingDuration' => 3600,
             ),

             'log' => array(
                 'class' => 'CLogRouter',
                 'routes' => array(
                     array('class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                           'enableOnlyByCookie' => true,
                           'cookieName' => 'YiiDebug',
                           'ipFilters' => array('127.0.0.1','::1', '83.145.211.92'),
                     ),
                     array(
                         'class' => 'ExtendedLogger',
                         'logFile' => 'myerrors.log',
                         'categories' => 'myerrors.*',
                     ),
                     array('class' => 'CFileLogRoute',
                         'levels' => CLogger::LEVEL_INFO,
                         'categories' => 'ext.yii-mail.YiiMail',
                         'logFile' => 'mail.log',
                     ),


//                     array(
//                         'class' => 'ExtendedLogger',
//                         'logFile' => 'db.log',
//                         'categories' => 'system.db.*',
//                     ),
//                     array(
//                         'class' => 'ExtendedLogger',
//                         'logFile' => 'sphinx.log',
//                         'categories' => 'CEXT.DGSphinxSearch.*',
//                     ),
                 )),

         ),

         'params' => array(
             'LangDir' => '/pictures/langv2/',
             'Base' => 'ruslania2.ptysh.ru',
             'PicDomain' => '',
         )
    )
);



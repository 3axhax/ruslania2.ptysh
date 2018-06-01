<?php

$r = CMap::mergeArray(
    require(dirname(__FILE__) . '/dev.php'),
    array(
        'components' => array(
            'request' => array(
                'hostInfo' => 'http://ruslania2.ptysh.ru',
                'baseUrl' => 'http://ruslania2.ptysh.ru',
                'scriptUrl' => ''
           ),

        'mssql' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'sqlsrv:Server=SERVER2\R2SERVERSQL;Database=baza_internetSQL;MultipleActiveResultSets=false;',
            'username' => 'RuslaniaManager',
            'password' => 'Zse45tgb',
            'charset' => 'utf8',
            'enableProfiling' => false,
            'enableParamLogging' => true,
        ),

            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CFileLogRoute',
                        'logFile' => 'site.log',
                        'categories' => 'siteupdate',
                    ))),

        )
    )
);

unset($r['onBeginRequest']);
unset($r['defaultController']);

return $r;



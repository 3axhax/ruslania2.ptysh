<?php

$r = CMap::mergeArray(
    require(dirname(__FILE__) . '/production.php'),
    array(

         'components' => array(
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



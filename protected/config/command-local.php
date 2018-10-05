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

            /*'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CFileLogRoute',
                        'logFile' => 'site.log',
                        'categories' => 'siteupdate',
                    ))),*/

        )
    )
);

unset($r['onBeginRequest']);
unset($r['defaultController']);


/*//TODO::для формирования старых адресов
unset($r['components']['urlManager']);

$r['components']['request'] = array(
    'hostInfo' => '',
    'baseUrl' => '',
    'scriptUrl' => ''
);
$r['components']['urlManager'] = array(
    'class' => 'MyUrlManager',
    'urlFormat' => 'path',
    'showScriptName' => false,
    'urlSuffix'=>'',
    'rules' => array(
        '<entity:(' . $entities . ')>/<id:(\d+)>/<title:([0-9a-z-.]+)\/?>' => 'product/view',

        'sale' => 'site/sale',
        'landingpage' => 'site/landingpage',
        'login' => 'site/login',
        'logout' => 'site/logout',
        'register' => 'site/register',
        'me' => 'client/me',

        '<entity:(' . $entities . ')>/filter' => 'entity/filter',
        '<entity:(' . $entities . ')>/bycategory/<cid:(\d+)>/<title:(.+)>' => 'entity/list',
        '<entity:(' . $entities . ')>' => 'entity/list',

        '<entity:(' . $entities . ')>/types/<type:(\d+)>' => 'entity/bytype',
        '<entity:(' . $entities . ')>/byyear/<year:(\d{4})>' => 'entity/byyear',
        '<entity:(' . $entities . ')>/yearreleases/<year:(\d{4})>' => 'entity/byyearrelease',

        '<entity:(' . $entities . ')>/byseries/<sid:(\d+)>/<title:(.+)>' => 'entity/byseries',
        '<entity:(' . $entities . ')>/bypublisher/<pid:(\d+)>/<title:(.+)>' => 'entity/bypublisher',
        '<entity:(' . $entities . ')>/byauthor/<aid:(\d+)>/<title:(.+)>' => 'entity/byauthor',
        '<entity:(' . $entities . ')>/bybinding/<bid:(\d+)>/<title:(.+)>' => 'entity/bybinding',
        '<entity:(' . $entities . ')>/bymedia/<mid:(\d+)>/<title:(.+)>' => 'entity/bymedia',
        '<entity:(' . $entities . ')>/byperformer/<pid:(\d+)>/<title:(.+)>' => 'entity/byperformer',
        '<entity:(' . $entities . ')>/bymagazinetype/<tid:(\d+)>/<title:(.+)>' => 'entity/bymagazinetype',
        '<entity:(' . $entities . ')>/byactor/<aid:(\d+)>/<title:(.+)>' => 'entity/byactor',
        '<entity:(' . $entities . ')>/bydirector/<did:(\d+)>/<title:(.+)>' => 'entity/bydirector',
        '<entity:(' . $entities . ')>/bysubtitle/<sid:(\d+)>/<title:(.+)>' => 'entity/bysubtitle',
        '<entity:(' . $entities . ')>/byaudiostream/<sid:(\d+)>/<title:(.+)>' => 'entity/byaudiostream',
        '<entity:(' . $entities . ')>/byyear/<year:(\d{4})>' => 'entity/byyear',
        '<entity:(' . $entities . ')>/byyearrelease/<year:(\d{4})>' => 'entity/byyearrelease',
        '<entity:(' . $entities . ')>/bytype/<type:(\d+)>' => 'entity/bytype',

        '<entity:(' . $entities . ')>/categories' => 'entity/categorylist',
        '<entity:(' . $entities . ')>/gift' => 'entity/gift',

        '<entity:(' . $entities . ')>/publishers/<char:(.)>' => 'entity/publisherlist',
        '<entity:(' . $entities . ')>/publishers' => 'entity/publisherlist',
        '<entity:(' . $entities . ')>/authors' => 'entity/authorlist',
        '<entity:(' . $entities . ')>/performers' => 'entity/performerlist',
        '<entity:(' . $entities . ')>/actors' => 'entity/actorlist',
        '<entity:(' . $entities . ')>/directors' => 'entity/directorlist',
        '<entity:(' . $entities . ')>/series' => 'entity/serieslist',
        '<entity:(' . $entities . ')>/bindings' => 'entity/bindingslist',
        '<entity:(' . $entities . ')>/audiostreams' => 'entity/audiostreamslist',
        '<entity:(' . $entities . ')>/subtitles' => 'entity/subtitleslist',
        '<entity:(' . $entities . ')>/media' => 'entity/medialist',
        '<entity:(' . $entities . ')>/types' => 'entity/typeslist',
        '<entity:(' . $entities . ')>/years' => 'entity/yearslist',

        'cart' => 'cart/view',
        'doorder' => 'cart/doorder',
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

        'for-<mode:firms|uni|lib|fs|alle2>' => 'offers/special',
        'site/search' => 'site/search',
        'advsearch' => 'site/advsearch',

        'offers' => 'offers/list',
        'offer/download/<oid:(\d+)>' => 'offers/download',
        'offer/download/<oid:(\d+)>/<title:(\w+)>' => 'offers/download',
        'offer/<oid:(\d+)>/<title:(.+)>' => 'offers/view',
        'offer/<oid:(\d+)>' => 'offers/view',

        '/<page:([\w_]+)>' => 'site/static',
    ),
);*/


return $r;



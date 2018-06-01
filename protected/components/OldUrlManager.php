<?php

class OldUrlManager
{
    public function CheckRequest($event)
    {
        $uri = $_SERVER['REQUEST_URI'];

        //http://www.ruslania.com/context-66/entity-1/language-2.html
        //http://www.ruslania.com/context-193/entity-4/sortby-12/page-1/perpage-50/category-0/language-6.html

        define ("CONTEXT_", 1 << 6); // NOTE: total count of context groups + 1

        define ("CONTEXT_CATALOG_", CONTEXT_ | 1 << 0);
        define ("CONTEXT_CATALOG_MAIN", CONTEXT_CATALOG_);
        define ("CONTEXT_CATALOG_BROWSE", CONTEXT_CATALOG_ | CONTEXT_ << 1);
        define ("CONTEXT_CATALOG_DETAILS", CONTEXT_CATALOG_ | CONTEXT_ << 2);
        define ("CONTEXT_CATALOG_FILTER", CONTEXT_CATALOG_ | CONTEXT_ << 3);
        define ("CONTEXT_CATALOG_SEARCH", CONTEXT_CATALOG_ | CONTEXT_ << 4);
        define ("CONTEXT_CATALOG_AZ_PROPERTYLIST", CONTEXT_CATALOG_ | CONTEXT_ << 5);
        define ("CONTEXT_CATALOG_CATEGORIES_TREE", CONTEXT_CATALOG_ | CONTEXT_ << 6);
        define ("CONTEXT_CATALOG_SERIES_LIST", CONTEXT_CATALOG_ | CONTEXT_ << 7);
        define ("CONTEXT_CATALOG_BASIC_SEARCH", CONTEXT_CATALOG_ | CONTEXT_ << 8);

        define ("CONTEXT_PERSONAL_", CONTEXT_ | 1 << 1);
//define ("CONTEXT_PERSONAL_MAIN",                CONTEXT_PERSONAL_);
//define ("CONTEXT_PERSONAL_SHOPCART",            CONTEXT_PERSONAL_ | CONTEXT_ << 1);
//define ("CONTEXT_PERSONAL_LOGIN",               CONTEXT_PERSONAL_ | CONTEXT_ << 2);
//define ("CONTEXT_PERSONAL_LOGOUT",              CONTEXT_PERSONAL_ | CONTEXT_ << 3);
        define ("CONTEXT_PERSONAL_ADDUSER", CONTEXT_PERSONAL_ | CONTEXT_ << 4);
//define ("CONTEXT_PERSONAL_MODIFYUSER",          CONTEXT_PERSONAL_ | CONTEXT_ << 5);
//define ("CONTEXT_PERSONAL_ADD_ADDRESS",         CONTEXT_PERSONAL_ | CONTEXT_ << 6);
//define ("CONTEXT_PERSONAL_MODIFY_ADDRESS",      CONTEXT_PERSONAL_ | CONTEXT_ << 7);
//define ("CONTEXT_PERSONAL_BROWSE_ADDRESS",      CONTEXT_PERSONAL_ | CONTEXT_ << 8);
//define ("CONTEXT_PERSONAL_BROWSE_ORDERS",       CONTEXT_PERSONAL_ | CONTEXT_ << 9);
//define ("CONTEXT_PERSONAL_BROWSE_REQUESTS",     CONTEXT_PERSONAL_ | CONTEXT_ << 17);
define ("CONTEXT_INFO_",                        CONTEXT_ | 1 << 3);
//define ("CONTEXT_INFO_ABOUT",                   CONTEXT_INFO_ | CONTEXT_ << 1);
//define ("CONTEXT_INFO_PRICELIST_WATCHER",       CONTEXT_INFO_ | CONTEXT_ << 2);
//define ("CONTEXT_INFO_CONTACTUS",               CONTEXT_INFO_ | CONTEXT_ << 3);
//define ("CONTEXT_INFO_TRANSLIT",                CONTEXT_INFO_ | CONTEXT_ << 4);
define ("CONTEXT_INFO_CONDITIONS",              CONTEXT_INFO_ | CONTEXT_ << 5);
define ("CONTEXT_INFO_CONDITIONS_SUBSCRIPTION", CONTEXT_INFO_ | CONTEXT_ << 6);
define ("CONTEXT_INFO_CONDITIONS_ORDER",        CONTEXT_INFO_ | CONTEXT_ << 7);
//define ("CONTEXT_INFO_FAQ",                     CONTEXT_INFO_ | CONTEXT_ << 8);
//define ("CONTEXT_INFO_SAFETY",                  CONTEXT_INFO_ | CONTEXT_ << 9);
//define ("CONTEXT_INFO_THAWTE",                  CONTEXT_INFO_ | CONTEXT_ << 10);
//define ("CONTEXT_INFO_PARTNERS",                CONTEXT_INFO_ | CONTEXT_ << 11);
//define ("CONTEXT_INFO_LINKS",                   CONTEXT_INFO_ | CONTEXT_ << 12);
//define ("CONTEXT_INFO_FREE",                    CONTEXT_INFO_ | CONTEXT_ << 13);
//define ("CONTEXT_INFO_DOWNLOAD_CAT",            CONTEXT_INFO_ | CONTEXT_ << 14);
//define ("CONTEXT_PERSONAL_REMIND_PASS",         CONTEXT_INFO_ | CONTEXT_ << 15);
//define ("CONTEXT_INFO_VISA_CARD",               CONTEXT_INFO_ | CONTEXT_ << 16);
//define ("CONTEXT_INFO_ZONE",                    CONTEXT_INFO_ | CONTEXT_ << 17);
//define ("CONTEXT_INFO_FEEDBACK",                CONTEXT_INFO_ | CONTEXT_ << 18);
//define ("CONTEXT_INFO_BANNERSTAT",              CONTEXT_INFO_ | CONTEXT_ << 19);
//define ("CONTEXT_INFO_LEGAL_NOTICE",            CONTEXT_INFO_ | CONTEXT_ << 20);
//define ("CONTEXT_INFO_PRIVACY_POLICY",          CONTEXT_INFO_ | CONTEXT_ << 21);
//define ("CONTEXT_INFO_PAYPAL",          	      CONTEXT_INFO_ | CONTEXT_ << 22);
define ("CONTEXT_OFFERS_",                      CONTEXT_ | 1 << 5);
define ("CONTEXT_OFFERS_FRMS",                  CONTEXT_OFFERS_ | CONTEXT_ << 1);
define ("CONTEXT_OFFERS_LIBS",                  CONTEXT_OFFERS_ | CONTEXT_ << 2);
define ("CONTEXT_OFFERS_UNIVERCITY",            CONTEXT_OFFERS_ | CONTEXT_ << 3);
define ("CONTEXT_OFFERS_PARTNERS",              CONTEXT_OFFERS_ | CONTEXT_ << 4);
//define ("CONTEXT_OFFERS_NEW",                   CONTEXT_OFFERS_ | CONTEXT_ << 5); //2144 ofid

        $matches = array();
        preg_match('|context-(\d+)|', $uri, $matches);

        if (empty($matches)) return;
        $context = intVal($matches[1]);

        $map = array(
            CONTEXT_CATALOG_MAIN => array('URL' => 'site/index', 'Params' => null),
            CONTEXT_CATALOG_BROWSE => array('URL' => 'entity/list', 'Params' => array('entity', 'cid', 'language')),
            CONTEXT_CATALOG_DETAILS => array('URL' => 'product/view', 'Params' => array('entity', 'id', 'title', 'language')),
            CONTEXT_PERSONAL_ADDUSER => array('URL' => 'site/register', 'Params' => null),
            CONTEXT_CATALOG_FILTER => array('URL' => '', 'Params' => null),
            CONTEXT_INFO_CONDITIONS => array('URL' => 'site/static', 'Params' => array('page' => 'conditions')),
            CONTEXT_INFO_CONDITIONS_SUBSCRIPTION => array('URL' => 'site/static', 'Params' => array('page' => 'conditions_subscription')),
            CONTEXT_INFO_CONDITIONS_ORDER => array('URL' => 'site/static', 'Params' => array('page' => 'conditions_order')),
            CONTEXT_OFFERS_FRMS => array('URL' => 'special/list', 'Params' => array('mode' => 'firms')),
            CONTEXT_OFFERS_LIBS => array('URL' => 'special/list', 'Params' => array('mode' => 'lib')),
            CONTEXT_OFFERS_UNIVERCITY => array('URL' => 'special/list', 'Params' => array('mode' => 'uni')),
            CONTEXT_OFFERS_PARTNERS => array('URL' => 'site/static', 'Params' => array('page' => 'offers_partners')),
        );

        // Получить entity
        preg_match('|entity-(\d)|', $uri, $matches);
        $entity = Entity::BOOKS;
        $entities = Entity::GetSiteEntitiesList();
        if (!empty($matches) && array_key_exists($matches[1], $entities)) $entity = $entities[$matches[1]]['human_id'];

        $entityNumber = $entity;
        $entity = Entity::GetUrlKey($entity);

        // получить язык
        preg_match('|language-(\d)|', $uri, $matches);
        $language = Yii::app()->language;
        if (!empty($matches))
        {
            $lang = Language::ConvertToString($matches[1]);
            if ($lang != $language) $language = $lang;
            else $language = null;
        }
        else $language = null;

        $variables = array(
            'id' => 'details',
            'cid' => 'category',
            'subid' => 'credits',
            'actorID' => 'actor',
            'formatID' => 'type',
            'directorID' => 'director',
            'authorID' => 'author',
            'publisherID' => 'publisher',
            'seriesID' => 'series',
        );

        foreach($variables as $var => $uPart)
        {
            $$var = 0;
            preg_match('|'.$uPart.'-(\d+)|', $uri, $matches);
            if (!empty($matches)) $$var = intVal($matches[1]);
        }

        // title
        $title = 'redirect';

        if (array_key_exists($context, $map))
        {
            $params = array();

            if (is_array($map[$context]['Params']))
            {
                foreach ($map[$context]['Params'] as $p=>$value)
                {
                    if(is_numeric($p))
                    {
                        $key = $value;
                        $value = $$value;
                    }
                    else
                    {
                        $key = $p;
                    }
                    $params[$key] = $value;
                }
            }

            if(array_key_exists('language', $params) && empty($params['language'])) unset($params['language']);

            $url = $map[$context]['URL'];
            if ($context == CONTEXT_CATALOG_BROWSE && !empty($cid)) $params['title'] = $title;
            if ($context == CONTEXT_CATALOG_FILTER)
            {
                $params['entity'] = $entity;
                $params['title'] = $title;
                // subtitles
                if (!empty($subid))
                {
                    $url = 'entity/bysubtitle';
                    $params['sid'] = $subid;
                }
                else if (!empty($actorID))
                {
                    $url = 'entity/byactor';
                    $params['aid'] = $actorID;
                }
                else if (!empty($formatID))
                {
                    if($entityNumber == Entity::PERIODIC)
                    {
                        $url = 'entity/bymagazinetype';
                        $params['tid'] = $formatID;
                    }
                    else
                    {
                        $url = 'entity/bymedia';
                        $params['mid'] = $formatID;
                    }
                }
                else if (!empty($directorID))
                {
                    $url = 'entity/bydirector';
                    $params['did'] = $directorID;
                }
                else if(!empty($authorID))
                {
                    $url = 'entity/byauthor';
                    $params['aid'] = $authorID;
                }
                else if(!empty($publisherID))
                {
                    $url = 'entity/bypublisher';
                    $params['pid'] = $publisherID;
                }
                else if(!empty($seriesID))
                {
                    $url = 'entity/byseries';
                    $params['sid'] = $seriesID;
                }
            }

            $url = Yii::app()->createAbsoluteUrl($url, $params);
            header('Location: ' . $url, true, 302);
            exit;
        }
    }
}

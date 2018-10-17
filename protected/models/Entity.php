<?php

class Entity {

    public static function GetEntitiesList() {
        return array(
            10 => array('title' => 'Книги', 'site_id' => 1, 'table' => 'tblBooks',
                'site_table' => 'books_catalog',
                'entity' => 'books', 'model' => 'Books',
                'site_category_table' => 'books_categories',
                'site_series_table' => 'books_series',
                'author_table' => 'books_authors',
                'author_entity_field' => 'book_id',
                'binding_table' => 'books_bindings',
                'uikey' => 'A_GOTOBOOKS',
                'with' => array(
                    'authors', 'publisher', 'category', 'subcategory', 'binding', 'lookinside',
                    'series', 'languages', 'offers', 'vendorData', 'vendorData.deliveryTime')),
            15 => array('title' => 'Ноты', 'site_id' => 6, 'table' => 'tblSheetMusic', 'site_table' => 'musicsheets_catalog',
                'entity' => 'musicsheets', 'model' => 'SheetMusic',
                'site_category_table' => 'musicsheets_categories',
                'site_series_table' => 'musicsheets_series',
                'author_table' => 'musicsheets_authors',
                'author_entity_field' => 'musicsheet_id',
                'binding_table' => 'musicsheets_bindings',
                'uikey' => 'A_GOTOMUSICSHEETS',
                'with' => array('authors', 'publisher', 'category', 'subcategory', 'binding', 'lookinside', 'series',
                    'languages', 'offers', 'vendorData', 'vendorData.deliveryTime')
            ),
            20 => array('title' => 'Аудиокниги', 'site_id' => 2, 'table' => 'tblAudio', 'site_table' => 'audio_catalog',
                'entity' => 'audio', 'model' => 'Audio',
                'site_category_table' => 'audio_categories',
                'author_table' => 'audio_authors',
                'author_entity_field' => 'audio_id',
                'performer_table_list' => 'audio_performerslist',
                'performer_table' => 'audio_performers',
                'performer_field' => 'audio_id',
                'uikey' => 'A_GOTOAUDIO',
                'with' => array('authors', 'performers', 'publisher', 'category', 'subcategory', 'media', 'lookinside',
                    'languages', 'offers', 'vendorData', 'vendorData.deliveryTime'),
            ),
            22 => array('title' => 'Музыка', 'site_id' => 7, 'table' => 'tblMusic', 'site_table' => 'music_catalog',
                'entity' => 'music', 'model' => 'Music',
                'site_category_table' => 'music_categories',
                'site_series_table' => 'music_series',
                'author_table' => 'music_authors',
                'author_entity_field' => 'music_id',
                'performer_table_list' => 'music_performerslist',
                'performer_table' => 'music_performers',
                'performer_field' => 'music_id',
                'uikey' => 'Music catalog',
                'with' => array('authors', 'performers', 'publisher', 'category', 'subcategory', 'media',
                    'lookinside', 'series', 'media', 'performers', 'languages', 'offers', 'vendorData', 'vendorData.deliveryTime'),
            ),
            24 => array('title' => 'Софт', 'site_id' => 8, 'table' => 'tblSoft', 'site_table' => 'soft_catalog',
                'entity' => 'soft', 'model' => 'Soft',
                'site_category_table' => 'soft_categories',
                'site_series_table' => 'soft_series',
                'author_table' => 'soft_authors',
                'author_entity_field' => 'soft_id',
                'uikey' => 'A_GOTOSOFT',
                'with' => array('publisher', 'category', 'subcategory', 'media', 'lookinside', 'media', 'authors',
                    'languages', 'offers', 'vendorData', 'vendorData.deliveryTime'),
            ),
            30 => array('title' => 'Периодика', 'site_id' => 4, 'table' => 'tblPereodics', 'site_table' => 'pereodics_catalog',
                'entity' => 'pereodics', 'model' => 'Periodic',
                'site_category_table' => 'pereodics_categories',
                'type_table' => 'pereodics_types',
                'uikey' => 'A_GOTOPEREODICALS',
                'with' => array('languages', 'category', 'subcategory', 'lookinside', 'magazinetype', 'periodicCountry', 'vendorData', 'vendorData.deliveryTime'),
            ),
            40 => array('title' => 'Видео', 'site_id' => 3, 'table' => 'tblVideo', 'site_table' => 'video_catalog',
                'entity' => 'video', 'model' => 'Video',
                'directors_table' => 'video_directors',
                'actors_table' => 'video_actors',
                'site_category_table' => 'video_categories',
                'uikey' => 'A_GOTOVIDEO',
                'with' => array('directors', 'actors', 'producers', 'subtitles', 'media',
                    'category', 'subcategory', 'lookinside',
                    'directors', 'actors', 'subtitles', 'zone2',
                    'languages', 'offers', 'audiostreams', 'vendorData', 'vendorData.deliveryTime'
                ),
            ),
            50 => array('title' => 'Печатная продукция', 'site_id' => 5, 'table' => 'tblPrinted', 'site_table' => 'printed_catalog',
                'entity' => 'printed', 'model' => 'Printed',
                'site_category_table' => 'printed_categories',
//                'author_table' => 'printed_authors',
//                'author_entity_field' => 'printed_id',
                'uikey' => 'A_GOTOPRINTED',
                'with' => array(/*'authors', */'publisher', 'category', 'subcategory', 'lookinside', 'languages', 'offers', 'vendorData', 'vendorData.deliveryTime'),
            ),
            60 => array('title' => 'Карты', 'site_id' => 9, 'table' => 'tblMaps', 'site_table' => 'maps_catalog',
                'entity' => 'maps', 'model' => 'Maps',
                'site_category_table' => 'maps_categories',
                'binding_table' => 'maps_bindings',
                'uikey' => 'A_GOTOMAPS',
                'with' => array('publisher', 'category', 'subcategory', 'binding', 'lookinside', 'languages', 'offers', 'vendorData', 'vendorData.deliveryTime'),
            ),
        );
    }

    public static function GetSiteEntitiesList() {
        return array(
            1 => array('title' => 'Книги', 'site_id' => 1, 'human_id' => 10, 'table' => 'tblBooks'),
            6 => array('title' => 'Ноты', 'site_id' => 6, 'human_id' => 15, 'table' => 'tblSheetMusic'),
            2 => array('title' => 'Аудиокниги', 'site_id' => 2, 'human_id' => 20, 'table' => 'tblAudio'),
            7 => array('title' => 'Музыка', 'site_id' => 7, 'human_id' => 22, 'table' => 'tblMusic'),
            8 => array('title' => 'Софт', 'site_id' => 8, 'human_id' => 24, 'table' => 'tblSoft'),
            4 => array('title' => 'Периодика', 'site_id' => 4, 'human_id' => 30, 'table' => 'tblPereodics'),
            3 => array('title' => 'Видео', 'site_id' => 3, 'human_id' => 40, 'table' => 'tblVideo'),
            5 => array('title' => 'Печатная продукция', 'site_id' => 5, 'human_id' => 50, 'table' => 'tblPrinted'),
            9 => array('title' => 'Карты', 'site_id' => 9, 'human_id' => 60, 'table' => 'tblMaps'),
        );
    }

    public static function GetTitle($entity) {
        $entities = self::GetEntitiesList();
        $e = $entities[$entity];
        return Yii::app()->ui->item($e['uikey']);
    }

    public static function CreateDataProvider($entity) {
        $entities = self::GetEntitiesList();
        $e = $entities[$entity];

        $dp = new MyDataProvider($e['model'], array('cacheID' => Yii::app()->params['DataProviderCacheID'],
            'cacheTimeout' => Yii::app()->params['DataProviderCacheTimeout']
        ));

        $criteria = new CDbCriteria;
        $criteria->with = $e['with'];
        $criteria->select = array('*', $entity . ' AS entity');

        $dp->criteria = $criteria;

        //var_dump($criteria->select);
        
        return $dp;
    }

    public static function GetUrlKey($entity) {
        switch ($entity) {
            case self::PERIODIC : return 'periodics';
            case self::BOOKS : return 'books';
            case self::SHEETMUSIC : return 'sheetmusic';
            case self::AUDIO : return 'audio';
            case self::MUSIC : return 'music';
            case self::VIDEO : return 'video';
            case self::SOFT : return 'soft';
            case self::MAPS : return 'maps';
            case self::PRINTED : return 'printed';
            default: throw new CException('Wrong entity [' . $entity . ']');
        }
    }

    public static function ParseFromString($entity) {
        $entity = strtolower($entity);
        switch ($entity) {
            case 'periodics' : return self::PERIODIC;
            case 'books' : return self::BOOKS;
            case 'sheetmusic' : return self::SHEETMUSIC;
            case 'audio' : return self::AUDIO;
            case 'music' : return self::MUSIC;
            case 'video' : return self::VIDEO;
            case 'soft' : return self::SOFT;
            case 'maps' : return self::MAPS;
            case 'printed' : return self::PRINTED;
            default: return false;
        }
    }

    public static function IsValid($entity) {
        $entities = self::GetEntitiesList();
        return isset($entities[$entity]);
    }

    const BOOKS = 10;
    const SHEETMUSIC = 15;
    const AUDIO = 20;
    const MUSIC = 22;
    const PERIODIC = 30;
    const VIDEO = 40;
    const SOFT = 24;
    const MAPS = 60;
    const PRINTED = 50;

    public static function ConvertToSite($entity) {
        return $entity;

        if ($entity < 10)
            return $entity;
        $entities = Entity::GetEntitiesList();
        return $entities[$entity]['site_id'];
    }

    public static function ConvertToHuman($entity) {
        if ($entity >= 10)
            return $entity;
        $entities = Entity::GetSiteEntitiesList();
        return $entities[$entity]['human_id'];
    }

    public static function GetEntityListForSelect() {
        $rows = array();
        foreach (self::GetEntitiesList() as $id => $data) {
            $rows[] = array('ID' => $id, 'Name' => self::GetTitle($id));
        }
        return $rows;
    }

    static function checkEntityParam($entity, $paramName) {
        if (!self::IsValid($entity)) return false;
        $settings = self::GetEntitiesList()[$entity];
        switch ($paramName) {
            case 'years': if ($entity == self::PERIODIC) return false; break;
            case 'gift': if ($entity != self::PERIODIC) return false; break;
            case 'yearreleases': if ($entity != self::VIDEO) return false; break;
            default: if (!in_array($paramName, $settings['with'])) return false; break;
        }
        return true;
    }
}

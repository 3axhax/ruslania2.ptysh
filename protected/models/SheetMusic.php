<?php

class SheetMusic extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'musicsheets_catalog';
    }

    function getEntity() { return Entity::SHEETMUSIC; }

    public function relations()
    {
        return array(
            'authors' => array(self::MANY_MANY, 'CommonAuthor', 'musicsheets_authors(musicsheet_id, author_id)'),
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'SheetMusicCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'SheetMusicCategory', 'subcode'),
            'binding' => array(self::BELONGS_TO, 'SheetMusicBinding', 'binding_id'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::SHEETMUSIC ),
            'series' => array(self::BELONGS_TO, 'Series', array('series_id' => 'id'), 'on' => 'series.entity='.Entity::SHEETMUSIC),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::SHEETMUSIC ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::SHEETMUSIC),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
        );
    }
}
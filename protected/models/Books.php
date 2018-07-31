<?php

class Books extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'books_catalog';
    }
    
    function getEntity() { return Entity::BOOKS; }

    public function relations()
    {
        return array(
            'authors' => array(self::MANY_MANY, 'CommonAuthor', 'books_authors(book_id, author_id)'),
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'BookCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'BookCategory', 'subcode'),
            'binding' => array(self::BELONGS_TO, 'BookBinding', 'binding_id'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::BOOKS ),
            'series' => array(self::BELONGS_TO, 'Series', array('series_id' => 'id'), 'on' => 'series.entity='.Entity::BOOKS),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::BOOKS ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::BOOKS),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),

        );
    }
}
<?php

class Printed extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'printed_catalog';
    }

    function getEntity() { return Entity::PRINTED; }

    public function relations()
    {
        return array(
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'PrintedCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'PrintedCategory', 'subcode'),
            'authors' => array(self::MANY_MANY, 'CommonAuthor', 'printed_authors(printed_id, author_id)'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::PRINTED ),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::PRINTED ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::PRINTED),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),

        );
    }
}
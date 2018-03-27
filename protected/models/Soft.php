<?php

class Soft extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'soft_catalog';
    }

    public function relations()
    {
        return array(
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'SoftCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'SoftCategory', 'subcode'),
            'media' => array(self::BELONGS_TO, 'SoftMedia', 'media_id'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::SOFT ),
            'authors' => array(self::MANY_MANY, 'CommonAuthor', 'soft_authors(soft_id, author_id)'),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::SOFT ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::SOFT),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
        );
    }
}
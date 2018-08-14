<?php

class Maps extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'maps_catalog';
    }

    function getEntity() { return Entity::MAPS; }

    public function relations()
    {
        return array(
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'MapsCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'MapsCategory', 'subcode'),
            'binding' => array(self::BELONGS_TO, 'MapsBinding', 'binding_id'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::MAPS ),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::MAPS ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::MAPS),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
        );
    }
}
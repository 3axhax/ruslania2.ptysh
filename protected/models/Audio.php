<?php

class Audio extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'audio_catalog';
    }

    function getEntity() { return Entity::AUDIO; }

    public function relations()
    {
        return array(
            'authors' => array(self::MANY_MANY, 'CommonAuthor', 'audio_authors(audio_id, author_id)'),
            'performers' => array(self::MANY_MANY, 'CommonAuthor', 'audio_performers(audio_id, person_id)'),
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'AudioCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'AudioCategory', 'subcode'),
            'media' => array(self::BELONGS_TO, 'AudioMedia', 'media_id'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::AUDIO ),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::AUDIO ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::AUDIO),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
        );
    }
}
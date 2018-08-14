<?php

class Video extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'video_catalog';
    }

    function getEntity() { return Entity::VIDEO; }

    //'with' => array('directors', 'roles', 'subtitles', 'media', 'zone', 'category', 'subcategory'),

    public function relations()
    {
        return array(
            'directors' => array(self::MANY_MANY, 'CommonAuthor', 'video_directors(video_id, person_id)'),
            'actors' => array(self::MANY_MANY, 'CommonAuthor', 'video_actors(video_id, person_id)'),
            'category' => array(self::BELONGS_TO, 'VideoCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'VideoCategory', 'subcode'),
            'producers' => array(self::MANY_MANY, 'VideoProducer', 'video_producers(video_id, producer_id)'),
            'media' => array(self::BELONGS_TO, 'VideoMedia', 'media_id'),
            'zone2' => array(self::BELONGS_TO, 'VideoZone', 'zone_id'),
            'subtitles' => array(self::MANY_MANY, 'VideoSubtitle', 'video_credits(video_id, credits_id)'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::VIDEO),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::VIDEO ),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::VIDEO),
            'audiostreams' => array(self::MANY_MANY, 'VideoAudioStream', 'video_audiostreams(video_id, stream_id)'),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
        );
    }
}
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
            'videoStudio' => array(self::BELONGS_TO, 'VideoStudio', 'studio'),
        );
    }

    function getPrices($ids) {
        if (empty($ids)) return array();

        $sql = ''.
            'select id, ' . Entity::VIDEO . ' entity, brutto, vat, discount, unitweight_skip, code, subcode, year '.
            'from ' . $this->tableName() . ' '.
            'where (id in (' . implode(',', $ids) . ')) '.
        '';
        $items = array();
        foreach (Yii::app()->db->createCommand($sql)->queryAll() as $item) {
            $items[$item['id']] = $item;
            $items[$item['id']]['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $items[$item['id']]['priceData']['unit'] = '';
        }
        return $items;
    }


}
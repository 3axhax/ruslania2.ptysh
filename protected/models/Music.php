<?php

class Music extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'music_catalog';
    }

    function getEntity() { return Entity::MUSIC; }

    public function relations()
    {
        return array(
            'authors' => array(self::MANY_MANY, 'CommonAuthor', 'music_authors(music_id, author_id)'),
            'performers' => array(self::MANY_MANY, 'CommonAuthor', 'music_performers(music_id, person_id)'),
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'category' => array(self::BELONGS_TO, 'MusicCategory', 'code'),
            'subcategory' => array(self::BELONGS_TO, 'MusicCategory', 'subcode'),
            'lookinside' => array(self::HAS_MANY, 'Lookinside', 'item_id', 'on' => 'lookinside.entity='.Entity::MUSIC ),
            'series' => array(self::BELONGS_TO, 'Series', array('series_id' => 'id'), 'on' => 'series.entity='.Entity::MUSIC),
            'media' => array(self::BELONGS_TO, 'Media', array('media_id' => 'id'), 'on' => 'media.entity='.Entity::MUSIC),
            'languages' => array(self::HAS_MANY, 'ItemLanguage', 'item_id', 'on' => 'languages.entity='.Entity::MUSIC),
            'offers' => array(self::MANY_MANY, 'Offer', 'offer_items(item_id, offer_id)', 'on' => 'offers_offers.entity_id='.Entity::MUSIC),
            'vendorData' => array(self::BELONGS_TO, 'Vendor', 'vendor'),
        );
    }

    function getPrices($ids) {
        if (empty($ids)) return array();

        $sql = ''.
            'select id, ' . Entity::MUSIC . ' entity, brutto, vat, discount, unitweight_skip, code, subcode, series_id, publisher_id, year '.
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
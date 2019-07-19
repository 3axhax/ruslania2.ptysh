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

    function getPrices($ids) {
        if (empty($ids)) return array();

        $sql = ''.
            'select id, ' . Entity::PRINTED . ' entity, brutto, vat, discount, unitweight_skip, code, subcode, publisher_id, year '.
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
<?php

class Offer extends CMyActiveRecord
{
    // Это ID в таблице offers
    const INDEX_PAGE = 2;
    const FIRMS = 3;
    const LIBRARY = 4;
    const UNI = 5;

    const FREE_SHIPPING = 777;
    const ALLE_2_EURO = 999;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'offers';
    }

    public function GetList()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'is_special=0 AND is_active=1';
        $criteria->order = 'creation_date DESC';
//        $criteria->select = 'id1, icon_entity, creation_date';

        $cnt = Offer::model()->count();

        $paginator = new CPagination($cnt);
        $paginator->setPageSize(5);
        $paginator->applyLimit($criteria);

        $list = Offer::model()->findAll($criteria);

        return array('Items' => $list, 'Paginator' => $paginator);
    }

    public function GetOffer($oid, $isSpecial, $isActive)
    {
        $sql = 'SELECT * FROM offers WHERE id=:oid AND is_active=:active';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':oid' => $oid, ':active' => $isActive));
        return $row;
    }

    public function GetItems($oid, $entity = false)
    {
        $key = 'Offer_'.$oid;

        $fullInfo = Yii::app()->dbCache->get($key);

        if($fullInfo === false)
        {
            //вместо * достаточно entity_id, item_id
            if (!$entity) {
                $sql = 'SELECT * FROM offer_items WHERE offer_id=:id ORDER BY group_order, sort_order limit 30';
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid));
            }
            else {
                $sql = 'SELECT * FROM offer_items WHERE offer_id=:id AND entity_id=:entity ORDER BY group_order, sort_order limit 30';
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid, ':entity' => $entity));
            }

            $items = array();
            foreach($rows as $row)
            {
                $items[$row['entity_id']][] = $row['item_id'];
            }

            $p = new Product();
            $fullInfo = array();
            foreach($items as $entity=>$ids)
            {
                $tmp = array();
                $list = $p->GetProductsV2($entity, $ids, true);
                foreach($items[$entity] as $iid)
                {
                    if(!isset($list[$iid])) continue;
                    $av = Availability::GetStatus($list[$iid]);
                    if($av == Availability::NOT_AVAIL_AT_ALL) continue; // В подборках нет товаров, которых не заказать

                    if(isset($list[$iid])) $tmp[] = $list[$iid];
                }

                $fullInfo[Entity::GetTitle($entity)] = array('entity' => $entity, 'items' => $tmp);
            }

            Yii::app()->dbCache->set($key, $fullInfo, Yii::app()->params['DbCache']);
        }

        return $fullInfo;
    }
    
    public function GetItemsAll($oid, $entity = false)
    {
        $key = 'Offer_'.$oid;

        $fullInfo = Yii::app()->dbCache->get($key);

        if($fullInfo === false)
        {
            //вместо * достаточно entity_id, item_id
            if (!$entity) {
                $sql = 'SELECT * FROM offer_items WHERE offer_id=:id ORDER BY group_order, sort_order limit 30';
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid));
            }
            else {
                $sql = 'SELECT * FROM offer_items WHERE offer_id=:id AND entity_id=:entity ORDER BY group_order, sort_order limit 30';
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid, ':entity' => $entity));
            }

            $items = array();
            foreach($rows as $row)
            {
                $items[$row['entity_id']][] = $row['item_id'];
            }

            $p = new Product();
            $fullInfo = array();
            foreach($items as $entity=>$ids)
            {
                $tmp = array();
                $list = $p->GetProductsV2($entity, $ids, true);
                foreach($items[$entity] as $iid)
                {
                    if(!isset($list[$iid])) continue;
                    $av = Availability::GetStatus($list[$iid]);
                    if($av == Availability::NOT_AVAIL_AT_ALL) continue; // В подборках нет товаров, которых не заказать

                    if(isset($list[$iid])) $tmp[] = $list[$iid];
                }

                $fullInfo = $tmp;
            }

            Yii::app()->dbCache->set($key, $fullInfo, Yii::app()->params['DbCache']);
        }

        return $fullInfo;
    }

    public function GetItemsV2($oid)
    {
        $key = 'Offer_'.$oid;

        $fullInfo = Yii::app()->dbCache->get($key);

        if($fullInfo === false)
        {
            $sql = 'SELECT * FROM offer_items WHERE offer_id=:id ORDER BY group_order, sort_order';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid));
            $items = array();
            foreach($rows as $row)
            {
                $items[$row['entity_id']][] = $row['item_id'];
            }

            $fullInfo = array();

            $sql = 'SELECT * FROM video_catalog WHERE id IN ('.implode(',', $items[40]).')';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();


        }

        return $fullInfo;
    }
}
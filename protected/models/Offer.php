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

    static function getMode($id) {
        //<mode:firms|uni|lib|fs|alle2>
        switch($id) {
            case self::FIRMS: return 'firms';
            case self::LIBRARY: return 'lib';
            case self::UNI: return 'uni';
            case self::FREE_SHIPPING: return 'fs';
            case self::ALLE_2_EURO: return 'alle2';
        }
        return '';
    }
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

    public function GetItemsExport($oid)
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

            Yii::app()->dbCache->set($key, $fullInfo, Yii::app()->params['DbCacheTime']);
        }

        return $fullInfo;
    }

    public function GetItems($oid, $entity = false)
    {
        $key = 'Offer_'.$oid;

        $fullInfo = Yii::app()->dbCache->get($key);

        if($fullInfo === false) {
            $items = array();
            if (!$entity) {
                $sql = ''.
                    'select entity_id, substring_index(group_concat(item_id order by group_order, sort_order), ",", 30) ids ' .
                    'from offer_items ' .
                    'where (offer_id=:id) ' .
                    'group by entity_id '.
                    'order by group_order, sort_order '.
                '';

//                $sql = 'SELECT * FROM offer_items WHERE offer_id=:id ORDER BY group_order, sort_order limit 30';
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid));
                foreach ($rows as $row) $items[$row['entity_id']] = explode(',', $row['ids']);
            }
            else {
                $sql = 'SELECT item_id FROM offer_items WHERE offer_id=:id AND entity_id=:entity ORDER BY group_order, sort_order limit 30';
                $items[$entity] = Yii::app()->db->createCommand($sql)->queryColumn(array(':id' => $oid, ':entity' => $entity));
//                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid, ':entity' => $entity));
            }

//            foreach($rows as $row)
//            {
//                $items[$row['entity_id']][] = $row['item_id'];
//            }

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

                    if(isset($list[$iid])) {
                        $list[$iid]['status'] = $p->GetStatusProduct($entity, $iid);
                        $tmp[] = $list[$iid];
                    }
                }

                $fullInfo[Entity::GetTitle($entity)] = array('entity' => $entity, 'items' => $tmp);
            }

            Yii::app()->dbCache->set($key, $fullInfo, Yii::app()->params['DbCacheTime']);
        }

        return $fullInfo;
    }
    
    public function GetItemsAll($oid, $entity = false)
    {
        $key = 'Offer_'.$oid;

        $fullInfo = Yii::app()->dbCache->get($key);

        if($fullInfo === false || $oid == 999)
        {
            //вместо * достаточно entity_id, item_id
            if (!$entity) {
                $sql = 'SELECT sql_calc_found_rows * FROM offer_items WHERE offer_id=:id ORDER BY group_order, sort_order limit 30';
                if ($oid == 999) {
                    $sql = "SELECT entity_id, item_id
                            FROM offer_items as oi
                                LEFT JOIN books_catalog as bc ON oi.entity_id = 10 AND oi.item_id = bc.id AND bc.avail_for_order = 1
                                LEFT JOIN musicsheets_catalog as muc ON oi.entity_id = 15 AND oi.item_id = muc.id AND muc.avail_for_order = 1
                                LEFT JOIN music_catalog as mc ON oi.entity_id = 22 AND oi.item_id = mc.id AND mc.avail_for_order = 1
                                LEFT JOIN soft_catalog as sc ON oi.entity_id = 24 AND oi.item_id = sc.id AND sc.avail_for_order = 1
                                LEFT JOIN pereodics_catalog as pc ON oi.entity_id = 30 AND oi.item_id = pc.id AND pc.avail_for_order = 1
                                LEFT JOIN video_catalog as vc ON oi.entity_id = 40 AND oi.item_id = vc.id AND vc.avail_for_order = 1
                                LEFT JOIN printed_catalog as prc ON oi.entity_id = 50 AND oi.item_id = prc.id AND prc.avail_for_order = 1
                                LEFT JOIN maps_catalog as mac ON oi.entity_id = 60 AND oi.item_id = mac.id AND mac.avail_for_order = 1
                            WHERE oi.offer_id=:id AND (bc.id IS NOT NULL OR muc.id IS NOT NULL OR mc.id IS NOT NULL OR sc.id IS NOT NULL OR pc.id IS NOT NULL OR vc.id IS NOT NULL OR prc.id IS NOT NULL OR mac.id IS NOT NULL)
                            ORDER BY RAND() LIMIT 15";
                }
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $oid));
            }
            else {
                $sql = 'SELECT sql_calc_found_rows * FROM offer_items WHERE offer_id=:id AND entity_id=:entity ORDER BY group_order, sort_order limit 30';
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

                $fullInfo = array_merge($fullInfo, $tmp);
            }

            Yii::app()->dbCache->set($key, $fullInfo, Yii::app()->params['DbCacheTime']);
        }

        shuffle($fullInfo);
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

    function getCounts($oid) {
        $this->fi();
    }
}
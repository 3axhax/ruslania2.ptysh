<?php

class OfferItem extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'offer_items';
    }

    function getList($oid, $eid = 0, $withoutGroups = false) {
        $criteria = new CDbCriteria();
        if ((int)$eid > 0) $criteria->condition = '(t.offer_id = :oid) and (t.entity_id = :eid)';
        else $criteria->condition = '(t.offer_id = :oid)';
        if ((int)$eid > 0) $criteria->params = array(':oid'=>$oid, ':eid'=>$eid);
        else $criteria->params = array(':oid'=>$oid);
        $cnt = $this->count($criteria);

        if (Yii::app()->getRequest()->getParam('all')) {
            $criteria->order = 'rand()';
            $paginator = null;
        }
        else {
            if ($withoutGroups) $criteria->order = 't.sort_order asc';
            else $criteria->order = 't.group_order asc, t.sort_order asc';
            $paginator = new CPagination($cnt);
            $paginator->setPageSize(Yii::app()->params['ItemsPerPage']);
            $paginator->applyLimit($criteria);
        }

        $rows = $this->findAll($criteria);
        $items = array();
        $allItems = array(); //массив для товаров по порядку из подборки
        foreach($rows as $row) {
            $eid = $row['entity_id'];
            if (empty($items[$eid])) $items[$eid] = array();
            $items[$eid][] = $row['item_id'];
            $allItems[$eid . '_' . $row['item_id']] = array();
        }

        $p = new Product();
        $fullInfo = array();
        foreach($items as $entity=>$ids) {
            $tmp = array();
            $list = $p->GetProductsV2($entity, $ids, true);
            foreach($items[$entity] as $iid) {
                if(!isset($list[$iid])) {
                    unset($allItems[$entity . '_' . $iid]);
                    continue;
                }
                $av = Availability::GetStatus($list[$iid]);
                if($av == Availability::NOT_AVAIL_AT_ALL) {
                    unset($allItems[$entity . '_' . $iid]);
                    continue;
                } // В подборках нет товаров, которых не заказать
                $allItems[$entity . '_' . $iid] = $list[$iid];
                $tmp[] = $list[$iid];
            }

            $fullInfo[Entity::GetTitle($entity)] = array('entity' => $entity, 'items' => $tmp);
        }

        if (!$withoutGroups) return array($fullInfo, $paginator);

        $result = array(
            0=>array('entity' => 0, 'items' => array_values($allItems)),
        );
        return array($result, $paginator);
    }

    function getEntitys($oid) {
        $sql = 'select entity_id from ' . $this->tableName() . ' where (offer_id = :oid) group by entity_id order by group_order asc';
        return Yii::app()->db->createCommand($sql)->queryColumn(array(':oid'=>$oid));
    }

    function forSlider($oid) {
/*
select tAll.item_id, tAll.eid, rand, tAll.idItem from (
select t.item_id, 10 eid, rand() rand, tI.id idItem from offer_items t left join books_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 10)
union
select t.item_id, 15 eid, rand() rand, tI.id idItem from offer_items t left join musicsheets_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 15)
union
select t.item_id, 22 eid, rand() rand, tI.id idItem from offer_items t left join music_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 22)
union
select t.item_id, 24 eid, rand() rand, tI.id idItem from offer_items t left join soft_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 24)
union
select t.item_id, 30 eid, rand() rand, tI.id idItem from offer_items t left join pereodics_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 30)
union
select t.item_id, 40 eid, rand() rand, tI.id idItem from offer_items t left join video_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 40)
union
select t.item_id, 60 eid, rand() rand, tI.id idItem from offer_items t left join maps_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 60)
union
select t.item_id, 50 eid, rand() rand, tI.id idItem from offer_items t left join printed_catalog tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) where (t.offer_id = 999) and (t.entity_id = 50)
) tAll
where (tAll.idItem is not null)
order by tAll.rand
*/
        $subquery = array();
        foreach (Entity::GetEntitiesList() as $eid=>$params) {
            $subquery[] = ''.
                'select t.item_id, ' . $eid . ' eid, t.sort_order, t.group_order, tI.id idItem, (select id from ' . $params['photo_table'] . ' where (iid = t.item_id) and (is_upload = 1) and (position = 1) limit 1) idPhoto '.
                'from offer_items t '.
                    'left join ' . $params['site_table'] . ' tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) '.
                'where (t.offer_id = ' . (int) $oid . ') and (t.entity_id = ' . $eid . ')' .
            '';
        }
        $sql = ''.
            'select tAll.item_id id, tAll.eid entity, tAll.idPhoto '.
            'from (' . implode(' union ', $subquery) . ') tAll '.
            'where (tAll.idItem is not null) and (tAll.idPhoto is not null) and (tAll.idPhoto > 0) '.
            'order by tAll.group_order, tAll.sort_order '.
            'limit 30 '.
        '';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $items = array();
        foreach($rows as $row) {
            if (empty($items[$row['entity']])) $items[$row['entity']] = array();
            $items[$row['entity']][$row['id']] = $row;
        }

        $fields = array('id', 'title_ru', 'title_rut', 'title_en', 'title_fi');
        foreach($items as $entity=>$ids) {
            $sql = 'select ' . implode(',', $fields) . ' from ' . Entity::GetEntitiesList()[$entity]['site_table'] . ' where (id in (' . implode(',',array_keys($ids)) . '))';
            foreach (Yii::app()->db->createCommand($sql)->queryAll() as $item) {
                $items[$entity][$item['id']] = array_merge($items[$entity][$item['id']], $item);
            }
        }

        return $items;
    }

    function forSliderAllData($oid) {
        $entitys = $this->getEntitys($oid);
        $items = array();
        foreach ($entitys as $eid) {
            $params = Entity::GetEntitiesList()[$eid];
            $subquery = ''.
                'select t.item_id id, (select id from ' . $params['photo_table'] . ' where (iid = t.item_id) and (is_upload = 1) and (position = 1) limit 1) idPhoto '.
                'from offer_items t '.
                    'join ' . $params['site_table'] . ' tI on (tI.id = t.item_id) and (tI.avail_for_order = 1) '.
                'where (t.offer_id = ' . (int) $oid . ') and (t.entity_id = ' . $eid . ') ' .
                'having (idPhoto > 0) '.
                'order by t.sort_order asc '.
                'limit 30 '.
            '';
            foreach (Yii::app()->db->createCommand($subquery)->queryAll() as $item) {
                if (empty($items[$eid])) $items[$eid] = array();
                $item['entity'] = $eid;
                $items[$eid][$item['id']] = $item;
            }
        }
        $p = new Product();
        $fullInfo = array();
        foreach($items as $entity=>$ids) {
            foreach($p->GetProductsV2($entity, array_keys($ids), true) as $item) {
                $items[$entity][$item['id']] = array_merge($items[$entity][$item['id']], $item);
            }
            $fullInfo[Entity::GetTitle($entity)] = array('entity' => $entity, 'items' => $items[$entity]);
        }
        return $fullInfo;
    }

}
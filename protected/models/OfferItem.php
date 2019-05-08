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

    function getList($oid) {
        $criteria = new CDbCriteria();
        $criteria->condition = '(t.offer_id = :oid)';
        $criteria->order = 't.group_order asc, t.sort_order asc';
        $criteria->params = array(':oid'=>$oid);

        $cnt = $this->count($criteria);

        $paginator = new CPagination($cnt);
        $paginator->setPageSize(Yii::app()->params['ItemsPerPage']);
        $paginator->applyLimit($criteria);

        $rows = $this->findAll($criteria);
        $items = array();
        foreach($rows as $row) $items[$row['entity_id']][] = $row['item_id'];

        $p = new Product();
        $fullInfo = array();
        foreach($items as $entity=>$ids) {
            $tmp = array();
            $list = $p->GetProductsV2($entity, $ids, true);
            foreach($items[$entity] as $iid) {
                if(!isset($list[$iid])) continue;
                $av = Availability::GetStatus($list[$iid]);
                if($av == Availability::NOT_AVAIL_AT_ALL) continue; // В подборках нет товаров, которых не заказать

                if(isset($list[$iid])) $tmp[] = $list[$iid];
            }

            $fullInfo[Entity::GetTitle($entity)] = array('entity' => $entity, 'items' => $tmp);
        }

        return array($fullInfo, $paginator);
    }


}
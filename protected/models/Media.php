<?php

class Media extends CMyActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function Url($item)
    {
        return
            Yii::app()->createUrl('entity/bymedia',
                array('entity' => Entity::GetUrlKey($item['entity']),
                      'mid' => $item['Media']['id'],
                      'title' => ProductHelper::ToAscii($item['Media']['title'])
                ));
    }

    public function tableName()
    {
        return 'all_media';
    }

    public function GetMedia($entity, $mid)
    {
        $sql = 'SELECT * FROM all_media WHERE entity=:entity AND id=:id LIMIT 1';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':entity' => $entity, ':id' => $mid));
        return $row;
    }

    public function GetTotalItems($entity, $mid, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];

        $sql = 'SELECT COUNT(*) FROM '.$data['site_table'].' WHERE media_id=:id';
        if($avail) $sql .= ' AND avail_for_order=1';
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $mid));
        return $cnt;
    }

    public function GetItems($entity, $mid, $paginator, $sort, $lang, $avail)
    {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->addCondition('media_id=:mid');
        $criteria->addCondition('avail_for_order=1');
        $criteria->params[':mid'] = $mid;
        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;
        $data = $dp->getData();
        return Product::FlatResult($data);
    }


}


/*
DELETE FROM all_media;
INSERT INTO `all_media` (entity, id, title)
SELECT 20 AS entity, id, title FROM audio_media
UNION
SELECT 22 AS entity, id, title FROM music_media
UNION
SELECT 40 AS entity, id, title FROM video_media
UNION
SELECT 24 AS entity, id, title FROM soft_media
*/
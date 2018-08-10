<?php

class Series extends CMyActiveRecord
{
    private $_perToPage = 150;
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'all_series';
    }

    public function GetList($entity, $lang) {
        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);

        $availSortLangs = array('ru', 'rut', 'en', 'fi');
        if(!in_array($lang, $availSortLangs)) $lang = 'en';
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];

        if(!array_key_exists('site_series_table', $data)) return array();
        $table = $data['site_series_table'];

        $sql = 'SELECT * FROM '.
            $table.' '.
            'ORDER BY title_'.$lang . ' '.
            'limit ' . ($page-1)*$this->_perToPage . ', ' . $this->_perToPage . ' '.
        '';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        return $rows;
    }

    public function GetTotalItems($entity, $sid, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('site_series_table', $data)) return 0;
        $table = $data['site_table'];

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE series_id=:id';
        if($avail) $sql .= ' AND avail_for_order=1';
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $sid));
        return $cnt;
    }

    public function GetItems($entity, $sid, $paginator, $sort, $lang, $avail)
    {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        if(!empty($sid))
        {
            $criteria->addCondition('series_id=:sid');
            $criteria->params[':sid'] = $sid;
        }

        if($avail)
            $criteria->addCondition('avail_for_order=1');

        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        return Product::FlatResult($data);
    }
    
     public function allSearch()
    {
        $sql = 'SELECT count(*) as cnt FROM `users_search_log`';
        $rows = Yii::app()->db->createCommand($sql)->queryScalar();
        return $rows;
    }
    
    public static function Url($item)
    {
        return
        Yii::app()->createUrl('entity/byseries',
            array('entity' => Entity::GetUrlKey($item['entity']),
                  'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($item)),
                  'sid' => $item['id']));
    }

    public function GetByIds($entity, $itemIDs)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('site_series_table', $data)) return array();

        $sql = 'SELECT * FROM '.$data['site_series_table'].' WHERE id IN ('.implode(',', $itemIDs).')';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }
}

/*
DELETE FROM all_series;
INSERT INTO `all_series` (entity, id, title_ru, title_rut, title_en, title_fi)
SELECT 10 AS entity, id, title_ru, title_rut, title_en, title_fi FROM books_series
UNION
SELECT 15 AS entity, id, title_ru, title_rut, title_en, title_fi FROM musicsheets_series
UNION
SELECT 22 AS entity, id, title_ru, title_rut, title_en, title_fi FROM music_series
UNION
SELECT 24 AS entity, id, title_ru, title_rut, title_en, title_fi FROM soft_series
*/
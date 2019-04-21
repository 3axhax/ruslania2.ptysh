<?php

class YearRetriever
{
    public function GetTotalItems($entity, $year, $avail)
    {
        if($entity == Entity::PERIODIC) return 0;
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $table = $data['site_table'];

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE `year`=:year';
        if(!empty($avail))
            $sql .= ' AND avail_for_order=1';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':year' => $year));
        return $cnt;
    }

    public function GetItems($entity, $year, $paginator, $sort, $lang, $avail)
    {
        if($entity == Entity::PERIODIC) return array();

        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();

        if(!empty($year))
        {
            $criteria->addCondition('t.year=:year');
            $criteria->params[':year'] = intVal($year);
        }

        if(!empty($avail))
        {
            $criteria->addCondition('t.avail_for_order=1');
        }

        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        return Product::FlatResult($data);
    }
	
	public function GetTotalItems2($entity, $year, $avail)
    {
        if($entity == Entity::PERIODIC) return 0;
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $table = $data['site_table'];

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE `release_year`=:year';
        if(!empty($avail))
            $sql .= ' AND avail_for_order=1';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':year' => $year));
        return $cnt;
    }

    public function GetItems2($entity, $year, $paginator, $sort, $lang, $avail)
    {
        if($entity == Entity::PERIODIC) return array();

        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();

        if(!empty($year))
        {
            $criteria->addCondition('t.release_year=:year');
            $criteria->params[':year'] = intVal($year);
        }

        if(!empty($avail))
        {
            $criteria->addCondition('t.avail_for_order=1');
        }

        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        return Product::FlatResult($data);
    }

    function getAll($entity) {
        $entities = Entity::GetEntitiesList();
        if (empty($entities[$entity])) return array();
        if ($entities[$entity]['site_table'] == 'pereodics_catalog') return array();

/*        $sql = ''.
            'select t.year, max(t.avail_for_order) avail_for_order '.
            'from `' . $entities[$entity]['site_table'] . '` t '.
            'where (t.year is not null) and (t.year > 0) '.
            'group by t.year '.
            'having (avail_for_order > 0) '.
            'order by t.year '.
        '';*/
        if (!Entity::checkEntityParam($entity, 'years')) return array();
        $sql = 'select `year` from items_years where (eid = ' . (int) $entity . ') order by `year`';
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }

    function getAllReleases($entity) {
        if (!Entity::checkEntityParam($entity, 'yearreleases')) return array();

        $sql = ''.
            'select t.release_year, max(t.avail_for_order) avail_for_order '.
            'from `' . Entity::GetEntitiesList()[$entity]['site_table'] . '` t '.
            'where (t.release_year is not null) and (t.release_year > 0) '.
            'group by t.release_year '.
            'having (avail_for_order > 0) '.
            'order by t.release_year '.
            '';
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }

}
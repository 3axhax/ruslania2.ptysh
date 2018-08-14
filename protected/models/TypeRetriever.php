<?php

class TypeRetriever
{
    public function GetTotalItems($entity, $type, $avail)
    {
        //if($entity == Entity::PERIODIC) return 0;
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $table = $data['site_table'];

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE `type`=:year';
        if(!empty($avail))
            $sql .= ' AND avail_for_order=1';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':year' => intVal($type)));
		
		//var_dump($cnt);
		
        return $cnt;
    }

    public function GetItems($entity, $type, $paginator, $sort, $lang, $avail)
    {
        //if($entity == Entity::PERIODIC) return array();
		
		
		
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();

        if(!empty($type))
        {
            $criteria->addCondition('t.`type`=:year');
            $criteria->params[':year'] = intVal($type);
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
	
	public function GetTotalItems2($entity, $type, $avail)
    {
        if($entity == Entity::PERIODIC) return 0;
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $table = $data['site_table'];

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE `release_year`=:year';
        if(!empty($avail))
            $sql .= ' AND avail_for_order=1';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':year' => $type));
        return $cnt;
    }

    public function GetItems2($entity, $type, $paginator, $sort, $lang, $avail)
    {
        if($entity == Entity::PERIODIC) return array();

        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();

        if(!empty($type))
        {
            $criteria->addCondition('t.release_year=:year');
            $criteria->params[':year'] = intVal($type);
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
        $lang = Yii::app()->language;
        $allowLangs = array('ru', 'rut', 'en', 'fi');
        if (!in_array($lang, $allowLangs)) $lang = 'en';

        $sql = ''.
            'select t.id, t.title_' . $lang . ' title '.
            'from `pereodics_types` t '.
                'join ('.
                    'select type id '.
                    'from ' . $entities[$entity]['site_table'] . ' '.
                    'where (type is not null) and (type > 0) '.
                    'group by type'.
                ') tI using (id) '.
            'order by title '.
        '';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    public function GetType ($entity, $tid) {
        $entitys = Entity::GetEntitiesList();
        $type_table = $entitys[$entity]['type_table'];
        $sql = 'SELECT * FROM '. $type_table .' WHERE id=:id LIMIT 1';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $tid));
        return $row;
    }
}
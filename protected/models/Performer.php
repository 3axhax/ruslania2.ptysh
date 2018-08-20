<?php

class Performer
{
    private $_perToPage = 150;
    public function GetPerformerList($entity, $lang, $char=null) {
        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $items = SearchPerformers::get()->getAll($entity, ($page-1)*$this->_perToPage . ', ' . $this->_perToPage, $counts);
        return array($items, $counts);

        $havePersormers = array(Entity::AUDIO, Entity::MUSIC);
        if(!in_array($entity, $havePersormers)) return array();

        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if (!array_key_exists('performer_table', $data)) return array();

        $key = 'PerformersList'.$entity.$lang;
        $rows = Yii::app()->dbCache->get($key);
        if($rows === false)
        {
            $sql = 'SELECT * FROM all_authorslist AS al '
                  .'WHERE id IN (SELECT person_id FROM '.$data['performer_table'].') '
                 .' ORDER BY title_'.$lang;
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            Yii::app()->dbCache->set($key, $rows, Yii::app()->params['DbCache']);
        }
        return $rows;
    }

    public function getPerformersBySearch($entity) {
        $page = max((int)Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $items = SearchPerformers::get()->getLike(
            $entity,
            (string)Yii::app()->getRequest()->getParam('qa'),
            array(),
            ($page - 1) * $this->_perToPage . ', ' . $this->_perToPage,
            false,
            $counts
        );
        return array($items, $counts);
    }


    public function GetById($entity, $pid) {
        if (!Entity::checkEntityParam($entity, 'performers')) return array();
//        $havePersormers = array(Entity::AUDIO, Entity::MUSIC);
//        if(!in_array($entity, $havePersormers)) return false;
        $entityParam = Entity::GetEntitiesList()[$entity];

        $sql = 'SELECT * FROM all_authorslist WHERE id=:pid limit 1';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':pid' => $pid));
        return $row;
    }

    public function GetTotalItems($entity, $pid, $avail)
    {
        if (!Entity::checkEntityParam($entity, 'performers')) return array();
//        $havePersormers = array(Entity::AUDIO, Entity::MUSIC);
//        if(!in_array($entity, $havePersormers)) return 0;

        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];

        if (!array_key_exists('performer_table', $data)) return 0;

        if($avail)
        {
            $sql = ''.
                'SELECT COUNT(*) '.
                'FROM ' . $data['performer_table'] . ' AS a '.
                'JOIN '.$data['site_table'].' AS b ON (a.'.$data['performer_field'].'=b.id) '.
                'WHERE a.person_id=:id AND b.avail_for_order=1'.
            '';

        }
        else
        {
            $sql = 'SELECT COUNT(*) FROM ' . $data['performer_table'] . ' WHERE person_id=:id ';
        }

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $pid));
        return $cnt;
    }

    public function GetItems($entity, $pid, $paginator, $sort, $lang, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->join = 'JOIN ' . $data['performer_table'] . ' AS j ON j.' . $data['performer_field'] . '=t.id ';
        $criteria->addCondition('j.person_id=:pid');
        if(!empty($avail))
            $criteria->addCondition('t.avail_for_order=1');

        $criteria->params[':pid'] = $pid;
        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        return Product::FlatResult($data);
    }
}
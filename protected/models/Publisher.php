<?php

class Publisher extends CMyActiveRecord {
    private $_perToPage = 150;
    function getPerToPage() {return $this->_perToPage; }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'all_publishers';
    }

    public function GetABC($lang, $entity) {
        if (!Entity::checkEntityParam($entity, 'publisher')) return array();

        $entityParam = Entity::GetEntitiesList()[$entity];
        $tableItems = $entityParam['site_table'];

        $sql = ''.
            'select`first_'.$lang.'` '.
            'from`all_publishers` '.
            'where (first_'.$lang.' regexp "[[:alpha:]]") '.
                'and (`first_'.$lang.'` != "") '.
                'and (is_' . $entity . ' > 0) '.
            'group by `first_'.$lang.'` '.
            'order by `first_'.$lang.'` '.
            '';
        $abc = array();
        foreach (Yii::app()->db->createCommand($sql)->queryColumn() as $alpha) {
            if (preg_match("/\w/ui", $alpha)) {
                $abc[] = array('first_'.$lang => $alpha);
            }
        }
        return $abc;
    }

    public function GetPublishersByFirstChar($char, $lang, $entity) {
        $counts = 0;
        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $authors = SearchPublishers::get()->getBegin(
            $entity,
            $char,
            array(),
            ($page-1)*150 . ', 150',
            $counts
        );
        return array($authors, $counts);
        $sql = 'SELECT * FROM all_publishers AS al '
//              .'JOIN all_publishers_entity AS e ON al.id=e.publisher '
              .'WHERE first_'.$lang.'=:char AND e.entity=:entity '
              .'ORDER BY title_'.$lang;
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':char' => $char, ':entity' => $entity));
        return $rows;
    }

    public function GetTotalItems($entity, $pid, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $table = $data['site_table'];

        $sql = 'SELECT COUNT(*) FROM '.$table.' WHERE publisher_id=:id';
        if($avail) $sql .= ' AND avail_for_order=1';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $pid));
        return $cnt;
    }

    public function GetItems($entity, $pid, $paginator, $sort, $lang, $avail)
    {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        if(!empty($pid))
        {
            $criteria->addCondition('publisher_id=:pid');
            $criteria->params[':pid'] = $pid;
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

    public function GetByID($entity, $pid)
    {
        $sql = 'SELECT * FROM all_publishers WHERE id=:pid';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':pid' => $pid));
        return $row;
    }

    public function GetByIDs($ids)
    {
        if(empty($ids) || !is_array($ids)) return array();
        $sql = 'SELECT * FROM all_publishers WHERE id IN ('.implode(',', $ids).')';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach($rows as $row) $ret[$row['id']] = $row;

        return $ret;
    }

    public function GetCountByIds($ids, $entity=null)
    {
        if(empty($ids) || !is_array($ids)) return array();

        $sql = 'SELECT * FROM all_publishers_entity WHERE publisher IN ('.implode(',', $ids).') ';
        $params = array();
        if(!empty($entity))
        {
            $sql .= ' AND entity=:entity';
            $params[':entity'] = $entity;
        }
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
        return $rows;
    }

    public function GetAll($entity, $lang)
    {
        $sql = 'SELECT * FROM all_publishers AS al '
            .'JOIN all_publishers_entity AS e ON al.id=e.publisher '
            .'WHERE e.entity=:entity AND qty > 0 '
            .'ORDER BY title_'.$lang;
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':entity' => $entity));
        return $rows;
    }

    public function GetPublishersBySearch($char, $lang, $entity) {
        $page = max((int)Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $authors = SearchPublishers::get()->getLike(
            $entity,
            (string)Yii::app()->getRequest()->getParam('qa'),
            array(),
            ($page - 1) * $this->_perToPage . ', ' . $this->_perToPage,
            false,
            $counts
        );
        return array('rows' => $authors, 'count' => $counts);
    }

    public function getPublisherList($entity, $lang, $char=null)
    {
        $page = max((int)Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $items = SearchPublishers::get()->getAll($entity, ($page - 1) * $this->_perToPage . ', ' . $this->_perToPage, $counts);
        return array($items, $counts);
    }
}
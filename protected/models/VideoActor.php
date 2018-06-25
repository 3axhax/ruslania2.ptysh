<?php

class VideoActor
{
    private $_perToPage = 150;
    function getPerToPage() {return $this->_perToPage; }
    public function GetByIds($ids) {
        //$sql = 'SELECT * FROM video_actorslist WHERE id IN ('.implode(',', $ids).')';
        $sql = ''.
            //'SELECT real_id id, title_' . SearchActors::get()->getSiteLang() . ', description_file_' . SearchActors::get()->getSiteLang() . ' '.
            'SELECT * '.
            'FROM all_authorslist '.
            'WHERE real_id IN ('.implode(',', $ids).') '.
//                'and (entity = 40) '.
            'group by id '.
//            'order by if (entity = ' . Entity::VIDEO . ', 0, 1) ' .
        '';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

    public function GetById($id) {
        $rows = $this->GetByIds(array($id));
        if (empty($rows)) return array();

        return array_shift($rows);
    }

    public function GetTotalItems($entity, $aid, $avail)
    {
        if($avail)
        {
            $sql = 'SELECT COUNT(*) FROM video_actors AS a '
                  .'JOIN video_catalog AS b ON a.video_id=b.id '
                  .'WHERE person_id=:id AND b.avail_for_order=1';
        }
        else
        {
            $sql = 'SELECT COUNT(*) FROM video_actors WHERE person_id=:id';
        }
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $aid));
        return $cnt;
    }

    public function GetItems($entity, $aid, $paginator, $sort, $lang, $avail)
    {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->join = 'JOIN video_actors AS j ON j.video_id=t.id ';
        $criteria->addCondition('j.person_id=:aid');
        if($avail)
            $criteria->addCondition('avail_for_order=1');
        $criteria->params[':aid'] = $aid;
        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        return Product::FlatResult($data);
    }

    public function getActorsBySearch($entity) {
        $page = max((int)Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $items = SearchActors::get()->getLike(
            $entity,
            (string)Yii::app()->getRequest()->getParam('qa'),
            array(),
            ($page - 1) * $this->_perToPage . ', ' . $this->_perToPage,
            false,
            $counts
        );
        return array($items, $counts);
    }

    public function GetActorList($entity, $lang) {
        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $items = SearchActors::get()->getAll($entity, ($page-1)*$this->_perToPage . ', ' . $this->_perToPage, $counts);
        return array($items, $counts);


        if($entity != Entity::VIDEO) return array();
        $sql = 'SELECT * FROM video_actorslist ORDER BY title_'.$lang;
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }
}
<?php

class VideoStudio extends CMyActiveRecord
{
    private $_perToPage = 150;

    function getPerToPage() {return $this->_perToPage; }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'video_studios';
    }

    public function GetTotalItems($entity, $sid, $avail) {
        $entities = Entity::GetEntitiesList();
        if (empty($entities[$entity])) return array();

        $condition = array('id'=>'(t.studio = ' . (int) $sid . ')');
        if ($avail) $condition['avail_for_order'] = '(t.avail_for_order = 1)';

        $sql = ''.
            'select count(*) '.
            'from ' . $entities[$entity]['site_table'] . ' t '.
            'where ' . implode(' and ', $condition) . ' '.
            '';
        return (int) Yii::app()->db->createCommand($sql)->queryScalar(array(':sid' => $sid));
    }

    public function GetItems($entity, $sid, $paginator, $sort, $lang, $avail) {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->addCondition('t.studio=:sid');
        if($avail) $criteria->addCondition('t.avail_for_order=1');
        $criteria->params[':sid'] = $sid;
        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        return Product::FlatResult($data);
    }

    function getAll($entity, $avail) {
        $entities = Entity::GetEntitiesList();
        if (empty($entities[$entity])) return array();

        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);

        $lang = Yii::app()->language;
        $allowLangs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se');
        if (!in_array($lang, $allowLangs)) $lang = 'en';

        $sql = ''.
            'select sql_calc_found_rows t.id, t.title_' . $lang . ' title '.
            'from ' . $this->tableName() . ' t '.
            'join (select studio id '.
            'from ' . $entities[$entity]['site_table'] . ' '.
            (empty($avail)?'':'where (avail_for_order = 1) ').
            'group by studio '.
            'order by studio '.
            ') tId using (id) '.
            'order by title '.
            'limit ' . ($page-1)*$this->_perToPage . ', ' . $this->_perToPage . ' '.
            '';
        $studios = Yii::app()->db->createCommand($sql)->queryAll();
        $sql = 'select found_rows();';
        $counts = Yii::app()->db->createCommand($sql)->queryScalar();
        return array($studios, $counts);
    }
}
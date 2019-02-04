<?php

class Binding {
    static private $_bindings = array();

    public function GetBinding($entity, $bid) {
        $bindings = self::getBindings($entity);
        if (isset($bindings[$bid])) return $bindings[$bid];
        return array();

        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('binding_table', $data)) return false;
        $table = $data['binding_table'];

        $sql = 'SELECT * FROM '.$table.' WHERE id=:id';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $bid));
        return $row;
    }

    public function GetTotalItems($entity, $bid, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('binding_table', $data)) return 0;

        $sql = 'SELECT COUNT(*) FROM '.$data['site_table'].' WHERE binding_id=:bid';
        if($avail)
            $sql .= ' AND avail_for_order=1';

        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':bid' => $bid));
        return $cnt;
    }

    public function GetItems($entity, $bid, $paginator, $sort, $lang, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('binding_table', $data)) return array();

        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();

        if(!empty($bid))
        {
            $criteria->addCondition('t.binding_id=:bid');
            $criteria->params[':bid'] = $bid;
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

        if (empty($entities[$entity]['binding_table'])) return array();
        $lang = Yii::app()->language;
        $allowLangs = array('ru', 'rut', 'en', 'fi');
        if (!in_array($lang, $allowLangs)) $lang = 'en';

        $sql = ''.
            'select t.id, t.title_' . $lang . ' title '.
            'from `' . $entities[$entity]['binding_table'] . '` t '.
                'join ('.
                    'select binding_id id '.
                    'from ' . $entities[$entity]['site_table'] . ' '.
                    'where (binding_id is not null) and (binding_id > 0) '.
                    'group by binding_id'.
                ') tI using (id) '.
            'order by title '.
        '';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    static function getBindings($entity) {
        if (!isset(self::$_bindings[$entity])) {
            self::$_bindings = array();
            $entities = Entity::GetEntitiesList();
            if (!empty($entities[$entity])) {
                if (!empty($entities[$entity]['binding_table'])) {
                    $sql = ''.
                        'select * '.
                        'from ' . $entities[$entity]['binding_table'] . ' ' .
                    '';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll();
                    foreach ($rows as $row) self::$_bindings[$entity][(int)$row['id']] = $row;
                }
            }
        }
        return self::$_bindings[$entity];
    }


}
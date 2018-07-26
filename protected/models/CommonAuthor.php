<?php

class CommonAuthor extends CMyActiveRecord {
    private $_perToPage = 150;
    function getPerToPage() {return $this->_perToPage; }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'all_authorslist';
    }

    public function GetABC($lang, $entity)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('author_table', $data)) return array();

        $sql = ''.
            'select`first_'.$lang.'` '.
            'from`all_authorslist` '.
            'where (first_'.$lang.' regexp "[[:alpha:]]") '.
                'and (`first_'.$lang.'` != "") '.
                'and (is_' . $entity . '_author > 0) '.
            'group by ord(`first_'.$lang.'`) '.
            'order by ord(`first_'.$lang.'`) '.
        '';
        $abc = array();
        foreach (Yii::app()->db->createCommand($sql)->queryColumn() as $alpha) {
            $abc[] = array('first_' . $lang => $alpha);
        }
        return $abc;
    }

    public function GetAuthorsByFirstChar($char, $lang, $entity)
    {
        $counts = 0;
        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $authors = SearchAuthors::get()->getBegin(
            $entity,
            $char,
            array(),
            ($page-1)*150 . ', 150',
            $counts
        );
        return array($authors, $counts);

        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('author_table', $data)) return array();
		
		$page = (int) $_GET['page'];
		
		if ($page > 0) { $page = $page-1; }
		
		$limit = ' LIMIT ' . ($page*50) . ', 150 ';
		
		
        $sql = ''.
            'SELECT title_'.$lang.', al.id '.
            'FROM all_authorslist AS al '.
                'JOIN '.$data['author_table'].' AS j ON al.id=j.author_id '.
            'WHERE ord(first_'.$lang.')=ord(:char) '.
            'group by al.id '.
            'ORDER BY title_'.$lang.' ASC '.
            $limit;
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':char' => $char));
        return $rows;
    }
	
	public function GetAuthorsBySearch($char, $lang, $entity) {
        $page = max((int) Yii::app()->getRequest()->getParam('page'), 1);
        $page = min($page, 100000);
        $counts = 0;
        $authors = SearchAuthors::get()->getLike(
            $entity,
            (string)Yii::app()->getRequest()->getParam('qa'),
            array(),
            ($page-1)*$this->_perToPage . ', ' . $this->_perToPage,
            false,
            $counts
        );
        return array('rows'=>$authors, 'count'=>$counts);

		$entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('author_table', $data)) return array();
		
		$page = (int) $_GET['page'];
		
		if ($page > 0) { $page = $page-1; }
		
		$limit = ' LIMIT ' . ($page*50) . ', 150 ';
		
		$s = addslashes($_GET['qa']);
		
		 $sql = 'SELECT DISTINCT(title_'.$lang.'), al.id as aid FROM all_authorslist AS al '
            .'JOIN '.$data['author_table'].' AS j ON al.id=j.author_id '
            .'WHERE `title_'.$lang.'` LIKE "%' . $s . '%"
            ORDER BY id DESC';
        $rows2 = Yii::app()->db->createCommand($sql)->queryAll();
		
        $sql = 'SELECT DISTINCT(title_'.$lang.'), al.id FROM all_authorslist AS al '
            .'JOIN '.$data['author_table'].' AS j ON al.id=j.author_id '
            .'WHERE `title_'.$lang.'` LIKE "%' . $s . '%"
            ORDER BY id DESC'.$limit;
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
		
        return array('rows'=>$rows, 'count'=>count($rows2));
		
	}
	
	public function GetAuthorsByFirstCharCount($char, $lang, $entity)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('author_table', $data)) return array();
		
		//$page = (int) $_GET['page'];
		
		//if ($page > 0) { $page = $page-1; }
		
		//$limit = ' LIMIT ' . ($page*50) . ', 50 ';
		
		$sql = 'SELECT DISTINCT(title_'.$lang.'), al.id FROM all_authorslist AS al '
            .'JOIN '.$data['author_table'].' AS j ON al.id=j.author_id '
            .'WHERE first_'.$lang.'=:char '
            .'ORDER BY title_'.$lang;
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':char' => $char));
        return $rows;
    }

    public function GetTotalItems($entity, $aid, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        if(!array_key_exists('author_table', $data)) return 0;

        if($avail)
        {
            $sql = 'SELECT COUNT(*) FROM '.$data['author_table'].' AS a '
                  .'JOIN '.$data['site_table'].' AS b ON a.'.$data['author_entity_field'].'=b.id '
                  .' WHERE a.author_id=:id AND b.avail_for_order=1';
        }
        else
        {
            $sql = 'SELECT COUNT(*) FROM '.$data['author_table'].' WHERE author_id=:id';
        }
        $cnt = Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $aid));
        return $cnt;
    }

    public function GetItems($entity, $aid, $paginator, $sort, $lang, $avail)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        if(!empty($aid))
        {
            $criteria->join = 'JOIN '.$data['author_table'].' AS j ON j.'.$data['author_entity_field'].'=t.id ';
            $criteria->addCondition('j.author_id=:aid');
            $criteria->params[':aid'] = $aid;
        }
		
		if (!isset($_GET['lang'])) {
			if (Yii::app()->getRequest()->cookies['langsel']->value) {
				$_GET['lang'] = Yii::app()->getRequest()->cookies['langsel']->value;
			}
		}
		
		if ($_GET['lang']) {
			
			$criteria->addCondition('t.id IN (SELECT item_id FROM `all_items_languages` WHERE entity = '.$entity.' AND language_id = '.$_GET['lang'].')');
			
		}
		
        if(!empty($avail))
        {
            $criteria->addCondition('t.avail_for_order=1');
        }

        $criteria->order = SortOptions::GetSQL($sort, $lang);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;


        /*echo '<pre>';
        print_r($criteria); die();*/

        $data = $dp->getData();

        return Product::FlatResult($data);
    }

    public function GetById($aid)
    {
        $sql = 'SELECT * FROM all_authorslist WHERE id=:id';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $aid));
        return $row;
    }

    public function GetByIds($ids)
    {
        $ret = array();
        if(!is_array($ids)) return array();
        foreach($ids as $id) $ret[] = intVal($id);

        $sql = 'SELECT * FROM all_authorslist WHERE id IN ('.implode(',', $ret).')';
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach($rows as $row) $ret[$row['id']] = $row;
        return $ret;
    }

    public function GetItemsByAuthorGroupedByEntity($ids)
    {
        if(!is_array($ids) || count($ids) == 0) return array();

        $entities = Entity::GetEntitiesList();
        $ret = array();
        foreach($entities as $entity=>$data)
        {
            if(!array_key_exists('author_table', $data)) continue;

            $sql = 'SELECT COUNT(*) AS cnt, author_id FROM '.$data['author_table']
                .' WHERE author_id IN ('.implode(',', $ids).') GROUP BY author_id ';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach($rows as $row)
            {
                $ret[$entity][$row['author_id']] = $row['cnt'];
            }
        }
        return $ret;
    }
}
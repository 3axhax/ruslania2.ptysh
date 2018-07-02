<?php

class Category {

    public function GetCategoryList($entity, $parent, $availCategory = false) {
        $entities = Entity::GetEntitiesList();
        $parent = intVal($parent);
        $eTable = $entities[$entity]['entity'];
        if ($availCategory !== false)
        {
            $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE id IN ('.implode(',' ,$availCategory).') ORDER BY title_'.Yii::app()->language . ' ASC';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true);
            //print_r(implode(',' ,$availCategory)); die();
            return $rows;
        }
        $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE parent_id=:parent  AND items_count > 0 ORDER BY title_'.Yii::app()->language . ' ASC';
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':parent' => $parent));
        return $rows;
    }

    public function exists_subcategoryes($entity, $cid) {

        $entities = Entity::GetEntitiesList();
        $cid = intVal($cid);
        $eTable = $entities[$entity]['entity'];

        $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE parent_id=:parent ORDER BY title_'.Yii::app()->language;
        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':parent' => $cid));

        return $rows;
    }

    function getFilterSlider($entity, $cid) {
		
		if ($entity != 30) {
		
        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        if ($cid > 0) {
            $sql = 'SELECT MAX(year) as max_year, MIN(year) as min_year, MAX(brutto) as cost_max, MIN(brutto) as cost_min FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) AND avail_for_order=1';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT MAX(year) as max_year, MIN(year) as min_year, MAX(brutto) as cost_max, MIN(brutto) as cost_min FROM ' . $tbl . ' WHERE avail_for_order=1';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }

        return array($rows[0]['min_year'], $rows[0]['max_year'], $rows[0]['cost_min'], $rows[0]['cost_max']);
		}
    }
	
    function getYearExists($entity, $cid) {

		if ((int)$entity === 30) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        if ($cid > 0) {
            $sql = 'SELECT DISTINCT year FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) AND avail_for_order=1';
            $rows = Yii::app()->db->createCommand($sql)->queryColumn(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT DISTINCT year FROM ' . $tbl . ' WHERE avail_for_order=1';
            $rows = Yii::app()->db->createCommand($sql)->queryColumn();
        }

        return $rows;

    }

	public function getFilterLangs($entity, $cid) {
		
		$entities = Entity::GetEntitiesList();
		$tbl = $entities[$entity]['site_table'];
					
		$sql = 'SELECT ln.id as lnid, ln.title_'.Yii::app()->language.' AS lntitle FROM `all_items_languages` AS ail, `languages` AS ln, `'.$tbl.'` AS t WHERE ln.id = ail.language_id AND ail.entity = '.$entity.' AND ail.item_id = t.id';
					
		if ($cid) {
					
			$sql .= ' AND (t.code = '.$cid.' OR t.subcode = '.$cid.')';
					
		}
					
		$sql .= ' GROUP BY ln.id ORDER BY ln.id ASC';
					
		$rows = Yii::app()->db->createCommand($sql)->queryAll();	
		
		
		
		return $rows;

	}
	
	public function getFilterLangsVideo($entity, $cid)
    {

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];

        $sql = 'SELECT vasl.* FROM `video_audiostreams` AS vas, `video_audiostreamlist` AS vasl, `' . $tbl . '` AS t WHERE vas.stream_id = vasl.id AND vas.video_id = t.id';

        if ($cid) {

            $sql .= ' AND (t.code = ' . $cid . ' OR t.subcode = ' . $cid . ')';

        }

        $lang = 'ru';
        if (isset(Yii::app()->language)) $lang=Yii::app()->language;
        $sql .= ' GROUP BY vasl.title_'.$lang.' ORDER BY vasl.title_'.$lang.' ASC';

        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        return $rows;
    }

    public function getSubtitlesVideo($entity, $cid)
    {

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];

        $sql = 'SELECT vcl.* FROM `video_credits` AS vc, `video_creditslist` AS vcl, `' . $tbl . '` AS t WHERE vc.credits_id = vcl.id AND vc.video_id = t.id';

        if ($cid) {

            $sql .= ' AND (t.code = ' . $cid . ' OR t.subcode = ' . $cid . ')';

        }

        $lang = 'ru';
        if (isset(Yii::app()->language)) $lang=Yii::app()->language;
        $sql .= ' GROUP BY vcl.title_'.$lang.' ORDER BY vcl.title_'.$lang.' ASC';

        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        return $rows;
    }

    public function getFilterFormatVideo($entity, $cid) {

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];

        $sql = 'SELECT vm.* FROM `video_media` AS vm, `'.$tbl.'` AS t WHERE t.media_id = vm.id';

        if ($cid) {

            $sql .= ' AND (t.code = '.$cid.' OR t.subcode = '.$cid.')';

        }

        $sql .= ' GROUP BY vm.id ORDER BY vm.title ASC';

        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        return $rows;
    }

    public function getFilterBinding($entity, $cid) {
        if ($entity != 15 AND $entity != 10) {

            if ($entity == 22 OR $entity == 24) {

                $entities = Entity::GetEntitiesList();
                $tbl = $entities[$entity]['site_table'];
                //$tbl_binding = $entities[$entity]['binding_table'];
                if ($cid > 0) {
                    $sql = 'SELECT media_id FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) AND avail_for_order=1 GROUP BY media_id';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
                } else {
                    $sql = 'SELECT media_id FROM ' . $tbl . ' WHERE avail_for_order=1 GROUP BY media_id';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll();
                }

                return $rows;
            } else {

                return array();
            }
        }
        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_binding = $entities[$entity]['binding_table'];
        if ($cid > 0) {
            $sql = 'SELECT binding_id FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) AND avail_for_order=1 AND `binding_id` IN ( SELECT id FROM `'.$tbl_binding.'` WHERE title_ru LIKE "%обложка%" OR title_ru LIKE "%переплет%" ) GROUP BY binding_id';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT binding_id FROM ' . $tbl . ' WHERE avail_for_order=1     AND binding_id IN ( SELECT id FROM `'.$tbl_binding.'` WHERE title_ru LIKE "%обложка%" OR title_ru LIKE "%переплет%" ) GROUP BY binding_id';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }

        return $rows;
    }

    public function getFilterPublisher($entity, $cid, $page = 1, $lang = '', $site_lang='') {
        if ($entity != 30 AND $entity != 40) {
            if ($page != 0) $limit = (($page - 1) * 50) . ',50';
            $entities = Entity::GetEntitiesList();
            $tbl = $entities[$entity]['site_table'];
            //$tbl_binding = $entities[$entity]['binding_table'];

            $sql = '';

            if ($lang != '') {

                $sql = ' AND tc.id IN (SELECT item_id FROM `all_items_languages` WHERE language_id = ' . $lang . ' AND entity = ' . $entity . ')';

            }

            if ($site_lang == '') $site_lang = 'ru';

            if ($cid > 0) {
                $sql = 'SELECT tc.publisher_id, ap.title_'.$site_lang.' as title FROM ' . $tbl . ' as tc, all_publishers as ap '.
                'WHERE (tc.`code`=:code OR tc.`subcode`=:code) AND tc.avail_for_order=1' . $sql .' '.
                'AND ap.id = tc.publisher_id '.
                'GROUP BY tc.publisher_id ORDER BY ap.title_'.$site_lang. (($page != 0) ? (' LIMIT ' . $limit) : '');
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
            } else {
                $sql = 'SELECT tc.publisher_id, ap.title_'.$site_lang.' as title FROM ' . $tbl . ' as tc, all_publishers as ap '.
                'WHERE tc.avail_for_order=1' . $sql. ' '.
                'AND ap.id = tc.publisher_id '.
                'GROUP BY tc.publisher_id ORDER BY ap.title_'.$site_lang. (($page != 0) ? (' LIMIT ' . $limit) : '');
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
            }

            $izd = [];
            foreach ($rows as $row) {
                $izd[(int)$row['publisher_id']] = $row['title'];
            }
            return $izd;
            return $rows;

        }
    }

    public function getFilterSeries($entity, $cid, $page = 1, $lang='', $site_lang = '') {
        if ($entity == 60 OR $entity == 50 OR $entity == 30 OR $entity == 40 OR $entity == 20)
            return array();
		
		$sql = '';
		if ($lang != '') {
			
			$sql = ' AND tc.id IN (SELECT item_id FROM `all_items_languages` WHERE language_id = '.$lang.' AND entity = '.$entity.')';
			
		}

        if ($page != 0) $limit = (($page - 1) * 50) . ',50';
        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $series_tbl = $entities[$entity]['site_series_table'];
        //$tbl_binding = $entities[$entity]['binding_table'];
        if ($site_lang == '') $site_lang = 'ru';
        if ($cid > 0) {
            $sql = 'SELECT tc.series_id, st.title_'.$site_lang.' as title FROM ' . $tbl . ' as tc, '.$series_tbl.' as st 
            WHERE (tc.`code`=:code OR tc.`subcode`=:code) 
            AND tc.avail_for_order=1 AND (tc.series_id > 0 AND tc.series_id <> "") AND tc.series_id=st.id' .$sql.' 
            GROUP BY st.title_'.Yii::app()->language. (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT tc.series_id, st.title_'.$site_lang.' as title FROM ' . $tbl . ' as tc, '.$series_tbl.' as st 
            WHERE tc.avail_for_order=1  AND (tc.series_id > 0 AND tc.series_id <> "") AND tc.series_id=st.id' .$sql.' 
            GROUP BY st.title_'.Yii::app()->language. (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }

        $series = [];
        foreach ($rows as $row) {
            $series[(int)$row['series_id']] = $row['title'];
        }
        return $series;
        return $rows;
    }

    public function getFilterAuthor($entity, $cid, $page = 1,$lang='', $site_lang='') {
        if (!Entity::checkEntityParam($entity, 'authors')) return array();

//        if ($entity == 60 OR $entity == 30 OR $entity == 40)
//            return array();
        if ($page != 0) $limit = (($page - 1) * 50) . ',50';
		$sql = '';
		if ($lang!='') {
			
			$sql = ' AND bc.id IN (SELECT item_id FROM `all_items_languages` WHERE language_id = '.$lang.' AND entity = '.$entity.')';
			
		}
		
        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];
        if ($site_lang == '') $site_lang = 'ru';
        if (isset(Yii::app()->language)) $site_lang = Yii::app()->language;

        if ($cid > 0) {
            $sql = 'SELECT ba.author_id, aa.title_'.$site_lang.' as title FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba, all_authorslist as aa 
            WHERE (bc.`code`=:code OR bc.`subcode`=:code) AND bc.avail_for_order=1 AND ba.' . $field . '=bc.id'.$sql.'
            AND ba.author_id=aa.id 
            GROUP BY ba.author_id ORDER BY aa.title_'.$site_lang. (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT ba.author_id, aa.title_'.$site_lang.' as title FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba, all_authorslist as aa 
            WHERE avail_for_order=1  AND bc.avail_for_order=1 AND ba.' . $field . '=bc.id'.$sql.'
            AND ba.author_id=aa.id 
            GROUP BY ba.author_id ORDER BY aa.title_'.$site_lang. (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }
        $authors = [];
        foreach ($rows as $row) {
            $authors[(int)$row['author_id']] = $row['title'];
        }
        //print_r($authors);
        return $authors;
    }

    public function getFilterAuthorForeSearch($entity, $lang='') {
        $sql = '';
        if ($lang!='') {

            $sql = ' AND bc.id IN (SELECT item_id FROM `all_items_languages` WHERE language_id = '.$lang.' AND entity = '.$entity.')';

        }

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];

        if ($lang == '') $lang = 'ru';
        $sql = 'SELECT ba.author_id as id, aa.title_' . $lang . ' as title FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba, all_authorslist as aa 
            WHERE avail_for_order=1  AND bc.avail_for_order=1 AND ba.' . $field . '=bc.id' . $sql . '
            AND ba.author_id=aa.id 
            GROUP BY ba.author_id ORDER BY aa.title_' . $lang;
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $authors = [];
        foreach ($rows as $row) {
            $authors[(int)$row['id']] = $row['title'];
        }
        return $authors;
    }

	public function get_count_categories_bread($id, $entity) {
		$entities = Entity::GetEntitiesList();
		$tbl = $entities[$entity]['site_table'];
		
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE code<>"" AND subcode<>"" AND id='.$id;
        
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		
		return $rows;
	}
	
	public function getCatsBreadcrumbs2($entity, $code) {
		
		$entities = Entity::GetEntitiesList();
		$tbl = $entities[$entity]['site_category_table'];
		
		$sql = 'SELECT * FROM ' . $tbl . ' WHERE id = '.$code;
        
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		
		if ($rows[0]['parent_id'] != '0') {
			
			$rows = array_merge($rows, self::getCatsBreadcrumbs2($entity, $rows[0]['parent_id']));
			
		}
		
		return $rows;
		
	}
	
	public function getCatsBreadcrumbs($entity, $code) {
        $arr = array();
		if ($code) {
			
			$arr = self::getCatsBreadcrumbs2($entity, $code);
			
		}
		
		return array_reverse($arr);
	}
	
    public function result_filter($data = array(), $lang_sel='') {

        if (!$data OR count($data) == 0) {
            return array();
        }

        list($entity,$cid,$author,$avail,$ymin,$ymax,$izda,$seria,$cmin,$cmax,$lang_sel,$search,$sort,$binding,$formatVideo,$langVideo) = array_values($data);
        $entities = Entity::GetEntitiesList();
        $data['binding_id'] = $binding;
        $data['year_min'] = (int) $ymin;
        $data['year_max'] = (int) $ymax;
        $data['min_cost'] = $cmin;
        $data['max_cost'] = $cmax;

        $tbl_author = $entities[$entity]['author_table'];
        $totalItems = self::count_filter($entity, $cid, $data);
        $field = $entities[$entity]['author_entity_field'];
        $paginator = new CPagination($totalItems);
        $paginator->setPageSize(Yii::app()->params['ItemsPerPage']);
        $formatVideo = $data['formatVideo'];
        $langVideo = $data['langVideo'];
        $subtitlesVideo = $data['subtitlesVideo'];

        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $lang = Yii::app()->language;
        if (!empty($cid)) {
            $allChildren = array();
            $allChildren = $this->GetChildren($entity, $cid);
            if (count($allChildren) > 0) {
                array_push($allChildren, $cid);
                $ids = '(' . implode(',', $allChildren) . ')';
                $criteria->addCondition('(code IN ' . $ids . ' OR subcode IN ' . $ids . ')');
            } else {
                $criteria->addCondition('code=:cid1 OR subcode=:cid2');
                $criteria->params[':cid1'] = $cid;
                $criteria->params[':cid2'] = $cid;
            }
        }

		if ($lang_sel != '') {
			
			$criteria->addCondition('t.id IN (SELECT item_id FROM `all_items_languages` WHERE entity = '.$entity.' AND language_id = '.$lang_sel.')');
			
		}
		
        if ($author AND $author!='undefined' AND $tbl_author) {
            
            $criteria->join .= ' LEFT JOIN '.$tbl_author.' as ba ON ba.'.$field.' = t.id';
            
           $criteria->addCondition('ba.author_id=:aid');
           $criteria->params[':aid'] = $author; 
        }
        
        if ($izda AND $entity !=40) {
            $criteria->addCondition('publisher_id=:pid');
            $criteria->params[':pid'] = $izda;
        }
        
        if ($seria AND $entity !=40) {
            $criteria->addCondition('series_id=:sid');
            $criteria->params[':sid'] = $seria;
        }

        if ($ymin) {
            $criteria->addCondition('year >= :ymin');
            $criteria->params[':ymin'] = $ymin;
        }
        if ($ymax) {
            $criteria->addCondition('year <= :ymax');
            $criteria->params[':ymax'] = $ymax;
        }

        if ($formatVideo && $formatVideo != '' && $formatVideo != '0') {
            $criteria->addCondition('media_id = :formatVideo');
            $criteria->params[':formatVideo'] = $formatVideo;
        }

        if ($langVideo && $langVideo != '' && $langVideo != '0') {

            $criteria->join .= ' JOIN `video_audiostreams` as vas ON vas.video_id = t.id';
            $criteria->addCondition('vas.stream_id = :langVideo');
            $criteria->params[':langVideo'] = $langVideo;
        }

        if ($subtitlesVideo && $subtitlesVideo != '' && $subtitlesVideo != '0') {

            $criteria->join .= ' JOIN `video_credits` as vc ON vc.video_id = t.id';
            $criteria->addCondition('vc.credits_id = :subtitlesVideo');
            $criteria->params[':subtitlesVideo'] = $subtitlesVideo;
        }

        if ($binding && $binding != 0 && $binding[0] != 0) {
            
            $str = ' binding_id=' . implode(' OR binding_id=', $binding);
            
            $criteria->addCondition($str);
            
        }
        
        if (mb_strlen($search) > 2) {
            
            $criteria->addCondition('t.title_'.Yii::app()->language.' LIKE "%'.$search.'%" OR isbn LIKE "%'.$search.'%"');
            
        }
		
		if ($_GET['sort']) {
			$sort = $_GET['sort'];
		} else {
			if (!$sort) $sort = 12;
		}

        /*$criteria->addCondition('brutto >= :brutto1 AND brutto<=:brutto2');
        $criteria->params[':brutto1'] = $cmin;
        $criteria->params[':brutto2'] = $cmax;*/

       if ($avail == 1) $criteria->addCondition('t.avail_for_order=1');
        $criteria->order = SortOptions::GetSQL($sort, $lang, $entity);
        $paginator->applyLimit($criteria);
        $dp->setCriteria($criteria);
        $dp->pagination = false;
        $datas = $dp->getData();

        $ret = Product::FlatResult($datas);

        $filter_ret = [];

        foreach ($ret as $r)
        {
            if (isset($r['real_price']) && $r['real_price'] >= $cmin && $r['real_price'] <= $cmax)
                $filter_ret[] = $r;
        }

        return $filter_ret;
    }

    public function count_filter($entity = 15, $cid, $post) {

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];

        /* post данные */
        
        $aid = $post['author'];
        $avail = $post['avail'];
        $izda = $post['izda'];
        $seria = $post['seria'];

        $year_min = (isset($post['year_min']) && $post['year_min'] != '') ? $post['year_min'] : 1900;
        $year_max = (isset($post['year_max']) && $post['year_max'] != '') ? $post['year_max'] : 2050;

        $cost_min = (isset($post['min_cost']) && $post['min_cost'] != '') ? $post['min_cost'] : 0;
        $cost_min = (float)str_replace(',','.', $cost_min);
        $cost_max = (isset($post['max_cost']) && $post['max_cost'] != '') ? $post['max_cost'] : 10000;
        $cost_max = (float)str_replace(',','.', $cost_max);

        $binding_id = $post['binding_id'];
        $search = $post['name_search'];
        $langsel = (int) $post['langsel'];

        $formatVideo = $post ['formatVideo'];
        $langVideo = $post ['langVideo'];
        $subtitlesVideo = $post ['subtitlesVideo'];

		if (!$langsel) {
			if (Yii::app()->getRequest()->cookies['langsel']->value) {
				$langsel = Yii::app()->getRequest()->cookies['langsel']->value;
			}
		}

        $query = array();
        $qstr = '';
		
		if ($langsel) {
			
			$query[] = '(ail.item_id=bc.id AND ail.entity=' . $entity.' AND ail.language_id = '.$langsel.')';
			$addtbl = ', `all_items_languages` as ail';
		}
		
        if ($aid AND $tbl_author) {
            $query[] = 'ba.' . $field . '=bc.id AND ba.author_id=' . $aid;
            $addtbl .= ', ' . $tbl_author . ' as ba';
        }
        if ($avail != '0') {
            $query[] = 'bc.avail_for_order=1';
        }
        if ($izda AND $entity !=40) {
            $query[] = 'bc.publisher_id = ' . $izda;
        }
        if ($seria AND $entity !=40) {
            $query[] = 'bc.series_id = ' . $seria;
        }
        if (mb_strlen($search) > 2) {
            $query[] = '(bc.title_'.Yii::app()->language.' LIKE "%'.$search.'%" OR bc.isbn LIKE "%'.$search.'%")';
        }
        if ($year_min != '' AND $year_max != '') {
            $query[] = '(bc.year >= ' . $year_min . ' AND bc.year <= ' . $year_max . ')';
        }
        if ($cost_min != '' AND $cost_max != '') {
            $query[] = '(bc.brutto >= ' . $cost_min . ' AND bc.brutto <= ' . $cost_max . ')';
        }

        if ($entity == 40 && $formatVideo != '' && $formatVideo != '0') {
            $query[] = '(bc.media_id = ' . $formatVideo . ')';
        }

        if ($entity == 40 && $langVideo != '' && $langVideo != '0') {
            $query[] = '(vas.stream_id = ' . $langVideo . ' AND vas.video_id = bc.id)';
            $addtbl .= ', `video_audiostreams` as vas';
        }

        if ($entity == 40 && $subtitlesVideo != '' && $subtitlesVideo != '0') {
            $query[] = '(vc.credits_id = ' . $subtitlesVideo . ' AND vc.video_id = bc.id)';
            $addtbl .= ', `video_credits` as vc';
        }

        if (count($binding_id) > 0 AND $binding_id[0] != false) {

            $query[] = '( bc.binding_id=' . implode(' OR bc.binding_id=', $binding_id) . ' )';
        }

        if (count($query) > 0) {
            $qstr = ' AND ' . implode(' AND ', $query);
        }

        if ($cid > 0) {
            $sql = 'SELECT COUNT(bc.title_ru) as cnt FROM ' . $tbl . ' as bc ' . $addtbl . ' WHERE (bc.`code`=:code OR bc.`subcode`=:code) ' . $qstr;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT COUNT(bc.title_ru) as cnt FROM ' . $tbl . ' as bc ' . $addtbl . ' WHERE bc.id <> 0 ' . $qstr;
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }
        return $rows[0]['cnt'];
    }

    public function GetCategoryPath($entity, $cid) {
        $entities = Entity::GetEntitiesList();
        $eTable = $entities[$entity]['entity'];
        $ret = array();

        while (true) {
            $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE id=:id';
            $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $cid));
            if (empty($row))
                break;
            $ret[] = $row;
            $cid = $row['parent_id'];
            if (empty($cid))
                break;
        }

        return array_reverse($ret);
    }

    public function filter_get_books_authors($entity, $cid, $aid) {

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        if ($cid > 0) {
            $sql = 'SELECT COUNT(ba.author_id) as cnt FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba WHERE (bc.`code`=:code OR bc.`subcode`=:code) AND bc.avail_for_order=1 AND ba.author_id=' . $aid . ' AND ba.book_id=bc.id';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT COUNT(ba.author_id) as cnt FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba WHERE bc.avail_for_order=1 AND ba.book_id=bc.id AND ba.author_id=' . $aid;
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            //echo '11';
        }

        return $rows[0]['cnt'];
    }

    public function GetTotalItems($entity, $category_id, $avail) {
        $entities = Entity::GetEntitiesList();
        $eTable = $entities[$entity]['site_category_table'];
        $eTable2 = $entities[$entity]['site_table'];

        // Только те, которые доступны для заказа
        $field = $avail ? 'avail_items_count' : 'items_count';

        $key = 'CategoryTotalItems_' . $entity . '_' . $category_id . '_' . $field;
        $cnt = Yii::app()->dbCache->get($key);
	
		if (!isset($_GET['lang'])) {
			if (Yii::app()->getRequest()->cookies['langsel']->value) {
				$_GET['lang'] = Yii::app()->getRequest()->cookies['langsel']->value;
			}
		}
	
        if ($cnt === false) {
            if ($category_id == 0) {
				
				if ($_GET['lang']) {
					
					$sql = 'SELECT COUNT(id) as cnt FROM `'.$eTable2.'` as t WHERE t.id IN (SELECT item_id FROM `all_items_languages` WHERE entity = '.$entity.' AND language_id = '.$_GET['lang'].')';
					
				} else {
					$sql = 'SELECT SUM(' . $field . ') AS cnt FROM ' . $eTable . ' WHERE parent_id=0';
				}
				
			} else {
				
				if ($_GET['lang']) {
					
					$sql = 'SELECT COUNT(id) as cnt FROM `'.$eTable2.'` as t WHERE t.id IN (SELECT item_id FROM `all_items_languages` WHERE entity = '.$entity.' AND language_id = '.$_GET['lang'].') AND (t.code = '.$category_id.' OR t.subcode = '.$category_id.')';
					
				} else {
					$sql = 'SELECT ' . $field . ' AS cnt FROM ' . $eTable . ' WHERE id=' . intVal($category_id);
				}
				
			}
                

            $cnt = Yii::app()->db->createCommand($sql)->queryScalar();
            Yii::app()->dbCache->set($key, $cnt, Yii::app()->params['DbCacheTime']);
        }
        return $cnt;
    }

    private function GetChildrenHelper($entity, $cid, &$ret) {
        $entities = Entity::GetEntitiesList();
        $eTable = $entities[$entity]['site_category_table'];

        $sql = 'SELECT id FROM ' . $eTable . ' WHERE parent_id=:id';
        $ids = Yii::app()->db->createCommand($sql)->queryColumn(array(':id' => $cid));
        foreach ($ids as $id) {
            $ret[] = $id;
            $this->GetChildrenHelper($entity, $id, $ret);
        }
    }

    // list of ID's of ALL children of current category
    public function GetChildren($entity, $cid) {
        $key = 'Category_' . $entity . '_' . $cid;
        $ret = Yii::app()->dbCache->get($key);
        if ($ret === false) {
            $ret = array();
            $this->GetChildrenHelper($entity, $cid, $ret);
            Yii::app()->dbCache->set($key, $ret, Yii::app()->params['DbCacheTime']);
        }
        return $ret;
    }

    public function GetItems($entity, $cid, $paginator, $sort, $lang, $avail, $lang = '') {
		$dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
		
		//$lang = 'fi';
		
		$criteria->alias = 't';
		
        if (!empty($cid)) {
            $allChildren = array();
            $allChildren = $this->GetChildren($entity, $cid);
            if (count($allChildren) > 0) {
                array_push($allChildren, $cid);
                $ids = '(' . implode(',', $allChildren) . ')';
                $criteria->addCondition('(code IN ' . $ids . ' OR subcode IN ' . $ids . ')');
            } else {
                $criteria->addCondition('code=:cid1 OR subcode=:cid2');
                $criteria->params[':cid1'] = $cid;
                $criteria->params[':cid2'] = $cid;
            }
        }
		
		
		if ($lang!='') {
			
			$criteria->addCondition('t.id IN (SELECT item_id FROM `all_items_languages` WHERE entity = '.$entity.' AND language_id = '.$lang.')');
			
		}
		
		//$criteria->addCondition('ruslania.`all_items_languages`.entity = '.$entity.' AND ruslania.`all_items_languages`.item_id = t.id AND ruslania.`all_items_languages`.language_id = 7');

        if (!empty($avail))
            $criteria->addCondition('t.avail_for_order=1');
		
		$criteria->order = SortOptions::GetSQL($sort, $lang, $entity);
        $paginator->applyLimit($criteria);
		
		//$criteria->join = ', `all_items_languages` `ail`';
		
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();
		
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/test/items.txt', print_r($criteria,1));
		
        $ret = Product::FlatResult($data);
		
		//echo count($ret);
		
        return $ret;
    }

    public static function parseTree($root, $tree, $idName, $pidName, $additionalParams = array()) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach ($tree as $idx => $node) {
            $parent = $node[$pidName];
            # A direct child is found
            if ($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$idx]);
                # Append the child into result array and parse it's children
                $p = array('payload' => $node,
                    'parent' => $parent,
                    'children' => self::parseTree($node[$idName], $tree, $idName, $pidName, $additionalParams));

                foreach ($additionalParams as $key => $val)
                    $p[$key] = $val;

                $return[] = $p;
            }
        }
        return empty($return) ? array() : $return;
    }

    public function GetCategoriesTree($entity) {
        $key = 'CategoryTree' . $entity;

        $tree = Yii::app()->dbCache->get($key);
        if ($tree === false) {
            $entities = Entity::GetEntitiesList();
            $eTable = $entities[$entity]['site_category_table'];
            $sql = 'SELECT * FROM ' . $eTable . ' ORDER BY title_'.Yii::app()->language.', sort_order';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();

            $tree = $this->parseTree(0, $rows, 'id', 'parent_id');
            Yii::app()->dbCache->set($key, $tree);
        }

        return $tree;
    }

    public function GetByIds($entity, $ids) {
        $entities = Entity::GetEntitiesList();
        $table = array_key_exists('site_category_table', $entities[$entity]) ? $entities[$entity]['site_category_table'] : false;
        if (empty($table))
            return array();

        if (is_array($ids)) $sql = 'SELECT * FROM ' . $table . ' WHERE id IN (' . implode(',', $ids) . ')';
        if (is_int($ids)) $sql = 'SELECT * FROM ' . $table . ' WHERE id='.$ids;
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

}

/*
 *
DELETE FROM all_categories;
INSERT INTO all_categories (real_id, entity, title_ru, title_en, title_rut, title_fi)
SELECT id AS real_id, 10 AS entity, title_ru, title_en, title_rut, title_fi FROM books_categories
UNION ALL
SELECT id AS real_id, 15 AS entity, title_ru, title_en, title_rut, title_fi FROM musicsheets_categories
UNION ALL
SELECT id AS real_id, 20 AS entity, title_ru, title_en, title_rut, title_fi FROM audio_categories
UNION ALL
SELECT id AS real_id, 22 AS entity, title_ru, title_en, title_rut, title_fi FROM music_categories
UNION ALL
SELECT id AS real_id, 24 AS entity, title_ru, title_en, title_rut, title_fi FROM soft_categories
UNION ALL
SELECT id AS real_id, 30 AS entity, title_ru, title_en, title_rut, title_fi FROM pereodics_categories
UNION ALL
SELECT id AS real_id, 40 AS entity, title_ru, title_en, title_rut, title_fi FROM video_categories
UNION ALL
SELECT id AS real_id, 50 AS entity, title_ru, title_en, title_rut, title_fi FROM printed_categories
UNION ALL
SELECT id AS real_id, 60 AS entity, title_ru, title_en, title_rut, title_fi FROM maps_categories

 */
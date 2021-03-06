<?php

class Category {

    public function GetCategoryList($entity, $parent, $availCategory = false) {
        $entities = Entity::GetEntitiesList();
        $parent = intVal($parent);
        $eTable = $entities[$entity]['entity'];
        if ($availCategory !== false)
        {
            HrefTitles::get()->getByIds($entity, 'entity/list', $availCategory);

            $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE id IN ('.implode(',' ,$availCategory).') ORDER BY title_'.Yii::app()->language . ' ASC';
            $cacheKey = md5($sql);
            $dataFromCache = Yii::app()->memcache->get($cacheKey);
            if ($dataFromCache === false) {
                $dataFromCache = Yii::app()->db->createCommand($sql)->queryAll(true);
                Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
            }
            //print_r(implode(',' ,$availCategory)); die();
            return $dataFromCache;
        }
        $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE parent_id=:parent  AND items_count > 0 ORDER BY title_'.Yii::app()->language . ' ASC';
        $params = array(':parent' => $parent);
        $cacheKey = md5($sql . serialize($params));
        $dataFromCache = Yii::app()->memcache->get($cacheKey);
        if ($dataFromCache === false) {
            $dataFromCache = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
            Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
        }
//        $ids = array();
//        foreach ($dataFromCache as $row) $ids[] = $row['id'];
//        HrefTitles::get()->getByIds($entity, 'entity/list', $ids);
        return $dataFromCache;
    }

    public function exists_subcategoryes($entity, $cid) {

        $entities = Entity::GetEntitiesList();
        $cid = intVal($cid);
        $eTable = $entities[$entity]['entity'];

        $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE parent_id=:parent ORDER BY title_'.Yii::app()->language;
        $params = array(':parent' => $cid);
        $cacheKey = md5($sql . serialize($params));
        $dataFromCache = Yii::app()->memcache->get($cacheKey);
        if ($dataFromCache === false) {
            $dataFromCache = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
            Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
        }

//        $ids = array();
//        foreach ($dataFromCache as $row) $ids[] = $row['id'];
//        HrefTitles::get()->getByIds($entity, 'entity/list', $ids);

        return $dataFromCache;
    }

    function getFilterSlider($entity, $cid) {
        $yearInterval = Condition::get($entity, $cid)->getYearInterval();
        $priceInterval = Condition::get($entity, $cid)->getPriceInterval();
        $releaseYearInterval = Condition::get($entity, $cid)->getReleaseYearInterval();
        $result = array();
        $result['year_min'] = empty($yearInterval['min'])?0:$yearInterval['min'];
        $result['year_max'] = empty($yearInterval['max'])?0:$yearInterval['max'];
        $result['cost_min'] = empty($priceInterval['min'])?0:$priceInterval['min'];
        $result['cost_max'] = empty($priceInterval['max'])?0:$priceInterval['max'];
        $result['rel_year_max'] = empty($releaseYearInterval['max_y'])?0:$releaseYearInterval['max_y'];
        $result['rel_year_min'] = empty($releaseYearInterval['min_y'])?0:$releaseYearInterval['min_y'];
        return $result;



        if (!Entity::checkEntityParam($entity, 'years')) return null;

        $cid = (int) $cid;
        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $condition = array();
        $condition['avail'] = '(avail_for_order = 1)';
        if ($cid > 0) $condition['cat'] = '(`code` = ' . $cid . ' OR `subcode` = ' . $cid . ')';

        $result = array();
        //2 запроса потому, что нет возможности подобрать индекс
        $sql = ''.
            'select max(year) as max_year, min(year) as min_year '.
            'from ' . $tbl . ' '.
            'where ' . implode(' and ', $condition) . ' '.
            'limit 1 '.
        '';
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        $result[] = empty($row['min_year'])?0:$row['min_year'];
        $result[] = empty($row['max_year'])?0:$row['max_year'];

        $sql = ''.
            'select max(brutto) as cost_max, min(brutto) as cost_min '.
            'from ' . $tbl . ' '.
            'where ' . implode(' and ', $condition) . ' '.
            'limit 1 '.
            '';
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        $result[] = empty($row['cost_min'])?0:$row['cost_min'];
        $result[] = empty($row['cost_max'])?0:$row['cost_max'];

        return $result;

/*

        if ($cid > 0) {
            $sql = 'SELECT MAX(year) as max_year, MIN(year) as min_year, MAX(brutto) as cost_max, MIN(brutto) as cost_min FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) AND avail_for_order=1';
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT MAX(year) as max_year, MIN(year) as min_year, MAX(brutto) as cost_max, MIN(brutto) as cost_min FROM ' . $tbl . ' WHERE avail_for_order=1';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }

        return array($rows[0]['min_year'], $rows[0]['max_year'], $rows[0]['cost_min'], $rows[0]['cost_max']);*/
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
		
		/*$entities = Entity::GetEntitiesList();
		$tbl = $entities[$entity]['site_table'];
					
		$sql = 'SELECT ln.id as lnid, ln.title_'.Yii::app()->language.' AS lntitle FROM `all_items_languages` AS ail, `languages` AS ln, `'.$tbl.'` AS t WHERE ln.id = ail.language_id AND ail.entity = '.$entity.' AND ail.item_id = t.id';
					
		if ($cid) {
					
			$sql .= ' AND (t.code = '.$cid.' OR t.subcode = '.$cid.')';
					
		}
					
		$sql .= ' GROUP BY ln.id ORDER BY ln.id ASC';
					
		$rows = Yii::app()->db->createCommand($sql)->queryAll();	*/

        $cat = $this->GetByIds($entity, $cid);
        if (!empty($cat)) $cat = array_pop($cat);
		$langs = ProductLang::getLangItems($entity, $cat);
        $rows = array();
        foreach ($langs as $id=>$lang) $rows[] = array('lnid'=>$lang['id'], 'lntitle'=>$lang['title']);
		
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

        $ids = array();
        foreach ($rows as $row) $ids[] = $row['id'];
        HrefTitles::get()->getByIds($entity, 'entity/byaudiostream', $ids);
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

        $ids = array();
        foreach ($rows as $row) $ids[] = $row['id'];
        HrefTitles::get()->getByIds($entity, 'entity/bysubtitle', $ids);

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

    public function getPeriodicCountry($entity, $cid)
    {

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];

        $sql = 'SELECT pc.* FROM `pereodics_countries` AS pc, `' . $tbl . '` AS t WHERE t.country = pc.id';

        if ($cid) {

            $sql .= ' AND (t.code = ' . $cid . ' OR t.subcode = ' . $cid . ')';

        }
        
        $sql .= ' GROUP BY pc.id ORDER BY pc.id ASC';

        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        $ids = array();
        foreach ($rows as $row) $ids[] = $row['id'];
        HrefTitles::get()->getByIds($entity, 'entity/bysubtitle', $ids);

        return $rows;
    }

    public function getFilterBinding($entity, $cid) {
        $cid = (int) $cid;
//        if (!Entity::checkEntityParam($entity, 'binding')) return array();
        switch ($entity) {
            case 15:case 10:
                $entities = Entity::GetEntitiesList();
                $tbl = $entities[$entity]['site_table'];
                $bindings = (new ProductHelper)->GetBindingListForSelect($entity);
                if (empty($bindings)) return array();

                $bindingIds = array();
                foreach ($bindings as $binding) $bindingIds[] = $binding['ID'];

                $condition = array('bindings'=>'(binding_id > 0)', 'avail'=>'(avail_for_order = 1)', ) ;
                if ($cid > 0) $condition['cat'] = '(`code` = ' . $cid . ' OR `subcode` = ' . $cid . ')';

                $sql = ''.
                    'select binding_id '.
                    'from ' . $tbl . ' '.
                    'where ' . implode(' and ', $condition) . ' '.
                    'group by binding_id '.
                '';
                $rows = Yii::app()->db->createCommand($sql)->queryAll();

            $ids = array();
            foreach ($rows as $row) $ids[] = $row['binding_id'];
            HrefTitles::get()->getByIds($entity, 'entity/bybinding', $ids);

                return $rows;
                break;
            case 22:case 24:
                $entities = Entity::GetEntitiesList();
                $tbl = $entities[$entity]['site_table'];
                if ($cid > 0) {
                    $sql = 'SELECT media_id FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) AND avail_for_order=1 GROUP BY media_id';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
                }
                else {
                    $sql = 'SELECT media_id FROM ' . $tbl . ' WHERE avail_for_order=1 GROUP BY media_id';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll();
                }

                $ids = array();
                foreach ($rows as $row) $ids[] = $row['media_id'];
                HrefTitles::get()->getByIds($entity, 'entity/bymedia', $ids);

                return $rows;
                break;
            case 30:
                $entities = Entity::GetEntitiesList();
                $tbl = $entities[$entity]['site_table'];
                $tbl_type = $entities[$entity]['type_table'];
                if ($cid > 0) {
                    $sql = 'SELECT type FROM ' . $tbl . ' WHERE (`code`=:code OR `subcode`=:code) GROUP BY type';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
                }
                else {
                    $sql = 'SELECT type FROM ' . $tbl . ' GROUP BY type';
                    $rows = Yii::app()->db->createCommand($sql)->queryAll();
                }

                return $rows;
                break;
        }
        return array();
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
                $sql = 'SELECT tc.publisher_id, ap.title_ru, ap.title_en FROM ' . $tbl . ' as tc, all_publishers as ap '.
                'WHERE (tc.`code`=:code OR tc.`subcode`=:code) AND tc.avail_for_order=1' . $sql .' '.
                'AND ap.id = tc.publisher_id '.
                'GROUP BY tc.publisher_id '. (($page != 0) ? (' LIMIT ' . $limit) : '');
                $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
            } else {
                $sql = 'SELECT tc.publisher_id, ap.title_ru, ap.title_en FROM ' . $tbl . ' as tc, all_publishers as ap '.
                'WHERE tc.avail_for_order=1' . $sql. ' '.
                'AND ap.id = tc.publisher_id '.
                'GROUP BY tc.publisher_id '. (($page != 0) ? (' LIMIT ' . $limit) : '');
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
            }

            $izd = [];
            foreach ($rows as $row) {
                $izd[(int)$row['publisher_id']]['ru'] = $row['title_ru'];
                if ($row['title_en'] != $row['title_ru'])
                    $izd[(int)$row['publisher_id']]['en'] = $row['title_en'];
            }

            if ($izd) HrefTitles::get()->getByIds($entity, 'entity/bypublisher', array_keys($izd));
            return $izd;
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
            $sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi FROM ' . $tbl . ' as tc, '.$series_tbl.' as st 
            WHERE (tc.`code`=:code OR tc.`subcode`=:code) 
            AND tc.avail_for_order=1 AND (tc.series_id > 0 AND tc.series_id <> "") AND tc.series_id=st.id' .$sql.
            (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi FROM ' . $tbl . ' as tc, '.$series_tbl.' as st 
            WHERE tc.avail_for_order=1  AND (tc.series_id > 0 AND tc.series_id <> "") AND tc.series_id=st.id' .$sql.
                (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }

        $series = [];
        foreach ($rows as $row) {
            $series[(int)$row['series_id']]['ru'] = $row['title_ru'];
            if ($row['title_rut'] != $row['title_ru'])
                $series[(int)$row['series_id']]['rut'] = $row['title_rut'];
            if ($row['title_en'] != $row['title_ru'] && $row['title_en'] != $row['title_rut'])
                $series[(int)$row['series_id']]['en'] = $row['title_en'];
            if ($row['title_fi'] != $row['title_ru'] && $row['title_fi'] != $row['title_rut'] && $row['title_fi'] != $row['title_en'])
                $series[(int)$row['series_id']]['fi'] = $row['title_fi'];
        }

        if ($series) HrefTitles::get()->getByIds($entity, 'entity/byseries', array_keys($series));
        return $series;
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
            $sql = 'SELECT ba.author_id, aa.title_ru, aa.title_rut, aa.title_en, aa.title_fi FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba, all_authorslist as aa 
            WHERE (bc.`code`=:code OR bc.`subcode`=:code) AND bc.avail_for_order=1 AND ba.' . $field . '=bc.id'.$sql.'
            AND ba.author_id=aa.id 
            GROUP BY ba.author_id '. (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT ba.author_id, aa.title_ru, aa.title_rut, aa.title_en, aa.title_fi FROM ' . $tbl . ' as bc, ' . $tbl_author . ' as ba, all_authorslist as aa 
            WHERE avail_for_order=1  AND bc.avail_for_order=1 AND ba.' . $field . '=bc.id'.$sql.'
            AND ba.author_id=aa.id 
            GROUP BY ba.author_id '. (($page != 0) ? (' LIMIT ' . $limit) : '');
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }
        $authors = [];
        foreach ($rows as $row) {
            $authors[(int)$row['author_id']]['ru'] = $row['title_ru'];
            if ($row['title_rut'] != $row['title_ru'])
                $authors[(int)$row['author_id']]['rut'] = $row['title_rut'];
            if ($row['title_en'] != $row['title_ru'] && $row['title_en'] != $row['title_rut'])
                $authors[(int)$row['author_id']]['en'] = $row['title_en'];
            if ($row['title_fi'] != $row['title_ru'] && $row['title_fi'] != $row['title_rut'] && $row['title_fi'] != $row['title_en'])
                $authors[(int)$row['author_id']]['fi'] = $row['title_fi'];
        }
        if ($authors) HrefTitles::get()->getByIds($entity, 'entity/byauthor', array_keys($authors));
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
        if ($authors) HrefTitles::get()->getByIds($entity, 'entity/byauthor', array_keys($authors));
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


        $ids = array();
        foreach ($rows as $row) $ids[] = $row['id'];
        HrefTitles::get()->getByIds($entity, 'entity/list', $ids);

		return $rows;
		
	}
	
	public function getCatsBreadcrumbs($entity, $code) {
        $arr = array();
		if ($code) {
			
			$arr = self::getCatsBreadcrumbs2($entity, $code);
			
		}
		
		return array_reverse($arr);
	}
	
    public function result_filter($data = array(), $lang_sel='', $page = 0) {

        if (!$data OR count($data) == 0) {
            return array();
        }


        $entity = $data['entity'];
        $cid = $data['cid'];

        $sort = 0;
        if (isset($_GET['sort'])) $sort = $_GET['sort'];
        elseif (isset($data['sort'])) $sort = $data['sort'];

        $sort = SortOptions::GetDefaultSort($sort);
        return $this->getFilterResult($entity, $cid, $sort, $page);



        $avail = $data['avail'];
        $lang_sel = $data['lang_sel'];
        $ymin = $data['year_min'];
        $ymax = $data['year_max'];
        $author = $data['author'];
        $publisher = $data['publisher'];
        $seria = $data['series'];
        $binding = $data['binding'];
        $cmin = $data['cost_min'];
        $cmax = $data['cost_max'];
        $formatVideo = $data['format_video'];
        $langVideo = $data['lang_video'];
        $subtitlesVideo = $data['subtitles_video'];


        $entities = Entity::GetEntitiesList();
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];


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
        
        if ($publisher AND $entity !=40) {
            $criteria->addCondition('publisher_id=:pid');
            $criteria->params[':pid'] = $publisher;
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
            if ($entity == 22 OR $entity == 24) {
                $str = ' media_id=' . implode(' OR media_id=', $binding);
            }
            else {
                $str = ' binding_id=' . implode(' OR binding_id=', $binding);
            }
            $criteria->addCondition($str);
        }

        $criteria->addCondition('brutto >= :brutto1 AND brutto<=:brutto2');
        $criteria->params[':brutto1'] = $cmin;
        $criteria->params[':brutto2'] = $cmax;

       if ($avail == 1) $criteria->addCondition('t.avail_for_order=1');
        $criteria->order = SortOptions::GetSQL($sort, $lang, $entity);
        $criteria->limit = Yii::app()->params['ItemsPerPage'];
        $criteria->offset = $page * $criteria->limit;
        $dp->setCriteria($criteria);
        $dp->pagination = false;
        $datas = $dp->getData();

        $ret = Product::FlatResult($datas);

        return $ret;
    }

    function getFilterResult($entity, $cid, $sort, $page) {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->order = SortOptions::GetSQL($sort, '', $entity);
        $criteria->limit = Yii::app()->params['ItemsPerPage'];
        $criteria->offset = 0;

       /* if ($_GET['sort']) {
            $sort = $_GET['sort'];
        } else {
            if (!$sort) $sort = 12;
        }
        $sort = SortOptions::GetDefaultSort($sort);*/

        $condition = Condition::get($entity, $cid)->getCondition();
        $join = Condition::get($entity, $cid)->getJoin();
//        $join['tVendots'] = 'left join vendors tVendots on (tVendots.id = t.vendor)';
//        $join['deliveryTime'] = 'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tVendots.dtid)';

        $sql = ''.
            'select t.id '.
            'from ' . $dp->model->tableName() . ' t '.
                implode(' ', $join) . ' '.
                (empty($condition)?'':'where ' . implode(' and ', $condition)) . ' '.
            'order by ' . $criteria->order . ' '.
            'limit ' . $page * $criteria->limit . ', ' . $criteria->limit . ' '.
        '';

        $dataFromCache = false;
        $cacheKey = md5($sql);
        if ($page < 5) $dataFromCache = Yii::app()->memcache->get($cacheKey);
        if ($dataFromCache === false) {
            $itemIds = Yii::app()->db->createCommand($sql)->queryColumn();
            if (empty($itemIds)) return array();

            HrefTitles::get()->getByIds($entity, 'product/view', $itemIds);

            Product::setActionItems($entity, $itemIds);
            Product::setOfferItems($entity, $itemIds);
            $criteria->alias = 't';
            $criteria->addCondition('t.id in (' . implode(',', $itemIds) . ')');
            $criteria->order = 'field(t.id, ' . implode(',', $itemIds) . ')';
            $dp->setCriteria($criteria);
            $dp->pagination = false;
            $data = $dp->getData();
            $dataFromCache = Product::FlatResult($data);
            if ($page < 5) Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
        }
        elseif (is_array($dataFromCache)) {
            $itemIds = array();
            foreach ($dataFromCache as $item) $itemIds[] = $item['id'];
            Product::setActionItems($entity, $itemIds);
            Product::setOfferItems($entity, $itemIds);
        }
        return $dataFromCache;
    }

    function getFilterCounts($entity, $cid) {
        $onlySupportLanguageCondition = Condition::get($entity, $cid)->onlySupportCondition();
        $condition = Condition::get($entity, $cid)->getCondition();
        $join = Condition::get($entity, $cid)->getJoin();

        $cacheKey = md5(serialize(array($entity, $cid, $condition, $join)));
        $dataFromCache = Yii::app()->memcache->get($cacheKey);
        if ($dataFromCache === false) {
            $distinct = '*';
            if (!empty($onlySupportLanguageCondition) //все данные есть в таблице _support_languages_
                ||(empty($condition)&&!empty($join['tL_support']))//все можно достать без таблицы _catalog
            ) {
                if (empty($onlySupportLanguageCondition)) $onlySupportLanguageCondition = Condition::get($entity, $cid)->onlySupportCondition(false);
                if (!empty($onlySupportLanguageCondition['cid'])) $distinct = 'distinct t.id';
                unset($join['tL_support']);
                $sql = ''.
                    'select count(' . $distinct . ') '.
                    'from _support_languages_' . Entity::GetUrlKey($entity) . ' t '.
                    implode(' ', $join) . ' '.
                    'where ' . implode(' and ', $onlySupportLanguageCondition) . ' '.
                    '';
                $dataFromCache = (int) Yii::app()->db->createCommand($sql)->queryScalar();
            }
            else {
                if (!empty($join['tL_support'])&&!empty($condition['cid'])) $distinct = 'distinct t.id';
                $entityParams = Entity::GetEntitiesList()[$entity];

                $sql = ''.
                    'select count(' . $distinct . ') '.
                    'from ' . $entityParams['site_table'] . ' t '.
                    implode(' ', $join) . ' '.
                    (empty($condition)?'':'where ' . implode(' and ', $condition)) . ' '.
                    '';
                $dataFromCache = (int) Yii::app()->db->createCommand($sql)->queryScalar();
            }
            Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
        }

        return $dataFromCache;
    }

    public function count_filter($entity = 15, $cid, $post, $isFilter = false) {
        $counts = $this->getFilterCounts($entity, $cid);
        return (($counts > 1000) && $isFilter) ? '>1000' : $counts;

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];

        /* post данные */

        $aid = $post['author'];
        $avail = $post['avail'];
        $publisher = $post['publisher'];
        $seria = $post['series'];

        if ($entity != 30) {
            $year_min = (isset($post['year_min']) && $post['year_min'] != '') ? $post['year_min'] : 1900;
            $year_max = (isset($post['year_max']) && $post['year_max'] != '') ? $post['year_max'] : 2050;
        }

        $cost_min = (isset($post['cost_min']) && $post['cost_min'] != '') ? $post['cost_min'] : 0;
        $cost_min = (float)str_replace(',','.', $cost_min);
        $cost_max = (isset($post['cost_max']) && $post['cost_max'] != '') ? $post['cost_max'] : 10000;
        $cost_max = (float)str_replace(',','.', $cost_max);

        $binding_id = $post['binding'];
        $langsel = (int) $post['lang_sel'];

        $formatVideo = $post ['format_video'];
        $langVideo = $post ['lang_video'];
        $subtitlesVideo = $post ['subtitles_video'];

        $query = array();
        $qstr = '';

        if ($cid>0) {
            $whereCid = '';
            $allChildren = array();
            $allChildren = $this->GetChildren($entity, $cid);
            if (count($allChildren) > 0) {
                array_push($allChildren, $cid);
                $ids = '(' . implode(',', $allChildren) . ')';
                $whereCid = '(bc.code IN ' . $ids . ' OR bc.subcode IN ' . $ids . ')';
            } else {
                $whereCid = 'bc.code=:code OR bc.subcode=:code';
            }
        }

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
        if ($publisher AND $entity !=40) {
            $query[] = 'bc.publisher_id = ' . $publisher;
        }
        if ($seria AND $entity !=40) {
            $query[] = 'bc.series_id = ' . $seria;
        }
        if ($entity != 30 && $year_min != '' && $year_max != '') {
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
                if ($entity == 22 OR $entity == 24) {
                    $query[] = '( bc.media_id=' . implode(' OR bc.media_id=', $binding_id) . ' )';
                }
                else {
                    $query[] = '( bc.binding_id=' . implode(' OR bc.binding_id=', $binding_id) . ' )';
                }
        }

        if (count($query) > 0) {
            $qstr = ' AND ' . implode(' AND ', $query);
        }

        if ($cid > 0) {
            $sql = 'SELECT COUNT(*) as cnt FROM (SELECT 1 FROM ' . $tbl . ' as bc ' . $addtbl . ' 
            WHERE ('.$whereCid.') ' . $qstr .' LIMIT 0,1001) as c';
            if (!$isFilter) $sql = 'SELECT COUNT(*) as cnt FROM ' . $tbl . ' as bc ' . $addtbl . ' 
            WHERE ('.$whereCid.') ' . $qstr;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid));
        } else {
            $sql = 'SELECT COUNT(*) as cnt FROM (SELECT 1 FROM ' . $tbl . ' as bc ' . $addtbl . ' 
            WHERE bc.id <> 0 ' . $qstr .' LIMIT 0,1001) as c';
            if (!$isFilter) $sql = 'SELECT COUNT(*) as cnt FROM ' . $tbl . ' as bc ' . $addtbl . ' 
            WHERE bc.id <> 0 ' . $qstr;
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
        }
        return ($rows[0]['cnt'] == 1001 && $isFilter) ? '>1000' : $rows[0]['cnt'];
    }

    public function GetCategoryPath($entity, $cid) {
        $cacheKey = md5(serialize(array('eid'=>$entity, 'cid'=>$cid)));
        $dataFromCache = Yii::app()->memcache->get($cacheKey);
        if ($dataFromCache === false) {
            $entities = Entity::GetEntitiesList();
            $eTable = $entities[$entity]['entity'];
            $dataFromCache = array();

            while (true) {
                $sql = 'SELECT * FROM ' . $eTable . '_categories WHERE id=:id';
                $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $cid));
                if (empty($row))
                    break;
                $dataFromCache[] = $row;
                $cid = $row['parent_id'];
                if (empty($cid))
                    break;
            }
            $dataFromCache = array_reverse($dataFromCache);
            Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
        }
        return $dataFromCache;
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
			if (isset(Yii::app()->getRequest()->cookies['langsel']->value)) {
				$_GET['lang'] = Yii::app()->getRequest()->cookies['langsel']->value;
			}
		}
	
        if ($cnt === false) {
            if ($category_id == 0) {
				
				if (isset($_GET['lang'])) {
					
					$sql = 'SELECT COUNT(id) as cnt FROM `'.$eTable2.'` as t WHERE t.id IN (SELECT item_id FROM `all_items_languages` WHERE entity = '.$entity.' AND language_id = '.$_GET['lang'].')';
					
				} else {
					$sql = 'SELECT SUM(' . $field . ') AS cnt FROM ' . $eTable . ' WHERE parent_id=0';
				}
				
			} else {
				
				if (isset($_GET['lang'])) {
					
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
        $ret = Yii::app()->memcache->get($key);
        if ($ret === false) {
            $ret = array();
            $this->GetChildrenHelper($entity, $cid, $ret);
            Yii::app()->memcache->set($key, $ret, Yii::app()->params['listMemcacheTime']);
        }
//        if ($ret) HrefTitles::get()->getByIds($entity, 'entity/list', $ret);
        return $ret;
    }

    public function GetItems($entity, $cid, $paginator, $sort, $language, $avail, $lang = '') {
        $lang = (int) $lang;
        $dp = Entity::CreateDataProvider($entity);
        $condition = $join = array();
        if (!empty($avail)) $condition['avail'] = '(t.avail_for_order=1)';
        if (!empty($cid)) {
            $allChildren = $this->GetChildren($entity, $cid);
            if (count($allChildren) > 0) {
                array_push($allChildren, $cid);
                $ids = '(' . implode(',', $allChildren) . ')';
                $condition['category'] = '(code IN ' . $ids . ' OR subcode IN ' . $ids . ')';
            }
            else {
                $condition['category'] = '(code = ' . (int)$cid . ' OR subcode = ' . (int)$cid . ')';
            }
        }
        if ($lang > 0) {
            $join['tAIL'] = 'join all_items_languages tAIL on (tAIL.item_id = t.id) and (language_id = ' . $lang . ')';
        }
        //	LEFT OUTER JOIN `vendors` `vendorData` ON (`t`.`vendor`=`vendorData`.`id`)
        //LEFT OUTER JOIN `delivery_time_list` `deliveryTime` ON (`vendorData`.`dtid`=`deliveryTime`.`dtid`)

//        $join['tV'] = 'left join vendors tV on (tV.id = t.vendor)';
//        $join['deliveryTime'] = 'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tV.dtid)';

        $sql = ''.
            'select t.id '.
            'from ' . $dp->model->tableName() . ' t '.
            implode(' ', $join) . ' '.
            (empty($condition)?'':'where ' . implode(' and ', $condition)) . ' '.
            'order by ' . SortOptions::GetSQL($sort, $lang, $entity) . ' '.
            (($paginator) ?
            ('limit ' . $paginator->getOffset() . ', ' . $paginator->getLimit() . ' ')
            : ' ').
            '';
        $itemIds = Yii::app()->db->createCommand($sql)->queryColumn();

        if (empty($itemIds)) return array();

        HrefTitles::get()->getByIds($entity, 'product/view', $itemIds);

        Product::setActionItems($entity, $itemIds);
        Product::setOfferItems($entity, $itemIds);
        $criteria = $dp->getCriteria();

		//$lang = 'fi';
		
		$criteria->alias = 't';
        $criteria->addCondition('t.id in (' . implode(',', $itemIds) . ')');
        $criteria->order = 'field(t.id, ' . implode(',', $itemIds) . ')';

/*        if (!empty($cid)) {
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
        $paginator->applyLimit($criteria);*/
		
		//$criteria->join = ', `all_items_languages` `ail`';
		
        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();
		
		//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/test/items.txt', print_r($criteria,1));
		
        $ret = Product::FlatResult($data);
		
		//echo count($ret);
		
        return $ret;
    }

    public static function parseTree($root, $tree, $idName, $pidName, $additionalParams = array(), $checkCountAvail = false) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach ($tree as $idx => $node) {
            $parent = $node[$pidName];
            # A direct child is found
            if ($parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$idx]);
                # Append the child into result array and parse it's children
                if (!$checkCountAvail || ($node['avail_items_count'] > 0)) {
                    $p = array('payload' => $node,
                        'parent' => $parent,
                        'children' => self::parseTree($node[$idName], $tree, $idName, $pidName, $additionalParams, $checkCountAvail));

                    foreach ($additionalParams as $key => $val)
                        $p[$key] = $val;

                    $return[] = $p;
                }
            }
        }
        return empty($return) ? array() : $return;
    }

    public function GetCategoriesTree($entity, $checkCountAvail = false) {
        $key = 'CategoryTree' . $entity . '_count' . (int) $checkCountAvail;

        $tree = Yii::app()->memcache->get($key);
        if ($tree === false) {
            $entities = Entity::GetEntitiesList();
            $eTable = $entities[$entity]['site_category_table'];
            $sql = 'SELECT * FROM ' . $eTable . ' ORDER BY title_'.Yii::app()->language.', sort_order';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();

            $tree = $this->parseTree(0, $rows, 'id', 'parent_id', array(), $checkCountAvail);
            Yii::app()->memcache->set($key, $tree, Yii::app()->params['listMemcacheTime']);
        }

//        $ids = array();
//        foreach ($tree as $row) $ids[] = $row['id'];
//        HrefTitles::get()->getByIds($entity, 'entity/list', $ids);

        return $tree;
    }

    public function getPeriodicsCategoriesTree($type) {
        $key = 'CategoryTree' . Entity::PERIODIC . '_type' . (int) $type;

        $tree = Yii::app()->memcache->get($key);
        if ($tree === false) {
            $sql = 'SELECT * FROM pereodics_categories where (avail_items_type_' . $type . ' > 0) ORDER BY title_'.Yii::app()->language.', sort_order';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();

            $tree = $this->parseTree(0, $rows, 'id', 'parent_id', array(), false);
            Yii::app()->memcache->set($key, $tree, Yii::app()->params['listMemcacheTime']);
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

        if (empty($sql)) return array();

        $cacheKey = md5($sql);
        $dataFromCache = Yii::app()->memcache->get($cacheKey);
        if ($dataFromCache === false) {
            $dataFromCache = Yii::app()->db->createCommand($sql)->queryAll();
            Yii::app()->memcache->set($cacheKey, $dataFromCache, Yii::app()->params['listMemcacheTime']);
        }

//        $ids = array();
//        foreach ($rows as $row) $ids[] = $row['id'];
//        HrefTitles::get()->getByIds($entity, 'entity/list', $ids);

        return $dataFromCache;
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
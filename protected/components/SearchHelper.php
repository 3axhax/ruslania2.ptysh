 <?php

class SearchHelper
{
    public static function Create()
    {
        $search = Yii::app()->search;
        $search->ResetFilters();
        $search->setSelect('*');
        $search->setArrayResult(false);
        $search->setMatchMode(SPH_MATCH_EXTENDED);
        $search->setRankingMode(SPH_RANK_PROXIMITY_BM25);
        $search->setSortMode(SPH_SORT_RELEVANCE);
        $search->SetFieldWeights(array('title_ru' => 500,
                                       'title_rut' => 500,
                                       'title_fi' => 500,
                                       'title_en' => 500,
                                       'title_de' => 500,
                                       'title_fr' => 500,
                                       'title_es' => 500,
                                       'description_ru' => 10,
                                       'description_rut' => 10,
                                       'description_en' => 10,
                                       'description_fi' => 10,
                                       'description_de' => 10,
                                       'description_fr' => 10,
                                       'description_es' => 10
                                 ));
        return $search;
    }

    public static function CreateSearcher($q)
    {
        $search = self::Create();
        $q = trim(str_replace('"', '', $q));

        $split = preg_split('/[\s,-]+/', $q, 5);
        if (!$split) return $search;

        $q2 = array();
        $words = array();
        $qStart = '(="' . $search->EscapeString(strtolower($q)) . '") | (';

        $stopwords = array('для', 'for');
        foreach ($split as $val)
        {
            $w = strtolower(trim($val));
            if (in_array($w, $stopwords)) continue;
            if (!empty($w)) $words[] = $w;
            $escaped = $search->EscapeString($w);
            if (strlen($val) >= 3) $q2[] = '(' . $escaped . ' | ' . $escaped . '* | =' . $escaped . ')';
        }
        $q = $qStart . implode(' & ', $q2) . ')';
//        var_dump($q);
        return array($search, $q, $words);
    }

    public static function BuildKeywords($query, $index, $genLastStep = true)
    {
        $search = self::Create();
        $queryWords = array();
        $tokens = array();
        $query = strtolower(trim($query));
        $words = explode(" ", $query);
        $realWords = array();

        $stopwords = array('для', 'for', 'dlja', 'и');

        foreach ($words as $word)
        {
            $word = str_replace(',', '', $word);
            $word = str_replace('.', '', $word);
            $word = trim($word);
            if (in_array($word, $stopwords)) continue;
            if(strlen($word) <= 2) continue;
            $realWords[] = $word;
        }

        $query = trim(implode($realWords, ' '));

        $queries = array();
        if (!empty($query))
            $queries[] = '("' . $search->EscapeString($query) . '"/' . (count($realWords)) . ')';

        $kw = $search->BuildKeywords($query, $index, false);


        if (!empty($kw))
        {
            foreach ($kw as $keyWord)
            {
                if (in_array($keyWord['normalized'], $stopwords)) continue;
                if (strlen($keyWord['normalized']) < 3) continue;
                $tokens[] = $keyWord['normalized'];
            }
        }

        if(count($tokens) > 0) $queries[] = '("' . implode($tokens, ' ') . '"/' . count($tokens) . ')';

        if ($genLastStep && count($tokens) > 0 && count($tokens) <= 4)
        {
            $len = count($tokens);
            $list = array();

            for ($i = 1; $i < (1 << $len); $i++)
            {
                $c = array();
                for ($j = 0; $j < $len; $j++)
                    if ($i & (1 << $j))
                        $c[] = $tokens[$j];

                $cnt = count($c);
                if ($cnt > 1 && $cnt < $len) $list[] = $c;
            }

            $tmp = array();
            foreach ($list as $item)
                $tmp[] = '(' . implode(' ', $item) . ')';

            if (count($tmp) > 0) $queries[] = implode(' | ', $tmp);

            $queries[] = '(' . implode(' | ', $tokens) . ')';
        }
		
		
		
        return array('OriginalQuery' => $query, 'Keywords' => $queryWords,
                     'Tokens' => $tokens, 'Queries' => array_unique($queries));
    }

    public static function QueryIndex($query, $index, $filters = array())
    {
//        if($index != 'categories') return array();
        $pre = self::BuildKeywords($query, $index);
        $result = array();

        $search = self::Create();

        foreach ($pre['Queries'] as $query)
        {
            if (empty($query)) continue;

            if (!empty($filters))
                foreach ($filters as $name => $value)
                    $search->SetFilter($name, array($value));



            $res = $search->query($query, $index);
            //CommonHelper::MyLog(array('q' => $query, 'i' => $index, 'res' => $res));

            if ($index == 'authors')
            {
//                var_dump($query);
//                var_dump($res);
//                echo '<hr/>';
            }

            if ($res['total_found'] > 0)
            {
                foreach ($res['matches'] as $key => $match)
                {
                    $d = array('key' => $key);
                    $attrs = $match['attrs'];
                    foreach ($attrs as $name => $value)
                    {
                        $d[$name] = $value;
                    }
                    $result[$key] = $d;
                }
                break; // если нашли по какому-то запросу, то ниже уже не идем
            }
        }
        return $result;
    }

    public static function SearchInCategories($query, $filters = array())
    {
        $result = self::QueryIndex($query, 'categories');

        if (empty($result)) return array();

        $where = array();
        foreach($result as $cat)
        {
            $where[] = '(entity='.intVal($cat['entity']).' AND real_id='.intVal($cat['real_id']).')';
        }

        if(empty($where)) return array();

        $sql = 'SELECT * FROM all_categories WHERE '.implode(' OR ', $where);
        $rows = Yii::app()->db->createCommand($sql)->queryAll();

        $ret = array();
        foreach ($rows as $item)
        {
            $itemTitle = ProductHelper::GetTitle($item);
            $row = array();
            $row['url'] = Yii::app()->createUrl('entity/list', array('cid' => $item['real_id'],
                                                                     'title' => ProductHelper::ToAscii($itemTitle),
                                                                     'entity' => Entity::GetUrlKey($item['entity'])));
            $row['title'] = Entity::GetTitle($item['entity']) . ' - ' . Yii::app()->ui->item('Related categories') . ': <b>' . $itemTitle . '</b>';
            $row['is_product'] = false;
            $row['orig_data'] = $item;
            $ret[] = $row;
        }
        return $ret;
    }

    public static function SearchInPublishers($query, $filters = array())
    {
        $result = self::QueryIndex($query, 'publishers');
        if (empty($result)) return array();

        $ids = array_keys($result);

        if (empty($ids)) return array();
        $idList = implode(', ', $ids);

        $params = array();
        $sql = 'SELECT * FROM all_publishers AS p JOIN all_publishers_entity AS pe ON (p.id=pe.publisher ';
        if (array_key_exists('entity', $filters))
        {
            $params[':entity'] = $filters['entity'];
            $sql .= ' AND pe.entity=:entity) ';
        }
        else $sql .= ') ';

        $sql .= ' WHERE id IN (' . $idList . ') '
            . ' ORDER BY FIELD(id, ' . $idList . ')';

        $rows = Yii::app()->db->createCommand($sql)->queryAll(true, $params);
        $ret = array();

        foreach ($rows as $row)
        {
            $item = array();
            $itemTitle = ProductHelper::GetTitle($row);
            $title = Entity::GetTitle($row['entity']) . '; '
                . sprintf(Yii::app()->ui->item('PUBLISHED_BY'), '<b>' . $itemTitle . '</b>');

            $item['is_product'] = false;
            $item['url'] = Yii::app()->createUrl('entity/bypublisher',
                array('entity' => Entity::GetUrlKey($row['entity']),
                      'title' => ProductHelper::ToAscii($itemTitle),
                      'pid' => $row['id']
                ));
            $item['title'] = $title;
            $item['orig_data'] = $row;
            $ret[] = $item;
        }

        return $ret;
    }

    public static function SearchInProducts($query, $filters, $page, $pp, &$totalFound)
    {
        $search = self::Create();
        //$search->SetLimits($pp * $page, $pp);
        $search->ResetFilters();

        if (!empty($filters))
            foreach ($filters as $name => $value)
                $search->SetFilter($name, array($value));

        //$q = '@(title_ru,title_rut,title_fi,title_en)  ' . $search->EscapeString($query);
        $q = '@* ' . $search->EscapeString($query);
		
		$search->SetSortMode(SPH_SORT_ATTR_DESC, "in_shop");
        $res = $search->query($q, 'products');

        $totalFound = $res['total_found'];
        $result = self::ProcessProducts($res);
		
		//var_dump($result);
		
        return $result;
    }

    public static function SearchCrossProdAuthors($query, $filters, $authorsResult, $page, $pp, &$totalFound)
    {
		
		 //if (empty($authorsResult)) return array();
        $pre = self::BuildKeywords($query, 'products');

        $search = self::Create();
        //$search->SetLimits($pp * $page, $pp);

        if (!empty($filters))
            foreach ($filters as $name => $value)
            {
                if($name != 'avail')
                    $search->SetFilter($name, array($value));
            }

        $ids = array();
        $authorNames = array();
        $keys = array('title_ru', 'title_rut', 'title_en', 'title_fi');
        foreach ($authorsResult as $author)
        {
			
            $ids['e'.$author['entity']] = $author['orig_data']['id'];
            foreach ($keys as $key)
            {
                $tmp = explode(' ', $author['orig_data'][$key]);
                foreach ($tmp as $t)
                {
                    $t = trim($t);
                    if (strlen($t) < 3) continue;
                    $authorNames[] = $t;
                }
            }
        }
		
		//return $ids;
		
        $authorNames = implode(' ', array_unique($authorNames));
        $t1 = self::BuildKeywords($authorNames, 'authors', false);

        $tokens = $t1['Tokens'];
        $qTokens = $pre['Tokens'];
        $newWord = array();
        if (!empty($tokens))
        {
            foreach ($qTokens as $qToken)
            {
                $found = false;
                foreach ($tokens as $token)
                {
                    $pos = strpos($qToken, $token);
                    if ($pos !== false)
                    {
                        $found = true;
                        break;
                    }
                }
                if (!$found) $newWord[] = $qToken;
            }
        }

        if (!empty($newWord)) $newQuery = implode(' ', $newWord);
        else $newQuery = reset($pre['Queries']);

        if (!empty($ids)) $search->SetFilter('author', $ids);
        if (array_key_exists('entity', $filters)) $search->SetFilter('entity', array($filters['entity']));
        if (array_key_exists('avail', $filters) && $filters['avail']) $search->SetFilter('avail', array($filters['avail']));
		$search->SetSortMode(SPH_SORT_EXPR, "@weight + in_shop*2000000 + in_stock*50000");
        $res = $search->query($newQuery, 'products');
        if ($res['total_found'] == 0)
        {
            $search->ResetFilters();
            if (array_key_exists('entity', $filters)) $search->SetFilter('entity', array($filters['entity']));
            if(array_key_exists('avail', $filters) && $filters['avail']) $search->SetFilter('avail', array($filters['avail']));
            $res = $search->query($newQuery, 'products');
        }
		
        $totalFound = $res['total_found'];
        //$result = self::ProcessProducts($res);

        return array('Items' => $ids, 'Total' => $res['total_found']);
    }

    public static function ProcessProducts($res)
    {
        $ids = array();
        foreach ($res['matches'] as $id => $data)
        {
            $attr = $data['attrs'];
            $ids['e'.$attr['entity']][] = $attr['real_id'];
        }

        $p = new Product;
		
		//var_dump($ids);
		
		return $ids;
		
        $result = array();
        $merged = array();
        foreach ($ids as $entity => $id)
        {
            $list = $p->GetProductsV2($entity, $id);
            $ret = array();
            if(empty($list)) return $ret;
			
			$arrEntityes = array();
			
            foreach ($list as $item)
            {
                $key = $entity . '-' . $item['id'];

                if (Yii::app()->request->isAjaxRequest)
                {
                    $row = array();
                    $row['is_product'] = true;
                    $row['item_entity'] = Entity::GetTitle($entity);
                    $row['avail'] = Availability::ToStr($item);
                    $row['url'] = ProductHelper::CreateUrl($item);
                    $row['picture_url'] = ProductHelper::Link2Picture($item, true);
                    $row['title'] = ProductHelper::GetTitle($item);
                    $row['id'] = $item['id'];
                    $row['entity'] = $entity;
					$row['category'] = $item['code'];
					$row['price'] = ProductHelper::FormatPrice($item['brutto']);
					
					//$curCount = (int) $result['Counts'][(string)$entity];
					
					//$result['Counts']['' . $entity] = $curCount+1;
					
					if ($item['subcode']) {
						$row['category'] = $item['subcode'];
					}
					
					$ret[$key] = $row;
                }
                else
                {
                    $item['is_product'] = true;
                    $ret[$key] = $item;
                }
            }
            $merged = array_merge($merged, $ret);
        }

        foreach ($res['matches'] as $id => $data)
        {
            $attr = $data['attrs'];
            $key = $attr['entity'] . '-' . $attr['real_id'];
            if (array_key_exists($key, $merged)) $result[] = $merged[$key];
        }
		
		
		
		
		

        return $result;
    }
	
	public static function ProcessProducts2($ids, $fillAjaData = true)
    {
		
        $result = array();
        $merged = array();
		
		$p = new Product;
		
		
        foreach ($ids as $entity => $id)
        {
            
			$entity = trim($entity,'e');
			$list = $p->GetProductsV2($entity, $id);
            $ret = array();
            if(empty($list)) return $ret;
			
			$arrEntityes = array();
			
            foreach ($list as $item)
            {	
				
				
				
				$key = $entity . '-' . $item['id'];

                if (Yii::app()->request->isAjaxRequest && $fillAjaData)
                {
                    $row = array();
                    $row['is_product'] = true;
                    $row['item_entity'] = Entity::GetTitle($entity);
                    $row['avail'] = Availability::ToStr($item);
                    $row['url'] = ProductHelper::CreateUrl($item);
                    $row['picture_url'] = ProductHelper::Link2Picture($item, true);
                    $row['title'] = ProductHelper::GetTitle($item);
                    $row['id'] = $item['id'];
                    $row['entity'] = $entity;
					$row['category'] = $item['code'];
					$row['price'] = ProductHelper::FormatPrice($item['brutto']);
					
					//$curCount = (int) $result['Counts'][(string)$entity];
					
					//$result['Counts']['' . $entity] = $curCount+1;
					
					if ($item['subcode']) {
						$row['category'] = $item['subcode'];
					}
					
					$ret[$key] = $row;
                }
                else
                {
                    $item['is_product'] = true;
                    $ret[$key] = $item;
                }
            }
            $merged = array_merge($merged, $ret);
        }

        	$result = $merged;	
		

        return $result;
    }

    public static function AdvancedSearch($e, $cid, $title, $author, $perf, $publisher, $only, $lang, $year, $pp, $page, $binding_id)
    {
		
		//var_dump($binding_id);
		
        $title = trim($title);
        $e = intVal($e);
        $year = intVal($year);
        if(empty($year) || $year <=0 || $year > date('Y')) $year = '';

        if(empty($title) && empty($author) && empty($perf) && empty($publisher) && empty($only) && empty($cid) && empty($lang))
        {
           return array('Items' => array(), 'Paginator' => new CPagination(0));
        }

        $search = self::Create();
        $search->ResetFilters();
        $authorIds = array();
        if (!empty($author))
        {
            $authorResult = self::SearchAuthor($author);
            foreach ($authorResult as $a) $authorIds[] = $a['orig_data']['id'];
            if(empty($authorResult)) $authorIds[] = -1;
        }


        $perfIds = array();
        if(!empty($perf))
        {
            $performerResult = self::SearchInPersons($perf, array('aentity' => Person::ROLE_PERFORMER));
            foreach($performerResult as $p) $perfIds[] = $p['orig_data']['id'];
            if(empty($performerResult)) $perfIds[] = -1;
        }

        $publisherIds = array();
        if (!empty($publisher))
        {
            $publisherResult = self::SearchPublisher($publisher);
            foreach ($publisherResult as $p) $publisherIds[] = $p;
            if(empty($publisherResult)) $publisherIds[] = -1;
        }

        $query = (empty($title)) ? '' : $search->EscapeString($title);
        // for paging
        //if(!empty($binding_id)) $search->SetFilter('binding', array($binding_id));
        $search->SetLimits(0, 1);
		if (!empty($e)) $search->SetFilter('entity', array($e));
        if (!empty($authorIds)) $search->SetFilter('author', $authorIds);
        //if (!empty($perfIds)) $search->SetFilter('author', $perfIds);
        if ($only == '1') $search->SetFilter('avail', array(1));
        if (!empty($publisherIds)) $search->SetFilter('publisher_id', $publisherIds);
        if (!empty($cid)) $search->SetFilter('category', array($cid));
        if(!empty($lang)) $search->SetFilter('language', array(intVal($lang)));
        if(!empty($year)) $search->SetFilter('year', array($year));
        if(!empty($binding_id)) $search->SetFilter('binding', array($binding_id));
        $tmp = $search->query($query, 'products'); // false not to reset filters here
        $totalFound = $tmp['total_found'];

        if($totalFound == 0) return array('Items' => array(), 'Paginator' => new CPagination(0));

        $paginator = new CPagination($totalFound);
        $paginator->setPageSize($pp);

        //echo '<li>'.$paginator->currentPage.' / '.$paginator->pageCount.' = '.$paginator->offset.' ['.$paginator->limit.']';

        $search->ResetFilters();
        // поставить фильтры еще раз TODO: сделать по красивее
        $search->SetLimits($paginator->offset, $pp);
        if (!empty($e)) $search->SetFilter('entity', array($e));
        if (!empty($authorIds)) $search->SetFilter('author', $authorIds);
        //if (!empty($perfIds)) $search->SetFilter('performer', $perfIds);
        if ($only == '1') $search->SetFilter('avail', array(1));
        if (!empty($publisherIds)) $search->SetFilter('publisher_id', $publisherIds);
        if (!empty($cid)) $search->SetFilter('category', array($cid));
        if(!empty($lang)) $search->SetFilter('language', array(intVal($lang)));
        if(!empty($year)) $search->SetFilter('year', array($year));
        if(!empty($binding_id)) $search->SetFilter('binding', array($binding_id));

        $res = $search->query($query, 'products');
        $result = self::ProcessProducts($res);

        return array('Items' => $result, 'Paginator' => $paginator);
    }

    public static function SearchAuthor($query)
    {
        return self::SearchInPersons($query, array('aentity' => Person::ROLE_AUTHOR));
    }

    public static function SearchInPersons($query, $filters = array())
    {
		//$filters = array();
		
        // Не применяем фильтр к авторам ибо в авторах нет ничего
        $result = self::QueryIndex($query, 'authors', $filters);
		//
		
        if (empty($result)) return array();
		
        $roles = array();
        $ids = array();
        $order = array();
        foreach($result as $r)
        {
            $roles[$r['aentity']][$r['real_id']] = $r;
            $ids[] = $r['real_id'];
        }

        $ids = array_unique($ids);
        if (empty($ids)) return array();
		
        $result = self::ProcessPersons($roles, $ids, $filters);

		//var_dump($ids);
		
        return $result;
    }


    static function ProcessPersons($roles, $ids, $filters=array())
    {
        if (!is_array($ids) || count($ids) == 0) return array();

        
	
		if ($ids[0]) {
	$idList = implode(', ', $ids);
        
		$sql = 'SELECT * FROM all_authorslist '
              .'WHERE id IN ('.$idList.') '
              .'ORDER BY FIELD(id, ' . $idList . ')';
		
		
		} else {
			return array();
		}
	
		//return array();
		
        $tmp = Yii::app()->db->createCommand($sql)->queryAll();
        $rows = array();
        foreach($tmp as $row)
        {
            $rows[$row['id']] = $row;
        }
        $ret = array();

        $routes = array(
            Person::ROLE_AUTHOR => 'entity/byauthor',
            Person::ROLE_ACTOR => 'entity/byactor',
            Person::ROLE_DIRECTOR => 'entity/bydirector',
            Person::ROLE_PERFORMER => 'entity/byperformer',
            Person::ROLE_PRODUCER => 'entity/byproducer'
        );

        $titles = array(
            Person::ROLE_AUTHOR => 'YM_FILTER_WRITTEN_BY',
            Person::ROLE_ACTOR => 'YM_FILTER_ACTOR_IS',
            Person::ROLE_DIRECTOR => 'DIRECTOR_IS',
            Person::ROLE_PERFORMER => 'YM_FILTER_PERFORMED_BY',
            Person::ROLE_PRODUCER => 'YM_FILTER_PRODUCED_BY'
        );

        foreach($roles as $role=>$ids)
        {
            $a = 0;
            $key = '';
            switch($role)
            {
                case Person::ROLE_ACTOR :
                case Person::ROLE_AUTHOR : $key = 'aid'; break;
                case Person::ROLE_PRODUCER :
                case Person::ROLE_PERFORMER : $key = 'pid'; break;
                case Person::ROLE_DIRECTOR: $key = 'did'; break;
                default: $key = 'aid'; break;
            }

            foreach($ids as $data)
            {
                $personID = $data['real_id'];
                $row = isset($rows[$personID]) ? $rows[$personID] : null;
                if(empty($row)) continue;

                $roleData = isset($roles[$role][$personID]) ? $roles[$role][$personID] : null;
                if(empty($roleData)) continue;

                $title = ProductHelper::GetTitle($row);
                $entity = $roleData['entity'];
                $data['url'] = Yii::app()->createUrl($routes[$role],
                    array('entity' => Entity::GetUrlKey($entity),
                          'title' => ProductHelper::ToAscii($title),
                          $key => $personID));
                $data['title'] = Entity::GetTitle($entity) . ': <b>' .
                    sprintf(Yii::app()->ui->item($titles[$role]), $title) . '</b>';
                $data['is_product'] = false;
                $data['orig_data'] = $row;
                $ret[] = $data;
            }
        }

        return $ret;
    }

    private static function SearchPublisher($query)
    {
        $search = self::Create();
        $search->ResetFilters();

        $query = '(="' . $search->EscapeString($query) . '")';
        $res = $search->query($query, 'publishers');
        $result = array();

        if ($res['total_found'] > 0)
        {
            $ids = array_keys($res['matches']);
            return $ids;
        }

        return $result;
    }

    public static function LogSearch($uid, $searchQuery, $filters, $searchResults)
    {
        if(empty($uid)) return;
        $sql = 'INSERT INTO users_search_log (uid, query, filters, found, date_of) VALUES (:uid, :query, :filters, :found, NOW())';
        Yii::app()->db->createCommand($sql)->execute(array(':uid' => $uid, ':query' => $searchQuery,
                                                           ':filters' => serialize($filters),
                                                           ':found' => $searchResults));
    }
}

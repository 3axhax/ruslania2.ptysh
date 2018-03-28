<?php

class Product
{
    public function GetProductsForIndex($data)
    {
        $entity = new Entity();
        $entity = $entity->GetEntitiesList();
        $groups = $rows = Yii::app()->queryCache->get('IndexProducts');

        if ($groups === false)
        {
            foreach ($data as $ent => $limit)
            {
                $sql = 'SELECT *, ' . $ent . ' AS entity FROM ' . $entity[$ent]['site_table'] . ' WHERE is_recommended=1 LIMIT ' . $limit;
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
                $groups[$ent] = $rows;
            }
            Yii::app()->queryCache->set('IndexProducts', $groups, Yii::app()->params['DbCacheTime']);
        }

        return $groups;
    }

    public function GetProductsFor($mode)
    {
        $entities = Entity::GetEntitiesList();
        $fields = array('firms' => 'offer_firms',
                        'lib' => 'offer_libraries',
                        'uni' => 'offer_univercity',
        );

        if (!array_key_exists($mode, $fields)) return array();

        $groups = array();
        foreach ($entities as $entity => $data)
        {
            $sql = 'SELECT *, ' . $entity . ' AS entity FROM ' . $data['site_table'] . ' WHERE ' . $fields[$mode] . '=1 ';
            $rows = Yii::app()->queryCache->get($sql);

            if ($rows === false)
            {
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
                Yii::app()->queryCache->set($sql, $rows, Yii::app()->params['DbCacheTime']);
            }

            $groups[$entity] = $rows;
        }
        return $groups;
    }


    public static function Trim($str, $len)
    {
        $ret = substr($str, 0, $len);
        return $ret;
    }

    public static function FlatResult($data, $cacheKey=false)
    {
        $ret = array();
        if(!empty($cacheKey))
        {
            $ret = Yii::app()->queryCacheFlat->get($cacheKey);
            if($ret !== false) return $ret;
        }

        $related = array('binding' => 'Binding',
                         'category' => 'Category',
                         'subcategory' => 'SubCategory',
                         'publisher' => 'Publisher',
        );
        foreach ($data as $d)
        {
            $item = $d->attributes;

            if (isset($d->authors)) foreach ($d->authors as $a) $item['Authors'][] = $a->attributes;
            if (isset($d->performers)) foreach ($d->performers as $a) $item['Performers'][] = $a->attributes;
            if (isset($d->actors)) foreach ($d->actors as $a) $item['Actors'][] = $a->attributes;
            if (isset($d->subtitles)) foreach ($d->subtitles as $a) $item['Subtitles'][] = $a->attributes;
            if (isset($d->directors)) foreach ($d->directors as $a) $item['Directors'][] = $a->attributes;
            if (isset($d->producers)) foreach ($d->producers as $a) $item['Producers'][] = $a->attributes;
            if (isset($d->lookinside)) foreach ($d->lookinside as $a) $item['Lookinside'][] = $a->attributes;
            if (isset($d->series)) $item['Series'] = $d->series->attributes;
            if (isset($d->media)) $item['Media'] = $d->media->attributes;
            if (isset($d->magazinetype)) $item['MagazineType'] = $d->magazinetype->attributes;
            if (isset($d->periodicCountry)) $item['Country'] = $d->periodicCountry->attributes;
            if(isset($d->zone2)) $item['Zone'] = $d->zone2->attributes;
            if(isset($d->languages)) foreach($d->languages as $a) $item['Languages'][] = $a->attributes;
            if(isset($d->offers)) foreach($d->offers as $o)
            {
                if($o['is_active']) $item['Offers'][] = $o->attributes;
            }
            if(isset($d->audiostreams)) foreach($d->audiostreams as $a) $item['AudioStreams'][] = $a->attributes;

            foreach ($related as $key => $name)
            {
                $t = array();
                if (isset($d->$key) && $d->$key != null) $t = $d->$key->attributes;
                $item[$name] = $t;
            }
            if(isset($d->vendorData) && !empty($d->vendorData) && isset($d->vendorData->deliveryTime) && !empty($d->vendorData->deliveryTime))
            {
                $item['DeliveryTime'] = $d->vendorData->deliveryTime->attributes;
            }
            else $item['DeliveryTime'] = false;
            //$item['status'] = Product::GetStatusProduct($item['entity'], $item['id']);

            $ret[] = $item;
        }

        if(!empty($cacheKey))
        {
            Yii::app()->queryCacheFlat->set($cacheKey, $ret, Yii::app()->params['DbCacheTime']);
        }

        return $ret;
    }

    public function GetBaseProductInfo($entity, $id)
    {
        $entities = Entity::GetEntitiesList();
        $data = $entities[$entity];
        $model = $data['model'];

        $model = new $model();
        $product = $model->findByPk($id);

        if (empty($product)) return array();
        $product = $product->attributes;
        $product['entity'] = $entity;
        return $product;
    }

    public function GetProducts($entity, $ids, $isMiniCart = 0)
    {
        $entity = Entity::ConvertToHuman($entity); 
        $entities = Entity::GetEntitiesList(); 
        $table = $entities[$entity]['site_table']; 

        $ids = array_unique($ids);
        
        $sql = 'SELECT *, ' . $entity . ' AS entity  ';
        if ($entity == Entity::PERIODIC)
        {
            $sql .= ', null AS ean_code, 1 AS in_shop, 0 AS unitweight, 1 AS unitweight_skip, '
                    . 'sub_fin_year AS brutto, discount ';
    
            } 
        
        if ($isMiniCart)
        {
            $cart_sql = $sql;
            $ret = array();
            foreach ($ids as $id_miniCart)
            {
                $sql .= ' FROM ' . $table
                . ' WHERE id = (' . $id_miniCart . ') ';
                $rows = Yii::app()->db->createCommand($sql)->queryAll();
                foreach ($rows as $row)
                    $ret[$row['id']] = $row;
                $sql = $cart_sql;
            }
            //CVarDumper::dump($ret, 10, true);
        }
        else
        {
            
            $sql .= ' FROM ' . $table
                . ' WHERE id IN (' . implode(', ', $ids) . ') ';
    
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            $ret = array();
            foreach ($rows as $row)
                $ret[$row['id']] = $row;
        }
        return $ret;
    }

    public function GetProductsV2($entity, $ids, $indexByPK=false)
    {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->addInCondition('t.id', $ids);

        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();
        if (empty($data)) return false;

        $data = Product::FlatResult($data);
        $ret = array();
        foreach($data as $idx=>$item)
        {
            $data[$idx]['entity'] = $entity;
            if($indexByPK)
            {
                $ret[$item['id']] = $data[$idx];
            }
        }

        if($indexByPK) return $ret;
        return $data;
    }

    public function GetProduct($entity, $id)
    {
        $dp = Entity::CreateDataProvider($entity);
        $criteria = $dp->getCriteria();
        $criteria->addCondition('t.id=:id');
        $criteria->params[':id'] = $id;

        $dp->setCriteria($criteria);
        $dp->pagination = false;

        $data = $dp->getData();

        if (empty($data)) return false;

        $data = Product::FlatResult($data);
        $data = $data[0];
        $data['entity'] = $entity;
        $data['status'] = Product::GetStatusProduct($entity, $id);
        return $data;
    }

    /* Получаем статус продука ("Новинка", "Акция", "В подборке") */
    public function GetStatusProduct($entity, $id)
    {
        $status = self::GetStatusProductAction($entity, $id);
        if(!$status) $status = self::GetStatusProductOffer($entity, $id);
        //$status = self::GetStatusProductOffer($entity, $id);
        return $status;
    }
    /* Получаем статус продука из таблицы "action_items" ("Новинка", "Акция") */
    private function GetStatusProductAction($entity, $id)
    {
        $status = false;
        $sql = 'SELECT * FROM `action_items` WHERE `item_id` = '.$id.' AND `entity` = '.$entity;
        $row = Yii::app()->db->createCommand($sql)->queryAll();
        if ($row && ($row[0]['type'] == 2)) $status = 'sale';
        if ($row && ($row[0]['type'] == 1)) $status = 'new';
        return $status;
    }
    /* Получаем статус продука из таблицы "offer_items" ("В подборке") */
    private function GetStatusProductOffer($entity, $id)
    {
        $status = false;
        $sql = 'SELECT * FROM `offer_items` WHERE `item_id` = '.$id.' AND `entity_id` = '.$entity;
        $row = Yii::app()->db->createCommand($sql)->queryAll();
        if ($row) $status = 'recommend';
        return $status;
    }

    public function IsQuantityAvailForOrder($entity, $id, $quantity)
    {
        $product = $this->GetProduct($entity, $id);
        if(empty($product)) return 0;

        // Для периодики, если это не 3 6 и 12 месяцев - то вернуть 12
        if($entity == Entity::PERIODIC)
        {
            $availQty = array(12);
            $ie = $product['issues_year'];
            $oneMonth = $ie / 12;

            $tmp1 = $oneMonth * 3;
            if(ctype_digit("$tmp1")) array_push($availQty, 3);
            $tmp1 = $oneMonth * 6;
            if(ctype_digit("$tmp1")) array_push($availQty, 6);

            if(!in_array($quantity, $availQty)) return 12;
            return $quantity;
        }

        // Логика такая: если есть галочка econet_skip, то можно заказывать сколько угодно шт.
        // если галочка econet_skip снята, то можно заказать максимум столько шт, сколько у нас в реальном количестве (поле in_shop)
        if(array_key_exists('econet_skip', $product) && array_key_exists('in_shop', $product))
        {
            if($product['econet_skip'] == 0 && $product['in_shop'] < $quantity)
                return $product['in_shop'];
            return $quantity;
        }
        else return 0;
    }
	
	public function related_goods($cid, $entity, $id, $title, $series_id, $author_id) {
		
		$title = addslashes($title);
		
		$arrLang = array('ru', 'en', 'fi', 'rut');
		
		$ln = Yii::app()->language;
		
		if (!in_array(Yii::app()->language, $arrLang)) {
			
			$ln = en;
			
		}
		
		if ($entity == 60)
            return array();
		$entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $tbl_author = $entities[$entity]['author_table'];
        $field = $entities[$entity]['author_entity_field'];
        
		if ($series_id) {
			$arr[] = '(series_id = '.$series_id.')';
		}
		
		$arr[] = '( id IN (SELECT ' . $field . ' FROM ' . $tbl_author . ' WHERE '. $author_id .') AND title_'.$ln.' LIKE \'%'.$title.'%\')';
		$arr[] = '( id IN (SELECT ' . $field . ' FROM ' . $tbl_author . ' WHERE '. $author_id .'))';
		if ($cid) {
			$arr[] = '(`code` = '.$cid.')';
		}
		
		
		
           $sql = 'SELECT
					id
					FROM
					' . $tbl . '
					WHERE 
					image <> "" AND 
					id <> '.$id.' AND ( 
					'.implode(' OR ', $arr).')
					ORDER BY `year` DESC, `add_date` DESC LIMIT 10';
			if ($entity == 30) {
				 $sql = 'SELECT
					id
					FROM
					' . $tbl . '
					WHERE 
					id <> '.$id.' AND (title_'.$ln.' LIKE \'%'.$title.'%\' OR `code` = '.$cid.')
					ORDER BY `add_date` DESC LIMIT 10';
			}
			
			if ($entity == 40) {
				
				$arr = array();
				
				if ($series_id) {
					$arr[] = '(series_id = '.$series_id.')';
				}
				
				$author_id = str_replace('author_id','actor_id', $author_id);
				
				$arr[] = '( id IN (SELECT `video_id` FROM `video_actors` WHERE '. $author_id .') AND title_'.$ln.' LIKE \'%'.$title.'%\')';
				//$arr[] = '';
				$arr[] = '(`code` = '.$cid.')';
				
				$sql = 'SELECT
					id
					FROM
					' . $tbl . '
					WHERE 
					id <> '.$id.' AND ( 
					'.implode(' OR ', $arr).')
					ORDER BY `year` DESC, `add_date` DESC LIMIT 10';
				
			}
			
			 $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array());
			
			$sql2 = 'SELECT * FROM `similar_items` WHERE `item_id` = '.$id.' AND `item_entity` = '.$entity.' LIMIT 10';
			
			$rows2 = Yii::app()->db->createCommand($sql2)->queryAll(true, array());
			
			$arrItemsManager = [];
			$rows4 = array();
			foreach ($rows2 as $item) {
				
				$tbl = $entities[$item['similar_entity']]['site_table'];
			
				$sql_items = 'SELECT
					id
					FROM
					' . $tbl . '
					WHERE 
					id <> '.$id.' AND id = '.$item['similar_id'].' LIMIT 1';
					
					$rows3 = Yii::app()->db->createCommand($sql_items)->queryAll(true, array());
					
					
					
					foreach ($rows3 as $it) {
						$rows4[] = array(
						'entity'=>$item['similar_entity'],
						'id'=>$it['id']
						);
					}
					
					//$arrItemsManager = array_merge($arrItemsManager, $rows4);
					
			}
			
			//file_put_contents($_SERVER['DOCUMENT_ROOT']. '/1.log', print_r($rows4,1));
	   

        return array_merge($rows4,$rows);
	
	}
	
	public function is_lang($lang, $cat_id = '', $entity) {
		
		$entities = Entity::GetEntitiesList();
					$tbl = $entities[$entity]['site_table'];
					
					$sql = 'SELECT ln.id FROM `all_items_languages` AS ail, `languages` AS ln, `'.$tbl.'` AS t WHERE ail.language_id = '.$lang.' AND
					ail.entity = '.$entity.' AND
					ail.item_id = t.id';
					
					if ($cat_id != '') {
					
						$sql .= ' AND (t.code = '.$cat_id.' OR t.subcode = '.$cat_id.')';
					
					}
					
					$rows = Yii::app()->db->createCommand($sql)->queryAll(true, array());
					
					//var_dump($sql);
					
					return count($rows);
		
	}

}

/*
 *
DROP VIEW all_products;
CREATE VIEW all_products AS
SELECT 100000000+id AS id, id AS real_id, 10 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM books_catalog
UNION ALL
SELECT 150000000+id AS id, id AS real_id, 15 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM musicsheets_catalog
UNION ALL
SELECT 200000000+id AS id, id AS real_id, 20 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM audio_catalog
UNION ALL
SELECT 220000000+id AS id, id AS real_id, 22 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM music_catalog
UNION ALL
SELECT 240000000+id AS id, id AS real_id, 24 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM soft_catalog
UNION ALL
SELECT 300000000+id AS id, id AS real_id, 30 AS entity, in_stock, in_shop, 1 AS econet_skip, NULL AS publisher_id, NULL AS isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, 0 AS year
FROM pereodics_catalog
UNION ALL
SELECT 400000000+id AS id, id AS real_id, 40 AS entity, in_stock, in_shop, econet_skip, NULL AS publisher_id, NULL AS isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM video_catalog
UNION ALL
SELECT 500000000+id AS id, id AS real_id, 50 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM printed_catalog
UNION ALL
SELECT 600000000+id AS id, id AS real_id, 60 AS entity, in_stock, in_shop, econet_skip, publisher_id, isbn, title_ru, title_en, title_rut, title_fi, stock_id, eancode, description_ru, description_en, description_fi, description_rut, year
FROM maps_catalog



 */
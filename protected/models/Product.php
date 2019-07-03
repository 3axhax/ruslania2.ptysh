<?php

class Product
{
    private static $_actionItems = array(), $_offerItems = array();//чтоб много раз не выполнять запрос в списке товаров

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

        $forHref = array();
        foreach ($data as $d)
        {
            $item = $d->attributes;
            $entity = $d->getEntity();
            if (!isset($forHref[$entity])) $forHref[$entity] = array();
            if (!isset($forHref[$entity]['product/view'])) $forHref[$entity]['product/view'] = array();
            if (!empty($item['id'])) $forHref[$entity]['product/view'][] = $item['id'];

            if (isset($d->authors)) {
                if (!isset($forHref[$entity]['entity/byauthor'])) $forHref[$entity]['entity/byauthor'] = array();
                foreach ($d->authors as $a) {
                    $attrs = $a->attributes;
                    if (!empty($attrs['id'])) $forHref[$entity]['entity/byauthor'][] = $attrs['id'];
                    $item['Authors'][] = $attrs;
                }
            }
            if (isset($d->performers)) {
                if (!isset($forHref[$entity]['entity/byperformer'])) $forHref[$entity]['entity/byperformer'] = array();
                foreach ($d->performers as $a) {
                    $attrs = $a->attributes;
                    if (!empty($attrs['id'])) $forHref[$entity]['entity/byperformer'][] = $attrs['id'];
                    $item['Performers'][] = $attrs;
                }
            }
            if (isset($d->actors)) {
                if (!isset($forHref[$entity]['entity/byactor'])) $forHref[$entity]['entity/byactor'] = array();
                foreach ($d->actors as $a) {
                    $attrs = $a->attributes;
                    if (!empty($attrs['id'])) $forHref[$entity]['entity/byactor'][] = $attrs['id'];
                    $item['Actors'][] = $attrs;
                }
            }
            if (isset($d->subtitles)) {
                if (!isset($forHref[$entity]['entity/bysubtitle'])) $forHref[$entity]['entity/bysubtitle'] = array();
                foreach ($d->subtitles as $a) {
                    $attrs = $a->attributes;
                    if (!empty($attrs['id'])) $forHref[$entity]['entity/bysubtitle'][] = $attrs['id'];
                    $item['Subtitles'][] = $attrs;
                }
            }
            if (isset($d->directors)) {
                if (!isset($forHref[$entity]['entity/bydirector'])) $forHref[$entity]['entity/bydirector'] = array();
                foreach ($d->directors as $a) {
                    $attrs = $a->attributes;
                    if (!empty($attrs['id'])) $forHref[$entity]['entity/bydirector'][] = $attrs['id'];
                    $item['Directors'][] = $attrs;
                }
            }
            if (isset($d->producers)) {
                foreach ($d->producers as $a) $item['Producers'][] = $a->attributes;
            }
            if (isset($d->lookinside)) foreach ($d->lookinside as $a) $item['Lookinside'][] = $a->attributes;
            if (isset($d->series)) {
                if (!isset($forHref[$entity]['entity/byseries'])) $forHref[$entity]['entity/byseries'] = array();
                $attrs = $d->series->attributes;
                if (!empty($attrs['id'])) $forHref[$entity]['entity/byseries'][] = $attrs['id'];
                $item['Series'] = $attrs;
            }
            if (isset($d->media)) {
                if (!isset($forHref[$entity]['entity/bymedia'])) $forHref[$entity]['entity/bymedia'] = array();
                $attrs = $d->media->attributes;
                if (!empty($attrs['id'])) $forHref[$entity]['entity/bymedia'][] = $attrs['id'];
                $item['Media'] = $attrs;
            }
            if (isset($d->videoStudio)) {
                if (!isset($forHref[$entity]['entity/bystudio'])) $forHref[$entity]['entity/bystudio'] = array();
                $attrs = $d->videoStudio->attributes;
                if (!empty($attrs['id'])) $forHref[$entity]['entity/bystudio'][] = $attrs['id'];
                $item['videoStudio'] = $attrs;
            }
            if (isset($d->magazinetype)) {
                if (!isset($forHref[$entity]['entity/bymagazinetype'])) $forHref[$entity]['entity/bymagazinetype'] = array();
                $attrs = $d->magazinetype->attributes;
                if (!empty($attrs['id'])) $forHref[$entity]['entity/bymagazinetype'][] = $attrs['id'];
                $item['MagazineType'] = $attrs;
            }
            if (isset($d->periodicCountry)) $item['Country'] = $d->periodicCountry->attributes;
            if(isset($d->zone2)) $item['Zone'] = $d->zone2->attributes;
            if(isset($d->languages)) foreach($d->languages as $a) $item['Languages'][] = $a->attributes;
            if(isset($d->offers)) foreach($d->offers as $o)
            {
                if($o['is_active']) $item['Offers'][] = $o->attributes;
            }
            if(isset($d->audiostreams)) foreach($d->audiostreams as $a) $item['AudioStreams'][] = $a->attributes;

            foreach ($related as $key => $name) {
                $t = array();
                if (isset($d->$key) && $d->$key != null) {
                    $t = $d->$key->attributes;
                    switch ($key) {
                        case 'binding':
                            if (!isset($forHref[$entity]['entity/bybinding'])) $forHref[$entity]['entity/bybinding'] = array();
                            if (!empty($t['id'])) $forHref[$entity]['entity/bybinding'][] = $t['id'];
                            break;
                        case 'category':
                        case 'subcategory':
                            if (!isset($forHref[$entity]['entity/list'])) $forHref[$entity]['entity/list'] = array();
                            if (!empty($t['id'])) $forHref[$entity]['entity/list'][] = $t['id'];
                            break;
                        case 'publisher':
                            if (!isset($forHref[$entity]['entity/bypublisher'])) $forHref[$entity]['entity/bypublisher'] = array();
                            if (!empty($t['id'])) $forHref[$entity]['entity/bypublisher'][] = $t['id'];
                            break;
                    }
                }
                $item[$name] = $t;
//                $related = array('binding' => 'Binding',
//                    'category' => 'Category',
//                    'subcategory' => 'SubCategory',
//                    'publisher' => 'Publisher',
//                );
            }
            if(isset($d->vendorData) && !empty($d->vendorData) && isset($d->vendorData->deliveryTime) && !empty($d->vendorData->deliveryTime))
            {
                $item['DeliveryTime'] = $d->vendorData->deliveryTime->attributes;
            }
            else $item['DeliveryTime'] = false;

            /*$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $real_price = $price[DiscountManager::WITH_VAT_WORLD];
            $item['real_price'] = $real_price;*/

            $ret[] = $item;
        }

        foreach ($forHref as $entity=>$routes) {
            foreach ($routes as $route=>$ids) {
                if ($ids) {
                    $ids = array_unique($ids);
                    if ($route == 'product/view') {
                        Product::setActionItems($entity, $ids);
                        Product::setOfferItems($entity, $ids);
                    }
                    HrefTitles::get()->getByIds($entity, $route, $ids);
                }
            }
        }

        if(!empty($cacheKey))
        {
            Yii::app()->queryCacheFlat->set($cacheKey, $ret, Yii::app()->params['DbCacheTime']);
        }

        return $ret;
    }

    public function GetBaseProductInfo($entity, $id)
    {
        if (!Entity::IsValid($entity)) return array();

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
        if (!Entity::IsValid($entity)) return array();

        $entity = Entity::ConvertToHuman($entity);
        $entities = Entity::GetEntitiesList();
        $table = $entities[$entity]['site_table'];

        $ids = array_unique($ids);
		
		
		//echo (implode(',', $ids) . '<br />');
		

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
                foreach ($rows as $row) {
                    $row['entity'] = $entity;
                    $ret[$row['id']] = $row;
                }
                $sql = $cart_sql;
            }
            //CVarDumper::dump($ret, 10, true);
        }
        else
        {
			
			//echo $entity . ',';
			//так надо, что бы в корзине правильно отображались сроки доставки
             $dp = Entity::CreateDataProvider($entity);
             $criteria = $dp->getCriteria();
             $criteria->alias = 't';
             $criteria->addCondition('t.id in (' . implode(',', $ids) . ')');
             $criteria->order = '';
             $dp->setCriteria($criteria);
             $dp->pagination = false;
//
             $data = $dp->getData();
             $rows = Product::FlatResult($data);
//           else {
//               $sql .= ' FROM ' . $table
//                   . ' WHERE id IN (' . implode(', ', $ids) . ') ';
//
//               $rows = Yii::app()->db->createCommand($sql)->queryAll();
//           }

            $ret = array();
            foreach ($rows as $row) {
                $row['entity'] = $entity;
                $ret[$row['id']] = $row;
            }
        }
        return $ret;
    }
	
	public function getProducts3($entity, $ids)
    {
        if (!Entity::IsValid($entity)) return array();
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
        $sql .= ' FROM ' . $table
            . ' WHERE id IN (' . implode(', ', $ids) . ') ';

        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        $ret = array();
        foreach ($rows as $row)
            $ret[$row['id']] = $row;
        return $ret;
    }
	
    public function GetProductsV2($entity, $ids, $indexByPK=false)
    {
        if (!Entity::IsValid($entity)) return false;

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
        if (!Entity::IsValid($entity)) return false;

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
        if (!empty($data['presale'])) {
            //последний день месяца предзаказа 23:59:59
            $datePresale = mktime(23, 59, 59, date('m', $data['presale'])+1, -1, date('Y', $data['presale']));
            if ($datePresale > time()) {

                $data['presaleMessage'] = Yii::app()->ui->item('PRESALE_MSG');
                $data['presaleMessage'] .= ': ' . Yii::app()->ui->item('A_NEW_SUBS_MONTH' . date('n', $datePresale)) . ' ' . date('Y', $datePresale);
            }
/*            $sql = ''.
                'select date_release '.
                'from presales '.
                'where (item_id = ' . (int) $id . ') and (entity_id = ' . (int) $entity . ') '.
                'limit 1 ' .
            '';
            $dateRelease = Yii::app()->db->createCommand($sql)->queryScalar($sql);
            $data['presaleMessage'] = Yii::app()->ui->item('PRESALE_MSG');
            if (!empty($dateRelease)) {
                $dt = new DateTime($dateRelease);
                $data['presaleMessage'] .= ' ' . $dt->format('m/Y') . '.';
            }*/
        }
        return $data;
    }

    /* Получаем статус продука ("Новинка", "Акция", "В подборке") */
    public function GetStatusProduct($entity, $id)
    {
        if (!Entity::IsValid($entity)) return false;

        $status = self::GetStatusProductAction($entity, $id);
        if(!$status) $status = self::GetStatusProductOffer($entity, $id);
        //$status = self::GetStatusProductOffer($entity, $id);
        return $status;
    }

    static function setActionItems($entity, $ids) {
        if (!Entity::IsValid($entity)) return;

        $sql = 'select * from action_items where (entity = ' . (int) $entity . ') and (item_id in (' . implode(',', $ids) . '))';
        if (!isset(self::$_actionItems[$entity])) self::$_actionItems[$entity] = array();
        foreach ($ids as $id) self::$_actionItems[$entity][$id] = array();
        if (!empty($ids)) {
            foreach(Yii::app()->db->createCommand($sql)->queryAll() as $row) {
                self::$_actionItems[$entity][$row['item_id']] = $row;
            }
        }
    }

    static function setOfferItems($entity, $ids) {
        if (!Entity::IsValid($entity)) return;

        $sql = 'select oi.* from offer_items as oi
        join offers as o on (o.id = oi.offer_id)
        where (o.is_active = 1) and (oi.entity_id = ' . (int) $entity . ') and (oi.item_id in (' . implode(',', $ids) . '))';
        if (!isset(self::$_offerItems[$entity])) self::$_offerItems[$entity] = array();
        foreach ($ids as $id) self::$_offerItems[$entity][$id] = array();
        if (!empty($ids)) {
            foreach (Yii::app()->db->createCommand($sql)->queryAll() as $row) {
                self::$_offerItems[$entity][$row['item_id']] = $row;
            }
        }
    }

    /* Получаем статус продука из таблицы "action_items" ("Новинка", "Акция") */
    private function GetStatusProductAction($entity, $id)
    {
        if (!Entity::IsValid($entity)) return false;

        if (!empty(self::$_actionItems[$entity])&&isset(self::$_actionItems[$entity][$id])) {
            $row = array(self::$_actionItems[$entity][$id]);
        }
        else {
            $sql = 'SELECT * FROM `action_items` WHERE `item_id` = '.$id.' AND `entity` = '.$entity;
            $row = Yii::app()->db->createCommand($sql)->queryAll();
        }
        $status = false;
        if (!empty($row[0]['type'])) {
            switch ((int)$row[0]['type']) {
                case 2: $status = 'sale'; break;
                case 1: $status = 'new'; break;
            }
        }
//        if (!empty($row[0]['type']) && ($row[0]['type'] == 2)) $status = 'sale';
//        if ($row && ($row[0]['type'] == 1)) $status = 'new';
        return $status;
    }
    /* Получаем статус продука из таблицы "offer_items" ("В подборке") */
    private function GetStatusProductOffer($entity, $id)
    {
        if (!Entity::IsValid($entity)) return false;

        if (/*false && */!empty(self::$_offerItems[$entity])&&isset(self::$_offerItems[$entity][$id])) {
            $row = array(self::$_offerItems[$entity][$id]);
        }
        else {
            //$sql = 'SELECT * FROM `offer_items` WHERE `item_id` = '.$id.' AND `entity_id` = '.$entity;
            $sql = sprintf("SELECT oi.id
                        FROM `offer_items` as oi
                        JOIN (select id from offers where is_active = 1) as of ON of.id = oi.offer_id
                        WHERE `item_id` = %d AND `entity_id` = %d",
                $id, $entity);
            $row = Yii::app()->db->createCommand($sql)->queryAll();
        }
        $status = false;
        if (!empty($row[0])) $status = 'recommend';
        return $status;
    }

    public function IsQuantityAvailForOrder($entity, $id, $quantity)
    {
        if (!Entity::IsValid($entity)) return 0;

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
        if (!Entity::IsValid($entity)) return array();


        $title = addslashes($title);

        $arrLang = array('ru', 'en', 'fi', 'rut');

        $ln = Yii::app()->language;

        if (!in_array(Yii::app()->language, $arrLang)) {

            $ln = en;

        }

        if (!Entity::checkEntityParam($entity, 'authors')) return array();

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
        if (!Entity::IsValid($entity)) return 0;


        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];

        //совсем не правильный запрос
//					$sql = 'SELECT ln.id FROM `all_items_languages` AS ail, `languages` AS ln, `'.$tbl.'` AS t WHERE ail.language_id = '.$lang.' AND
//					ail.entity = '.$entity.' AND
//					ail.item_id = t.id';
//        if ($cat_id != '') {
//
//            $sql .= ' AND (t.code = '.$cat_id.' OR t.subcode = '.$cat_id.')';
//
//        }

        $condition = array(
            'language_id'=>'(tL.language_id = ' . (int)$lang . ')',
            'entity'=>'(tL.entity = ' . (int)$entity . ')',
        );
        $join = array();

        $cat_id = (int) $cat_id;
        if ($cat_id > 0) {
            $join['t'] = 'join `' . $tbl . '` t on (t.id = tL.item_id) and ((t.code = ' . $cat_id . ') OR (t.subcode = ' . $cat_id . ')) and (t.avail_for_order > 0)';
        }
        $sql = ''.
            'select count(*) '.
            'from all_items_languages tL '.
            implode(' ', $join) . ' '.
            'where ' . implode(' and ', $condition) . ' '.
            '';


//					$rows = Yii::app()->db->createCommand($sql)->queryScalar(array());

        //var_dump($sql);

        return (int) Yii::app()->db->createCommand($sql)->queryScalar(array());

    }

    /**
     * @param $uid int ид клиента
     * @param $eid int ид раздела
     * @param $iid int ид товара
     * @return bool|DateTime
     */
    static function isPurchased($uid, $eid, $iid) {
        $sql = ''.
            'select min(tP.timestamp) date_buy '.
            'from users_orders t '.
//                'join users_orders_states tUOS on (tUOS.oid = t.id) and (tUOS.state in (2,8,17,4)) './/статус заказа - оплачен
                'left join (select t1.id from users_orders t1 join users_orders_states t2 on (t2.oid = t1.id) and (t2.state in (5, 16)) where (t1.uid = ' . (int) $uid . ') group by t1.id) tUOS using (id) '. //заказ отменен
                'join users_orders_items tUOI on (tUOI.oid = t.id) and (tUOI.entity = ' . (int) $eid . ') and (tUOI.iid = ' . (int) $iid . ') '.
                'join users_orders_states tP on (tP.oid = t.id) '.
            'where (t.uid = ' . (int) $uid . ') and (tUOS.id is null) '.
            'group by t.id '.
            'order by min(tP.timestamp) desc '.
            'limit 1 '.
        '';
        $dateBuy = Yii::app()->db->createCommand($sql)->queryScalar();
        if (empty($dateBuy)) return false;

        return new DateTime($dateBuy);
    }

    /**
     * @param $uid int ид клиента
     * @param $eid int ид раздела
     * @param $iid int ид товара
     * @return array
     */
    static function purchasedOrders($uid, $eid, $iid) {
        $sql = ''.
            'select t.id '.
            'from users_orders t '.
//                'join users_orders_states tUOS on (tUOS.oid = t.id) and (tUOS.state in (2,8,17,4)) './/статус заказа - оплачен
                'left join (select t1.id from users_orders t1 join users_orders_states t2 on (t2.oid = t1.id) and (t2.state in (5, 16)) where (t1.uid = ' . (int) $uid . ') group by t1.id) tUOS using (id) '. //заказ отменен
                'join users_orders_items tUOI on (tUOI.oid = t.id) and (tUOI.entity = ' . (int) $eid . ') and (tUOI.iid = ' . (int) $iid . ') '.
                'join users_orders_states tP on (tP.oid = t.id) '.
            'where (t.uid = ' . (int) $uid . ') and (tUOS.id is null) '.
            'group by t.id '.
            'order by min(tP.timestamp) desc '.
        '';
        $orderIds = Yii::app()->db->createCommand($sql)->queryColumn();
        if (empty($orderIds)) return array();
        return $orderIds;
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
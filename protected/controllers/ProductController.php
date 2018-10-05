<?php

class ProductController extends MyController
{
    public function actionView($entity, $id)
    {

		$entity = Entity::ParseFromString($entity);
        if($entity === false) throw new CHttpException(404);

        $product = new Product();
        $data = $product->GetProduct($entity, $id);

	    if(empty($data)) throw new CHttpException(404);

	    $this->_checkUrl($data);
	    
        $c = new Cart;
        $cart = $c->GetCart($this->uid, $this->sid);
        foreach($cart as $item)
        {
            if($entity == $item['entity'] && $item['id'] == $data['id'])
            {
                $data['AlreadyInCart'] = $item['quantity'];
            }
        }
		
		//ноты
		
		if ( $entity == 15) { //ноты
			
			SEO::seo_change_meta_sheets_view($data, $entity);
			
		} elseif ( $entity == 10) { //книги

			SEO::seo_change_meta_books_view($data, $entity);
			
		}elseif ( $entity == 30) { //периодика
			
			SEO::seo_change_meta_periodic_view($data, $entity);
			
		} elseif ( $entity == 22) { //музыка

            SEO::seo_change_meta_music_view($data, $entity);

		} else { //остальные
            SEO::seo_change_meta_other_view($data, $entity);
        }

        $title = Entity::GetTitle($entity, Yii::app()->language);
        $this->breadcrumbs[$title] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));

        $keys = array(
            Entity::AUDIO => 'audio',
            Entity::BOOKS => 'book',
            Entity::MAPS => 'map',
            Entity::MUSIC => 'music',
            Entity::PERIODIC => 'periodicals',
            Entity::PRINTED => 'printed',
            Entity::SOFT => 'soft',
            Entity::VIDEO => 'video',
            Entity::SHEETMUSIC => 'book',
        );
		
//		$get_cats = Category::get_count_categories_bread($id, $entity);
//
//		if (count($get_cats) == 0) {
//
//			$entities = Entity::GetEntitiesList();
//			$tbl = $entities[$entity]['site_table'];
//
//			$sql = 'SELECT `code`, `subcode` FROM ' . $tbl . ' WHERE id='.$id;
//
//			$rows = Yii::app()->db->createCommand($sql)->queryAll();
//
//			$code = (int) $rows[0]['code'];
//
//			if (!$rows[0]['code']) {
//				$code = $rows[0]['subcode'];
//			}

			$code = (int) $data['code'];

			if (empty($code)) {
				$code = (int) $data['subcode'];
			}
			$arr = Category::getCatsBreadcrumbs($entity, $code);
			
			foreach($arr as $a) {
				
				$title2 = ProductHelper::GetTitle($a);
				
				$this->breadcrumbs[$title2] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $a['id'], 'title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($a))));
				
			}
			
			$this->breadcrumbs[] = ProductHelper::GetTitle($data);
			
//		} else {
//			$this->breadcrumbs[] = ProductHelper::GetTitle($data);
//		}
		
        
        //

		if (($entity == Entity::PERIODIC) && (!empty($data['issues_year'])))
        {
            $data['issues_year'] = Periodic::getCountIssues($data['issues_year']);
        }

        $this->render('view', array('item' => $data, 'entity' => $entity));
    }

	private function _checkUrl($item) {
		if (empty($item)) return;

		$path = urldecode(getenv('REQUEST_URI'));
		$ind = mb_strpos($path, "?", null, 'utf-8');
		$query = '';
		if ($ind !== false) {
			$query = mb_substr($path, $ind, null, 'utf-8');
			$path = substr($path, 0, $ind);
		}

		$this->_canonicalPath = ProductHelper::CreateUrl($item, Yii::app()->language);
		if ((mb_strpos($this->_canonicalPath, '?') !== false)&&!empty($query)) $query = '&' . mb_substr($query, 1, null, 'utf-8');
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				if ($lang === Yii::app()->language) $this->_otherLangPaths[$lang] = $this->_canonicalPath;
				else $this->_otherLangPaths[$lang] = ProductHelper::CreateUrl($item, $lang);
			}
		}
		$canonicalPath = $this->_canonicalPath;
		$ind = mb_strpos($canonicalPath, "?", null, 'utf-8');
		if ($ind !== false) {
			$canonicalPath = mb_substr($canonicalPath, 0, $ind, 'utf-8');
		}
		if ($canonicalPath === $path) return;

		$this->_redirectOldPages($path, $this->_canonicalPath, $query, array('entity'=>$item['entity'], 'id'=>$item['id']));
		throw new CHttpException(404);
	}
}
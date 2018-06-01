<?php

class ProductController extends MyController
{
    public function actionView($entity, $id)
    {

		$entity = Entity::ParseFromString($entity);
        if($entity === false) throw new CHttpException(404);

        $product = new Product();
        $data = $product->GetProduct($entity, $id);

	    if (($realPath = $this->_checkUrl($data)) !== false) {
		    $this->redirect($realPath, true, 301);
	    }

        $c = new Cart;
        $cart = $c->GetCart($this->uid, $this->sid);
        foreach($cart as $item)
        {
            if($entity == $item['entity'] && $item['id'] == $data['id'])
            {
                $data['AlreadyInCart'] = $item['quantity'];
            }
        }
		
		switch ($entity) {
			case 10:
				if ($data['isbn']) {
					$this->pageTitle = ProductHelper::GetTitle($data) . ' - ' . $data['isbn'];
				} else {
					$this->pageTitle = ProductHelper::GetTitle($data);
				}
			break;
			case 30:
				
				if ($data['MagazineType']['id']) {
					$this->pageTitle = ProductHelper::GetTitle($data['MagazineType']) . ' - ' .ProductHelper::GetTitle($data) . " - Подписка";
				} else {
					$this->pageTitle = ProductHelper::GetTitle($data);
				}
			
			break;
			case 22:
			
				//var_dump($data['Media']);
			
				if ($data['Media']) {
					$this->pageTitle = ProductHelper::GetTitle($data) . ' - ' . $data['Media']['title'] . " - купить онлайн";
				} else {
					$this->pageTitle = ProductHelper::GetTitle($data);
				}
			break;
			
			case 40:
			
				//var_dump($data['Media']);
			
				if ($data['Media']) {
					$this->pageTitle = ProductHelper::GetTitle($data) . ' - ' . $data['Media']['title'] . " - купить онлайн";
				} else {
					$this->pageTitle = ProductHelper::GetTitle($data);
				}
			break;
			
			case 50:
			case 60:
			case 24:
			
			
					$this->pageTitle = ProductHelper::GetTitle($data) . " - купить онлайн";
				
			break;
		}
		
		//остальные
		
		if (Yii::app()->language == 'ru') {
				
				$isbn = '';
				
				if ($data['isbn']) {
					
					$isbn = ','.$data['isbn'];
					
				} elseif ($data['eancode']) {
					
					$isbn = ','.$data['eancode'];
					
				}
				
				$this->pageTitle = ProductHelper::GetTitle($data) . $isbn . ' - купить онлайн в интернет-магазине Ruslania.com';
				
				$this->pageDescription = ProductHelper::GetTitle($data).' - купить онлайн с доставкой по всему миру в интернет-магазине Руслания.';
				
			} else {
				
				$isbn = '';
				
				if ($data['isbn']) {
					
					$isbn = ','.$data['isbn'];
					
				} elseif ($data['eancode']) {
					
					$isbn = ','.$data['eancode'];
					
				}
				
				$this->pageTitle = ProductHelper::GetTitle($data) . $isbn . ' - buy online at Ruslania.com';
				
				$this->pageDescription = ProductHelper::GetTitle($data).' - buy online with worldwide delivery at Ruslania.com.';
				
			}
		
		
		//ноты
		
		if ( $entity == 15) {
			
			if (Yii::app()->language == 'ru') {
			
				$this->pageTitle = ProductHelper::GetTitle($data).' | '.$data['isbn'].' | Купить онлайн';
				
				$this->pageDescription = ProductHelper::GetTitle($data).', '.ProductHelper::GetTitle($data['Authors'][0]).', '.$data['isbn'].' - купить онлайн с доставкой по всему миру.';
				
			} else {
				
				$this->pageTitle = ProductHelper::GetTitle($data).' | '.$data['isbn'].' | Buy online';
				
				$this->pageDescription = ProductHelper::GetTitle($data).' by '.ProductHelper::GetTitle($data['Authors'][0]).', '.$data['isbn'].' - buy sheet music online at Ruslania with worldwide delivery.';
				
			}
			
		}
		
		
		//книги
		
		if ( $entity == 10) {
			
			if (Yii::app()->language == 'ru') {
			
				$this->pageTitle = ProductHelper::GetTitle($data). ' | '.ProductHelper::GetTitle($data['Authors'][0]).' | '.$data['isbn'].' | Купить книгу';
				
				$row = Binding::GetBinding($entity, $data['binding_id']);
				
				$this->pageDescription = ProductHelper::GetTitle($data). ', '.ProductHelper::GetTitle($data['Authors'][0]).', '.$data['isbn'].', '.$row['title_' . Yii::app()->language].' - купить онлайн с доставкой по всему миру.';
				
			} else {
				
				$this->pageTitle = ProductHelper::GetTitle($data). ' | '.ProductHelper::GetTitle($data['Authors'][0]).' | '.$data['isbn'].' | Buy book online';
				
				$row = Binding::GetBinding($entity, $data['binding_id']);
				
				$this->pageDescription = ProductHelper::GetTitle($data). ', '.ProductHelper::GetTitle($data['Authors'][0]).', '.$data['isbn'].', '.$row['title_' . Yii::app()->language].', Russian edition, buy online at Ruslania with worldwide delivery.';
				
			}
			
		}
		
		//Периодика
		
		if ( $entity == 30) {
			
			if (Yii::app()->language == 'ru') {
				
				$binding = ProductHelper::GetTypes($entity, $data['type']);
				
				$this->pageTitle = 'Подписка на '.ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' - русское издание';
				
				$row = Binding::GetBinding($entity, $data['binding_id']);
				
				$subsisbn='';
				if ($data['isbn']) {
					$subsisbn = ', '.$data['isbn'];
				}
				
				$this->pageDescription = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). $subsisbn . ' - подписаться онлайн.';
				
			} else {
				
				$binding = ProductHelper::GetTypes($entity, $data['type']);
				
				$this->pageTitle = ProductHelper::GetTitle($binding).' '.ProductHelper::GetTitle($data). ' subscription - '.ProductHelper::GetTitle($data). ' Russian edition';
				
				$row = Binding::GetBinding($entity, $data['binding_id']);
				$subsisbn='';
				if ($data['isbn']) {
					$subsisbn = ' Subscribe to '.$data['isbn'].'.';
				}
				
				$this->pageDescription = 'Subscribe online to '.ProductHelper::GetTitle($data).' '.ProductHelper::GetTitle($binding).' Russian edition at Ruslania.com.'.$subsisbn;
				
			}
			
		}
		
		//Музыка
		
		if ( $entity == 22) {
			
			if (Yii::app()->language == 'ru') {
				
				
				
				$this->pageTitle = ProductHelper::GetTitle($data).' - '.ProductHelper::GetTitle($data['Authors'][0]).' - '.$data['Media']['title'].' - купить онлайн';
				
				$this->pageDescription = ProductHelper::GetTitle($data).', '.ProductHelper::GetTitle($data['Authors'][0]).', '.$data['ean'].', '.$data['Media']['title'].' - купить онлайн с доставкой по всему миру.';
				
			} else {
				
				
				$this->pageTitle = ProductHelper::GetTitle($data).' - '.ProductHelper::GetTitle($data['Authors'][0]).' - '.$data['Media']['title'].' - buy online';
				
				$this->pageDescription = ProductHelper::GetTitle($data).', '.ProductHelper::GetTitle($data['Authors'][0]).', '.$data['ean'].', '.$data['Media']['title'].' - buy online with worldwide delivery at Ruslania.com.';
				
			}
			
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
		
		$get_cats = Category::get_count_categories_bread($id, $entity);
		
		if (count($get_cats) == 0) {
			
			$entities = Entity::GetEntitiesList();
			$tbl = $entities[$entity]['site_table'];
			
			$sql = 'SELECT `code`, `subcode` FROM ' . $tbl . ' WHERE id='.$id;
			
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			
			$code = (int) $rows[0]['code'];
			
			if (!$rows[0]['code']) {
				$code = $rows[0]['subcode'];
			}
			
			$arr = Category::getCatsBreadcrumbs($entity, $code);
			
			foreach($arr as $a) {
				
				$title2 = ProductHelper::GetTitle($a);
				
				$this->breadcrumbs[$title2] = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $a['id']));
				
			}
			
			$this->breadcrumbs[] = ProductHelper::GetTitle($data);
			
		} else {
			$this->breadcrumbs[] = ProductHelper::GetTitle($data);
		}
		
        
        //

        if(empty($data)) throw new CHttpException(404);

        $this->render('view', array('item' => $data, 'entity' => $entity));
    }

	private function _checkUrl($item) {
		if (empty($item)) return false;

		$path = urldecode(getenv('REQUEST_URI'));
		$ind = mb_strpos($path, "?", null, 'utf-8');
		$query = '';
		if ($ind !== false) {
			$query = mb_substr($path, $ind, null, 'utf-8');
			$path = substr($path, 0, $ind);
		}

		$this->_canonicalPath = ProductHelper::CreateUrl($item);
		if ($this->_canonicalPath === $path) return false;
		return $this->_canonicalPath . $query;

	}
}
<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class Test123Controller extends MyController {

    public function actionTest123() {

        $this->render('test123');
    }
    public function actionItems() {

	$entity = 10;
	$db = new mysqli('localhost', 'ruslania', 'K7h9E6r2', 'ruslania');
	$sql = 'SELECT id FROM books_catalog ORDER BY add_date DESC LIMIT 0, 40';
 
	$items = Yii::app()->db->createCommand($sql)->queryAll();
	
		$filter_data = array(
		'author'=>'',
		'binding_id'=>array(),
		'year_min'=>'',
		'year_max'=>'',
		'min_cost'=>'',
		'max_cost'=>''
		);	
	
		$this->render('items', array('categoryList' => '',
            'entity' => $entity, 'items' => $items,
            'paginatorInfo' => '',
            'cid'=>'', 'filter_data' => $filter_data,
            'info' => '', 'filter_year' => '',
            'bgs' => '', 'pubs' => '', 'series'=>'', 'authors'=>'', 'title_cat'=>''));	
			
		// $this->render('list', array('categoryList' => $catList,
            // 'entity' => $entity, 'items' => $items,
            // 'paginatorInfo' => $paginatorInfo,
            // 'cid'=>$cid, 'filter_data' => $filter_data,
            // 'info' => $categoryInfo, 'filter_year' => $maxminyear,
            // 'bgs' => $bg, 'pubs' => $pubs, 'series'=>$series, 'authors'=>$authors, 'title_cat'=>$title_cat));	
	
        // $this->render('items');
    }

	function actionCategory() {
		Seo_settings::get();
		$category = new Category();
		Debug::staticRun(array(ProductHelper::GetTitle($category->GetByIds(50, [30])[0])));
	}

	function actionVectori() {
		$this->render('vectori');
	}

	function actionCurrency() {
		Debug::staticRun(array(Yii::app()->createUrl('buy/doorder', array('__langForUrl'=>'rut'))));


		Debug::staticRun(array(
			Yii::app()->ui->item('ADDED_TO_CART', Yii::app()->createUrl('cart/view')),
			Yii::app()->ui->item('ADDED_TO_CART_ALREADY'),
			sprintf(Yii::app()->ui->item('ADDED_TO_CART_ALREADY'), Yii::app()->createUrl('cart/view'), 3)
		));

		Debug::staticRun(array(Yii::app()->createUrl('cart/orderPay', array('ptype'=>7, 'id'=>7060087, 'currency'=>3))));
	}

	function actionMorphy() {
		/*
 * word_count - кол-во найденных слов
 * lcs - максимальная длина слов по порядку
 * min_hit_pos - позиция первого найденного слова
 * min_gaps - минимальное расстояние между поисковыми словами
 * exact_hit - точное соответствие (0/1)
 * exact_order - найдены все слова в порядке поискового запроса
 * bm25 - вес, который считает сфинкса для документа по поисковому запросу
 */

		$q = 'Rimsky-Korsakov';
		$sp = new SearchProducts(1);
		list($searchWords, $realWords, $useRealWord) = $sp->getNormalizedWords($q);
		Debug::staticRun(array($searchWords));
		$field_weights = array(
			'title_ru=100',
			'title_en=100',
			'title_fi=100',
			'title_rut=100',
			'title_eco=100',
			'title_original=80',
			'description_ru=80',
			'description_en=80',
			'description_fi=80',
			'description_de=80',
			'description_fr=80',
			'description_es=80',
			'description_se=80',
			'description_rut=90',
			'authors=90',
		);
		$sql = ''.
			'select entity, real_id, weight() '.
			'from music_boolean_with_translite ' .
			'where match(\'' . implode(' ', $searchWords) . '\') and (avail = 1) '.
			'order by weight() desc, position asc, time_position asc '.
			'limit 0, 40 '.
			'option ranker=expr(\'top((word_count + (lcs - 1)/5 + 1/(min_hit_pos*3 + 1) + (word_count > 1)/(min_gaps + 1) + exact_hit + exact_order)*user_weight)\'), field_weights=(' . implode(',',$field_weights) . '), max_matches=100000 './/*(word_count > 2)
		'';
		$find = SphinxQL::getDriver()->multiSelect($sql);
		Debug::staticRun(array($sql, $find));

/*		$s = 'Matryoshka 12 Textbook + 3 юзефович audio CD';
		$result = SphinxQL::getDriver()->multiSelect("call keywords (" . SphinxQL::getDriver()->mest($s) . ", 'forSnippet')");
		$sp = new SearchProducts(1);
		Debug::staticRun(array($s, $result, $sp->getNormalizedWords($s)));*/

		//Matryoshka 12 Textbook + 3 audio CD
/*		$word = 'цирк';//'Schönberg';
		$sp = new SearchProducts(1);
		list($searchWords, $realWords, $useRealWord) = $sp->getNormalizedWords($word);
		list($tables, $condition, $order, $option) = $sp->getSqlParam($searchWords, $realWords, $useRealWord, 0);
		$resulTime = microtime(true);
		$sql = ''.
			'select entity, real_id, weight() '.
			'from ' . implode(', ', $tables) . ' ' .
			'where ' . implode(' and ', $condition) . ' '.
			'order by ' . implode(', ', $order) . ' '.
			'limit 0, 400 '.
		'option ' . implode(', ', $option) . ' '.
		'';
		$find = SphinxQL::getDriver()->multiSelect($sql);
		Debug::staticRun(array($sql, $find, number_format(microtime(true)-$resulTime, 4)));

		$sql = ''.
			'select entity, real_id '.
			'from pereodics_boolean_mode ' .
			'where (issn = ' . SphinxQL::getDriver()->mest('1562-2258') . ')'.// | (index = ' . SphinxQL::getDriver()->mest('1562-2258') . ') '.
		'';
		Debug::staticRun(array($sql, SphinxQL::getDriver()->multiSelect($sql)));
		$sql = ''.
			'select * '.
			'from wrong_isbn ' .
			'where match(' . SphinxQL::getDriver()->mest('951-581-054-X') . ') '.
			'option ranker=none '.
		'';
		Debug::staticRun(array($sql, SphinxQL::getDriver()->multiSelect($sql)));

		$sql = ''.
			'select entity, count(*) counts '.
			'from books_boolean_mode, pereodics_boolean_mode, printed_boolean_mode, music_boolean_mode, musicsheets_boolean_mode, video_boolean_mode, maps_boolean_mode, soft_boolean_mode ' .
			'where ' . implode(' and ', $condition) . ' '.
			'group by entity '.
			"option ranker=expr('top(word_count*user_weight)'), field_weights=(title=100,authors=90,description=80), max_matches=100000 ".
		'';
		Debug::staticRun(array($sql, SphinxQL::getDriver()->multiSelect($sql), number_format(microtime(true)-$resulTime, 4)));*/
	}
        
    function actionUrlencode() {
			$entity = 10;
			$item = ['id' => 12528, 'title_ru' => 'Черная сотня'];
			$lang = 'ru';
			
			$urlParams = array(
					'entity' => Entity::GetUrlKey($entity),
					'pid' => $item['id'],
					//'title' => '123456', //$item['title_' . $lang],
					'__useTitleParams' => 1,
					//'__langForUrl' => 'en'
					//'test' => 1
			);
			
			echo '/' . Entity::GetUrlKey($entity) . '/bypublisher/' . $item['id'] . '/' . urlencode($item['title_' . $lang]); //Yii::app()->createUrl('entity/bypublisher', $urlParams),
			echo '<br>';
require_once Yii::getPathOfAlias('webroot') . '/protected/config/command-local.php';
			define('OLD_PAGES', 1);
            echo Yii::app()->createUrl('entity/bypublisher', $urlParams);
			
			echo '<br>';
			
//			var_dump($r);
                                        
                                        
		
	}

	function actionMemcache() {
		Debug::staticRun(array(Yii::app()->memcache));
//		Yii::app()->memcache->set('123', 'sss', 1);
		Debug::staticRun(array(Yii::app()->memcache->get('123')));
	}

	function actionCreatePhotos() {
		$src = '/var/www/www-root/data/ruslania2.ptysh.ru/pictures/books_photos/9/93530/9785699586813_orig.jpg';
		$dst = '/var/www/www-root/data/ruslania2.ptysh.ru/pictures/books_photos/9/93530/9785699586813_crop.jpg';

		/**@var $model ModelsPhotos*/
		$model = Books_photos::model();
		$model->cropFoto($dst, $src, 100);

		echo '
	<img src="https://ruslania.com/pictures/books_photos/9/93530/9785699586813_orig.jpg" alt="">
	<img src="https://ruslania.com/pictures/books_photos/9/93530/9785699586813_crop.jpg" alt="">
';
	}
}
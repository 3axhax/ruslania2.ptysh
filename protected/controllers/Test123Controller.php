<?php

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
}
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

	function actionCurrency() {
		Debug::staticRun(array(
			Yii::app()->ui->item('ADDED_TO_CART', Yii::app()->createUrl('cart/view')),
			Yii::app()->ui->item('ADDED_TO_CART_ALREADY'),
			sprintf(Yii::app()->ui->item('ADDED_TO_CART_ALREADY'), Yii::app()->createUrl('cart/view'), 3)
		));

		Debug::staticRun(array(Yii::app()->createUrl('cart/orderPay', array('ptype'=>7, 'id'=>7060087, 'currency'=>3))));
	}

	function actionMorphy() {
		$word = 'Me ja Mosso. Suomalaisten yhteiseloa Moskovassa yli kolme vuosikymmentä';
		$result = SphinxQL::getDriver()->multiSelect("call keywords (" . SphinxQL::getDriver()->mest($word) . ", 'forMorphy')");
		Debug::staticRun(array($result));
	}
}
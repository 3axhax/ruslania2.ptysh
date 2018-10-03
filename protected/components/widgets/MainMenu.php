<?php
/*Created by Кирилл (05.09.2018 19:25)*/
/**
 * Class MainMenu
 */
class MainMenu extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array();
	/**
	 * @var Category
	 */
	private $_category = null;
	/**
	 * здесь должны быть все ид категорий, которые используются в меню, что бы обним запросом получить их данные
	 */
	private $_categoryIds = array(
		'books'=>array(181, 16, 206, 211, 189, 65, 67, 202, 213),
		'sheetmusic'=>array(47, 160, 249, 128, 136, 217),
		'music'=>array(78, 74, 4, 11, 6, 17, 2, 73, 38, 21),
		'periodics'=>array(67, 47, 19, 48, 61, 44, 9, 12, 50, 100),
		'printed'=>array(33, 6, 41, 38, 43, 55, 42, 61, 2, 3, 30, 44, 15, 8, 34, 42, 37),
		'video'=>array(23, 8, 109, 107, 43),
		'maps'=>array(9, 8),
		'soft'=>array(1, 20, 16),
	);

	/**
	 * здесь будут все категории, которые используются в меню, но у другого раздела
	 */
	private $_relocated = array(
		'books'=>array(264=>array()),
		'sheetmusic'=>array(47=>array()),
		'music'=>array(),
		'periodics'=>array(),
		'printed'=>array(33=>array()),
		'video'=>array(),
		'maps'=>array(),
		'soft'=>array(),
	);

	/**
	 * здесь будут все категории - распродажи
	 */
	private $_sales = array(
		'books'=>array(213=>array()),
		'sheetmusic'=>array(217=>array()),
		'music'=>array(21=>array()),
		'periodics'=>array(/*100=>array()*/),
		'printed'=>array(37=>array()),
		'video'=>array(43=>array()),
		'maps'=>array(8=>array()),
		'soft'=>array(16=>array()),
	);

	/**
	 * для данных категорий
	 */
	private $_categorys = array();

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
		$this->_category = new Category();

		foreach ($this->_categoryIds as $entityStr=>$ids) {
			$entityId = Entity::ParseFromString($entityStr);
			$_categorys[$entityStr] = array();
			$ids = array_merge($ids, array_keys($this->_relocated[$entityStr]), array_keys($this->_sales[$entityStr]));
			foreach ($this->_category->GetCategoryList($entityId, 0, $ids) as $category) {
				if (isset($this->_relocated[$entityStr][$category['id']])) $this->_relocated[$entityStr][$category['id']] = $category;
				elseif (isset($this->_sales[$entityStr][$category['id']])) $this->_sales[$entityStr][$category['id']] = $category;
				else $this->_categorys[$entityStr][$category['id']] = $category;
			}
		}
	}

	function run() {
		$ctrl = $this->getController()->id;
		if ($ctrl == 'cart') return;

		$file = Yii::getPathOfAlias('webroot') . '/test/mainmenu_' . Yii::app()->language . '.html.php';
		//TODO:: когда будут готовы переводы  сделать сохранение в файл
		if (file_exists($file)) unlink($file);

		file_put_contents($file, $this->render('MainMenu/main_menu', array('widget'=>$this), true), FILE_APPEND);
		readfile($file);
	}

	function viewBooks() {
		$entityStr = 'books';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		$category = $this->_relocated['sheetmusic'][47];
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => 'sheetmusic', 'cid' => $category['id'])),
			'name'=>ProductHelper::GetTitle($category)
		);
		usort($rows, array($this, '_sort'));
//		$result = array();
//		foreach ($rows as $row) $result[] = $row;

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/books_menu', array('rows'=>$rows));
	}

	function viewSheetmusic() {
		$entityStr = 'sheetmusic';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		$category = $this->_relocated['sheetmusic'][47];
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => 'sheetmusic', 'cid' => $category['id'])),
			'name'=>ProductHelper::GetTitle($category)
		);
		usort($rows, array($this, '_sort'));

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/sheetmusic_menu', array('rows'=>$rows));
	}

	function viewMusic() {
		$entityStr = 'music';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		usort($rows, array($this, '_sort'));

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/music_menu', array('rows'=>$rows));
	}

	function viewSuvenirs() {
		$entityStr = 'printed';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => 6, 'lang'=>7)),
			'name'=>Yii::app()->ui->item('PRINTED_RUS')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => 6, 'lang'=>14)),
			'name'=>Yii::app()->ui->item('PRINTED_FIN')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $categorys[43]['id'])),
			'name'=>ProductHelper::GetTitle($categorys[43])
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $categorys[38]['id'])),
			'name'=>ProductHelper::GetTitle($categorys[38])
		);
		$this->render('MainMenu/suvenirs_menu', array('rows'=>$rows));
	}

	function viewMaps() {
		$entityStr = 'maps';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		usort($rows, array($this, '_sort'));

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/maps_menu', array('rows'=>$rows));
	}

	function viewPrinted() {
		$entityStr = 'printed';
		$categorys = $this->_categorys[$entityStr];
		unset($categorys[33], $categorys[6], $categorys[41], $categorys[38], $categorys[43], $categorys[55], $categorys[61]);
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		$category = $this->_relocated['books'][264];
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => 'books', 'cid' => $category['id'])),
			'name'=>ProductHelper::GetTitle($category)
		);
		usort($rows, array($this, '_sort'));

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/printed_menu', array('rows'=>$rows));
	}

	function viewSoft() {
		$entityStr = 'soft';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		usort($rows, array($this, '_sort'));

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/soft_menu', array('rows'=>$rows));
	}

	function viewVideo() {
		$entityStr = 'video';
		$categorys = $this->_categorys[$entityStr];
//
		$rows = array();
		foreach ($categorys as $category) {
			$rows[] = array(
				'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
				'name'=>ProductHelper::GetTitle($category)
			);
		}
		usort($rows, array($this, '_sort'));

		array_unshift($rows, array(
			'href'=>Yii::app()->createUrl('entity/bysubtitle', array('entity' => $entityStr, 'sid' => 2)),
			'name'=>Yii::app()->ui->item('A_NEW_VIDEO_EN_SUBTITLES')
		));

		array_unshift($rows, array(
			'href'=>Yii::app()->createUrl('entity/bysubtitle', array('entity' => $entityStr, 'sid' => 8)),
			'name'=>Yii::app()->ui->item('A_NEW_VIDEO_FI_SUBTITLES')
		));

		$saleCategorys = $this->_sales[$entityStr];
		$category = array_shift($saleCategorys);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/list', array('entity' => $entityStr, 'cid' => $category['id'])),
			'name'=>Yii::app()->ui->item('A_NEW_SALE')
		);
		$rows[] = array(
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => $entityStr)),
			'name'=>Yii::app()->ui->item('A_NEW_ALL_CATEGORIES')
		);
		$this->render('MainMenu/video_menu', array('rows'=>$rows));
	}

	function viewPeriodics() {
		$availCategory2 = array(67=>'NAME_POPULAR', 47=>'NAME_SCIENCE', 19=>'NAME_FORCHILDS', 48=>'NAME_FORFEMALE', 61=>'NAME_FORMALE');
		$availCategory1 = array(1=>'NAME_POLICY', 44=>'NAME_SPORT', 9=>'NAME_HEALTH', 12=>'NAME_HISTORY', 50=>'NAME_ASTROLOGY');
		$availCategorySale = array(100=>'MENU_SALE_PERIODICS');
		$rows = $this->_categorys['periodics'];
		$availCategory = array();
		foreach ($rows as $row) $availCategory[$row['id']] = $row;
		$printed = $this->_relocated['printed'][33];
		$this->render('MainMenu/periodics_menu', array('availCategory2'=>$availCategory2, 'availCategory1'=>$availCategory1, 'availCategorySale'=>$availCategorySale, 'availCategory'=>$availCategory, 'printed'=>$printed));
	}

	private function _sort($a, $b) {
		$name = 'name';
		return strcmp($a[$name], $b[$name]);
	}

}
<?php
/*Created by Кирилл (18.05.2018 20:10)*/
class Sitemap {
	private $_file = null, $_tabPx = 20;
	/**
	 * @var array ключ - название в Entity::GetEntitiesList()[entity]['with']
	 * 0=>здесь хотел название функции в Category:: для получения справочника
	 * 1=>название - индекс для перевода
	 * 2=>route для href всех
	 * 3=>route Для href тега
	 */
	private $_tags = array(
		'yearreleases'=>array(  '', 'A_NEW_FILTER_YEAR',        'yearreleaseslist', 'byyearrelease'),
		'publisher'=>array(     '', 'A_NEW_FILTER_PUBLISHER',   'publisherlist',    'bypublisher'),
		'series'=>array(        '', 'A_NEW_FILTER_SERIES',      'serieslist',       'byseries'),
		'authors'=>array(       '', 'A_NEW_FILTER_AUTHOR',      'authorlist',       'byauthor'),
		'actors'=>array(        '', 'Actor',                    'actorlist',        'byactor'),
		'performers'=>array(    '', 'Performer',                'performerlist',    'byperformer'),
		'directors'=>array(     '', 'Director',                 'directorlist',     'bydirector'),
//		'languages'=>array(     '', 'CATALOGINDEX_CHANGE_LANGUAGE', '', ''),
		'binding'=>array(       '', 'Binding',                  'bindingslist',     'bybinding'),
		'audiostreams'=>array(  '', 'AUDIO_STREAMS',            'audiostreamslist', 'byaudiostream'),
		'subtitles'=>array(     '', 'Credits',                  'subtitleslist',    'bysubtitle'),
		'media'=>array(         '', 'A_NEW_FILTER_TYPE2',       'medialist',        'bymedia'),
		'magazinetype'=>array(  '', 'A_NEW_TYPE_IZD',           'typeslist',        'bytype'),
		'videoStudio'=>array(       '', 'STUDIOS',                  'studioslist',      'bystudio'),
	);
	private $_tagsAll = array(
		'years'=>array('', 'A_NEW_FILTER_YEAR', 'yearslist', 'byyear', array('pereodics_catalog')),
		'gift'=>array('', 'A_NEW_PERIODIC_FOR_GIFT', 'gift', '', array()),
	);

	private $_tagsHand = array(//в итоге получились не теги, но менять название не стал
		'sale'=>array('', 'MENU_SALE', 'site/sale', ''),
		'register'=>array('', 'A_REGISTER', 'site/register', ''),
		'login'=>array('', 'A_SIGNIN', 'site/login', ''),
		'cartView'=>array('', 'A_SHOPCART', 'cart/view', ''),
		'me'=>array('', 'YM_CONTEXT_PERSONAL_MAIN', 'client/me', ''),
	);

	private $_staticPages = array(
/*		'conditions' => 'MSG_CONDITIONS_OF_USE',
		'conditions_order' => 'YM_CONTEXT_CONDITIONS_ORDER_ALL',
		'conditions_subscription' => 'YM_CONTEXT_CONDITIONS_ORDER_PRD',
		'contact' => 'YM_CONTEXT_CONTACTUS',
		'legal_notice' => 'YM_CONTEXT_LEGAL_NOTICE',
		'faq' => 'A_FAQ',
		'aboutus' => 'A_ABOUTUS',
//		'partners' => 'A_PARTNERS',
//		'links' => 'A_LINKS',
		'ourstore' => 'A_STORE',
		'csr' => 'A_CSR',
		'offers_partners' => 'YM_CONTEXT_OFFERS_PARTNERS',
//		'thawte' => 'MSG_YAHLIST_INFO_THAWTE',
		'safety' => 'MSG_YAHLIST_INFO_PAYMENTS_ARE_SECURE',
		'zone_info' => 'Zone',
		'paypal' => 'MSG_WHAT_IS_PAYPAL',
		'sitemap' => 'A_SITEMAP',
//		'aboutus'=>'A_ABOUTUS',
//		'csr'=>'A_CSR',
//		'conditions'=>'MSG_CONDITIONS_OF_USE',
//		'conditions_order'=>'YM_CONTEXT_CONDITIONS_ORDER_ALL',
//		'conditions_subscription'=>'YM_CONTEXT_CONDITIONS_ORDER_PRD',
//		'contact'=>'YM_CONTEXT_CONTACTUS',
//		'legal_notice'=>'YM_CONTEXT_LEGAL_NOTICE',
//		'faq'=>'A_FAQ',
//		'sitemap'=>'A_SITEMAP',
//		'offers_partners'=>'A_OFFERS',*/
		'sale'=>array('route'=>'site/sale', 'name'=>'MENU_SALE'),
		'register'=>array('route'=>'site/register', 'name'=>'A_LEFT_PERSONAL_REGISTRATION'),
		'login'=>array('route'=>'site/login', 'name'=>'YM_CONTEXT_PERSONAL_LOGIN'),
		'cart'=>array('route'=>'cart/view', 'name'=>'A_NEW_CART'),
		'advsearch' => array('route'=>'site/advsearch', 'name'=>'A_ADVANCED_SEARCH'),
		'me'=>array('route'=>'client/me', 'name'=>'YM_CONTEXT_PERSONAL_MAIN'),
	);

	function __construct() {

	}

	/**
	 * @return array список основных страниц
	 */
	function getStaticPages() { return $this->_staticPages; }

	/**
	 * @return array 0=>список тегов по разделам, 1=>список тегов для всех разделов
	 */
	function getTags() { return array($this->_tags, $this->_tagsAll, $this->_tagsHand); }


	function builder($rewrite = false) {
		$this->_setFile($rewrite);
		if (!$rewrite&&file_exists($this->_file)) return $this->_file;

		$i=1;

		$this->_putFile('<ul>');
		$this->_putFile('<li><a href="/">' . Yii::app()->ui->item('A_TITLE_HOME') . '</a>');
		$this->_putFile('<ul style="margin-left: ' . ($this->_tabPx) . 'px">');
		foreach (Entity::GetEntitiesList() as $id=>$entity) {
			$this->_putFile('<li><a href="' . Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($id))) . '">' . Entity::GetTitle($id) . '</a>');
			$this->_log('Раздел и категории "' . Yii::app()->ui->item($entity['uikey']) . '"');
			$this->_categories($id, 0, $i+1);
			$this->_tags($id, $i+1);
			$this->_putFile('</li>');
			$this->_log('');
		}

		foreach ($this->_tagsHand as $tag=>$param) {
			$this->_log(Yii::app()->ui->item($param[1]));
			$this->_putFile('<li><a href="' . Yii::app()->createUrl($param[2]) . '">' . Yii::app()->ui->item($param[1]) . '</a></li>');
		}

		foreach (StaticUrlRule::getTitles() as $pageName=>$name) {
			$this->_log(Yii::app()->ui->item($name));
			$this->_putFile('<li><a href="' . Yii::app()->createUrl('site/static', array('page'=>$pageName)) . '">' . Yii::app()->ui->item($name) . '</a></li>');
		}
		foreach ($this->_staticPages as $pageName=>$param) {
			$this->_log(Yii::app()->ui->item($param['name']));
			$this->_putFile('<li><a href="' . Yii::app()->createUrl($param['route']) . '">' . Yii::app()->ui->item($param['name']) . '</a></li>');
		}

		$this->_offers();

		$this->_putFile('<li><a href="' . Yii::app()->createUrl('bookshelf/list') . '">' . Yii::app()->ui->item('BOOKSHELF_LIST') . '</a></li>');

		$this->_putFile('</ul>');
		$this->_putFile('</li>');
		$this->_putFile('</ul>');
		return $this->_file;
	}

	private function _offers() {
		//не знаю как получить список констант из класса Offer и связь с тегом
		$const = array(
//			Offer::INDEX_PAGE => 'index',
			Offer::FIRMS => 'firms',
			Offer::LIBRARY => 'lib',
			Offer::UNI => 'uni',
			Offer::FREE_SHIPPING => 'fs',
			Offer::ALLE_2_EURO => 'alle2',
		);
		$this->_putFile('<ul style="margin-left: ' . ($this->_tabPx) . 'px">');
		$this->_putFile('<li><a href="' . Yii::app()->createUrl('offers/list') . '">' . Yii::app()->ui->item('A_OFFERS') . '</a></li>');

		$o = new Offer;
		foreach ($const as $id=>$name) {
			$offer = $o->GetOffer($id, true, true);
			if ($offer) {
				$title = ProductHelper::GetTitle($offer);
				$url = Yii::app()->createUrl('offers/special', array('mode' => $const[$id]));
				$this->_putFile('<li><a href="' . $url . '">' . $title. '</a></li>');
			}
		}

	}

	private function _setFile($rewrite) {
		$this->_file = Yii::getPathOfAlias('webroot') . '/test/sitemap_' . Yii::app()->language . '.html.php';
		if ($rewrite&&file_exists($this->_file)) unlink($this->_file);
	}

	private function _putFile($s) {
		file_put_contents($this->_file, $s . "\n", FILE_APPEND);
	}

	private function _categories($entity, $idParent, $i) {
		$cats = (new Category())->GetCategoryList($entity, $idParent);
		if (!empty($cats)) {//
			$this->_putFile('<ul style="margin-left: ' . ($this->_tabPx) . 'px">');
			foreach ($cats as $cat) {
				$this->_putFile('<li><a href="' . Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'], 'title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($cat)))) . '">' . ProductHelper::GetTitle($cat) . '</a>');
				$this->_categories($entity, $cat['id'], $i+1);
				$this->_putFile('</li>');
			}
			$this->_putFile('</ul>');
		}
	}

	private function _tags($entity, $i) {
		$tags = $this->_tags;
		$this->_putFile('<ul style="margin-left: ' . ($this->_tabPx) . 'px">');
		foreach ($tags as $tag=>$param) {
			$this->_log($tag . ' - ' . $entity . ' - ' . (int) $this->_checkTagByEntity($tag, $entity));
			if (!empty($param[2])&&$this->_checkTagByEntity($tag, $entity)) {
				$this->_log(Yii::app()->ui->item(Entity::GetEntitiesList()[$entity]['uikey']) . ' ' . Yii::app()->ui->item($param[1]));

				$this->_putFile('<li><a href="' . Yii::app()->createUrl('entity/' . $param[2], array('entity' => Entity::GetUrlKey($entity))) . '">' . Yii::app()->ui->item($param[1]) . '</a>');
				$this->_putFile('</li>');
			}
		}
		foreach ($this->_tagsAll as $tag=>$param) {
			$this->_log($tag . ' - ' . $entity . ' - ' . (int) $this->_checkTagByEntity($tag, $entity));
			if (!empty($param[2])&&$this->_checkTagByEntity($tag, $entity)) {
				$this->_log(Yii::app()->ui->item(Entity::GetEntitiesList()[$entity]['uikey']) . ' ' . Yii::app()->ui->item($param[1]));
				$href = Yii::app()->createUrl('entity/' . $param[2], array('entity' => Entity::GetUrlKey($entity)));
				$this->_putFile('<li><a href="' . $href . '">' . Yii::app()->ui->item($param[1]) . '</a>');
				$this->_putFile('</li>');
			}
		}
		$this->_putFile('</ul>');
	}

	private function _checkTagByEntity($tag, $entity) {
		return Entity::checkEntityParam($entity, $tag);
	}

	function checkTagByEntity($tag, $entity) { return $this->_checkTagByEntity($tag, $entity); }

	private function _log($s) {
//		echo $s . '<br>';
	}

}
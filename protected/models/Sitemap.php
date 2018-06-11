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
//		'yearreleases'=>array('', 'A_NEW_FILTER_YEAR', '', 'byyear'),
		'publisher'=>array(     '', 'A_NEW_FILTER_PUBLISHER',   'publisherlist',    'bypublisher'),
		'series'=>array(        '', 'A_NEW_FILTER_SERIES',      'serieslist',       'byseries'),
		'authors'=>array(       '', 'A_NEW_FILTER_AUTHOR',      'authorlist',       'byauthor'),
		'actors'=>array(        '', 'Actor',                    'actorlist',        'byactor'),
		'performers'=>array(    '', 'Performer',                'performerlist',    'byperformer'),
		'directors'=>array(     '', 'Director',                 'directorlist',     'bydirector'),
		'languages'=>array(     '', 'CATALOGINDEX_CHANGE_LANGUAGE', '', ''),
		'binding'=>array(       '', 'Binding',                  'bindingslist',     'bybinding'),
		'audiostreams'=>array(  '', 'AUDIO_STREAMS',            'audiostreamslist', 'byaudiostream'),
		'subtitles'=>array(     '', 'Credits',                  'subtitleslist',    'bysubtitle'),
		'media'=>array(         '', 'A_NEW_FILTER_TYPE2',       'medialist',        'bymedia'),
		'magazinetype'=>array(  '', 'A_NEW_TYPE_IZD',           'typeslist',            'bytype'),
	);
	private $_tagsAll = array(
		'years'=>array('', 'A_NEW_FILTER_YEAR', 'yearslist', 'byyear', array('pereodics_catalog')),
	);

	private $_staticPages = array(
		'aboutus'=>'A_ABOUTUS',
		'csr'=>'A_CSR',
		'conditions'=>'MSG_CONDITIONS_OF_USE',
		'conditions_order'=>'YM_CONTEXT_CONDITIONS_ORDER_ALL',
		'conditions_subscription'=>'YM_CONTEXT_CONDITIONS_ORDER_PRD',
		'contact'=>'YM_CONTEXT_CONTACTUS',
		'legal_notice'=>'YM_CONTEXT_LEGAL_NOTICE',
		'faq'=>'A_FAQ',
		'sitemap'=>'A_SITEMAP',
	);

	/**
	 * @return array список основных страниц
	 */
	function getStaticPages() { return $this->_staticPages; }

	/**
	 * @return array 0=>список тегов по разделам, 1=>список тегов для всех разделов
	 */
	function getTags() { return array($this->_tags, $this->_tagsAll); }


	function builder($rewrite = false) {
		$this->_setFile($rewrite);
		if (!$rewrite&&file_exists($this->_file)) return $this->_file;

		$i=1;

		$this->_putFile('<ul>');
		$this->_putFile('<li><a href="/">' . Yii::app()->ui->item('A_TITLE_HOME') . '</a>');
		$this->_putFile('<ul style="margin-left: ' . ($this->_tabPx) . 'px">');
		foreach (Entity::GetEntitiesList() as $id=>$entity) {
			$this->_putFile('<li><a href="' . Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($id))) . '">' . Entity::GetTitle($id) . '</a>');
			$this->_categories($id, 0, $i+1);
			$this->_tags($id, $i+1);
			$this->_putFile('</li>');
		}
		$this->_putFile('</ul>');
		$this->_putFile('</li>');
		$this->_putFile('</ul>');
		return $this->_file;
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
				$this->_putFile('<li><a href="' . Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cat['id'])) . '">' . ProductHelper::GetTitle($cat) . '</a>');
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
			if (!empty($param[2])&&$this->_checkTagByEntity($tag, $entity)) {
				$this->_putFile('<li><a href="' . Yii::app()->createUrl('entity/' . $param[2], array('entity' => Entity::GetUrlKey($entity))) . '">' . Yii::app()->ui->item($param[1]) . '</a>');
				$this->_putFile('</li>');
			}
		}
		foreach ($this->_tagsAll as $tag=>$param) {
			if (!empty($param[2])) {
				$href = Yii::app()->createUrl('entity/' . $param[2], array('entity' => Entity::GetUrlKey($entity)));
				$this->_putFile('<li><a href="' . $href . '">' . Yii::app()->ui->item($param[1]) . '</a>');
				$this->_putFile('</li>');
			}
		}
		$this->_putFile('</ul>');
	}

	private function _checkTagByEntity($tag, $entity) {
		$entitys = Entity::GetEntitiesList();
		if (!empty($entitys[$entity])) return in_array($tag, $entitys[$entity]['with']);
		return false;
	}

	function checkTagByEntity($tag, $entity) { return $this->_checkTagByEntity($tag, $entity); }

}
<?php
/*Created by Кирилл (05.06.2018 21:23)
	<?php if (Entity::checkEntityParam($entity, 'category')): ?>
		<a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey($entity))); ?>" class="order_start" style="width: 100%"><?=Yii::app()->ui->item('A_NEW_VIEW_ALL_CATEGORY'); ?></a>
	<?php endif; ?>

*/

class LinksToList extends CWidget {
	protected $_params = array('links'=>['category', 'publisher', 'authors', 'series', 'actors', 'directors']);//здесь массив начальных значений

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function init() {
		$sitemap = new Sitemap();
		list($tags, $tagsAll) = $sitemap->getTags();
		foreach ($tags as $linkName=>$tag) {
			if (!in_array($linkName, $this->_params['links'])) $this->_params['links'][] = $linkName;
		}
		foreach ($tagsAll as $linkName=>$tag) {
			if (!in_array($linkName, $this->_params['links'])) $this->_params['links'][] = $linkName;
		}
	}

	function run() {
		$links = array();
		foreach ($this->_params['links'] as $linkName) {
			$funkName = '_' . $linkName . 'Link';
			if (method_exists($this, $funkName)) {
				$link = $this->$funkName();
				if (!empty($link)) $links[$linkName] = $link;
			}
		}
		if (empty($links)) return;

		$this->render('links_to_list', array('links'=>$links));
	}

	private function _categoryLink() {
		if (!$this->_checkByEntity('category')) return array();

		return array(
			'name'=>Yii::app()->ui->item('ALL_CATEGORY'),
			'href'=>Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _publisherLink() {
		if (!$this->_checkByEntity('publisher')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_PUBLISHERS'),
			'href'=>Yii::app()->createUrl('entity/publisherlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _authorsLink() {
		if (!$this->_checkByEntity('authors')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_AUTHORS'),
			'href'=>Yii::app()->createUrl('entity/authorlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _seriesLink() {
		if (!$this->_checkByEntity('series')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_SERIES_PROPERTYLIST'),
			'href'=>Yii::app()->createUrl('entity/serieslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _actorsLink() {
		if (!$this->_checkByEntity('actors')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_ACTORS'),
			'href'=>Yii::app()->createUrl('entity/actorlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _directorsLink() {
		if (!$this->_checkByEntity('directors')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_DIRECTORS'),
			'href'=>Yii::app()->createUrl('entity/directorlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _performersLink() {
		if (!$this->_checkByEntity('performers')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_LEFT_' . mb_strtoupper(Entity::GetUrlKey($this->_params['entity']), 'utf-8') . '_AZ_PROPERTYLIST_PERFORMERS'),
			'href'=>Yii::app()->createUrl('entity/performerlist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _languagesLink() {
		return array();
	}

	private function _bindingLink() {
		if (!$this->_checkByEntity('binding')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_NEW_TYPOGRAPHY'),
			'href'=>Yii::app()->createUrl('entity/bindingslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _audiostreamsLink() {
		if (!$this->_checkByEntity('audiostreams')) return array();

		return array(
			'name'=>Yii::app()->ui->item('AUDIO_STREAMS'),
			'href'=>Yii::app()->createUrl('entity/audiostreamslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _subtitlesLink() {
		if (!$this->_checkByEntity('subtitles')) return array();

		return array(
			'name'=>Yii::app()->ui->item('Credits'),
			'href'=>Yii::app()->createUrl('entity/subtitleslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _mediaLink() {
		if (!$this->_checkByEntity('media')) return array();

		return array(
			'name'=>($this->_params['entity'] == Entity::MUSIC) ?
                Yii::app()->ui->item('A_NEW_FILTER_TYPE3') :
                Yii::app()->ui->item('A_NEW_FILTER_TYPE2'),
			'href'=>Yii::app()->createUrl('entity/medialist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _magazinetypeLink() {
		if (!$this->_checkByEntity('magazinetype')) return array();

		return array(
			'name'=>Yii::app()->ui->item('A_NEW_TYPE_IZD'),
			'href'=>Yii::app()->createUrl('entity/typeslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _yearsLink() {
		if (Entity::GetEntitiesList()[$this->_params['entity']]['site_table'] == 'pereodics_catalog') return array();

		return array(
			'name'=>Yii::app()->ui->item('A_NEW_FILTER_YEAR'),
			'href'=>Yii::app()->createUrl('entity/yearslist', array('entity' => Entity::GetUrlKey($this->_params['entity']))),
		);
	}

	private function _checkByEntity($linkName) {
		return Entity::checkEntityParam($this->_params['entity'], $linkName);
	}


}
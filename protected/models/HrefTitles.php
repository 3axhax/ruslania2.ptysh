<?php
/*Created by Кирилл (12.07.2018 21:38)*/
class HrefTitles {
	static private $_self = null;

	private $_titles = array();

	/**
	 * @return HrefTitles
	 */
	static function get() {
		if (self::$_self === null) self::$_self = new self;
		return self::$_self;
	}

	function getById($entity, $route, $id) {
		$idTitles = $this->getByIds($entity, $route, array($id));
		return $idTitles[$id];
	}

	function getByIds($entity, $route, $ids) {
		$idTitles = array();
		foreach ($ids as $id) $idTitles[$id] = array();
		$table = $this->getTable($entity, $route);
		if (empty($table)) return $idTitles; //если не нашел таблицу с title то дальше не ищу

		if (!isset($this->_titles[$route])) $this->_titles[$route] = array();
		if (!isset($this->_titles[$route][$entity])) $this->_titles[$route][$entity] = array();

		//это, чтоб выполнить запрос только для ид, которых еще нет
		foreach ($this->_titles[$route][$entity] as $id=>$titles) {
			$idTitles[$id] = unserialize($titles);
		}
		//это, чтоб выполнить запрос только для ид, которых еще нет
		$withoutTitles = array_filter($idTitles, [$this, '_checkEmpty']);
		if (empty($withoutTitles)) return $idTitles;

		$sql = ''.
			'select id, titles '.
			'from seo_href_titles '.
			'where (entity = :entity) '.
				'and (route = :route) '.
				'and (id in (' . implode(',', array_keys($withoutTitles)) . ')) '.
		'';
		foreach (Yii::app()->db->createCommand($sql)->queryAll(true, array(':entity'=>$entity, ':route'=>$route)) as $row) {
			$this->_titles[$route][$entity][$row['id']] = $row['titles'];
			$idTitles[$row['id']] = unserialize($row['titles']);
		}
		$withoutTitles = array_filter($idTitles, [$this, '_checkEmpty']);
		if (empty($withoutTitles)) return $idTitles;

		//далее, если есть ид без title

		$table = $this->getTable($entity, $route);
		if (!empty($table)) {
			$langs = $this->getLangs($entity, $route);
			if (empty($langs)) {
				$langs[] = 'en';
				$sql = 'select id, title title_en ';
			}
			else $sql = 'select id, title_' . implode(', title_', $langs) . ' ';
			$sql .= 'from ' . $table . ' '.
				'where (id in (' . implode(',', array_keys($withoutTitles)) . ')) ';

			foreach (Yii::app()->db->createCommand($sql)->queryAll() as $row) {
				$this->_save($entity, $route, $row);
				$titles = $this->_getTitles($entity, $route, $row);
				$idTitles[$row['id']] = $titles;
				$this->_titles[$route][$entity][$row['id']] = serialize($titles);
			}
		}

		return $idTitles;
	}

	/** возвращает таблицу с данными title
	 * @param $entity
	 * @param $route
	 */
	function getTable($entity, $route) {
		switch ($route) {
			case 'product/view': return Entity::GetEntitiesList()[$entity]['site_table']; break;
			case 'entity/list': return Entity::GetEntitiesList()[$entity]['site_category_table']; break;
			case 'entity/bypublisher': return 'all_publishers'; break;
			case 'entity/bybinding': return Entity::GetEntitiesList()[$entity]['binding_table']; break;
			case 'entity/byseries': return Entity::GetEntitiesList()[$entity]['site_series_table']; break;
			case 'entity/bymedia': return 'all_media'; break;
			case 'entity/byaudiostream': return 'video_audiostreamlist'; break;
			case 'entity/bysubtitle': return 'video_creditslist'; break;
//			case 'entity/bytype': return 'pereodics_types'; break;
			case 'entity/bymagazinetype': return 'pereodics_types'; break;
			case 'entity/byauthor':
			case 'entity/byactor':
			case 'entity/bydirector':
			case 'entity/byperformer':
				return 'all_authorslist';
				break;
		}
		return '';
	}

	/** возвращает доступные языки в таблице с title
	 * @param $entity
	 * @param $route
	 */
	function getLangs($entity, $route) {
		switch ($route) {
			case 'product/view': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/list': return array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'es', 'se'); break;
			case 'entity/bypublisher': return array('ru', 'en'); break;
			case 'entity/bybinding': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/byseries': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/bymedia': return array(); break;
			case 'entity/byaudiostream': return array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se'); break;
			case 'entity/bysubtitle': return array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se'); break;
//			case 'entity/bytype': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/bymagazinetype': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/byauthor':
			case 'entity/byactor':
			case 'entity/bydirector':
			case 'entity/byperformer':
				return array('ru', 'rut', 'en', 'fi');
				break;
		}
		return array();
	}

	private function _getTitles($entity, $route, $item) {
		$langs = $this->getLangs($entity, $route);
		if (empty($langs)) $langs[] = 'en';

		$titles = array();
		foreach ($langs as $lang) {
			$titles[$lang] = ProductHelper::ToAscii($item['title_' . $lang]);
		}
		return $titles;
	}

	private function _save($entity, $route, $item) {
		$titles = $this->_getTitles($entity, $route, $item);
		$insertParams = array(
			':entity'=>$entity,
			':route'=>$route,
			':id'=>$item['id'],
			':titles'=>serialize($titles),
		);
		$insertSql = ''.
			'insert ignore into seo_href_titles set '.
				'entity = :entity, '.
				'route = :route, '.
				'id = :id, '.
				'titles = :titles '.
		'';
		Yii::app()->db->createCommand($insertSql)->query($insertParams);
	}

	private function _checkEmpty($title) {
		return empty($title);
	}

	function isTitleRoute($route) {
		$idName = $this->getIdName(10, $route);
		return !empty($idName);
	}

	function getIdName($entity, $route) {
		switch ($route) {
			case 'product/view': return 'id'; break;
			case 'entity/list': return 'cid'; break;
			case 'entity/bypublisher': return 'pid'; break;
			case 'entity/bybinding': return 'bid'; break;
			case 'entity/byseries': return 'sid'; break;
			case 'entity/bymedia': return 'mid'; break;
			case 'entity/byaudiostream': return 'sid'; break;
			case 'entity/bysubtitle': return 'sid'; break;
//			case 'entity/bytype': return 'tid'; break;
			case 'entity/bymagazinetype': return 'tid'; break;
			case 'entity/byauthor': case 'entity/byactor': return 'aid'; break;
			case 'entity/bydirector': return 'did'; break;
			case 'entity/byperformer': return 'pid'; break;
		}
		return '';
	}

	function redirectOldPage($url) {
		if (!$this->isTitleRoute(Yii::app()->getController()->id . '/' . Yii::app()->getController()->action->id)) return;

		if (mb_strpos($url, '/', null, 'utf-8') !== 0) $url = '/' . $url;
		$sql = ''.
			'select entity, route, id, lang '.
			'from seo_redirects '.
			'where (path = :url) '.
			'limit 1 '.
		'';
		Debug::staticRun(array($sql));
		$row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':url'=>$url));
		if (!empty($row)) {
			$titles = $this->getById($row['entity'], $row['route'], $row['id']);
			if (!empty($titles)) {
				$title = isset($titles[$row['lang']])?$titles[$row['lang']]:$titles['en'];
				$urlParams = array(
					'entity'=>Entity::GetUrlKey($row['entity']),
					$this->getIdName($row['entity'], $row['route'])=>$row['id'],
					'title'=>$title,
					'__langForUrl'=>$row['lang'],
				);
				$url = Yii::app()->createUrl($row['route'], $urlParams);
				if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
			}
		}
	}

}
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

	private function __construct() {
/*		if (!defined('cronAction')) {
			//на всех страницах в меню есть ссылки на разделы. По этому всегда получаю все эти ссылки
			$key = 'hreParentCategorys';
			$this->_titles = Yii::app()->dbCache->get($key);
			if ($this->_titles === false) {
				foreach (Entity::GetEntitiesList() as $entity=>$param) {
					if (!empty($param['site_category_table'])) {
						$sql = 'select id from ' . $param['site_category_table'] . ' where (parent_id = 0)';
						$ids = Yii::app()->db->createCommand($sql)->queryColumn();
						if (!empty($ids)) $this->getByIds($entity, 'entity/list', $ids);
					}
				}
				Yii::app()->dbCache->set($key, $this->_titles);
			}
		}*/
	}

	function getById($entity, $route, $id) {
/*		$sql = ''.
			'select id, titles '.
			'from seo_href_titles '.
			'where (entity = :entity) '.
				'and (route = :route) '.
				'and (id = :id) '.
		'';
		$titles = Yii::app()->db->createCommand($sql)->queryAll(true, array(':entity'=>$entity, ':route'=>$route, ':id'=>$id));
		if (empty($titles)) {
			$table = $this->getTable($entity, $route);
			if (!empty($table)) {
				$langs = $this->getLangs($entity, $route);
				if (empty($langs)) {
					$langs[] = 'en';
					$sql = 'select id, title title_en ';
				}
				else $sql = 'select id, title_' . implode(', title_', $langs) . ' ';
				$sql .= 'from ' . $table . ' '.
					'where (id = :id) '.
					(($table == 'all_media')?' and (entity = ' . (int) $entity . ') ':'') .
					'limit 1'.
				'';

				$row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id'=>$id));
				$this->_save($entity, $route, $row);
				return $this->_getTitles($entity, $route, $row);
			}
			return array();
		}
		return unserialize($titles);*/

		if (!empty($this->_titles[$route][$entity][$id])) {
			return $this->_titles[$route][$entity][$id];
		}

		$idTitles = $this->_getByIds($entity, $route, array($id));
		return $idTitles[$id];
	}

	function getByIds($entity, $route, $ids) { return []; }
	private function _getByIds($entity, $route, $ids) {
		$idTitles = array();
/*		foreach ($ids as $id) $idTitles[$id] = array();
		$table = $this->getTable($entity, $route);
		if (empty($table)) return $idTitles; //если не нашел таблицу с title то дальше не ищу

		if (!isset($this->_titles[$route])) $this->_titles[$route] = array();
		if (!isset($this->_titles[$route][$entity])) $this->_titles[$route][$entity] = array();

		//это, чтоб выполнить запрос только для ид, которых еще нет
		foreach ($this->_titles[$route][$entity] as $id=>$titles) {
			$idTitles[$id] = $titles;
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
			$this->_titles[$route][$entity][$row['id']] = unserialize($row['titles']);
			$idTitles[$row['id']] = $this->_titles[$route][$entity][$row['id']];
		}
		$withoutTitles = array_filter($idTitles, [$this, '_checkEmpty']);
		if (empty($withoutTitles)) return $idTitles;
*/
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
				'where (id in (' . implode(',', $ids/*array_keys($withoutTitles)*/) . ')) '.
				(($table == 'all_media')?' and (entity = ' . (int) $entity . ')':'')
			;

			foreach (Yii::app()->db->createCommand($sql)->queryAll() as $row) {
				$this->_save($entity, $route, $row);
				$titles = $this->_getTitles($entity, $route, $row);
				$idTitles[$row['id']] = $titles;
				$this->_titles[$route][$entity][$row['id']] = $titles;
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
			case 'offers/view': return 'offers'; break;
			case 'product/view': return Entity::GetEntitiesList()[$entity]['site_table']; break;
			case 'entity/list': return Entity::GetEntitiesList()[$entity]['site_category_table']; break;
			case 'entity/bypublisher': return 'all_publishers'; break;
			case 'entity/bybinding': return Entity::GetEntitiesList()[$entity]['binding_table']; break;
			case 'entity/byseries': return Entity::GetEntitiesList()[$entity]['site_series_table']; break;
			case 'entity/bymedia': return 'all_media'; break;
			case 'entity/byaudiostream': return 'video_audiostreamlist'; break;
			case 'entity/bysubtitle': return 'video_creditslist'; break;
			case 'entity/bytype': return 'pereodics_types'; break;
			case 'entity/bymagazinetype': return 'pereodics_types'; break;
			case 'entity/byauthor':
			case 'entity/byactor':
			case 'entity/bydirector':
			case 'entity/byperformer':
				return 'all_authorslist';
				break;
			case 'entity/bystudio': return 'video_studios'; break;
		}
		return '';
	}

	/** возвращает доступные языки в таблице с title
	 * @param $entity
	 * @param $route
	 */
	function getLangs($entity, $route) {
		switch ($route) {
			case 'offers/view': return array('ru', 'en', 'fi',); break;
			case 'product/view': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/list': return array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'es', 'se'); break;
			case 'entity/bypublisher': return array('ru', 'en'); break;
			case 'entity/bybinding': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/byseries': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/bymedia': return array(); break;
			case 'entity/byaudiostream': return array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se'); break;
			case 'entity/bysubtitle': return array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se'); break;
			case 'entity/bytype': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/bymagazinetype': return array('ru', 'rut', 'en', 'fi'); break;
			case 'entity/byauthor':
			case 'entity/byactor':
			case 'entity/bydirector':
			case 'entity/byperformer':
				return array('ru', 'rut', 'en', 'fi');
				break;
			case 'entity/bystudio': return array('ru', 'rut', 'en', 'fi'); break;
		}
		return array();
	}

	private function _getTitles($entity, $route, $item) {
		$langs = $this->getLangs($entity, $route);
		if (empty($langs)) $langs[] = 'en';

		$titles = array();
		foreach ($langs as $lang) {
			$titles[$lang] = ProductHelper::ToAsciiForUrl($item['title_' . $lang]);
		}
		return $titles;
	}

	private function _save($entity, $route, $item) {
		return;
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

	function isTitleRoute($url) {
		if (mb_strpos($url, '/', null, 'utf-8') === 0) $url = mb_substr($url, 1, null, 'utf-8');
		$noTitleRoutes = array(
			'site',
			'cart',
			'liveSeach',
			'search',
			'bookshelf',
			'client',
			'offers',
			'order',
			'payment',
			'paytrail',
			'request',
			'special',
		);
		$url = mb_strtolower($url, 'utf-8');
		foreach ($noTitleRoutes as $route) {
			$route = mb_strtolower($route, 'utf-8');
			if (mb_strpos($url, $route, null, 'utf-8') === 0) return false;
		}
		return true;
	}

	function getIdName($entity, $route) {
		$routes = EntityUrlRule::getRoutes();
		if (empty($routes[$route]['idName'])) {
			switch ($route) {
				case 'bookshelf/view': return 'id'; break;
				case 'offers/view': return 'oid'; break;
			}
			return '';
		}

		return $routes[$route]['idName'];
	}

	function redirectOldPage($url) {
		$copyUrl = $url;
//		if (!$this->isTitleRoute($url)) return;

		if (mb_strpos($url, '/', null, 'utf-8') !== 0) $url = '/' . $url;
		$sql = ''.
			'select entity, route, id, lang '.
			'from seo_redirects '.
			'where (path like :url) '.
			'limit 1 '.
		'';
		$row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':url'=>$url));
		if (empty($row)) {
			$url = explode('/', $url);
			if (count($url) > 3) {
				$last = array_pop($url);
				if (empty($last)) $last = array_pop($url);
				if (is_numeric($last)) $url[] = $last;
				$url[] = '%';
				$url = implode('/', $url);
				$row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':url'=>$url));
				if (!empty($row)) {
					file_put_contents(Yii::getPathOfAlias('webroot') . '/test/redirects_old_sites.log', implode("\t", array(
						date('d.m.Y H:i:s'),
						$copyUrl,
						getenv('HTTP_REFERER'),
					)) . "\n", FILE_APPEND);
				}
			}
		}

		if (!empty($row['entity'])&&($row['entity'] == 20)) {
			throw new CHttpException(404);
		}

		if (!empty($row)) {
			if (empty($row['id'])&&empty($row['entity'])) {
				switch ($row['route']) {
					case 'site/static':
						$urlParams = array(
							'page'=>mb_substr($url, 1, null, 'utf-8'),
							'__langForUrl'=>((Yii::app()->language == 'ru')&&!empty($row['lang']))?$row['lang']:Yii::app()->language,
						);
						$url = Yii::app()->createUrl($row['route'], $urlParams);
						if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
						break;
					case 'offers/special':
						$urlParams = array(
							'mode'=>mb_substr($url, 5, null, 'utf-8'),
							'__langForUrl'=>((Yii::app()->language == 'ru')&&!empty($row['lang']))?$row['lang']:Yii::app()->language,
						);
						if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 0) {
							$urlParams['page'] = $page;
						}
						if (($avail = Yii::app()->getRequest()->getParam('avail')) !== null) {
							if (empty($avail)) $urlParams['avail'] = 0;
						}
						$url = Yii::app()->createUrl($row['route'], $urlParams);
						if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
						break;
					default:
						$urlParams = array(
							'__langForUrl'=>((Yii::app()->language == 'ru')&&!empty($row['lang']))?$row['lang']:Yii::app()->language,
						);
						$url = Yii::app()->createUrl($row['route'], $urlParams);
						Debug::staticRun(array($sql, $row, $urlParams, $url, 'exit'));
						if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
						break;
				}
			}
			elseif (empty($row['entity'])&&!empty($row['id'])) {
				switch ($row['route']) {
					case 'offers/view':
						$titles = $this->getById(0, $row['route'], $row['id']);
						if (!empty($titles)) {
							$title = isset($titles[$row['lang']])?$titles[$row['lang']]:$titles['en'];
							$urlParams = array(
								'oid'=>$row['id'],
								'title'=>$title,
								'__langForUrl'=>((Yii::app()->language == 'ru')&&!empty($row['lang']))?$row['lang']:Yii::app()->language,
							);
							if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 0) {
								$urlParams['page'] = $page;
							}
							if (($avail = Yii::app()->getRequest()->getParam('avail')) !== null) {
								if (empty($avail)) $urlParams['avail'] = 0;
							}
							$url = Yii::app()->createUrl($row['route'], $urlParams);
							if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
						}
						break;
					default:
						$urlParams = array(
							'id'=>$row['id'],
							'__langForUrl'=>((Yii::app()->language == 'ru')&&!empty($row['lang']))?$row['lang']:Yii::app()->language,
						);
						if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 0) {
							$urlParams['page'] = $page;
						}
						if (($avail = Yii::app()->getRequest()->getParam('avail')) !== null) {
							if (empty($avail)) $urlParams['avail'] = 0;
						}
						$url = Yii::app()->createUrl($row['route'], $urlParams);
						if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
						break;
				}
			}
			elseif (empty($row['id'])&&!empty($row['entity'])) {
				// страница раздела
				$urlParams = array(
					'entity'=>Entity::GetUrlKey($row['entity']),
					'__langForUrl'=>((Yii::app()->language == 'ru')&&!empty($row['lang']))?$row['lang']:Yii::app()->language,
				);
				if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 0) {
					$urlParams['page'] = $page;
				}
				if (($avail = Yii::app()->getRequest()->getParam('avail')) !== null) {
					if (empty($avail)) $urlParams['avail'] = 0;
				}
				$url = Yii::app()->createUrl($row['route'], $urlParams);
				Debug::staticRun(array($url, $urlParams, $_GET, 'exit'));
				if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
			}
			else {
				// страница категории
				$titles = $this->getById($row['entity'], $row['route'], $row['id']);
				if (!empty($titles)) {
					$title = isset($titles[$row['lang']])?$titles[$row['lang']]:$titles['en'];
					$urlParams = array(
						'entity'=>Entity::GetUrlKey($row['entity']),
						$this->getIdName($row['entity'], $row['route'])=>$row['id'],
						'title'=>$title,
						'__langForUrl'=>(Yii::app()->getLanguage() === 'rut')?'ru':Yii::app()->getLanguage(),
					);
					if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 0) {
						$urlParams['page'] = $page;
					}
					if (($avail = Yii::app()->getRequest()->getParam('avail')) !== null) {
						if (empty($avail)) $urlParams['avail'] = 0;
					}
					$url = Yii::app()->createUrl($row['route'], $urlParams);
					if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
				}
				elseif($idName = $this->getIdName($row['entity'], $row['route'])) {
					$urlParams = array(
						'entity'=>Entity::GetUrlKey($row['entity']),
						$idName=>$row['id'],
						'__langForUrl'=>(Yii::app()->getLanguage() === 'rut')?'ru':Yii::app()->getLanguage(),
					);
					if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 0) {
						$urlParams['page'] = $page;
					}
					if (($avail = Yii::app()->getRequest()->getParam('avail')) !== null) {
						if (empty($avail)) $urlParams['avail'] = 0;
					}
					$url = Yii::app()->createUrl($row['route'], $urlParams);
					if (!empty($url)) Yii::app()->getRequest()->redirect($url,true,301);
				}
			}
		}
	}

	function getOldNames($entity, $route, $id, $lang) {
		$sql = ''.
			'select titles '.
			'from seo_old_href_titles '.
			'where (entity = :entity) '.
				'and (route = :route) '.
				'and (id = :id) '.
		'';
		$titles = array();
		foreach (Yii::app()->db->createCommand($sql)->queryColumn( array(':entity'=>$entity, ':route'=>$route, ':id'=>$id)) as $row) {
			$row = @unserialize($row);
			if (empty($row[$lang])) $titles[] = $row['en'];
			else $titles[] = $row[$lang];
		}
		return $titles;
	}
}
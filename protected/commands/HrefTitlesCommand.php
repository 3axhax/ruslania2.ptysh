<?php
/*Created by Кирилл (12.07.2018 19:45)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php hreftitles
 * Class RepairAuthorsCommand
 */

require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
class HrefTitlesCommand extends CConsoleCommand {
	protected $_counts = 49500; //кол-во записей за один проход
	/**
	 * @var CDbCommand
	 */
	private $_insertPdo;

	public function actionIndex() {
		echo 'start ' . date('d.m.Y H:i:s') . "\n";
		$insertSql = ''.
			'insert ignore into seo_href_titles set '.
			'entity = :entity, '.
			'route = :route, '.
			'id = :id, '.
			'titles = :titles '.
		'';
		$this->_insertPdo = Yii::app()->db->createCommand($insertSql);
		$this->_insertPdo->prepare();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$this->_itemPages($entity, $params);
			$this->_categoryPages($entity, $params);
			$this->_tagPages($entity, $params);
		}
		echo 'end ' . date('d.m.Y H:i:s') . "\n\n";

	}

	private function _save($entity, $items, $langs, $route) {
		foreach ($items as $item) {
			$titles = array();
			foreach ($langs as $lang) {
				$titles[$lang] = ProductHelper::ToAscii($item['title_' . $lang]);
			}
			$insertParams = array(
				':entity'=>$entity,
				':route'=>$route,
				':id'=>$item['id'],
				':titles'=>serialize($titles),
			);
			$this->_insertPdo->getPdoStatement()->execute($insertParams);
		}
	}

	private function _categoryPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'es', 'se');
		$step = 0;
		while (($items = $this->_query($this->_sqlCategorys($entity, $params['site_category_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/list');
			unset($items);
			echo $params['site_category_table'] . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _itemPages($entity, $params) {
		$step = 0;
		$langs = array('ru', 'rut', 'en', 'fi');
		while (($items = $this->_query($this->_sqlItems($entity, $params['site_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'product/view');
			unset($items);
			echo $params['site_table'] . ' ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _tagPages($entity, $params) {
		$smapHtml = new Sitemap();
		list($tags, $tagsAll, $tagsHand) = $smapHtml->getTags();
		foreach ($tags as $tag=>$param) {
			$funcName = '_' . $tag . 'Pages';
			if ($smapHtml->checkTagByEntity($tag, $entity)&&method_exists($this, $funcName)) {
				$this->$funcName($entity, $params);
			}
		}

	}

	private function _query($sql, $params = null) {
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

	private function _sqlItems($entity, $table, $step, $langs) {
		return ''.
		'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
		'from `' . $table . '` t ' .
			'join ('.
				'select tI.id '.
				'from `' . $table . '` tI '.
				'order by tI.id '.
				'limit ' . $this->_counts*$step . ', ' . $this->_counts . ''.
			') tId using (id) '.
		'';
	}

	private function _sqlCategorys($entity, $table, $step, $langs) {
		return ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `' . $table . '` t ' .
				'join ('.
					'select tI.id '.
					'from `' . $table . '` tI '.
					'order by tI.id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ''.
				') tId using (id) '.
		'';
	}

	private function _publisherPages($entity, $params) {
		$langs = array('ru', 'en');
		$step = 0;
		while (($items = $this->_query($this->_sqlPublisher($entity, $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/bypublisher');
			unset($items);
			echo 'all_publishers ' . $entity . ' bypublisher ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _seriesPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlSeries($entity, $params['site_series_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/byseries');
			unset($items);
			echo $params['site_series_table'] . ' ' . $entity . ' byseries ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _authorsPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlAuthors($entity, $params['author_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/byauthor');
			unset($items);
			echo $params['author_table'] . ' ' . $entity . ' byauthor ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _actorsPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlActors($entity, $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/byactor');
			unset($items);
			echo 'video_actors ' . $entity . ' byactor ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _performersPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlPerformers($entity, $params['performer_table'], $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/byperformer');
			unset($items);
			echo $params['performer_table'] . ' ' . $entity . ' byperformer ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _directorsPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$step = 0;
		while (($items = $this->_query($this->_sqlDirectors($entity, $step++, $langs)))&&(($itemCounts = $items->count()) > 0)) {
			$this->_save($entity, $items, $langs, 'entity/bydirector');
			unset($items);
			echo 'video_directors ' . $entity . ' bydirector ' . (($step-1)*$this->_counts + $itemCounts) . "\n";
			if ($itemCounts < $this->_counts) break;
		}
	}

	private function _bindingPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$items = $this->_query($this->_sqlBindings($entity, $params['binding_table'], $langs));
		$this->_save($entity, $items, $langs, 'entity/bybinding');
		unset($items);
		echo $params['binding_table'] . ' ' . $entity . ' bybinding ' . "\n";
	}

	private function _audiostreamsPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se');
		$items = $this->_query($this->_sqlAudiostreams($entity, $langs));
		$this->_save($entity, $items, $langs, 'entity/byaudiostream');
		unset($items);
		echo "video_audiostreamlist\n";
	}

	private function _subtitlesPages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi', 'de', 'fr', 'it', 'es', 'se');
		$items = $this->_query($this->_sqlSubtitles($entity, $langs));
		$this->_save($entity, $items, $langs, 'entity/bysubtitle');
		unset($items);
		echo "video_creditslist\n";
	}

	private function _mediaPages($entity, $params) {
		$items = $this->_query($this->_sqlMedia($entity));
		$this->_save($entity, $items, array('en'), 'entity/bymedia');
		unset($items);
		echo "all_media\n";
	}

	private function _magazinetypePages($entity, $params) {
		$langs = array('ru', 'rut', 'en', 'fi');
		$items = $this->_query($this->_sqlMagazinetype($entity, $langs));
		$this->_save($entity, $items, $langs, 'entity/bymedia');
		unset($items);
		echo "pereodics_types\n";
	}


	private function _sqlPublisher($entity, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_publishers` t '.
				'join (select tI.id '.
					'from `all_publishers` tI '.
					'order by tI.id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlSeries($entity, $tagTable, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `' . $tagTable . '` t '.
				'join (select tI.id '.
					'from `' . $tagTable . '` tI '.
					'order by tI.id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlAuthors($entity, $tagTable, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.author_id id '.
					'from `' . $tagTable . '` tI '.
					'group by tI.author_id '.
					'order by tI.author_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlActors($entity, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.person_id id '.
					'from `video_actors` tI '.
					'group by tI.person_id '.
					'order by tI.person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlPerformers($entity, $tagTable, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.person_id id '.
					'from `' . $tagTable . '` tI '.
					'group by tI.person_id '.
					'order by tI.person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlDirectors($entity, $step, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `all_authorslist` t '.
				'join (select tI.person_id id '.
					'from `video_directors` tI '.
					'group by tI.person_id '.
					'order by tI.person_id '.
					'limit ' . $this->_counts*$step . ', ' . $this->_counts . ' '.
				') tId using (id) '.
		'';
		return $sql;
	}

	private function _sqlBindings($entity, $tableList, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `' . $tableList . '` t '.
		'';
		return $sql;
	}

	private function _sqlAudiostreams($entity, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `video_audiostreamlist` t '.
		'';
		return $sql;
	}

	private function _sqlSubtitles($entity, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `video_creditslist` t '.
		'';
		return $sql;
	}

	private function _sqlMedia($entity) {
		$sql = ''.
			'select t.id, t.title title_en '.
			'from `all_media` t '.
		'';
		return $sql;
	}

	private function _sqlMagazinetype($entity, $langs) {
		$sql = ''.
			'select t.id, t.title_' . implode(', t.title_', $langs) . ' '.
			'from `pereodics_types` t '.
		'';
		return $sql;
	}

}

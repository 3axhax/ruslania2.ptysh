<?php
/*Created by Кирилл (15.09.2018 0:46)*/

/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php beforesphinx
 * Class BeforeSphinxCommand
 */
require_once dirname(__FILE__) . '/MorphyCommand.php';
if (!defined('cronAction')) define('cronAction', 1);
class BeforeSphinxCommand extends CConsoleCommand {
	private $_counts = 10000;

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";
//		$this->_morphyAuthors();
//		return;

		$this->_fillProductsAuthors();

		$sql = 'truncate _items_with_label';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'insert ignore into _items_with_label (entity_id, item_id, type) select entity_id, item_id, 2 from offer_items';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'insert ignore into _items_with_label (entity_id, item_id, type) select entity entity_id, item_id, 1 from action_items';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'truncate all_categories';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if (($entity != 20)&&!empty($params['site_category_table'])) {
				$sql = ''.
					'insert ignore into all_categories (entity, real_id, title_ru, title_en, title_fi, title_rut) '.
					'select ' . $entity . ', id, title_ru, title_en, title_fi, title_rut '.
					'from ' . $params['site_category_table'] . ' '.
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();
			}
		}

		$sql = 'truncate all_series';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if (($entity != 20)&&!empty($params['site_series_table'])) {
				$sql = ''.
					'insert ignore into all_series (entity, id, title_ru, title_en, title_fi, title_rut) '.
					'select ' . $entity . ', id, title_ru, title_en, title_fi, title_rut '.
					'from ' . $params['site_series_table'] . ' '.
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();

				$sql = ''.
					'update all_series t '.
					'left join ' . $params['site_table'] . ' tI on (tI.series_id = t.id) AND (tI.avail_for_order = 1) '.
					'set is_' . $entity . ' = if(tI.id is null, 0, 1) '.
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();

			}
		}

		$this->_morphy();
		Yii::app()->memcache->flushValues();
		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}


	protected function _fillProductsAuthors() {
		$sql = 'truncate _supprort_products_authors';
		Yii::app()->db->createCommand()->setText($sql)->execute();
//		$allow = array('authors', 'actors', 'directors', 'performers');
		$insertSql = 'insert into _supprort_products_authors (`id`, `eid`, `name`, `position`) values(:id, :eid, :name, :pos)';
		$insertPDO = Yii::app()->db->createCommand($insertSql);
		$insertPDO->prepare();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			//$sqlItems = 'select id, title_ru, positionDefault pos from ' . $params['site_table'] . ' where (avail_for_order > 0) limit ';
			$sqlItems = 'select id, title_ru, positionDefault pos from ' . $params['site_table'] . ' limit ';
			$step = 0;
			while (($items = $this->_query($sqlItems . $this->_counts*$step . ', ' . $this->_counts))&&($items->count() > 0)) {
				$step++;
				switch (true) {
					case Entity::checkEntityParam($entity, 'authors'):
						foreach ($items as $item) {
							$sql = ''.
								'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
								'from all_authorslist tA '.
									'join ' . $params['author_table'] . ' t on (t.author_id = tA.id) '.
										'and (t.author_id > 0)'.
										'and (t.' . $params['author_entity_field'] . ' = ' . (int)$item['id'] . ') '.
							'';
							$persons = $this->_query($sql);
							$names = $this->_getAuthorNames($persons);
							if (!empty($names)) {
								$insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
							}
						}
						break;
					case Entity::checkEntityParam($entity, 'actors'):
						foreach ($items as $item) {
							$sql = ''.
								'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
								'from all_authorslist tA '.
								'join ' . $params['actors_table'] . ' t on (t.person_id = tA.id) '.
									'and (t.person_id > 0) '.
									'and (t.video_id = ' . (int)$item['id'] . ') '.
							'';
							$persons = $this->_query($sql);
							$names = $this->_getAuthorNames($persons);
							if (!empty($names)) {
								$insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
							}
						}
						break;
					case Entity::checkEntityParam($entity, 'directors'):
						foreach ($items as $item) {
							$sql = ''.
								'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
								'from all_authorslist tA '.
								'join ' . $params['directors_table'] . ' t on (t.person_id = tA.id) '.
									'and (t.person_id > 0) '.
									'and (t.video_id = ' . (int)$item['id'] . ') '.
							'';
							$persons = $this->_query($sql);
							$names = $this->_getAuthorNames($persons);
							if (!empty($names)) {
								$insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
							}
						}
						break;
					case Entity::checkEntityParam($entity, 'performers'):
						foreach ($items as $item) {
							$sql = ''.
								'select tA.id, tA.title_ru, tA.title_rut, tA.title_en, tA.title_fi '.
								'from all_authorslist tA '.
								'join ' . $params['performer_table'] . ' t on (t.person_id = tA.id) '.
								'and (t.person_id > 0) '.
								'and (t.' . $params['performer_field'] . ' = ' . (int)$item['id'] . ') '.
							'';
							$persons = $this->_query($sql);
							$names = $this->_getAuthorNames($persons);
							if (!empty($names)) {
								$insertPDO->execute(array(':id'=>$item['id'], ':eid'=>$entity, ':name'=>implode(' ', $names), ':pos'=>$item['pos']));
							}
						}
						break;
				}
			}
		}

	}

	private function _query($sql, $params = null) {
		require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

	private function _getAuthorNames($persons) {
		$names = array();
		foreach ($persons as $person) {
			$names = array_merge($names, preg_split("/\W/ui", $person['title_ru']));
			$names = array_merge($names, preg_split("/\W/ui", $person['title_rut']));
			$names = array_merge($names, preg_split("/\W/ui", $person['title_en']));
			$names = array_merge($names, preg_split("/\W/ui", $person['title_fi']));
			$sql = 'select xml_value from compliances where (db_id = ' . (int)$person['id'] . ') and (type_id = 4)';
			foreach ($this->_query($sql) as $compliance) {
				$names = array_merge($names, preg_split("/\W/ui", $compliance['xml_value']));
			}
		}
		$names = array_filter($names);
		return array_unique($names);
	}

	private function _morphy() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$insertPDO = null;
			$insertSql = ''.
				'insert into _morphy_' . $params['entity'] . ' (real_id, isbnnum, title, authors, description) '.
				'values(:real_id, :isbnnum, :title, :authors, :description) '.
				'on duplicate key update isbnnum = :isbnnum, title = :title, authors = :authors, description = :description '.
			'';
			$insertPDO = Yii::app()->db->createCommand($insertSql);
			$insertPDO->prepare();

			$sqlItems = ''.
				'select t.id, ' . ((in_array($entity, array(30, 40)))?'""':'t.isbn') . ' isbn, '.
					't.title_ru, t.title_en, t.title_rut, t.title_fi, '.
					'ifnull(tA.name, "") authors, '.
					't.description_ru, t.description_en, t.description_fi, t.description_rut '.
				'from ' . $params['site_table'] . ' t '.
					'left join _supprort_products_authors tA on (tA.id = t.id) and (tA.eid = ' . (int)$entity . ') '.
				'join _change_items tCI on (tCI.id = t.id) and (tCI.eid = ' . (int)$entity . ') '.
			'';
			echo $sqlItems . "\n";
			$step = 0;
			while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
				$step++;
				foreach ($items as $item) {
					$title = MorphyCommand::getMorphyNames(array('ru'=>$item['title_ru'],'en'=>$item['title_en'],'fi'=>$item['title_fi'],'rut'=>$item['title_rut'],));
					$desc = MorphyCommand::getMorphyNames(array('ru'=>$item['description_ru'],'en'=>$item['description_en'],'fi'=>$item['description_fi'],'rut'=>$item['description_rut'],), $title);
					$insertPDO->execute(array(
						':real_id'=>$item['id'],
						':isbnnum'=>MorphyCommand::getIsbn($item['isbn']),
						':title'=>implode(' ', $title),
						':authors'=>implode(' ', MorphyCommand::getAuthorsMorphy($item['authors'], $title)),
						':description'=>implode(' ', $desc),
					));
					$sql = 'delete from _change_items where (eid = ' . (int)$entity . ') and (id = ' . (int) $item['id'] . ')';
					Yii::app()->db->createCommand($sql)->execute();
				}
				echo date('d.m.Y H:i:s') . "\n";
//			if ($step > 1) break;
			}
			echo date('d.m.Y H:i:s') . "\n";
		}


		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

	/** при поиске авторов морфология не должна учитываться, но должны быть учитаны окончания фамилий при склонении (или множественное/единственное чило)
	 * еще есть авторы в транслите
	 * по этому я решил в поиске использовать словарь stem_enru, а в индекс добавлять только транслит
	 * при поиске нужно так же не забыть преобразовать в транслит
	 * @throws CDbException
	 */
	private function _morphyAuthors() {
		$sql = 'truncate _morphy_authors';
		Yii::app()->db->createCommand($sql)->execute();

		$insertSql = ''.
			'insert into _morphy_authors (real_id, name, morphy_name) '.
			'values(:real_id, :name, :morphy_name) '.
		'';
		$insertPDO = Yii::app()->db->createCommand($insertSql);
		$insertPDO->prepare();
		$sqlItems = ''.
			'select t.id, t.title_ru, t.title_en, t.title_fi, t.title_rut '.
			'from all_authorslist t '.
				'join (select id from all_authorslist order by id limit {start}, {end}) t1 using (id) '.
		'';
		$step = 0;
		$sp = new SearchProducts(0);
		while (($items = $this->_query(str_replace(array('{start}', '{end}'), array($step*$this->_counts, $this->_counts), $sqlItems)))&&($items->count() > 0)) {
			$step++;
			foreach ($items as $item) {
				$name = array();
				$words = preg_split("/\W/ui", $item['title_ru'] . ' ' . $item['title_en'] . ' ' . $item['title_fi'] . ' ' . $item['title_rut']);
				$words = array_unique($words);
				foreach ($words as $k=>$v) {
					if (is_numeric($v)) continue;
					if (mb_strlen($v, 'utf-8') < 2) continue;
					$name[] = $v;
				}
				$morphy = $sp->getNormalizedTransliteWord(implode(' ', $name));
				$insertPDO->execute(array(
					':real_id'=>$item['id'],
					':name'=>implode(' ', $name),
					':morphy_name'=>implode(' ', $morphy),
				));
			}
			echo date('d.m.Y H:i:s') . "\n";
		}

		$sqlItems = ''.
			'select t.db_id, t.xml_value '.
			'from compliances t '.
				'join (select id from compliances where (type_id = 4) order by id limit {start}, {end}) t1 using (id) '.
		'';
		$step = 0;
		while (($items = $this->_query(str_replace(array('{start}', '{end}'), array($step*$this->_counts, $this->_counts), $sqlItems)))&&($items->count() > 0)) {
			$step++;
			foreach ($items as $item) {
				$name = array();
				$words = preg_split("/\W/ui", $item['xml_value']);
				$words = array_unique($words);
				foreach ($words as $k=>$v) {
					if (is_numeric($v)) continue;
					if (mb_strlen($v, 'utf-8') < 2) continue;
					$name[] = $v;
				}
				$morphy = $sp->getNormalizedTransliteWord(implode(' ', $name));
				$insertPDO->execute(array(
					':real_id'=>$item['db_id'],
					':name'=>implode(' ', $name),
					':morphy_name'=>implode(' ', $morphy),
				));
			}
			echo date('d.m.Y H:i:s') . "\n";
		}

	}

}

<?php
/*Created by Кирилл (15.09.2018 0:46)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php beforesphinx
 * товары по языкам. только avail=1
 * Class BeforeSphinxCommand
 */

define('cronAction', 1);
class BeforeSphinxCommand extends CConsoleCommand {
	private $_counts = 10000;

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

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
			$sqlItems = 'select id, title_ru, positionDefault pos from ' . $params['site_table'] . ' where (avail_for_order > 0) limit ';
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

}

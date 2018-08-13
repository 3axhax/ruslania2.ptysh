<?php
/*Created by Кирилл (07.06.2018 20:24)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php repairauthors
 * Class RepairAuthorsCommand
 */
require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
class RepairAuthorsCommand extends CConsoleCommand {
	private $_table = 'all_authorslist';
	private $_apostropheAuthors = array();//авторы, у которых есть апострофы. Надо, что бы сравнить О'Генри и О. Генри и О.Генри

	public function actionIndex() {

		echo 'start ' . date('d.m.Y H:i:s') . "\n";
		foreach ($this->_query($this->_sql100()) as $author) {
			$author = $this->_checkInitials($author);
			if (!empty($author['repair_title_ru'])) $this->_update($author);
		}

		//заполняю массив с апострофами, что бы похожие записи не изменять
		foreach ($this->_query($this->_sqlApostrophe()) as $author) {
			$this->_apostropheAuthors[$author['id']] = preg_replace("/\W+/iu", '', $author['title_ru']);
		}

		foreach ($this->_query($this->_sqlInitials()) as $author) {
			if (!$this->_isApostrophe($author['title_ru'])) {
				$author = $this->_checkInitials($author);
				if (!empty($author['repair_title_ru'])) $this->_update($author);
			}
		}

		foreach ($this->_query($this->_sqlFirstNoAlpha()) as $author) {
			$author = $this->_ltrim($author);
			if (!empty($author['repair_title_ru'])) $this->_update($author);
		}

		$sql = ''.
			'update ' . $this->_table . ' set '	.
				'first_ru = upper(left(trim(title_ru), 1)), '.
				'first_en = upper(left(trim(title_en), 1)) '.
			'where (first_ru is null) or (first_ru = "") '.
		'';
		$this->_query($sql);
		echo 'start ' . date('d.m.Y H:i:s') . " first letter authors complete\n";

		$sql = ''.
			'update all_publishers set '	.
			'first_ru = upper(substring(trim(title_ru), 1, 1)), '.
			'first_en = upper(substring(trim(title_en), 1, 1)) '.
			'where (trim(title_ru) not regexp "^[[:alnum:]]+") and (trim(title_ru) regexp "[[:alnum:]]+") '.
		'';
		$this->_query($sql);

		$sql = ''.
			'update all_publishers set '	.
			'first_ru = upper(left(trim(title_ru), 1)), '.
			'first_en = upper(left(trim(title_en), 1)) '.
			'where (first_ru is null) or (first_ru = "") '.
		'';
		$this->_query($sql);
		echo 'start ' . date('d.m.Y H:i:s') . " first letter publishers complete\n";

		$this->_updateLables();
		$this->_updateLablesPublishers();
		echo 'start ' . date('d.m.Y H:i:s') . " is product complete\n";

		$this->_allRoles();
		echo 'start ' . date('d.m.Y H:i:s') . " all_roles complete\n";
	}

	/** проверяет похож автор на одного из авторов с апострофами
	 * @param $author
	 * @return bool
	 */
	private function _isApostrophe($author) {
		$author = preg_replace("/\W+/iu", '', $author);
		foreach ($this->_apostropheAuthors as $id => $authorApostrophe) {
			if (mb_strpos($author, $authorApostrophe) === 0) return true;
			if (mb_strpos($authorApostrophe, $author) === 0) return true;
		}
		return false;
	}

	/** если получилось определить фамилию, то дополняю массив ключами "repair_.." и исправляю поле "first_.."
	 * @param $author
	 * @return array()
	 */
	private function _checkInitials($author) {
		//на русском языке инициалы по одной букве, а фамилия пусть будет более 1 буквы
		$title = preg_split("/\W+/ui", $author['title_ru']);
		$indexSurname = 0;
		foreach ($title as $i=>$surname) {
			if (mb_strlen($surname, 'utf-8') > 1) {
				$indexSurname = $i;
				break;
			}
		}
		$repairTitleRu = $this->_repair($author['title_ru'], $title, $indexSurname);
		if (!empty($repairTitleRu)) {
			$author['repair_title_ru'] = $repairTitleRu;
			$author['first_ru'] = mb_substr($repairTitleRu, 0, 1, 'utf-8');
			$author['repair_title_en'] = (string) $this->_repair($author['title_en'], preg_split("/\W+/ui", $author['title_en']), $indexSurname);
			if (!empty($author['repair_title_en'])) $author['first_en'] = mb_substr($author['repair_title_en'], 0, 1, 'utf-8');
			$author['repair_title_fi'] = (string) $this->_repair($author['title_fi'], preg_split("/\W+/ui", $author['title_fi']), $indexSurname);
			$author['repair_title_rut'] = (string) $this->_repair($author['title_rut'], preg_split("/\W+/ui", $author['title_rut']), $indexSurname);
		}
		return $author;
	}

	/** очищаю слева не буквы
	 * @param $author
	 * @return mixed
	 */
	private function _ltrim($author) {
		$title = $this->_strToUpperFirst(preg_replace("/^\W+/ui", '', $author['title_ru']));
		$author['repair_title_ru'] = $title;
		$author['first_ru'] = mb_substr($title, 0, 1, 'utf-8');

		$title = $this->_strToUpperFirst(preg_replace("/^\W+/ui", '', $author['title_en']));
		$author['repair_title_en'] = $title;
		if (!empty($author['repair_title_en'])) $author['first_en'] = mb_substr($title, 0, 1, 'utf-8');

		$title = $this->_strToUpperFirst(preg_replace("/^\W+/ui", '', $author['title_fi']));
		$author['repair_title_fi'] = $title;

		$title = $this->_strToUpperFirst(preg_replace("/^\W+/ui", '', $author['title_rut']));
		$author['repair_title_rut'] = $title;
		return $author;
	}

	/**
	 * @param string $title автор
	 * @param array $names ФИО
	 * @param int $indexSurname индекс фамилии в ФИО
	 * @return bool|string
	 */
	private function _repair($title, $names, $indexSurname) {
		if (empty($indexSurname)) return false;

		$posSurname = mb_strpos($title, $names[$indexSurname], null, 'utf-8');
		if (empty($posSurname)) return false;

		return $this->_strToUpperFirst(trim(
			trim(mb_substr($title, $posSurname, null, 'utf-8')) .
			' ' .
			trim(mb_substr($title, 0, $posSurname, 'utf-8'))
		));
	}

	private function _query($sql, $params = null) {
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

	private function _sqlApostrophe() {
		$sql = ''.
			'SELECT id, trim(title_ru) title_ru '.
			'FROM ' . $this->_table . ' '.
			'WHERE (trim(title_ru) regexp "^[[:alpha:]]{1,2}[\'|`]") '.
				'and (repair_title_ru = "") '.
		'';
		return $sql;
	}

	private function _sqlInitials() {
		$sql = ''.
			'SELECT id, trim(title_ru) title_ru, trim(title_rut) title_rut, trim(title_en) title_en, trim(title_fi) title_fi, first_ru, first_en '.
			'FROM ' . $this->_table . ' '.
			'WHERE (trim(title_ru) regexp "^[[:alpha:]]{1,2}[^[:alpha:]]+") '.
				'and (repair_title_ru = "") '.
		'';
		return $sql;
	}

	private function _sqlFirstNoAlpha() {
		$sql = ''.
			'SELECT id, trim(title_ru) title_ru, trim(title_rut) title_rut, trim(title_en) title_en, trim(title_fi) title_fi, first_ru, first_en '.
			'FROM ' . $this->_table . ' '.
			'WHERE (trim(title_ru) not regexp "^[[:alpha:]]") '.
				'and (repair_title_ru = "") '.
			'';
		return $sql;
	}

	/** 100% вначале инициалы
	 * @return string
	 */
	private function _sql100() {
		$sql = ''.
			'select id, trim(title_ru) as title_ru, trim(title_rut) as title_rut, trim(title_en) as title_en, trim(title_fi) as title_fi, first_ru, first_en '.
			'from ' . $this->_table . ' '.
			'where (trim(title_ru) regexp "^[[:alpha:]]{1,2}[^[:alpha:]]+[[:alpha:]]{1,2}[^[:alpha:]]+") '.
				'and (repair_title_ru = "") '.
		'';
		return $sql;
	}

	private function _update($author) {
		$columns = $author;
		unset($columns['id'], $columns['title_ru'], $columns['title_rut'], $columns['title_en'], $columns['title_fi']);
		$pdo = Yii::app()->db->createCommand();
		$pdo->update($this->_table, $columns, '(id = ' . (int) $author['id'] . ')');
	}

	private function _strToUpperFirst($s) {
		if ($s) return mb_strtoupper(mb_substr($s, 0, 1,'utf-8'), 'utf-8') . mb_substr($s, 1, null, 'utf-8');
		return $s;

	}

	private function _updateLables() {
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			switch ((int)$entity) {
				case 10: case 15: case 24:
					$sql = ''.
						'update ' . $this->_table . ' t '.
							'left join ' . $params['author_table'] . ' tIA on (tIA.author_id = t.id) '.
							'left join ' . $params['site_table'] . ' tI on (tI.id = tIA.' . $params['author_entity_field'] . ') AND (tI.avail_for_order = 1) '.
						'set is_' . $entity . '_author = if(tI.id is null, 0, 1) '.
					'';
					$this->_query($sql);
					break;
				case 20:case 22:
				$sql = ''.
					'update ' . $this->_table . ' t '.
						'left join ' . $params['author_table'] . ' tIA on (tIA.author_id = t.id) '.
						'left join ' . $params['site_table'] . ' tI on (tI.id = tIA.' . $params['author_entity_field'] . ') AND (tI.avail_for_order = 1) '.
					'set is_' . $entity . '_author = if(tI.id is null, 0, 1), '.
						'is_' . $entity . '_performer = if(tI.id is null, 0, 1) '.
				'';
				$this->_query($sql);
				$sql = ''.
					'update ' . $this->_table . ' t '.
						'left join ' . $params['performer_table'] . ' tIA on (tIA.person_id = t.id) '.
						'left join ' . $params['site_table'] . ' tI on (tI.id = tIA.' . $params['author_entity_field'] . ') AND (tI.avail_for_order = 1) '.
					'set is_' . $entity . '_performer = if(tI.id is null, 0, 1) '.
				'';
				$this->_query($sql);
					break;
				case 40:
					$sql = ''.
						'update ' . $this->_table . ' t '.
							'left join video_directors tID on (tID.person_id = t.id) '.
							'left join video_catalog tI on (tI.id = tID.video_id) AND (tI.avail_for_order = 1) '.
						'set is_' . $entity . '_director = if(tI.id is null, 0, 1) '.
					'';
					$this->_query($sql);
					$sql = ''.
						'update ' . $this->_table . ' t '.
							'left join video_actors tIA on (tIA.person_id = t.id) '.
							'left join video_catalog tI on (tI.id = tIA.video_id) AND (tI.avail_for_order = 1) '.
						'set is_' . $entity . '_actor = if(tI.id is null, 0, 1) '.
					'';
					$this->_query($sql);
					break;
			}
		}
	}

	private function _updateLablesPublishers() {
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if (in_array('publisher', $params['with'])) {

				$sql = ''.
					'update all_publishers t '.
						'left join ' . $params['site_table'] . ' tI on (tI.publisher_id = t.id) AND (tI.avail_for_order = 1) '.
					'set is_' . $entity . ' = if(tI.id is null, 0, 1) '.
				'';
				$this->_query($sql);
			}
		}
	}

	private function _allRoles() {
		$sql = 'truncate all_roles';
		$this->_query($sql);

		$sql = ''.
			'insert into all_roles (item_id, entity, role_id, person_id, real_item_id) '.
			'select 100000000+t.book_id item_id, 10 entity, 1 role_id, tA.id person_id, t.book_id real_item_id from books_authors t join all_authorslist tA on (tA.id = t.author_id) '.
		'';
		$this->_query($sql);

		$sql = ''.
			'insert into all_roles (item_id, entity, role_id, person_id, real_item_id) '.
			'select 220000000+t.music_id item_id, 22 entity, 1 role_id, tA.id person_id, t.music_id real_item_id from music_authors t join all_authorslist tA on (tA.id = t.author_id) '.
			'';
		$this->_query($sql);

		$sql = ''.
			'insert into all_roles (item_id, entity, role_id, person_id, real_item_id) '.
			'select 240000000+t.soft_id item_id, 24 entity, 1 role_id, tA.id person_id, t.soft_id real_item_id from soft_authors t join all_authorslist tA on (tA.id = t.author_id) '.
			'';
		$this->_query($sql);

		$sql = ''.
			'insert into all_roles (item_id, entity, role_id, person_id, real_item_id) '.
			'select 400000000+t.video_id item_id, 40 entity, 3 role_id, tA.id person_id, t.video_id real_item_id from video_actors t join all_authorslist tA on (tA.id = t.person_id) '.
			'';
		$this->_query($sql);

		$sql = ''.
			'insert into all_roles (item_id, entity, role_id, person_id, real_item_id) '.
			'select 400000000+t.video_id item_id, 40 entity, 4 role_id, tA.id person_id, t.video_id real_item_id from video_directors t join all_authorslist tA on (tA.id = t.person_id) '.
			'';
		$this->_query($sql);

		$sql = ''.
			'insert into all_roles (item_id, entity, role_id, person_id, real_item_id) '.
			'select 220000000+t.music_id item_id, 22 entity, 2 role_id, tA.id person_id, t.music_id real_item_id from music_performers t join all_authorslist tA on (tA.id = t.person_id) '.
			'';
		$this->_query($sql);
	}

}
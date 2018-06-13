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
			'SELECT id, title_ru '.
			'FROM ' . $this->_table . ' '.
			'WHERE (title_ru regexp "^[[:alpha:]]{1,2}[\'|`]") '.
				'and (repair_title_ru = "") '.
		'';
		return $sql;
	}

	private function _sqlInitials() {
		$sql = ''.
			'SELECT id, title_ru, title_rut, title_en, title_fi, first_ru, first_en '.
			'FROM ' . $this->_table . ' '.
			'WHERE (title_ru regexp "^[[:alpha:]]{1,2}[^[:alpha:]]+") '.
				'and (repair_title_ru = "") '.
		'';
		return $sql;
	}

	/** 100% вначале инициалы
	 * @return string
	 */
	private function _sql100() {
		$sql = ''.
			'select id, title_ru, title_rut, title_en, title_fi, first_ru, first_en '.
			'from ' . $this->_table . ' '.
			'where (title_ru regexp "^[[:alpha:]]{1,2}[^[:alpha:]]+[[:alpha:]]{1,2}[^[:alpha:]]+") '.
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

}
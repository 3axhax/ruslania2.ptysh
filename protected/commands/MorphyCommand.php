<?php
/*Created by Кирилл (14.05.2019 17:32)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php morphy
 * готовлю тоблицу для сфинкса
 * для каждого товара оставляю только уникальные слова в нормальной форме
 * Class MorphyCommand
 */

define('cronAction', 1);
class MorphyCommand extends CConsoleCommand {
	private $_counts = 10000;

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		$insertSql = ''.
			'insert into _morphy_books (real_id, avail, isbnnum, eancode, stock_id, title, authors, description, time_position, exists_position) '.
			'values(:real_id, :avail, :isbnnum, :eancode, :stock_id, :title, :authors, :description, :time_position, :exists_position)'.
		'';
		$insertPDO = Yii::app()->db->createCommand($insertSql);
		$insertPDO->prepare();

		$sqlItems = ''.
			'select t.id, t.avail_for_order, t.isbn, t.eancode, t.stock_id, '.
				't.title_ru, t.title_en, t.title_rut, t.title_fi, '.
				'ifnull(tA.name, "") authors, '.
				't.description_ru, t.description_en, t.description_fi, t.description_rut, '.
				't.positionTimeHL time_position, '.
				'case when (t.in_shop between 1 and 5) then 1 when (t.in_shop > 5) then 2 when (tIWL.item_id is not null) then 3 when (t.econet_skip > 0) then 5 else 6 end exists_position '.
			'from books_catalog t '.
				'left join _items_with_label tIWL on (tIWL.item_id = t.id) and (tIWL.entity_id = 10) '.
				'left join _supprort_products_authors tA on (tA.id = t.id) and (tA.eid = 10) '.
				'join (select t1.id from books_catalog t1 left join _morphy_books tMB on (tMB.real_id = t1.id) where (tMB.real_id is null) limit ' . $this->_counts . ') t2 on (t2.id = t.id) '.
			'';
		//TODO:: доделать (для каждого языка свой индекс)
		$step = 0;
		while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
			$step++;
			foreach ($items as $item) {
				$title = $this->_getMorphyNames($item['title']);
				$desc = $this->_getMorphyNames($item['description'], $title, 2);
				$insertPDO->execute(array(
					':real_id'=>$item['id'],
					':avail'=>$item['avail_for_order'],
					':isbnnum'=>$this->_getIsbn($item['isbn']),
					':eancode'=>$item['eancode'],
					':stock_id'=>$item['stock_id'],
					':title'=>implode(' ', $title),
					':authors'=>$item['authors'],
					':description'=>implode(' ', $desc),
					':time_position'=>$item['time_position'],
					':exists_position'=>$item['exists_position'],
				));
			}
//			if ($step > 1) break;
		}

		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}


	private function _query($sql, $params = null) {
		require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

	private function _getMorphyNames($name, $excludeWords = array(), $minLen = 0) {
		$names = array();
		$names = array_merge($names, preg_split("/\W/ui", $name));
		if ($minLen > 0) {
			foreach ($names as $i=>$s) {
				if (mb_strlen($s, 'utf-8') <= $minLen) unset($names[$i]);
			}
		}
		$names = array_unique($names);

		$morphyNames = array();
		foreach(SphinxQL::getDriver()->multiSelect("call keywords (" . SphinxQL::getDriver()->mest(implode(' ', $names)) . ", 'forMorphy')") as $result) {
			if (!in_array($result['normalized'], $excludeWords)) $morphyNames[] = $result['normalized'];
		}
		return array_unique($morphyNames);
	}

	private function _getIsbn($s) {
		return preg_replace("/\D/ui", '', $s);
	}
}

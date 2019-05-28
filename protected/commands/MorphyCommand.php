<?php
/*Created by Кирилл (14.05.2019 17:32)*/
ini_set('max_execution_time', 9600);
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

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
//			if ($entity == 10) continue;
//			if ($entity == 15) continue;
//			if ($entity == 22) continue;
//			if ($entity == 24) continue;
//			if ($entity == 30) continue;
			$insertPDO = null;
			$insertSql = ''.
				'insert into _morphy_' . $params['entity'] . ' (real_id, isbnnum, title, authors, description) '.
				'values(:real_id, :isbnnum, :title, :authors, :description)'.
				'';
			$insertPDO = Yii::app()->db->createCommand($insertSql);
			$insertPDO->prepare();

			$sqlItems = ''.
				'select t.id, ' . ((in_array($entity, array(30, 40)))?'""':'t.isbn') . ' isbn, '.
					't.title_ru, t.title_en, t.title_rut, t.title_fi, '.
					'ifnull(tA.name, "") authors, '.
					't.description_ru, t.description_en, t.description_fi, t.description_rut '.
				'from ' . $params['site_table'] . ' t '.
					'left join _supprort_products_authors tA on (tA.id = t.id) and (tA.eid = ' . $entity . ') '.
					'join (select t1.id from ' . $params['site_table'] . ' t1 left join _morphy_' . $params['entity'] . ' tMB on (tMB.real_id = t1.id) where (tMB.real_id is null) limit ' . $this->_counts . ') t2 on (t2.id = t.id) '.
			'';
//			echo $sqlItems . "\n";
			$step = 0;
			while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
				$step++;
				foreach ($items as $item) {
					$title = self::getMorphyNames(array('ru'=>$item['title_ru'],'en'=>$item['title_en'],'fi'=>$item['title_fi'],'rut'=>$item['title_rut'],));
					$desc = self::getMorphyNames(array('ru'=>$item['description_ru'],'en'=>$item['description_en'],'fi'=>$item['description_fi'],'rut'=>$item['description_rut'],), $title);
					$insertPDO->execute(array(
						':real_id'=>$item['id'],
						':isbnnum'=>self::getIsbn($item['isbn']),
						':title'=>implode(' ', $title),
						':authors'=>implode(' ', self::getAuthorsMorphy($item['authors'], $title)),
						':description'=>implode(' ', $desc),
					));
				}
//				echo date('d.m.Y H:i:s') . "\n";
//			if ($step > 1) break;
			}
//			echo date('d.m.Y H:i:s') . "\n";
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

	static function getMorphyNames($names, $addWords = array()) {
		$allWords = $addWords;
		foreach ($names as $lang=>$name) {
			$words = array();
			$words = array_merge($words, preg_split("/\W/ui", $name));
			$words = array_unique($words);
			if ($lang == 'rut') {
				foreach($words as $normForm) {
					if (is_numeric($normForm)) $allWords[] = $normForm;
					elseif (mb_strlen($normForm, 'utf-8') > 1) $allWords[] = $normForm;
				}
			}
			else {
				$allWords = array_merge($allWords, $words);
			}
		}
		$allWords = array_unique($allWords);
		if (!empty($addWords)&&empty($allWords)) return array();

		$sp = new SearchProducts(0);
		list($searchWords, $realWords, $useRealWord) = $sp->getNormalizedWords(implode(' ', $allWords));
		return $searchWords;
	}

	static function getAuthorsMorphy($names, $addWords = array()) {
		if (empty($names)) return array();
		$morphyNames = self::getMorphyNames(array('ru'=>$names), $addWords);
		$words = array();
		$words = array_merge($words, preg_split("/\W/ui", $names));
		$words = array_unique($words);
		foreach ($words as $author) {
			$author = ProductHelper::ToAscii($author, array('onlyTranslite'=>true, 'lowercase'=>false));
			if (!in_array($author, $morphyNames)) $morphyNames[] = $author;
		}
		return $morphyNames;
	}

	static function getIsbn($s) {
		if (empty($s)) return '';
		return preg_replace("/\D/ui", '', $s);
	}
}

<?php
/*Created by Кирилл (14.05.2019 17:32)*/
ini_set('max_execution_time', 99600);
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
			$sql = 'create table if not exists _tmp_' . $params['site_table'] . ' (`id` int, key(id)) engine=myisam';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$sql = 'truncate _tmp_' . $params['site_table'];
//			Yii::app()->db->createCommand($sql)->execute();

			$sql = ''.
				'insert into _tmp_' . $params['site_table'] . ' (id) '.
				'select t.id '.
				'from ' . $params['site_table'] . ' t '.
			'';
//			Yii::app()->db->createCommand()->setText($sql)->execute();

			$fields = array(
				'title_ru', 'title_en', 'title_fi', 'title_rut', 'title_eco', //'title_original',
				'description_ru', 'description_en', 'description_fi', 'description_de',
				'description_fr', 'description_es', 'description_se', 'description_rut',
			);
			if ($entity == Entity::BOOKS) $fields[] = 'title_original';

			$sphynxPDO = null;
			$sphynxSql = ''.
				'insert into _sphinx_' . $params['entity'] . ' (real_id, isbnnum, ' . implode(', ', $fields) . ', authors) '.
				'values(:real_id, :isbnnum, :' . implode(', :', $fields) . ', :authors)'.
				'on duplicate key update isbnnum = :isbnnum'.
			'';
			foreach ($fields as $f) $sphynxSql .= ', ' . $f . ' = :' . $f;
			$sphynxPDO = Yii::app()->db->createCommand($sphynxSql);
			$sphynxPDO->prepare();

			$sqlItems = ''.
				'select t.id, ' . ((in_array($entity, array(30, 40)))?'""':'t.isbn') . ' isbn, '.
				't.' . implode(', t.', $fields) . ', '.
				'ifnull(tA.name, "") authors '.
				'from ' . $params['site_table'] . ' t '.
					'left join _supprort_products_authors tA on (tA.id = t.id) and (tA.eid = ' . (int)$entity . ') '.
					'join _tmp_' . $params['site_table'] . ' tCI on (tCI.id = t.id) '.
				'limit ' . $this->_counts .
			'';
//			echo $sqlItems . "\n";
			$step = 0;
			while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
				$step++;
				foreach ($items as $item) {
					$authors = self::getMorphy($item['authors']);
					$data = array(
						':real_id'=>$item['id'],
						':isbnnum'=>self::getIsbn($item['isbn']),
						':authors'=>array(),
					);
					if (!empty($authors)) {
						$data[':authors'] = array_merge($authors, self::getMorphy(ProductHelper::ToAscii($item['authors'], array('onlyTranslite'=>true))));
						$data[':authors'] = array_merge($data[':authors'], self::getMorphy(ProductHelper::ToAscii(implode(' ', $authors), array('onlyTranslite'=>true))));
					}
					foreach ($fields as $field) {
						if (mb_strpos($field, 'title_') === 0) {
							$data[':' . $field] = self::getMorphy($item[$field]);
							if (!empty($authors)) $data[':authors'] = array_merge($data[':' . $field], $data[':authors']);
							$data[':' . $field] = implode(' ', $data[':' . $field]);
						}
						else $data[':' . $field] = implode(' ', self::getMorphy($item[$field]));
					}
					$data[':authors'] = array_unique($data[':authors']);
					$data[':authors'] = implode(' ', $data[':authors']);
					$sphynxPDO->execute($data);

					$sql = 'delete from _tmp_' . $params['site_table'] . ' where (id = ' . (int) $item['id'] . ')';
					Yii::app()->db->createCommand($sql)->execute();
				}
				echo date('d.m.Y H:i:s') . "\n";
//			if ($step > 1) break;
			}
			echo date('d.m.Y H:i:s') . "\n";

/*//			if ($entity == 10) continue;
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
//			echo date('d.m.Y H:i:s') . "\n";*/
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

		$sp = new SphinxProducts(0);
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

	static function getMorphy($s) {
		if (empty($s)) return array();

		$sp = new SphinxProducts(0);
		list($title, $realWords, $useRealWord) = $sp->getNormalizedWords($s);
		return $title;
	}


}

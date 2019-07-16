<?php
/*Created by Кирилл (12.07.2019 21:19)*/
ini_set('max_execution_time', 9600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php morphyTranslite
 * готовлю тоблицу для сфинкса
 * для каждого товара оставляю только уникальные слова в нормальной форме
 * для поиска 2 основных поля - из слов на транслите и из слов на нормальных языках
 * (?) слова из транслита не должны попадать в поле для нормальных языков (я еще подумаю, это надо, что бы уменьшить кол-во слов в индексе)
 *
 * порядок слов в поле важен для веса, потому сначала должно быть наименование, потом авторы, потом описание
 * Class MorphyCommand
 */

define('cronAction', 1);
class MorphyTransliteCommand extends CConsoleCommand {
	private $_counts = 10000;

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if ($entity != 10) continue;
			$sql = 'truncate _sphinx_' . $params['entity'];
			Yii::app()->db->createCommand($sql)->execute();

			$fields = array(
				'title_ru', 'title_en', 'title_fi', 'title_rut', 'title_eco', //'title_original',
				'description_ru', 'description_en', 'description_fi', 'description_de',
				'description_fr', 'description_es', 'description_se', 'description_rut',
			);

			$insertPDO = null;
			$insertSql = ''.
				'insert into _sphinx_' . $params['entity'] . ' (real_id, isbnnum, ' . implode(', ', $fields) . ', authors) '.
				'values(:real_id, :isbnnum, :' . implode(', :', $fields) . ', :authors)'.
			'';
			$insertPDO = Yii::app()->db->createCommand($insertSql);
			$insertPDO->prepare();

			$sqlItems = ''.
				'select t.id, ' . ((in_array($entity, array(30, 40)))?'""':'t.isbn') . ' isbn, '.
					't.' . implode(', t.', $fields) . ', '.
					'ifnull(tA.name, "") authors '.
				'from ' . $params['site_table'] . ' t '.
					'left join _supprort_products_authors tA on (tA.id = t.id) and (tA.eid = ' . $entity . ') '.
					'join (select t1.id from ' . $params['site_table'] . ' t1 order by t1.id limit {start}, {end}) t2 on (t2.id = t.id) '.
			'';
			echo $sqlItems . "\n";
			$step = 0;
			while (($items = $this->_query(str_replace(array('{start}', '{end}'), array($step*$this->_counts, $this->_counts), $sqlItems)))&&($items->count() > 0)) {
				$step++;
				foreach ($items as $item) {
					$authors = $this->getMorphy($item['authors']);
					$data = array(
						':real_id'=>$item['id'],
						':isbnnum'=>self::getIsbn($item['isbn']),
						':authors'=>array(),
					);
					if (!empty($authors)) {
						$data[':authors'] = array_merge($authors, $this->getMorphy(ProductHelper::ToAscii($item['authors'], array('onlyTranslite'=>true))));
						$data[':authors'] = array_merge($data[':authors'], $this->getMorphy(ProductHelper::ToAscii(implode(' ', $authors), array('onlyTranslite'=>true))));
					}
					foreach ($fields as $field) {
						if (mb_strpos($field, 'title_') === 0) {
							$data[':' . $field] = $this->getMorphy($item[$field]);
							if (!empty($authors)) $data[':authors'] = array_merge($data[':' . $field], $data[':authors']);
							$data[':' . $field] = implode(' ', $data[':' . $field]);
						}
						else $data[':' . $field] = implode(' ', $this->getMorphy($item[$field]));
					}
					$data[':authors'] = array_unique($data[':authors']);
					$data[':authors'] = implode(' ', $data[':authors']);
					$insertPDO->execute($data);
				}
				echo date('d.m.Y H:i:s') . "\n";
//			if ($step > 1) break;
			}
			echo date('d.m.Y H:i:s') . "\n";
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

	protected function getMorphy($s) {
		if (empty($s)) return array();

		$sp = new SearchProducts(0);
		list($title, $realWords, $useRealWord) = $sp->getNormalizedWords($s);
		return $title;
	}

	static function getMorphyNames($item) {
		$transliteTitle = array_unique(preg_split("/\W/ui", $item['title_rut']));
		$transliteTitleWithoutNumeric = array_filter($transliteTitle, function($s) {return !is_numeric($s);});
		$transliteDesc = array_unique(preg_split("/\W/ui", $item['description_rut']));
		$transliteDescWithoutNumeric = array_filter($transliteDesc, function($s) {return !is_numeric($s);});

		$ruTitle = array_unique(preg_split("/\W/ui", $item['title_ru']));
		$ruTitle = array_diff($ruTitle, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);
		$ruDesc = array_unique(preg_split("/\W/ui", $item['description_ru']));
		$ruDesc = array_diff($ruDesc, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);

		$enTitle = array_unique(preg_split("/\W/ui", $item['title_en']));
		$enTitle = array_diff($enTitle, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);
		$enDesc = array_unique(preg_split("/\W/ui", $item['description_en']));
		$enDesc = array_diff($enDesc, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);

		$fiTitle = array_unique(preg_split("/\W/ui", $item['title_fi']));
		$fiTitle = array_diff($fiTitle, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);
		$fiDesc = array_unique(preg_split("/\W/ui", $item['description_fi']));
		$fiDesc = array_diff($fiDesc, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);

		$authors = array_unique(preg_split("/\W/ui", $item['authors']));
		$authors = array_diff($authors, $transliteTitleWithoutNumeric, $transliteDescWithoutNumeric);

		$sp = new SearchProducts(0);
		list($title, $realWords, $useRealWord) = $sp->getNormalizedWords(implode(' ', $ruTitle) . ' ' . implode(' ', $fiTitle) . ' ' . implode(' ', $enTitle));
		list($desc, $realWords, $useRealWord) = $sp->getNormalizedWords(implode(' ', $ruDesc) . ' ' . implode(' ', $fiDesc) . ' ' . implode(' ', $enDesc));
		list($authors, $realWords, $useRealWord) = $sp->getNormalizedWords(implode(' ', $authors));

//		$translite = array_merge($transliteTitle, $transliteDesc);

		$translite = $sp->getNormalizedTransliteWord(
			implode(' ', $transliteTitle) . ' ' .
			implode(' ', $title) . ' ' .
			implode(' ', $authors) . ' ' .
			implode(' ', $transliteDesc) . ' ' .
			implode(' ', $desc)
			, false
		);

		$txt = array_unique(array_merge($title, $authors, $desc));
		$txt = array_diff($txt, $translite);
		return array($txt, $translite);
	}

	static function getIsbn($s) {
		if (empty($s)) return '';
		return preg_replace("/\D/ui", '', $s);
	}

}

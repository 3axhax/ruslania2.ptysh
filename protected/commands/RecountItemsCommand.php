<?php
/*Created by Кирилл (30.07.2018 19:00)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php recountitems
 * Class RecountItemsCommand
 */

class RecountItemsCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";
		$sql = 'create temporary table _tmp_least_categorys (id int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_counts (id int, items_count int, avail_items_count int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$sql = 'update ' . $params['site_category_table'] . ' set items_count = 0, avail_items_count = 0';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = ''.
				'insert into _tmp_least_categorys (id) '.
				'select t.`id` '.
				'FROM `' . $params['site_category_table'] . '` t '.
					'left join ' . $params['site_category_table'] . ' tC on (tC.parent_id = t.id) '.
				'where (tC.id is null) '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$this->_recount($params['site_category_table'], $params['site_table']);
			$sql = 'truncate _tmp_least_categorys';
			Yii::app()->db->createCommand()->setText($sql)->execute();
		}
		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

	private function _recount($catTable, $itemTable) {
		$sql = ''.
			'update ' . $catTable . ' tCat '.
				'join ('.
					'select tT.id, count(*) itemCounts, sum(t.avail_for_order) itemCountsAvail '.
					'from ' . $itemTable . ' t '.
						'join _tmp_least_categorys tT on (tT.id = t.code) '.
					'group by t.code'.
				') tCount using (id) '.
			'set tCat.items_count = tCount.itemCounts, tCat.avail_items_count = itemCountsAvail '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		echo $sql . "\n";
		$sql = ''.
			'update ' . $catTable . ' tCat '.
				'join ('.
					'select tT.id, count(*) itemCounts, sum(t.avail_for_order) itemCountsAvail '.
					'from ' . $itemTable . ' t '.
						'join _tmp_least_categorys tT on (tT.id = t.subcode) '.
					'group by t.subcode'.
				') tCount using (id) '.
			'set tCat.items_count = tCat.items_count + tCount.itemCounts, tCat.avail_items_count = tCat.avail_items_count + itemCountsAvail '.
		'';
		echo $sql . "\n";
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$i=0;//на случай, если какой то категории нет товаров
		do {
			$sql = ''.
				'insert into _tmp_counts (id, items_count, avail_items_count) '.
				'select t.id, sum(tC.items_count), sum(tC.avail_items_count) '.
				'from ' . $catTable . ' t '.
					'join ' . $catTable . ' tC on (tC.parent_id = t.id) '.
				'where (t.items_count = 0) '.
				'group by t.id '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			echo $sql . "\n";

			$sql = ''.
				'update ' . $catTable . ' t '.
					'join _tmp_counts tC using (id) '.
				'set t.items_count = tC.items_count, t.avail_items_count = tC.avail_items_count '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = 'truncate _tmp_counts';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$isCount = (bool) Yii::app()->db->createCommand('select 1 from ' . $catTable . ' where (items_count = 0) limit 1')->queryScalar();
		} while ($isCount&&(++$i<10)); // делаю 10 итераций на всякий случай

	}

}

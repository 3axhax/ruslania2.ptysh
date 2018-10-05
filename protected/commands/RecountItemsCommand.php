<?php
/*Created by Кирилл (30.07.2018 19:00)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php recountitems
 * пересчет количества в разделах
 * Class RecountItemsCommand
 */

class RecountItemsCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";
		$sql = 'create temporary table _tmp_least_categorys (id int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_counts (id int, items_count int, avail_items_count int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_types (id int, avail_items_type_1 int, avail_items_type_2 int, avail_items_type_3 int, avail_items_type_4 int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_position (position int AUTO_INCREMENT, id int, primary key(position), UNIQUE INDEX (id))';
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

			if ($entity == Entity::PERIODIC) $this->_periodikTypes($params['site_category_table'], $params['site_table']);

			$sql = 'truncate _tmp_least_categorys';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$this->_updatePosition($entity, $params);
		}
		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

	private function _recount($catTable, $itemTable) {
		$sql = 'update ' . $catTable . ' set items_count = 0, avail_items_count = 0';
		Yii::app()->db->createCommand()->setText($sql)->execute();
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
//		echo $sql . "\n";
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
//		echo $sql . "\n";
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
//			echo $sql . "\n";

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

	private function _periodikTypes($catTable, $itemTable) {
		$sql = 'update ' . $catTable . ' set avail_items_type_1 = 0,avail_items_type_2 = 0, avail_items_type_3 = 0, avail_items_type_4 = 0';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = ''.
			'update ' . $catTable . ' tCat '.
				'join ('.
					'select tT.id, sum(if(t.type = 1, 1, 0)) type1, sum(if(t.type = 2, 1, 0)) type2, sum(if(t.type = 3, 1, 0)) type3, sum(if(t.type = 4, 1, 0)) type4 '.
					'from ' . $itemTable . ' t '.
						'join _tmp_least_categorys tT on (tT.id = t.code) '.
					'where (t.avail_for_order = 1) '.
					'group by t.code'.
				') tCount using (id) '.
			'set tCat.avail_items_type_1 = tCount.type1, tCat.avail_items_type_2 = tCount.type2, tCat.avail_items_type_3 = tCount.type3, tCat.avail_items_type_4 = tCount.type4 '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();
//		echo $sql . "\n";
		$sql = ''.
			'update ' . $catTable . ' tCat '.
				'join ('.
					'select tT.id, sum(if(t.type = 1, 1, 0)) type1, sum(if(t.type = 2, 1, 0)) type2, sum(if(t.type = 3, 1, 0)) type3, sum(if(t.type = 4, 1, 0)) type4 '.
					'from ' . $itemTable . ' t '.
						'join _tmp_least_categorys tT on (tT.id = t.subcode) '.
					'where (t.avail_for_order = 1) '.
					'group by t.subcode'.
				') tCount using (id) '.
			'set tCat.avail_items_type_1 = tCat.avail_items_type_1 + tCount.type1, '.
				'tCat.avail_items_type_2 = tCat.avail_items_type_2 + tCount.type2, '.
				'tCat.avail_items_type_3 = tCat.avail_items_type_3 + tCount.type3, '.
				'tCat.avail_items_type_4 = tCat.avail_items_type_4 + tCount.type4 '.
			'';
//		echo $sql . "\n";
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$i=0;//на случай, если какой то категории нет товаров
		do {
			$sql = ''.
				'insert into _tmp_types (id, avail_items_type_1, avail_items_type_2, avail_items_type_3, avail_items_type_4) '.
				'select t.id, sum(tC.avail_items_type_1), sum(tC.avail_items_type_2), sum(tC.avail_items_type_3), sum(tC.avail_items_type_4) '.
				'from ' . $catTable . ' t '.
					'join ' . $catTable . ' tC on (tC.parent_id = t.id) '.
				'where (t.avail_items_type_1 = 0) and (t.avail_items_type_2 = 0) and (t.avail_items_type_3 = 0) and (t.avail_items_type_4 = 0) '.
				'group by t.id '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();
//			echo $sql . "\n";

			$sql = ''.
				'update ' . $catTable . ' t '.
				'join _tmp_types tC using (id) '.
				'set t.avail_items_type_1 = tC.avail_items_type_1, t.avail_items_type_2 = tC.avail_items_type_2, t.avail_items_type_3 = tC.avail_items_type_3, t.avail_items_type_4 = tC.avail_items_type_4 '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = 'truncate _tmp_types';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$isCount = (bool) Yii::app()->db->createCommand('select 1 from ' . $catTable . ' where (items_count = 0) limit 1')->queryScalar();
		} while ($isCount&&(++$i<10)); // делаю 10 итераций на всякий случай

	}

	private function _updatePosition($entity, $params) {
		foreach (SortOptions::GetSortData() as $sort=>$name) {
			$sql = ''.
				'insert into _tmp_position (id) '.
				'select t.id '.
				'from ' . $params['site_table'] . ' t '.
					'left join vendors tVendots on (tVendots.id = t.vendor) '.
					'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tVendots.dtid) '.
				'order by ' . SortOptions::GetSQLPrepare($sort, '', $entity) .
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = ''.
				'update ' . $params['site_table'] . ' t '.
					'join _tmp_position tTmp using (id) '.
				'set ' . SortOptions::GetSQL($sort, '', $entity) . ' = tTmp.position '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = 'truncate _tmp_position';
			Yii::app()->db->createCommand()->setText($sql)->execute();
		}
	}

}

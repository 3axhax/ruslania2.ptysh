<?php
/*Created by Кирилл (30.07.2018 19:00)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php recountitems
 * пересчет количества в разделах
 * Class RecountItemsCommand
 */

define('cronAction', 1);
class RecountItemsCommand extends CConsoleCommand {

	function __destruct() {
		foreach (SortOptions::GetSortData() as $sort=>$name) {
			$sql = 'drop table if exists _tmp_position_' . $sort;
			Yii::app()->db->createCommand()->setText($sql)->execute();
		}
		$sql = 'drop table if exists items_years';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'RENAME TABLE `_years` TO `items_years`';
		Yii::app()->db->createCommand()->setText($sql)->execute();
	}

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";
		$sql = 'drop table if exists _years';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create table _years (`year` int, eid int, key(eid)) engine=myisam';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_least_categorys (id int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_counts (id int, items_count int, avail_items_count int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create temporary table _tmp_types (id int, avail_items_type_1 int, avail_items_type_2 int, avail_items_type_3 int, avail_items_type_4 int, primary key(id))';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		foreach (SortOptions::GetSortData() as $sort=>$name) {
			$sql = 'create table if not exists _tmp_position_' . $sort . ' (position int AUTO_INCREMENT, id int, primary key(position), UNIQUE INDEX (id))';
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = 'truncate _tmp_position_' . $sort . '';
			Yii::app()->db->createCommand()->setText($sql)->execute();
		}

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$this->_offers($entity, $params);
			$this->_years($entity, $params);
			if ($entity == 20) continue;
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

		$this->_clearSimilar();
		$this->_clearBanners();
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

	/** очистка подборок от товаров, которых не должно быть
	 * @param $entity
	 * @param $params
	 */
	private function _offers($entity, $params) {
		if ($entity == Entity::PERIODIC) return;

		$sql = ''.
			'delete t '.
			'from offer_items t '.
				'join ' . $params['site_table'] . ' tI on (tI.id = t.item_id) and (((tI.brutto > 2) and (tI.discount = 0)) or (tI.discount > 2)) '.
			'where (t.offer_id = 999) and (t.entity_id = ' . (int) $entity . ') '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$sql = 'select group_order from offer_items where (offer_id = 999) limit 1';
		$groupOrder = (int)Yii::app()->db->createCommand()->setText($sql)->queryScalar();
		$brutto = Condition::get($entity, 0)->getBruttoWithDiscount(false);
		$sql = ''.
			'insert ignore into offer_items (offer_id, entity_id, item_id, group_order, sort_order) '.
			'select 999, ' . (int) $entity . ', t.id, ' . $groupOrder . ', 1 '.
			'from ' . $params['site_table'] . ' t '.
			'where (' . $brutto . ' > 0) and (' . $brutto . ' <= 2) '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$sql = ''.
			'delete t '.
			'from offer_items t '.
				'join ' . $params['site_table'] . ' tI on (tI.id = t.item_id) and (tI.unitweight_skip = 0) and (tI.unitweight > 0) '.
			'where (t.offer_id = 777) and (t.entity_id = ' . (int) $entity . ') '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$sql = 'select group_order from offer_items where (offer_id = 777) limit 1';
		$groupOrder = (int)Yii::app()->db->createCommand()->setText($sql)->queryScalar();
		$sql = ''.
			'insert ignore into offer_items (offer_id, entity_id, item_id, group_order, sort_order) '.
			'select 777, ' . (int) $entity . ', t.id, ' . $groupOrder . ', 1 '.
			'from ' . $params['site_table'] . ' t '.
			'where (t.unitweight_skip > 0) or (t.unitweight = 0) '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();
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

	/**
	 *
	1. Новинка
	2. В магазине или заканчивается, дата вписывания до 6 мес
	3. skip, дата вписывания до 2 мес
	4. В магазине или заканчивается, дата вписывания от 6 мес до 1 года
	5. skip, дата вписывания от 2 мес до 1 года
	6. В магазине или заканчивается, дата вписывания более 1 года
	7. skip, дата вписывания более 1 года
	8. нет в наличии по дате вписания
	 *
	 * @param $entity
	 * @param $params
	 * @throws CDbException
	 * @throws CException
	 */
	private function _updatePosition($entity, $params) {
		foreach (SortOptions::GetSortData() as $sort=>$name) {
			if ($sort == SortOptions::DefaultSort) {
				switch ($entity) {
					case Entity::PERIODIC: $this->_updatePositionDefault30($params); break;
					case Entity::SHEETMUSIC: $this->_updatePositionDefault15($params); break;
					default:
						$time = date('Y-m-d H:0:0');
						/*1 товары с флажками "новинка"
                        *внутри них: дата вписывания, свежая дата вперед
                        *независимо от того, есть-ли товар на складе или нет
                        */
						$sql = ''.
							'insert into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'join action_items tAI on (tAI.item_id = t.id) and (tAI.entity = ' . (int) $entity . ') and (tAI.type = 1) '. //3-товар дня, 1-новинка
							'order by t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*2 товар есть в магазине или "заканчивается в магазине"
                        * внутри них: дата вписывания, свежая дата вперед
                        * дата вписывания до 6 мес */
						$sql = ''.
							'insert ignore into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'where (t.avail_for_order = 1) and (t.in_shop > 0) and (t.add_date > DATE_ADD("' . $time . '", INTERVAL -6 MONTH)) '.
							'order by t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*3 товары, который нет в магазине, но которые можно купить (есть скип)
                        * дата вписывания до 2 мес
                        * по сроку доставки (зависит от поставщика) (у каждого поставщика есть свой срок доставки)
                        * внутри них: дата вписывания, свежая дата вперед
                        */
						$sql = ''.
							'insert ignore into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'left join vendors tVendots on (tVendots.id = t.vendor) '.
							'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tVendots.dtid) '.
							'where (t.avail_for_order = 1) and (t.in_shop = 0) and (t.add_date > DATE_ADD("' . $time . '", INTERVAL -2 MONTH)) '.
							'order by deliveryTime.position ASC, t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*4 В магазине или заканчивается, дата вписывания от 6 мес до 1 года
                        * внутри них: дата вписывания, свежая дата вперед
                        */					$sql = ''.
						'insert ignore into _tmp_position_' . $sort . ' (id) '.
						'select t.id '.
						'from ' . $params['site_table'] . ' t '.
						'where (t.avail_for_order = 1) and (t.in_shop > 0) and (t.add_date between DATE_ADD("' . $time . '", INTERVAL -12 MONTH) and DATE_ADD("' . $time . '", INTERVAL -6 MONTH)) '.
						'order by t.add_date desc ' .
						'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*5 товары, который нет в магазине, но которые можно купить (есть скип)
                        * дата вписывания от 2 мес до 1 года
                        * по сроку доставки (зависит от поставщика) (у каждого поставщика есть свой срок доставки)
                        * внутри них: дата вписывания, свежая дата вперед
                        */
						$sql = ''.
							'insert ignore into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'left join vendors tVendots on (tVendots.id = t.vendor) '.
							'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tVendots.dtid) '.
							'where (t.avail_for_order = 1) and (t.in_shop = 0) and (t.add_date between DATE_ADD("' . $time . '", INTERVAL -12 MONTH) and DATE_ADD("' . $time . '", INTERVAL -2 MONTH)) '.
							'order by deliveryTime.position ASC, t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*6 товар есть в магазине или "заканчивается в магазине"
                        * внутри них: дата вписывания, свежая дата вперед
                        * дата вписывания более 1 года */
						$sql = ''.
							'insert ignore into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'where (t.avail_for_order = 1) and (t.in_shop > 0) and (t.add_date < DATE_ADD("' . $time . '", INTERVAL -12 MONTH)) '.
							'order by t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*7 товары, который нет в магазине, но которые можно купить (есть скип)
                        * дата вписывания более 1 годаа
                        * по сроку доставки (зависит от поставщика) (у каждого поставщика есть свой срок доставки)
                        * внутри них: дата вписывания, свежая дата вперед
                        */
						$sql = ''.
							'insert ignore into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'left join vendors tVendots on (tVendots.id = t.vendor) '.
							'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tVendots.dtid) '.
							'where (t.avail_for_order = 1) and (t.in_shop = 0) and (t.add_date < DATE_ADD("' . $time . '", INTERVAL -12 MONTH)) '.
							'order by deliveryTime.position ASC, t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();

						/*нет в наличии по дате вписания */
						$sql = ''.
							'insert ignore into _tmp_position_' . $sort . ' (id) '.
							'select t.id '.
							'from ' . $params['site_table'] . ' t '.
							'where (t.avail_for_order = 0) '.
							'order by t.add_date desc ' .
							'';
						Yii::app()->db->createCommand()->setText($sql)->execute();
						break;
				}
			}
			else {
				$sql = ''.
					'insert into _tmp_position_' . $sort . ' (id) '.
					'select t.id '.
					'from ' . $params['site_table'] . ' t '.
						'left join vendors tVendots on (tVendots.id = t.vendor) '.
						'left join delivery_time_list deliveryTime on (deliveryTime.dtid = tVendots.dtid) '.
					'order by ' . SortOptions::GetSQLPrepare($sort, '', $entity) .
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();
			}
		}

		$sql = 'update ' . $params['site_table'] . ' t ';
		foreach (SortOptions::GetSortData() as $sort=>$name) {
			$sql .= 'left join _tmp_position_' . $sort . ' tTmp' . $sort . ' on (tTmp' . $sort . '.id = t.id) ';
		}
		$sql .= 'set ';
		foreach (SortOptions::GetSortData() as $sort=>$name) {
			$sql .= SortOptions::GetSQL($sort, '', $entity) . ' = tTmp' . $sort . '.position, ';
		}
		$sql = mb_substr($sql, 0, -2, 'utf-8');
		Yii::app()->db->createCommand()->setText($sql)->execute();

		foreach (SortOptions::GetSortData() as $sort=>$name) {
			$sql = 'truncate _tmp_position_' . $sort . '';
			Yii::app()->db->createCommand()->setText($sql)->execute();
		}
	}

	/**
	 * запрос для получения годов, в которых есть товар очень медленный,
	 * здесь года сохраняются в таблицу
	 */
	private function _years($entity, $params) {
		if (Entity::checkEntityParam($entity, 'years')) {
			$sql = ''.
				'insert into _years (`year`, eid) '.
				'select t.year, ' . (int) $entity . ' '.
				'from `' . $params['site_table'] . '` t '.
				'where (t.year is not null) and (t.year > 0) and (t.avail_for_order > 0) '.
				'group by t.year '.
			'';
//			echo $sql . "\n";
			Yii::app()->db->createCommand()->setText($sql)->execute();
		}
	}

	/**
	 * очистка таблицы _similar_items
	 * удаляется все, что старее 14 дней
	 */
	private function _clearSimilar() {
		$sql = 'delete from _similar_items where (date_add < ' . mktime(0, 0, 0, date('n'), date('j')-14, date('Y')) . ')';
		Yii::app()->db->createCommand($sql)->query();
	}

	/**
	 * очистка таблицы _banner_items
	 * удаляется все, что старее 1 мес
	 */
	private function _clearBanners() {
		$sql = 'delete from _banner_items where (date_add < ' . mktime(0, 0, 0, date('n')-1, date('j'), date('Y')) . ')';
		Yii::app()->db->createCommand($sql)->query();
	}

	/** для подписки
	 * @param $params
	 * @throws CDbException
	 * @throws CException
	 */
	private function _updatePositionDefault30($params) {
		$entity = Entity::PERIODIC;
		$sort = SortOptions::DefaultSort;
		$sql = ''.
			'insert into _tmp_position_' . $sort . ' (id) '.
			'select t.id '.
			'from ' . $params['site_table'] . ' t '.
			'join _items_with_label tAI on (tAI.item_id = t.id) and (entity_id = ' . (int) $entity . ') and (tAI.type <> 3) '. //3-товар дня
//						'where (t.avail_for_order = 1) '.
			'order by t.avail_for_order desc, tAI.type asc, t.add_date desc ' .
			'';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = ''.
			'insert ignore into _tmp_position_' . $sort . ' (id) '.
			'select t.id '.
			'from ' . $params['site_table'] . ' t '.
			'left join _items_with_label tAI on (tAI.item_id = t.id) and (entity_id = ' . (int) $entity . ') and (tAI.type <> 3) '. //3-товар дня
			'where (tAI.item_id is null) '.
			'order by t.avail_for_order desc, t.add_date desc ' .
			'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

	}

	/** для нот
	 * @param $params
	 * @throws CDbException
	 * @throws CException
	 *
	 * Порядок:

	Сначала
	товары "в наличии" или "можно купить, есть скип" (эти два типа товаров не отличаются)

	1 товары с флажками "новинка"
	 *внутри них: дата вписывания, свежая дата вперед

	2 товары с флажками "в рекомендованных"
	 *внутри них: дата вписывания, свежая дата вперед

	после товаров с флажками:
	3 товар есть в магазине или "заканчивается в магазине" или "можно купить", т.е. есть скип
	 *внутри них: дата вписывания, свежая дата вперед

	После - товар не в наличии, "сообщить о поступлении"
	 *внутри них: дата вписывания, свежая дата вперед
	 */
	private function _updatePositionDefault15($params) {
		$entity = Entity::SHEETMUSIC;
		$sort = SortOptions::DefaultSort;
		$time = date('Y-m-d H:0:0');
		/*
			1 товары с флажками "новинка"
			 *внутри них: дата вписывания, свежая дата вперед
		*/
		$sql = ''.
			'insert ignore into _tmp_position_' . $sort . ' (id) '.
			'select t.id '.
			'from ' . $params['site_table'] . ' t '.
				'join action_items tAI on (tAI.item_id = t.id) and (tAI.entity = ' . (int) $entity . ') and (tAI.type = 1) '. //3-товар дня, 1-новинка
			'where (t.avail_for_order = 1) '.
			'order by t.add_date desc ' .
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		/*
			2 товары с флажками "в рекомендованных"
			 *внутри них: дата вписывания, свежая дата вперед
		*/
		$sql = ''.
			'insert ignore into _tmp_position_' . $sort . ' (id) '.
			'select t.id '.
			'from ' . $params['site_table'] . ' t '.
				'join offer_items tAI on (tAI.item_id = t.id) and (tAI.entity_id = ' . (int) $entity . ') ' .
				'join offers tOf on (tOf.id = tAI.offer_id) and (tOf.is_active > 0) and (tOf.id not in (777, 999)) '.
			'where (t.avail_for_order = 1) '.
			'order by t.add_date desc ' .
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		/*
			3 товар есть в магазине или "заканчивается в магазине" или "можно купить", т.е. есть скип
			 *внутри них: дата вписывания, свежая дата вперед
		 */
		$sql = ''.
			'insert ignore into _tmp_position_' . $sort . ' (id) '.
			'select t.id '.
			'from ' . $params['site_table'] . ' t '.
			'where (t.avail_for_order = 1) '.
			'order by t.add_date desc ' .
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		/*
			После - товар не в наличии, "сообщить о поступлении"
			 *внутри них: дата вписывания, свежая дата вперед
		*/
		$sql = ''.
			'insert ignore into _tmp_position_' . $sort . ' (id) '.
			'select t.id '.
			'from ' . $params['site_table'] . ' t '.
			'where (t.avail_for_order = 0) '.
			'order by t.add_date desc ' .
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();
	}

}

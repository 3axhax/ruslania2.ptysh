<?php
/*Created by Кирилл (07.08.2018 19:56)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php categorydata
 * минимальная и максимальная цена и год
 * Class CategoryDataCommand
 */

class CategoryDataCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		$supportTable = '_support_category_data';
		$tmpTable = '_tmp_support_category';
		$sql = 'drop table if exists ' . $tmpTable;
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'create table ' . $tmpTable . ' like ' . $supportTable;
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$inswerSql = ''.
			'insert ignore into ' . $tmpTable . ' set '.
			'entity = :entity, '.
			'category_id = :catId, '.
			'settings = :settings'.
		'';
		$pdo = Yii::app()->db->createCommand($inswerSql);
		$pdo->prepare();

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if ($entity == Entity::PERIODIC) continue;

			$result = array();
			$brutto = Condition::get($entity, 0)->getBruttoWithDiscount(false);
			$sql = ''.
				'select 0 catId, min(t.brutto) minP, max(t.brutto) maxP, '.
					'min(' . $brutto . ') minPD, max(' . $brutto . ') maxPD, '.
					'min(t.year) minY, max(t.year) maxY '.
				'from ' . $params['site_table'] . ' t '.
				'where (t.avail_for_order = 1) '.
			'';
			$result = $this->_fill($result, Yii::app()->db->createCommand($sql)->queryAll());

			$sql = ''.
				'select t.code catId, min(t.brutto) minP, max(t.brutto) maxP, '.
					'min(' . $brutto . ') minPD, max(' . $brutto . ') maxPD, '.
					'min(t.year) minY, max(t.year) maxY '.
				'from ' . $params['site_table'] . ' t '.
				'where (t.avail_for_order = 1) '.
				'group by t.code '.
			'';
			$result = $this->_fill($result, Yii::app()->db->createCommand($sql)->queryAll());

			$sql = ''.
				'select t.subcode catId, min(t.brutto) minP, max(t.brutto) maxP, '.
					'min(' . $brutto . ') minPD, max(' . $brutto . ') maxPD, '.
					'min(t.year) minY, max(t.year) maxY '.
				'from ' . $params['site_table'] . ' t '.
				'where (t.avail_for_order = 1) '.
				'group by t.subcode '.
			'';
			$result = $this->_fill($result, Yii::app()->db->createCommand($sql)->queryAll());

			$result = $this->_settingsParentCategory($params, $result);
			foreach ($result as $id=>$set) {
				if (!empty($set)) {
					$settitngs = array(
						'year_min' => $set['minY'],
						'year_max' => $set['maxY'],
						'cost_min' => $set['minP'],
						'cost_max' => $set['maxP'],
						'cost_min_discount' => $set['minPD'], //все скидки без учета скидок пользователя
						'cost_max_discount' => $set['maxPD'], //все скидки без учета скидок пользователя
					);
					$insertParams = array(
						':entity'=>$entity,
						':catId'=>$id,
						':settings'=>serialize($settitngs),
					);
					$pdo->getPdoStatement()->execute($insertParams);
				}
			}

		}
		$sql = 'drop table if exists ' . $supportTable;
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'rename table ' . $tmpTable . ' to ' . $supportTable;
		Yii::app()->db->createCommand()->setText($sql)->execute();

		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

	private function _fill($result, $rows) {
		foreach ($rows as $cat) {
			$catId = $cat['catId'];
			unset($cat['catId']);
			if (empty($result[$catId])) $result[$catId] = $cat;
			else $result[$catId] = $this->_getRow($result[$catId], $cat);
		}
		return $result;
	}

	private function _getRow($row, $cat) {
		$row['minP'] = min($row['minP'], $cat['minP']);
		$row['maxP'] = max($row['maxP'], $cat['maxP']);
		$row['minPD'] = min($row['minPD'], $cat['minPD']);
		$row['maxPD'] = max($row['maxPD'], $cat['maxPD']);
		$row['minY'] = min($row['minY'], $cat['minY']);
		$row['maxY'] = max($row['maxY'], $cat['maxY']);
		return $row;
	}

	private function _settingsParentCategory($params, $result) {
		$childIds = array_filter(array_keys($result));

		$i=0;//на случай, если какой то категории нет товаров
		do {
			$sql = ''.
				'select parent_id, group_concat(id) ids '.
				'from ' . $params['site_category_table'] . ' '.
				'where (id in (' . implode(', ', $childIds) . ')) '.
				'group by parent_id'.
			'';
			$parentIds = array();
			foreach (Yii::app()->db->createCommand($sql)->queryAll() as $cat) {
				if ($cat['parent_id'] > 0) $parentIds[$cat['parent_id']] = explode(',', $cat['ids']);
			}

			$parentSet = array();
			foreach ($parentIds as $pId=>$ids) {
				foreach ($ids as $id) {
					if (!empty($result[$id])) {
						if (empty($parentSet[$pId])) {
							$parentSet[$pId] = $result[$id];
							$parentSet[$pId]['catId'] = $pId;
						}
						else {
							$parentSet[$pId] = $this->_getRow($parentSet[$pId], $result[$id]);
						}
					}
				}
			}
			$result = $this->_fill($result, $parentSet);
			$childIds = array_filter(array_keys($parentSet));
		} while (!empty($parentIds)&&(++$i<10)); // делаю 10 итераций на всякий случай

		return $result;
	}
}

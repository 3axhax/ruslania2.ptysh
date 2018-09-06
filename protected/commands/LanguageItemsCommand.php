<?php
/*Created by Кирилл (31.07.2018 20:39)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php languageitems
 * товары по языкам. только avail=1
 * Class LanguageItemsCommand
 */

class LanguageItemsCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		$sql = 'delete from all_items_languages where (entity = 30)';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		$sql = ''.
			'insert into all_items_languages (entity, item_id, language_id) '.
			'select 30, periodic_id, language_id '.
			'from periodics_languages '.
		'';
		Yii::app()->db->createCommand()->setText($sql)->execute();

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
//			if ($entity == 30) continue;

			$tmpTable = '_support_languages_';
			$sql = 'drop table if exists ' . $tmpTable;
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = 'create table ' . $tmpTable . ' like _support_languages_books';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$year = '0';
			if (Entity::checkEntityParam($entity, 'years')) $year = 't.year';

			$sql = ''.
				'insert ignore into ' . $tmpTable . ' (id, category_id, language_id, year, brutto, discount, isSubcode) ' .
				'select t.id, t.code, tL.language_id, ' . $year . ', ' . (($entity == 30)?'0':'t.brutto') . ', t.discount, 0 '.
				'from ' . $params['site_table'] . ' t '.
					'join all_items_languages tL on (tL.item_id = t.id) and (tL.entity = ' . $entity . ') and (tL.language_id > 0) '.
				'where (t.avail_for_order = 1) '.
			'';
//			echo $sql . "\n";
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$sql = ''.
				'insert ignore into ' . $tmpTable . ' (id, category_id, language_id, year, brutto, discount, isSubcode) ' .
				'select t.id, t.subcode, tL.language_id, ' . $year . ', ' . (($entity == 30)?'0':'t.brutto') . ', t.discount, 1 '.
				'from ' . $params['site_table'] . ' t '.
					'join all_items_languages tL on (tL.item_id = t.id) and (tL.entity = ' . $entity . ') and (tL.language_id > 0) '.
				'where (t.avail_for_order = 1) and (t.subcode not in (ifnull(subcode, 0), t.code)) '.
			'';
//			echo $sql . "\n";
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$entityStr = Entity::GetUrlKey($entity);
			$supportTable = '_support_languages_' . $entityStr;
			$sql = 'drop table if exists ' . $supportTable;
			Yii::app()->db->createCommand()->setText($sql)->execute();
			$sql = 'rename table ' . $tmpTable . ' to ' . $supportTable;
			Yii::app()->db->createCommand()->setText($sql)->execute();

		}
		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

}

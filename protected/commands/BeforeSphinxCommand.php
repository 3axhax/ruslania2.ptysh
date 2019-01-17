<?php
/*Created by Кирилл (15.09.2018 0:46)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php beforesphinx
 * товары по языкам. только avail=1
 * Class BeforeSphinxCommand
 */

define('cronAction', 1);
class BeforeSphinxCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		$sql = 'truncate _items_with_label';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'insert ignore into _items_with_label (entity_id, item_id, type) select entity_id, item_id, 2 from offer_items';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'insert ignore into _items_with_label (entity_id, item_id, type) select entity entity_id, item_id, 1 from action_items';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		$sql = 'truncate all_categories';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if (($entity != 20)&&!empty($params['site_category_table'])) {
				$sql = ''.
					'insert ignore into all_categories (entity, real_id, title_ru, title_en, title_fi, title_rut) '.
					'select ' . $entity . ', id, title_ru, title_en, title_fi, title_rut '.
					'from ' . $params['site_category_table'] . ' '.
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();
			}
		}

		$sql = 'truncate all_series';
		Yii::app()->db->createCommand()->setText($sql)->execute();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if (($entity != 20)&&!empty($params['site_series_table'])) {
				$sql = ''.
					'insert ignore into all_series (entity, id, title_ru, title_en, title_fi, title_rut) '.
					'select ' . $entity . ', id, title_ru, title_en, title_fi, title_rut '.
					'from ' . $params['site_series_table'] . ' '.
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();

				$sql = ''.
					'update all_series t '.
					'left join ' . $params['site_table'] . ' tI on (tI.series_id = t.id) AND (tI.avail_for_order = 1) '.
					'set is_' . $entity . ' = if(tI.id is null, 0, 1) '.
				'';
				Yii::app()->db->createCommand()->setText($sql)->execute();;

			}
		}

		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

}

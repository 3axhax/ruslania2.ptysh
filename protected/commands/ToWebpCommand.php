<?php
/*Created by Кирилл (24.07.2019 21:37)*/

ini_set('max_execution_time', 99600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php towebp
 * конвертация картинок в формат webp
 * Class MorphyCommand
 */

define('cronAction', 1);
class ToWebpCommand extends CConsoleCommand {
	private $_counts = 100;

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			if ($entity != 10) continue;

			$sql = 'create table if not exists _tmp_' . $params['photo_table'] . ' (`id` int, `eancode` varchar(100), `image` varchar(100), key(id)) engine=myisam';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$sql = 'truncate _tmp_' . $params['photo_table'];
			Yii::app()->db->createCommand($sql)->execute();

			$sql = ''.
				'insert into _tmp_' . $params['photo_table'] . ' (id, eancode, image) '.
				'select t.id, t.eancode, t.image '.
				'from ' . $params['site_table'] . ' t '.
				'left join ' . $params['photo_table'] . ' tF on (tF.iid = t.id) '.
				'where (tF.iid is null) '.
			'';
			Yii::app()->db->createCommand()->setText($sql)->execute();

			$modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
			/**@var $model ModelsPhotos*/
			$model = $modelName::model();
			$sqlItems = ''.
				'select t.id, t.eancode, t.image '.
				'from _tmp_' . $params['photo_table'] . ' t '.
				'order by t.id desc '.
				'limit ' . $this->_counts . ' '.
			'';
//			echo $sqlItems . "\n";
			$step = 0;
			while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
				$step++;
				foreach ($items as $item) {
					$sql = 'delete from _tmp_' . $params['photo_table'] . ' where (id = ' . (int) $item['id'] . ')';
					Yii::app()->db->createCommand()->setText($sql)->execute();
					if (empty($item['image'])) continue;

					$filePhoto = Yii::getPathOfAlias('webroot') . '/pictures/big/' . $item['image'];
					if (file_exists($filePhoto)) {
						$model->setAttributes(array('iid'=>$item['id'], 'is_upload'=>1), false);
						$model->setIsNewRecord(true);
						$model->id = null;
						$model->insert();
						if ($model->createFotos($filePhoto, $model->id, $item['eancode'])) {
							$model->setAttribute('is_upload', 2);
							$model->update();
							$sql = 'insert ignore into _no_photo (eid, id, ean) values (:eid, :id, :ean)';
							Yii::app()->db->createCommand($sql)->execute(array(':eid'=>$entity, ':id'=>$item['id'], ':ean'=>$item['eancode']));
						}
					}
					else {
						$model->setAttributes(array('iid'=>$item['id'], 'is_upload'=>2), false);
						$model->setIsNewRecord(true);
						$model->id = null;
						$model->insert();
						$sql = 'insert ignore into _no_photo (eid, id, ean) values (:eid, :id, :ean)';
						Yii::app()->db->createCommand($sql)->execute(array(':eid'=>$entity, ':id'=>$item['id'], ':ean'=>$item['eancode']));
					}
//					echo $model->id . ' ' . $item['image'] . "\n\r";
				}
				if (!($step%10)) echo $step . ' ' . $params['photo_table'] . ' ' . date('d.m.Y H:i:s') . "\n";
//break;
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

}
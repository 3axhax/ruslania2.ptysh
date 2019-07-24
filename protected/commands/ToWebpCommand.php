<?php
/*Created by Кирилл (24.07.2019 21:37)*/

ini_set('max_execution_time', 9600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php towebp
 * конвертация картинок в формат webp
 * Class MorphyCommand
 */

define('cronAction', 1);
class ToWebpCommand extends CConsoleCommand {
	private $_counts = 10000;

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
			/**@var $model ModelsPhotos*/
			$model = $modelName::model();
			if ($entity != 10) continue;

			$sqlItems = ''.
				'select t.id, eancode, image '.
				'from ' . $params['site_table'] . ' t '.
				'join (select t1.id from ' . $params['site_table'] . ' t1 left join ' . $params['photo_table'] . ' tF on (tF.iid = t1.id) where (tF.iid is null) and (t1.image <> "") limit ' . $this->_counts . ') t2 on (t2.id = t.id) '.
			'';
//			echo $sqlItems . "\n";
			$step = 0;
			while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
				$step++;
				foreach ($items as $item) {
					$filePhoto = Yii::getPathOfAlias('webroot') . '/pictures/big/' . $item['image'];
					if (file_exists($filePhoto)) {
						$model->setAttributes(array('iid'=>$item['id'], 'is_upload'=>1), false);
						$model->setIsNewRecord(true);
						$model->id = null;
						$model->insert();
						$model->createFotos($filePhoto, $model->id, $item['eancode']);
					}
					else {
						$model->setAttributes(array('iid'=>$item['id'], 'is_upload'=>2), false);
						$model->setIsNewRecord(true);
						$model->id = null;
						$model->insert();
						$sql = 'insert into _no_photo (eid, id, ean) values (:eid, :id, :ean)';
						Yii::app()->db->createCommand($sql)->execute(array(':eid'=>$entity, ':id'=>$item['id'], ':ean'=>$item['eancode']));
					}
//					echo $model->id . ' ' . $item['image'] . "\n\r";
				}
				echo $step . ' ' . $params['photo_table'] . ' ' . date('d.m.Y H:i:s') . "\n";
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

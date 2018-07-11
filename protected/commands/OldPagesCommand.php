<?php
/*Created by Кирилл (11.07.2018 19:20)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php oldpages
 * Class RepairAuthorsCommand
 */
define('OLD_PAGES', 1);

require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
class OldPagesCommand extends CConsoleCommand {
	protected $_counts = 10; //кол-во записей за один проход

	public function actionIndex() {

		$inswerSql = ''.
			'insert ignore into seo_redirects set '.
				'entity = :entity, '.
				'route = :route, '.
				'id = :id, '.
				'path = :path, '.
				'lang = :lang '.
		'';
		$pdo = Yii::app()->db->createCommand($inswerSql);
		$pdo->prepare();
		foreach (Entity::GetEntitiesList() as $entity=>$params) {
			$step = 0;
			while (($items = $this->_query($this->_sqlItems($params['site_table'], $step++)))&&($items->count() > 0)) {
				foreach ($items as $item) {
					$item['entity'] = $entity;
					$params = array(
						':entity'=>$entity,
						':route'=>'product/view',
						':id'=>$item['id'],
						':path'=>ProductHelper::CreateUrl($item, 'ru'),
						':lang'=>'ru',
					);
					$pdo->getPdoStatement()->execute($params);
					$params[':path'] = ProductHelper::CreateUrl($item, 'rut');
					$params[':lang'] = 'rut';
					$pdo->getPdoStatement()->execute($params);
					$params[':path'] = ProductHelper::CreateUrl($item, 'en');
					$params[':lang'] = 'en';
					$pdo->getPdoStatement()->execute($params);
					$params[':path'] = ProductHelper::CreateUrl($item, 'fi');
					$params[':lang'] = 'fi';
					$pdo->getPdoStatement()->execute($params);
				}
				return;
			}
		}

	}

	private function _query($sql, $params = null) {
		$pdo = Yii::app()->db->createCommand($sql);
		$pdo->prepare();
		$pdo->getPdoStatement()->execute($params);
		return new IteratorsPDO($pdo->getPdoStatement());
	}

	private function _sqlItems($table, $step) {
		return ''.
		'select t.id, t.title_ru, title_rut, title_en, title_fi '.
		'from `' . $table . '` t ' .
			'join (select id from `' . $table . '` order by id limit ' . $this->_counts*$step . ', ' . $this->_counts . ') tId using (id) '.
		'';
	}

}

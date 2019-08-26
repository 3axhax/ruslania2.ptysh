<?php /** Created by Кирилл rkv@dfaktor.ru 07.08.2019 20:13*/
ini_set('max_execution_time', 99600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php downloadphotos
 * закачка картинок с других сайтов
 * Class MorphyCommand
 */

define('cronAction', 1);
class DownloadPhotosCommand extends CConsoleCommand {
    private $_counts = 10000;

    public function actionIndex() {
        echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            $modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
            /**@var $model ModelsPhotos*/
            $model = $modelName::model();
            $sqlItems = ''.
                'select tF.id, tF.href, tF.crop, t.eancode '.
                'from ' . $params['photo_table'] . ' tF '.
                    'join ' . $params['site_table'] . ' t on (t.id = tF.iid) '.
                'where (tF.is_upload = 0) and (tF.href <> "") '.
                'order by t.id desc '.
                'limit ' . $this->_counts . ' '.
            '';
//			echo $sqlItems . "\n";
            $step = 0;
            $items = $this->_query($sqlItems);
            if ($items->count() > 0) {
                $step++;
                foreach ($items as $item) {
                    $filePhoto = $model->downloadFile($item['href'], $item['id'], $item['eancode'], $item['crop']);
                    if ($filePhoto&&file_exists($filePhoto)&&$model->createFotos($filePhoto, $item['id'], '', 80, false, false)) {
                        $sql = 'update ' . $params['photo_table'] . ' set is_upload = 1 where (id = ' . (int) $item['id'] . ')';
                        Yii::app()->db->createCommand()->setText($sql)->execute();
                    }
                    else {
                        $sql = 'update ' . $params['photo_table'] . ' set is_upload = 3 where (id = ' . (int) $item['id'] . ')';
                        Yii::app()->db->createCommand()->setText($sql)->execute();
                    }
                }
                if (!($step%10)) echo $step . ' ' . $params['photo_table'] . ' ' . date('d.m.Y H:i:s') . "\n";
            }
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

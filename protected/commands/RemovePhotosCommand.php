<?php
/**
 * Created by PhpStorm.
 * User: kiril
 * Date: 02.08.2019
 * Time: 19:11
 */

ini_set('max_execution_time', 99600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php removePhotos
 * Class RemovePhotosCommand
 */

define('cronAction', 1);
class RemovePhotosCommand extends CConsoleCommand {
    private $_counts = 100;

    public function actionIndex() {
        echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

//select t.id, t.last_modification_date, t.eancode
//from books_catalog t
//where (t.last_modification_date > '2019-07-25')
//order by t.last_modification_date
        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            $sql = '' .
                'insert ignore into _no_photo (eid, id, ean) ' .
                'select ' . (int)$entity . ', t.id, t.eancode ' .
                'from ' . $params['site_table'] . ' t ' .
                'where (t.last_modification_date > "2019-07-25") ' .
            '';
            echo $sql . "\n";
            Yii::app()->db->createCommand($sql)->execute();
        }

        echo 'end ' . date('d.m.Y H:i:s') . "\n";
    }
/*    public function actionIndex() {
        echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            if ($entity == 10) continue;

            $modelName = mb_strtoupper(mb_substr($params['photo_table'], 0, 1, 'utf-8'), 'utf-8') . mb_substr($params['photo_table'], 1, null, 'utf-8');
            $model = $modelName::model();

            $sql = 'drop table if exists _tmp_' . $params['photo_table'] . '_to_update';
            Yii::app()->db->createCommand()->setText($sql)->execute();

            $sql = 'create table if not exists _tmp_' . $params['photo_table'] . '_to_update (`id_foto` int, `id` int, `is_upload` int, `href` varchar(250), `eancode` varchar(250), key(id)) engine=myisam';
            Yii::app()->db->createCommand()->setText($sql)->execute();

            $sql = 'truncate _tmp_' . $params['photo_table'] . '_to_update';
            Yii::app()->db->createCommand($sql)->execute();

            $sql = ''.
                'insert into _tmp_' . $params['photo_table'] . '_to_update (id_foto, id, is_upload, href, eancode) '.
                'select tF.id id_foto, t.id, tF.is_upload, tF.href, t.eancode '.
                'from ' . $params['site_table'] . ' t '.
                    'left join ' . $params['photo_table'] . ' tF on (tF.iid = t.id) '.
            '';
            echo $sql . "\n";
            Yii::app()->db->createCommand()->setText($sql)->execute();

            $sqlItems = ''.
                'select t.id_foto, t.id, t.is_upload, t.href, t.eancode '.
                'from _tmp_' . $params['photo_table'] . '_to_update t '.
                'order by t.id '.
                'limit ' . $this->_counts . ' '.
            '';
			echo $sqlItems . "\n";
            $step = 0;
            while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
                $step++;
                foreach ($items as $item) {
                    $sql = 'delete from _tmp_' . $params['photo_table'] . '_to_update where (id = ' . (int) $item['id'] . ')';
//                    var_dump($item); echo $sql . "\n";
                    Yii::app()->db->createCommand()->setText($sql)->execute();

                    $filePhoto = $model->getUnixDir() . $model->getRelativePath($item['id_foto']);
                    switch ((int) $item['is_upload']) {
                        case 0: break;
                        case 1:
                            $this->_renameFotos($filePhoto);
                            break;
                        default:
                            $this->_renameFotos($filePhoto);
                            $sql = 'insert ignore into _no_photo (eid, id, ean) values (:eid, :id, :ean)';
                            Yii::app()->db->createCommand($sql)->execute(array(':eid'=>$entity, ':id'=>$item['id'], ':ean'=>$item['eancode']));
                            break;
                    }
//                    exit;
                }
                if (!($step%100)) echo $step . ' ' . $params['photo_table'] . ' ' . date('d.m.Y H:i:s') . "\n";
            }
			echo $params['photo_table'] . ' ' . date('d.m.Y H:i:s') . "\n";
        }


        echo 'end ' . date('d.m.Y H:i:s') . "\n";
    }*/


/*        public function actionIndex() {
        echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            if ($entity != 10) continue;

            $sql = 'create table if not exists _tmp_' . $params['photo_table'] . '_to_remove (`id` int, `eancode` varchar(100), `image` varchar(100), key(id)) engine=myisam';
            Yii::app()->db->createCommand()->setText($sql)->execute();

            $sql = 'create table if not exists _tmp_' . $params['photo_table'] . '_remove (`id` int, key(id)) engine=myisam';
            Yii::app()->db->createCommand()->setText($sql)->execute();

            $sql = 'truncate _tmp_' . $params['photo_table'] . '_to_remove';
            Yii::app()->db->createCommand($sql)->execute();

            $sql = ''.
                'insert into _tmp_' . $params['photo_table'] . '_to_remove (id, eancode, image) '.
                'select t.id, t.eancode, t.image '.
                'from ' . $params['site_table'] . ' t '.
                    'join ' . $params['photo_table'] . ' tF on (tF.iid = t.id) and (tF.is_upload = 1) '.
                    'left join _tmp_' . $params['photo_table'] . '_remove tR on (tR.id = t.id) '.
                'where (tR.id is null) '.
            '';
            Yii::app()->db->createCommand()->setText($sql)->execute();

            $sqlItems = ''.
                'select t.id, t.eancode, t.image '.
                'from _tmp_' . $params['photo_table'] . '_to_remove t '.
                'order by t.id '.
                'limit ' . $this->_counts . ' '.
            '';
//			echo $sqlItems . "\n";
            $step = 0;
            while (($items = $this->_query($sqlItems))&&($items->count() > 0)) {
                $step++;
                foreach ($items as $item) {
                    $sql = 'delete from _tmp_' . $params['photo_table'] . '_to_remove where (id = ' . (int) $item['id'] . ')';
//                    echo $sql . "\n";
                    Yii::app()->db->createCommand()->setText($sql)->execute();
                    $sql = 'insert ignore into _tmp_' . $params['photo_table'] . '_remove (id) values (:id)';
//                    echo $sql . "\n";
                    Yii::app()->db->createCommand($sql)->execute(array(':id'=>$item['id']));
                    if (empty($item['image'])) continue;

                    $filePhotoBig = Yii::getPathOfAlias('webroot') . '/pictures/big/' . $item['image'];
                    $filePhotoSmall = Yii::getPathOfAlias('webroot') . '/pictures/small/' . $item['image'];
                    if (file_exists($filePhotoBig)) {
//                        echo $filePhotoBig . "\n";
                        @unlink($filePhotoBig);
                    }
                    if (file_exists($filePhotoSmall)) {
//                        echo $filePhotoSmall . "\n";
                        @unlink($filePhotoSmall);
                    }
                }
//                exit;
                if (!($step%10)) echo $step . ' ' . $params['photo_table'] . ' ' . date('d.m.Y H:i:s') . "\n";
//break;
            }
//			echo date('d.m.Y H:i:s') . "\n";
        }


        echo 'end ' . date('d.m.Y H:i:s') . "\n";
    }*/


    private function _query($sql, $params = null) {
        require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
        $pdo = Yii::app()->db->createCommand($sql);
        $pdo->prepare();
        $pdo->getPdoStatement()->execute($params);
        return new IteratorsPDO($pdo->getPdoStatement());
    }

    protected function _renameFotos($dirPath) {
        if (file_exists($dirPath) && is_dir($dirPath)){
            $dirHandle = opendir($dirPath);
            while (false !== ($file = readdir($dirHandle))) {
                if ($file != '.' && $file != '..') {
                    $newName = explode('_', $file);
                    if (count($newName) > 1) {
                        $newName = array_pop($newName);
                        if ($newName === 'orig.jpg') {
                            unlink($dirPath . '/' . $file);
                        }
                        else {
                            rename($dirPath . '/' . $file, $dirPath . '/' . $newName);
                        }
                    }
                }
            }
            closedir($dirHandle);
            return true;
        }
        return false;
    }



}

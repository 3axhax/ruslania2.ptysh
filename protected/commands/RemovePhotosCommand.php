<?php
/**
 * Created by PhpStorm.
 * User: kiril
 * Date: 02.08.2019
 * Time: 19:11
 */

ini_set('max_execution_time', 99600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php removePhotos
 * удаление старых фоток
 * Class RemovePhotosCommand
 */

define('cronAction', 1);
class RemovePhotosCommand extends CConsoleCommand {
    private $_counts = 100;

    public function actionIndex() {
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
                    'join ' . $params['photo_table'] . ' tF on (tF.iid = t.id) '.
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
    }


    private function _query($sql, $params = null) {
        require_once Yii::getPathOfAlias('webroot') . '/protected/iterators/PDO.php';
        $pdo = Yii::app()->db->createCommand($sql);
        $pdo->prepare();
        $pdo->getPdoStatement()->execute($params);
        return new IteratorsPDO($pdo->getPdoStatement());
    }

}

<?php /** Created by Кирилл rkv@dfaktor.ru 03.09.2019 21:45*/
ini_set('max_execution_time', 99600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php clearphotos
 * удаление файлов фотографий из файловой системы сервера
 * Class ClearPhotosCommand
 */

define('cronAction', 1);
class ClearPhotosCommand extends CConsoleCommand {
    private $_existsPDO = null;

    public function actionIndex() {
        echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";
        $sql = 'create table if not exists _exists_photos (`id` int, `eid` varchar(100), key(id, eid)) engine=myisam';
        Yii::app()->db->createCommand()->setText($sql)->execute();
        $sql = 'truncate _exists_photos';
        Yii::app()->db->createCommand()->setText($sql)->execute();

        foreach (Entity::GetEntitiesList() as $entity=>$params) {
//            $this->_clearTable($params['photo_table'], $params['site_table']);
            $this->_existsPhotos($params['photo_table'], $entity);
        }
        foreach (Entity::GetEntitiesList() as $entity=>$params) {
            $this->_remove($params['photo_table'], $entity);
        }
        echo 'end ' . date('d.m.Y H:i:s') . "\n";
    }

    /** очистка таблицы фотографий от записей, если товаров не существует
     * исключение is_upload = 0, потому что товары могут появиться после синхронизации.
     * @param $tablePhoto
     * @param $tableCatalog
     */
    private function _clearTable($tablePhoto, $tableCatalog) {
        return; //таблицу чистить нельзя потому, что есть товары, которых нет на сайте, но есть в базе mssql
        $sql = ''.
            'delete t '.
            'from ' . $tablePhoto . ' tF '.
                'left join ' . $tableCatalog . ' t on (t.id = tF.iid) '.
            'where (tF.is_upload > 0) and (t.id is null) '.
        '';
        echo $sql . "\n";
    }

    private function _existsPhotos($tablePhoto, $eid) {
        if ($this->_existsPDO === null) {
            $insertSql = 'insert into _exists_photos (`id`, `eid`) values(:id, :eid)';
            $this->_existsPDO = Yii::app()->db->createCommand($insertSql);
            $this->_existsPDO->prepare();
        }
        $modelName = mb_strtoupper(mb_substr($tablePhoto, 0, 1, 'utf-8'), 'utf-8') . mb_substr($tablePhoto, 1, null, 'utf-8');
        /**@var $photoModel ModelsPhotos*/
        $photoModel = $modelName::model();
        $dir = $photoModel->getUnixDir();
        $it = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
        foreach ($it as $mil) {
            if ($mil->isDir()) {
                $milInt = $mil->getFilename();
                $decFolder = new FilesystemIterator($dir . $milInt . '/', FilesystemIterator::SKIP_DOTS);
                foreach ($decFolder as $unit) {
                    if ($unit->isDir()) {
                        $this->_existsPDO->execute(array(':id' => $unit->getFilename(), ':eid' => $eid));
                    }
                }
            }
        }
    }

    private function _remove($tablePhoto, $eid) {
        $modelName = mb_strtoupper(mb_substr($tablePhoto, 0, 1, 'utf-8'), 'utf-8') . mb_substr($tablePhoto, 1, null, 'utf-8');
        /**@var $photoModel ModelsPhotos*/
        $photoModel = $modelName::model();
        $sql = ''.
            'select t.id '.
            'from _exists_photos t '.
                'left join ' . $tablePhoto . ' tF on (tF.id = t.id) and (tF.is_upload = 1) '.
            'where (t.eid = ' . (int) $eid . ') and (tF.id is null) '.
        '';
        foreach (Yii::app()->db->createCommand($sql)->queryColumn() as $idPhoto) {
            $photoModel->remove($idPhoto);
        }
    }

}

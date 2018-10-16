<?php
/*Created by Кирилл (07.08.2018 19:56)*/

ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php graberstatic
 * копирую тексты статических страниц с сайта https://ruslania.com/pictures/templates-static/aboutus_fi.html.php
 *
 * Скрипт, если находит данные на сайте ruslania.com, делает копию текущей версии файла (добавляет в имя файла _дата_время) и сохраняет новый файл с новыми данными
 *
 * Class GraberStaticCommand
 */
define('cronAction', 1);
class GraberStaticCommand extends CConsoleCommand {

    function actionIndex() {
        $langs = Yii::app()->params['ValidLanguages'];
        $pages = StaticUrlRule::getTitles();
        $path = Yii::getPathOfAlias('webroot') . '/pictures/templates-static/';
        if (!is_dir($path)) mkdir($path);
/*        foreach ($pages as $page=>$name) {
            foreach ($langs as $lang) {
                $txt = trim($this->_loadText($page, $lang));
                if (!empty($txt)) $this->_saveText($path . $page . '_' . $lang . '.html.php', $txt);
            }
        }*/

        /*'это для восстановления файлов
         *
         * $files = new FilesystemIterator($path);
        foreach($files as $file) {
            $fileName = $file->getFilename();
            if (preg_match("/(_\d+_\d+)$/ui", $fileName, $m)) {
                $fileNew = str_replace($m[0], '', $fileName);
                @unlink($path . $fileNew);
                rename($path . $fileName, $path . $fileNew);
            }
        }*/

        $files = new FilesystemIterator($path);
        foreach($files as $file) {
            $fileName = $file->getFilename();
            if (preg_match("/_(ru|rut|en|fi|de|fr|se|es)\.html\.php$/ui", $fileName, $m)) {
//                @unlink($path . $fileNew);
//                rename($path . $fileName, $path . $fileNew);
//                var_dump($m[0]); echo "\n";
                $name = str_replace($m[0], '', $fileName);
                $lang = $m[1];
                $sql = ''.
                    'insert into static_pages (name, description_' . $lang . ') '.
                    'values (:name, :desc) '.
                    'on duplicate key update description_' . $lang . ' = :desc ' .
                '';
                Yii::app()->db->createCommand()->setText($sql)->execute(array(':name'=>$name, ':desc'=>@file_get_contents($path . $fileName)));
            }
        }



    }

    private function _loadText($page, $lang) {
        $url = 'https://ruslania.com/pictures/templates-static/' . $page . '_' . $lang . '.html.php';
        return @file_get_contents($url);
    }

    private function _saveText($file, $txt) {
        if (file_exists($file)) rename($file, $file . date('_dmY_his'));
        file_put_contents($file, $txt, FILE_APPEND);
    }

}

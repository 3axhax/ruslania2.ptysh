<?php /** Created by Кирилл rkv@dfaktor.ru 14.08.2019 21:04*/

require_once dirname(__FILE__) . '/Photos.php';
class PhotosConvert extends ModelsPhotos {

    function tableName() {
        return Entity::GetEntitiesList()[Entity::BOOKS]['photo_table'];
    }

    function createFotos($tmpName, $idFoto, $ean, $quality = 80, $removeExistsFiles = true, $createOrig = true) {
        $dirPath = Yii::getPathOfAlias('webroot') . '/new_img/pay/';
        if (file_exists($dirPath) && is_dir($dirPath)){
            $dirHandle = opendir($dirPath);
            while (false !== ($file = readdir($dirHandle))) {
                if ($file != '.' && $file != '..') {
                    switch ($file) {
                        case 'paytrail.gif': $h = 100; $w = 100; break;
                        case 'visa.gif':case 'visael.gif':case 'mastercart.gif':case 'pasti.gif': $h = 40; $w = 500; break;
                        default: $h = 32; $w = 500; break;
                    }
                    $this->_createNewFoto($dirPath . mb_substr($file, 0, -4, 'utf-8'), $dirPath . $file, $w, $h, $quality);
                }
            }
            closedir($dirHandle);
        }
        return true;
    }


}
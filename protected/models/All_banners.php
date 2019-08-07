<?php
/*Created by Кирилл (24.07.2019 19:16)*/
require_once dirname(__FILE__) . '/Photos/Photos.php';
class All_banners extends ModelsPhotos {

	protected $_lables = array(
		'orig'=>['width'=>0, 'height'=>0],//оригинальный размер
		'mb'=>['width'=>1170, 'height'=>261],//Большой на главной (вместо слайдера акционных товаров)
		'ms'=>['width'=>570, 'height'=>157],//Маленький на главной справа/слева (вместо товара дня)
		'l'=>['width'=>900, 'height'=>247],//в карточке или в списке
	);

	static function model($className = __CLASS__) {
		return parent::model($className);
	}
	function tableName() {
		return 'all_banners';
	}

	function createFotos($tmpName, $id, $ean){
		parent::createFotos($tmpName, $id, $ean);
		$sql = 'update ' . $this->tableName() . ' set webp_' . $ean . ' = 1 where (id = ' . (int)$id . ') limit 1';
		Yii::app()->db->createCommand($sql)->execute();
		return true;
	}

	protected function _createFolderForFotos($idFoto = null, $removeExistsFiles = false) {
		$directory = $this->getUnixDir();
		if ($idFoto !== null){
			$directory .= $this->getRelativePath($idFoto);
		}
		$this->_mkDirRecursive($directory);
		return $directory;
	}

	function getFirstId($id) {
		return $id;
	}

	function getPhotoIds($id) {
		return array($id);
	}

	function getPhotos($ids) {
		return array();
	}

}
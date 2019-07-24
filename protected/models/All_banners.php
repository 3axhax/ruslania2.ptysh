<?php
/*Created by Кирилл (24.07.2019 19:16)*/
require_once dirname(__FILE__) . '/Photos/Photos.php';
class All_banners extends ModelsPhotos {

	static function model($className = __CLASS__) {
		return parent::model($className);
	}
	function tableName() {
		return 'all_banners';
	}

	/**
	 * @param $tmpName
	 * @param $id
	 * @param $ean string язык сайта
	 * @return bool
	 */
	function createFotos($tmpName, $id, $ean){
		$param = $this->_getFotoParams($tmpName);
		$fotoDir = $this->_createFolderForFotos($id);
		$this->_createNewFoto($fotoDir . $ean, $tmpName, $param['width'], $param['height'], 75);
		$sql = 'update ' . $this->tableName() . ' set webp_' . $ean . ' = 1 where (id = ' . (int)$id . ') limit 1';
		Yii::app()->db->createCommand($sql)->execute();
		return true;
	}

	protected function _createFolderForFotos($idFoto = null) {
		$directory = $this->getUnixDir();
		if ($idFoto !== null){
			$directory .= $this->getRelativePath($idFoto);
		}
		$this->_mkDirRecursive($directory);
		return $directory;
	}

}
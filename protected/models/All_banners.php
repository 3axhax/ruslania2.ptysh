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

	function getHrefPath($idFoto, $label, $ean, $ext) {
		if (!empty($this->_externalPhotos[$idFoto])) return $this->_externalPhotos[$idFoto];

		$path = Yii::app()->params['PicDomain'] . '/pictures/' . $this->tableName() . '/' . $this->getRelativePath($idFoto) . $ean;
		if (!empty($label)) $path .= '_' . $label;
		switch ($ext) {
			case 'jpg':case 'webp': $path .= '.' . $ext; break;
//			case '': break;
			default: return '/';
		}
		return $path;
	}

	function createFotos($tmpName, $id, $ean, $quality = 80, $removeExistsFiles = false, $createOrig = true){
		parent::createFotos($tmpName, $id, $ean, $quality, $removeExistsFiles, $createOrig);
		$sql = 'update ' . $this->tableName() . ' set webp_' . $ean . ' = 1 where (id = ' . (int)$id . ') limit 1';
		Yii::app()->db->createCommand($sql)->execute();
		return true;
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
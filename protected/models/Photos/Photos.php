<?php
/*Created by Кирилл (23.07.2019 21:31)*/
class ModelsPhotos extends CActiveRecord {
	protected $_lables = array(
		'orig'=>['width'=>0, 'height'=>0],//оригинальный размер
		'l'=>['width'=>150, 'height'=>250],//в списке
		'd'=>['width'=>300, 'height'=>500],//в карточке
		'sb'=>['width'=>150, 'height'=>130],//слайдер-баннеров
		'si'=>['width'=>200, 'height'=>150],//слайдер товаров
	);

	function createFotos($tmpName, $id, $ean){
		$fotoDir = $this->_createFolderForFotos(Yii::getPathOfAlias('webroot') . '/pictures/' . $this->tableName() . '/', $id);
		foreach ($this->_lables as $label => $param) {
			$this->_createNewFoto($fotoDir . $ean . '_' . $label, $tmpName, $param['width'], $param['height']);
		}
		return true;
	}

	protected function _createNewFoto($newTmp, $tmp, $newWidth, $newHeight) {
		if (empty($newWidth) && empty($newHeight)) return $this->_copyFoto($newTmp, $tmp);
		$fotoParams = $this->_getFotoParams($tmp);
		switch ($fotoParams['ext']) {
			case 'gif': $src = @imagecreatefromgif($tmp); break;
			case 'jpg': case 'jpeg': $src = @imagecreatefromjpeg($tmp); break;
			case 'png': $src = @imagecreatefrompng($tmp); break;
			default: $src = false; break;
		}

		if (!empty($src)){
			$resultWidth = $fotoParams['width'];
			$resultHeight = $fotoParams['height'];
			if (!empty($newHeight) && ($resultHeight > $newHeight)){
				$resultWidth = ceil($resultWidth * $newHeight / $resultHeight);
				$resultHeight = $newHeight;
			}
			if (!empty($newWidth) && ($resultWidth > $newWidth)){
				$resultHeight = ceil($resultHeight * $newWidth / $resultWidth);
				$resultWidth = $newWidth;
			}
			$dst = imagecreatetruecolor($resultWidth, $resultHeight);

			if (($fotoParams['ext'] == 'gif')||($fotoParams['ext'] == 'png')) {
				$bgc = imagecolorallocate($dst, 255, 255, 255);
				imagefilledrectangle($dst, 0, 0, $resultWidth, $resultHeight, $bgc);
			}

			if (imagecopyresampled($dst, $src, 0, 0, 0, 0, $resultWidth, $resultHeight, $fotoParams['width'], $fotoParams['height'])){
				imagewebp($dst, $newTmp . '.webp', 90);
				imagejpeg($dst, $newTmp . '.jpg', 90);
				imagedestroy($dst);
				@chmod($newTmp . '.jpg', 0644);
				return true;
			}
			else {
				return $this->_copyFoto($newTmp, $tmp);
			}
		}
		return false;
	}

	protected function _copyFoto($newTmp, $tmp){
		if (file_exists($tmp)) return copy($tmp, $newTmp.'.jpg') && chmod($newTmp.'.jpg', 0644);
		return false;
	}

	protected function _createFolderForFotos($directory, $idFoto = null) {
		if ($idFoto !== null){
			$ten = floor($idFoto / 10000);
			$directory .= $ten . '/' . $idFoto;
			if (file_exists($directory) && is_dir($directory)) $this->_removeDirWithFotos($directory);
		}
		$this->_mkDirRecursive($directory);
		return $directory . '/';
	}

	private function _mkDirRecursive($directory){
		if (!file_exists($directory) || !is_dir($directory)){
			$dir = Yii::getPathOfAlias('webroot') . '/pictures/' . $this->tableName() . '/';
			$directory = mb_substr($directory, mb_strlen($dir, 'utf-8'), null, 'utf-8');
			foreach (explode('/', $directory) as $part) {
				$dir .= $part . '/';
				if (!is_dir($dir) && $part !== '') {
					if (@mkdir($dir, 0755)){
						@chmod($dir, 0755);
						@chown($dir, 'www-data');
						@lchgrp($dir, 'www-data');
					}
				}
			}
		}
	}

	private function _removeDirWithFotos($dirPath) {
		if (file_exists($dirPath) && is_dir($dirPath)){
			$dirHandle = opendir($dirPath);
			$hasFolders = false;
			while (false !== ($file = readdir($dirHandle))) {
				if ($file != '.' && $file != '..'){
					$tmpPath = $dirPath . '/' . $file;
					if (!is_dir($tmpPath)) unlink($tmpPath);
					else $hasFolders = true;
				}
			}
			closedir($dirHandle);
			if (!$hasFolders) rmdir($dirPath);
			return true;
		}
		return false;
	}

	private function _getFotoParams($addFoto) {
		static $result = array();
		$hash = md5($addFoto);
		if (!isset($result[$hash])){
			$photoParams = array();
			if (file_exists($addFoto)) {
				$size = getimagesize($addFoto);
				$photoParams['width'] = $size[0];
				$photoParams['height'] = $size[1];
				$photoParams['type'] = $size[2];
				$photoParams['ext'] = null;
				switch ($photoParams['type']) {
					case 1: $photoParams['ext'] = 'gif'; break;
					case 2: $photoParams['ext'] = 'jpg'; break;
					case 3: $photoParams['ext'] = 'png'; break;
				}
			}
			$result[$hash] = $photoParams;
		}
		return $result[$hash];
	}

}
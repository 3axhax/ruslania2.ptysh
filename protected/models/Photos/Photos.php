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

	function getUnixDir() {
		return Yii::getPathOfAlias('webroot') . '/pictures/' . $this->tableName() . '/';
	}

	function getHrefPath($idFoto, $label, $ean, $ext) {
		$path = Yii::app()->params['PicDomain'] . '/pictures/' . $this->tableName() . '/' . $this->getRelativePath($idFoto) . $ean;
		if (!empty($label)) $path .= '_' . $label;
		switch ($ext) {
			case 'jpg':case 'webp': $path .= '.' . $ext; break;
//			case '': break;
			default: return '/';
		}
		return $path;
	}

	function getRelativePath($idFoto) {
		$ten = floor($idFoto / 10000);
		return $ten . '/' . $idFoto . '/';
	}

	function createFotos($tmpName, $id, $ean, $quality = 80){
		$fotoDir = $this->_createFolderForFotos($id);
		foreach ($this->_lables as $label => $param) {
			$this->_createNewFoto($fotoDir . $ean . '_' . $label, $tmpName, $param['width'], $param['height'], $quality);
			if ($label == 'orig') {
				$param = $this->_getFotoParams($tmpName);
				$label = 'o';
				$this->_createNewFoto($fotoDir . $ean . '_' . $label, $tmpName, $param['width'], $param['height'], $quality);
			}
		}
		return true;
	}

	protected function _createNewFoto($newTmp, $tmp, $newWidth, $newHeight, $quality) {
		$fotoParams = $this->_getFotoParams($tmp);
		if (empty($newWidth) && empty($newHeight)) return $this->_copyFoto($newTmp . '.' . $fotoParams['ext'], $tmp);
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
				imagedestroy($src);
				imagejpeg($dst, $newTmp . '.jpg', $quality);
				imagewebp($dst, $newTmp . '.webp', $quality);
				imagedestroy($dst);
				if (filesize($newTmp . '.webp') % 2 == 1) {
					file_put_contents($newTmp . '.webp', "\0", FILE_APPEND);
				}
			}
			else {
				return $this->_copyFoto($newTmp . '.' . $fotoParams['ext'], $tmp);
			}
		}
		return false;
	}

	protected function _copyFoto($newTmp, $tmp){
		if (file_exists($tmp)) return copy($tmp, $newTmp) && chmod($newTmp, 0644);
		return false;
	}

	protected function _createFolderForFotos($idFoto = null) {
		$directory = $this->getUnixDir();
		if ($idFoto !== null){
			$directory .= $this->getRelativePath($idFoto);
			if (file_exists($directory) && is_dir($directory)) $this->_removeDirWithFotos($directory);
		}
		$this->_mkDirRecursive($directory);
		return $directory;
	}

	protected function _mkDirRecursive($directory){
		if (!file_exists($directory) || !is_dir($directory)){
			$dir = $this->getUnixDir();
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

	protected function _removeDirWithFotos($dirPath) {
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

	protected function _getFotoParams($addFoto) {
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

	function getPhotos($ids) {
		if (empty($ids)) return array();

		$result = array();
		foreach ($ids as $id) $result[$id] = array();
		$sql = ''.
			'select id, iid, href, is_upload '.
			'from ' . $this->tableName() . ' '.
			'where (iid in (' . implode(',',$ids) . ')) '.
			'order by iid, position '.
		'';
		foreach (Yii::app()->db->createCommand($sql)->queryAll() as $photo) {
			if ($photo['is_upload'] == 2) continue;
			$result[$photo['iid']][] = $photo;
		}
		return $result;
	}
}
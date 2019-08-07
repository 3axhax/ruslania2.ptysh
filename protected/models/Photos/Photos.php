<?php
/*Created by Кирилл (23.07.2019 21:31)*/
class ModelsPhotos extends CActiveRecord {
	protected $_photos = array();
	protected $_externalPhotos = array();

	protected $_lables = array(
		'o'=>['width'=>0, 'height'=>0],//оригинальный размер
		'l'=>['width'=>150, 'height'=>250],//в списке
		'd'=>['width'=>300, 'height'=>500],//в карточке
		'sb'=>['width'=>150, 'height'=>130],//слайдер-баннеров
		'si'=>['width'=>200, 'height'=>150],//слайдер товаров
	);

	function getUnixDir() {
		return Yii::getPathOfAlias('webroot') . '/pictures/' . $this->tableName() . '/';
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

	function getRelativePath($idFoto) {
		$ten = floor($idFoto / 10000);
		return $ten . '/' . $idFoto . '/';
	}

	/**
	 * @param string $tmpName путь до файла оригинала
	 * @param int $idFoto ключевое поле из таблицы ..._photos
	 * @param string $ean ean товара, нужен для названия файла
	 * @param int $quality качество картинки-копии
	 * @param bool|true $removeExistsFiles если false, то не будет удалять существующие файлы из папки
	 * @param bool|true $createOrig если false, то не будет делаться копия оригинала
	 * @return bool
	 */
	function createFotos($tmpName, $idFoto, $ean, $quality = 80, $removeExistsFiles = true, $createOrig = true){
		$paramOrig = $this->_getFotoParams($tmpName);
		if (empty($paramOrig)) return false;

		$fotoDir = $this->_createFolderForFotos($idFoto, $removeExistsFiles);
		if (!file_exists($fotoDir)) return false;

		foreach ($this->_lables as $label => $param) {
			if (($label == 'orig')&&$createOrig) {
				$this->_createNewFoto($fotoDir . $ean . '_' . $label, $tmpName, $param['width'], $param['height'], $quality);
			}
			elseif ($label != 'orig') $this->_createNewFoto($fotoDir . $ean . '_' . $label, $tmpName, $param['width'], $param['height'], $quality);
//			if ($label == 'orig') {
//				$label = 'o';
//				$this->_createNewFoto($fotoDir . $ean . '_' . $label, $tmpName, $paramOrig['width'], $paramOrig['height'], $quality);
//			}
		}
		return true;
	}

	protected function _createNewFoto($newTmp, $tmp, $newWidth, $newHeight, $quality) {
		$fotoParams = $this->_getFotoParams($tmp);
		if (empty($fotoParams)) return false;

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
				return true;
			}
			else {
				return $this->_copyFoto($newTmp . '.' . $fotoParams['ext'], $tmp);
			}
		}
		return false;
	}

	protected function _copyFoto($newTmp, $tmp){
		if (file_exists($tmp)) return @copy($tmp, $newTmp) && @chmod($newTmp, 0644);
		return false;
	}

	protected function _createFolderForFotos($idFoto = null, $removeExistsFiles = true) {
		$directory = $this->getUnixDir();
		if ($idFoto !== null){
			$directory .= $this->getRelativePath($idFoto);
			if ($removeExistsFiles&&file_exists($directory) && is_dir($directory)) $this->_removeDirWithFotos($directory);
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
				$size = @getimagesize($addFoto);
				if ($size) {
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
			}
			$result[$hash] = $photoParams;
		}
		return $result[$hash];
	}

	function getFirstId($id) {
		if (!isset($this->_photos[$id])) $this->getPhotos(array($id));
		if (empty($this->_photos[$id])) return 0;
		return $this->_photos[$id][0]['id'];
	}

	function getPhotoIds($id) {
		if (!isset($this->_photos[$id])) $this->getPhotos(array($id));
		return $this->_photos[$id];
	}

	function getPhotos($ids) {
		foreach ($ids as $i=>$id) {
			if (!isset($this->_photos[$id])) $this->_photos[$id] = array();
			else unset($ids[$i]);
		}
		if (!empty($ids)) {
			$sql = ''.
				'select id, iid, href, is_upload '.
				'from ' . $this->tableName() . ' '.
				'where (iid in (' . implode(',',$ids) . ')) '.
				'order by iid, position '.
			'';
			foreach (Yii::app()->db->createCommand($sql)->queryAll() as $photo) {
				if ($photo['is_upload'] > 2) continue;
				if ($photo['is_upload'] == 0) {
					if (empty($photo['href']))  continue;
					$this->_externalPhotos[$photo['id']] = $photo['href'];
				}
				$this->_photos[$photo['iid']][] = $photo;
			}
		}
		return $this->_photos;
	}

	function remove($idPhoto) {
		$directory = $this->getUnixDir() . $this->getRelativePath($idPhoto);
		$this->_removeDirWithFotos($directory);
	}

	/** Через курл пытается загрузить фотографию. В случае успеха возвращает путь до файла, иначе - false
	 * @return mixed */
	function downloadFile($url, $idFoto, $ean, $crop = 0){
		$dir = $this->_createFolderForFotos($idFoto);
		$file = $dir . $ean . '_orig.jpg';

		$fp = fopen($file, 'w');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false); //не записывать в файл заголовки
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);//Максимально позволенное количество секунд для выполнения cURL-функций.
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);//Количество секунд ожидания при попытке соединения. Используйте 0 для бесконечного ожидания.
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//следовать за редиректами
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);//3 редиректа - максимум
		curl_setopt($ch, CURLOPT_FILE, $fp);//записываем в файл
		curl_exec($ch);

		$curlInfo = curl_getinfo($ch);
		$errors = curl_errno($ch) || ($curlInfo['http_code'] >= 300);

		curl_close($ch);
		fclose($fp);

		if ($errors || !@filesize($file)){
			unlink($file);
			return false;
		}

		if ($crop > 0) {
			$fileCrop = $dir . $ean . '_crop.jpg';
			if ($this->_cropFoto($fileCrop, $file, $crop)) return $fileCrop;
		}

		return $file;
	}

	protected function _cropFoto($newTmp, $tmp, $crop) {
		$fotoParams = $this->_getFotoParams($tmp);
		if (empty($fotoParams)) return false;

		switch ($fotoParams['ext']) {
			case 'gif': $src = @imagecreatefromgif($tmp); break;
			case 'jpg': case 'jpeg': $src = @imagecreatefromjpeg($tmp); break;
			case 'png': $src = @imagecreatefrompng($tmp); break;
			default: $src = false; break;
		}

		if (!empty($src)){
			$resultWidth = $fotoParams['width'];
			$resultHeight = $fotoParams['height'] - $crop;
			$dst = imagecreatetruecolor($resultWidth, $resultHeight);

			if (($fotoParams['ext'] == 'gif')||($fotoParams['ext'] == 'png')) {
				$bgc = imagecolorallocate($dst, 255, 255, 255);
				imagefilledrectangle($dst, 0, 0, $resultWidth, $resultHeight, $bgc);
			}

			if (imagecopyresampled($dst, $src, 0, 0, 0, 0, $resultWidth, $resultHeight, $resultWidth, $resultHeight)){
				imagedestroy($src);
				imagejpeg($dst, $newTmp);
				imagedestroy($dst);
				return true;
			}
		}
		return false;
	}

}
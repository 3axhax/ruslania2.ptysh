<?php
/*Created by Кирилл (29.05.2019 21:30)*/

class PhotoStamp {
	protected $_pathToFontFile;//шрифт
	protected $_text, $_pathToFileFoto, $_pathToFileLogo;
	protected $_stampWidth, //ширина штампа
	$_stampHeigth,//высота штампа
	$_stampOpacity = 100,//позрачность штампа
	$_stampColor = ['r'=>255, 'g'=>252, 'b'=>5],//цвет шрифта штампа (не делать [247, 247, 247])
	$_stampSize,//размер шрифта штампа
	$_stampY;//отступ

	function __construct($pathToFileFoto, $stampText) {
		$this->_pathToFileFoto = $pathToFileFoto;
		$fotoParams = $this->_getFotoParams($this->_pathToFileFoto);
		switch ($fotoParams['ext']) {
			case 'gif': $im = @imagecreatefromgif($this->_pathToFileFoto); break;
			case 'jpg': case 'jpeg': $im = @imagecreatefromjpeg($this->_pathToFileFoto); break;
			case 'png': $im = @imagecreatefrompng($this->_pathToFileFoto); break;
			default: $im = false; break;
		}

		if ($im) {
			if ($this->_getFotoParams($stampText)) {
				$this->_pathToFileLogo = $stampText;
			}
			else {
				$this->_text = $stampText;
				$this->_pathToFontFile = Yii::getPathOfAlias('webroot') . '/new_style/fonts/arial.ttf';
			}
			$this->_stampSize = ceil(imagesy($im)/5);
			$picWidth = ceil(imagesx($im)/10*9);//ширина фото
			$sizeMin = ceil(imagesy($im)/20);
			imagedestroy($im);
			if (!empty($this->_text)) {
				$size = imagettfbbox ($this->_stampSize, 0, $this->_pathToFontFile, $this->_text . ' ');//иногда последняя буква не влезает, поэтому добавил пробел для ширины (чтоб чуть побольше стала)
				$this->_stampWidth = $size[2]-$size[0];
				if ($this->_stampWidth > $picWidth) {
					//пытаюсь подобрать размер шрифта к фотографии в соответствии с длиной строки
					$this->_stampSize = ceil($this->_stampSize/$this->_stampWidth*$picWidth);
					if ($this->_stampSize < $sizeMin) $this->_stampSize = $sizeMin;
					$size = imagettfbbox ($this->_stampSize, 0, $this->_pathToFontFile, $this->_text . ' ');//иногда последняя буква не влезает, поэтому добавил пробел для ширины (чтоб чуть побольше стала)
					$this->_stampWidth = $size[2]-$size[0];
				}
				$this->_stampHeigth = $size[1]-$size[7];
			}
			else {
				$this->_stampHeigth = $sizeMin*4;
				$this->_stampWidth = $picWidth;
			}
		}
	}

	/** получаем ресурс штампа с прозрачным фоном
	 * после использованием ресурса обязательно imagedestroy($stamp);
	 * @return resource
	 */
	function getStamp() {
		if (!empty($this->_text)) {
			$stamp = imagecreatetruecolor($this->_stampWidth, $this->_stampHeigth);
			$white = imagecolorallocate($stamp, $this->_stampColor['r'], $this->_stampColor['g'], $this->_stampColor['b']);
			$grey = imagecolorallocate($stamp, 247, 247, 247);
			imagefilledrectangle($stamp, 0, 0, $this->_stampWidth, $this->_stampHeigth, $grey);
			$size = imagettfbbox ($this->_stampSize, 0, $this->_pathToFontFile, $this->_text);
			imagettftext($stamp, $this->_stampSize, 0, 0, -$size[7], $white, $this->_pathToFontFile, $this->_text);
			imagecolortransparent($stamp, $grey);
			return $stamp;
		}
		elseif (!empty($this->_pathToFileLogo)) {
			$fotoParams = $this->_getFotoParams($this->_pathToFileLogo);
			switch ($fotoParams['ext']) {
				case 'gif': $src = @imagecreatefromgif($this->_pathToFileLogo); break;
				case 'jpg': case 'jpeg': $src = @imagecreatefromjpeg($this->_pathToFileLogo); break;
				case 'png': $src = @imagecreatefrompng($this->_pathToFileLogo); break;
				default: $src = false; break;
			}
			if (!empty($src)){
				$resultWidth = $fotoParams['width'];
				$resultHeight = $fotoParams['height'];
				$resultWidth = ceil($resultWidth * $this->_stampHeigth / $resultHeight);
				$resultHeight = $this->_stampHeigth;
				if ($resultWidth > $this->_stampWidth){
					$resultHeight = ceil($resultHeight * $this->_stampWidth / $resultWidth);
					$resultWidth = $this->_stampWidth;
				}
				$this->_stampWidth = $resultWidth;
				$stamp = imagecreatetruecolor($resultWidth, $resultHeight);

				if (($fotoParams['ext'] == 'gif')||($fotoParams['ext'] == 'png')) {
					$bgc = imagecolorallocate($stamp, 255, 255, 255);
					imagefilledrectangle($stamp, 0, 0, $resultWidth, $resultHeight, $bgc);
				}

				if (imagecopyresampled($stamp, $src, 0, 0, 0, 0, $resultWidth, $resultHeight, $fotoParams['width'], $fotoParams['height'])){
					return $stamp;
				}
			}
		}
		return false;
	}

	/** получаем ресурс изображения, объединенного с штампом
	 * после использованием ресурса обязательно imagedestroy($im);
	 * @return resource
	 */
	function merge() {
		$stamp = $this->getStamp();
		if ($stamp) {
			$fotoParams = $this->_getFotoParams($this->_pathToFileFoto);
			switch ($fotoParams['ext']) {
				case 'gif': $im = @imagecreatefromgif($this->_pathToFileFoto); break;
				case 'jpg': case 'jpeg': $im = @imagecreatefromjpeg($this->_pathToFileFoto); break;
				case 'png': $im = @imagecreatefrompng($this->_pathToFileFoto); break;
				default: $im = false; break;
			}
			if ($im) {
				if (!empty($this->_text)) imagecopymerge($im, $stamp, ceil(imagesx($im) / 2 - $this->_stampWidth / 2), imagesy($im) - $this->_stampHeigth * 2, 0, 0, imagesx($stamp), imagesy($stamp), $this->_stampOpacity);
				else imagecopymerge($im, $stamp, ceil(imagesx($im) - $this->_stampWidth - 5), imagesy($im) - $this->_stampHeigth - 5, 0, 0, imagesx($stamp), imagesy($stamp), $this->_stampOpacity);
				imagedestroy($stamp);
				return $im;
			}
		}
		return false;
	}

	function saveFile() {
		$im = $this->merge();
		if ($im) {
			imagejpeg($im, $this->_pathToFileFoto, 75);
			imagedestroy($im);
		}
	}

	private function _getFotoParams($addFoto) {
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
		return $photoParams;
	}

}
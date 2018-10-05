<?php /*Created by Кирилл (27.09.2018 21:38)*/

class InfoText extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array('isFrame'=>1);
	static private $_text = null;

	function init() { }

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		$ctrl = $this->getController()->id;
		$action = $this->getController()->action->id;
		if ($ctrl == 'cart') return;

		$text = self::_getItem();
		if (empty($text)) return;

		if (!empty($this->_params['isFrame'])) $this->render('info_text_frame', array('text'=>$text));
		else $this->render('info_text', array('text'=>$text));
	}

	private function _getItem() {
		if (self::$_text === null) {
			$langs = array('ru', 'en', 'fi', 'de', 'fr', 'se', 'es');
			$lang = strtolower(Yii::app()->language);
			if (!in_array($lang, $langs)) $lang = 'en';

			$sql = 'select text_' . $lang . ' from info_text order by id desc limit 1';
			self::$_text = (string) Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return self::$_text;
	}


}


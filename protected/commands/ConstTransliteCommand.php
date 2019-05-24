<?php
/*Created by Кирилл (24.05.2019 21:19)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php consttranslite
 * из файла xlsx с переводами в файл массив
 * Class ConstTransliteCommand
 */

define('cronAction', 1);
class ConstTransliteCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		$fileRu = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'] . 'ru/uiconst.class.php';
		$fileRut = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'] . 'rut/uiconst.class.php';

		$ru = require_once($fileRu);
		$rut = array();
		foreach ($ru as $k=>$v) {
			$rut[$k] = ProductHelper::ToAscii($v, array('onlyTranslite'=>true, 'lowercase'=>false));
		}

		file_put_contents($fileRut, '<?php // FILE: language constants, generated at ' . date('d.m.Y H:i:s') . '
return ' . var_export($rut, true) . ';');
		chown($fileRut, 'www-root');
		chgrp($fileRut, 'www-root');
	}

}

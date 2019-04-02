<?php
/*Created by Кирилл (24.12.2018 22:52)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php xlsxtolangv
 * из файла xlsx с переводами в файл массив
 * Class XlsxToLangvCommand
 */

define('cronAction', 1);
class XlsxToLangvCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		require_once dirname(dirname(__FILE__)) . '/extensions/excel/xlsx/simplexlsx.class.php';
		$file = dirname(dirname(__FILE__)) . '/doc/langs_2019_03_29.xlsx';

		//первая строка - заголовки ОБЯЗАТЕЛЬНО с обозначение языков
		if ( $xlsx = SimpleXLSX::parse($file)) {
			$rows = $xlsx->rows();
			$langs = array_shift($rows);
			array_shift($langs);
			var_dump($langs);
			$result = array();
			foreach ($langs as $lang) {
//				if ($lang != 'de') continue;
//				if ($lang == 'ru') $lang = 'rut';
				$fileLang = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/uiconst.class.php';
				$result[$lang] = require_once($fileLang);
				if ($lang == 'ru') {
					$fileLang = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].'rut/uiconst.class.php';
					$result['rut'] = require_once($fileLang);
				}
			}

			foreach ($rows as $i=>$str) {
				$key = array_shift($str);
				foreach ($str as $langPos=>$v) {
					$v = trim($v);
					if (!empty($v)) {
						$lang = $langs[$langPos];
//						if ( //если значение в php файле пустое
//							empty($result[$lang][$key])//если значение ключа пустое
//							||(($lang != 'ru')&&preg_match("/[а-я]/ui", $result[$lang][$key])) // или для не русского языка в значении ключа есть русские буквы
//						) {
							$result[$lang][$key] = $v;
							if ($lang == 'ru') {
								$v = ProductHelper::ToAscii($v, array('onlyTranslite'=>true));
								$v = mb_strtoupper(mb_substr($v, 0, 1, 'utf-8'), 'utf-8') . mb_substr($v, 1, null, 'utf-8');
								$result['rut'][$key] = $v;
							}
//						}
					}
				}
			}
			foreach ($result as $lang=>$translite) {
				$fileLang = Yii::getPathOfAlias('webroot').Yii::app()->params['LangDir'].$lang.'/' . date('dmYHis') . '_uiconst.class.php';
				file_put_contents($fileLang, '<?php // FILE: language constants, generated at ' . date('d.m.Y H:i:s') . '
return ' . var_export($translite, true) . ';');
				chown($fileLang, 'www-root');
				chgrp($fileLang, 'www-root');
				echo $lang . ' complite' . "\n";
			}
		} else {
			echo SimpleXLSX::parse_error();
		}

		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

}

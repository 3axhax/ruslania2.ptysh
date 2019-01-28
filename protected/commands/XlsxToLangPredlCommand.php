<?php
/*Created by Кирилл (28.01.2019 19:41)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php xlsxtolangpredl
 * из файла xlsx с переводами в таблицу языков на предложном падеже
 * Class XlsxToLangPredlCommand
 */

define('cronAction', 1);
class XlsxToLangPredlCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";

		require_once dirname(dirname(__FILE__)) . '/extensions/excel/xlsx/simplexlsx.class.php';
		$file = dirname(dirname(__FILE__)) . '/doc/langs_predl.xlsx';

		//первая строка - заголовки ОБЯЗАТЕЛЬНО с обозначение языков
		if ( $xlsx = SimpleXLSX::parse($file)) {
			$rows = $xlsx->rows();
			$langs = array_shift($rows);
			array_shift($langs);
			$result = array();

			foreach ($rows as $i=>$str) {
				$key = array_shift($str);
				$result = array();
				foreach ($str as $langPos=>$v) {
					$v = trim($v);
					if (!empty($v)) {
						$lang = $langs[$langPos];
						$result[$lang] = $v;
						if ($lang == 'ru') {
							$v = ProductHelper::ToAscii($v, array('onlyTranslite'=>true));
							$v = mb_strtoupper(mb_substr($v, 0, 1, 'utf-8'), 'utf-8') . mb_substr($v, 1, null, 'utf-8');
							$result['rut'] = $v;
						}
					}
				}
				$sql = 'update languages set predl = :predl where (in_path = :key) limit 1';
				Yii::app()->db->createCommand($sql)->query(array(':predl'=>serialize($result), ':key'=>$key));
			}
		} else {
			echo SimpleXLSX::parse_error();
		}

		echo 'end ' . date('d.m.Y H:i:s') . "\n";
	}

}


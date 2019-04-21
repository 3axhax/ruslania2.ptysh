<?php
/*Created by Кирилл (13.12.2018 18:56)*/
ini_set('max_execution_time', 3600);
/** /usr/bin/php /var/www/www-root/data/ruslania2.ptysh.ru/command.php cygwin
 * проверка запуска из cygwin
 * Class CygwinCommand
 */

class CygwinCommand extends CConsoleCommand {

	public function actionIndex() {
		echo "\n" . 'start ' . date('d.m.Y H:i:s') . "\n";
		echo "\n" . 'end ' . date('d.m.Y H:i:s') . "\n";
	}

}

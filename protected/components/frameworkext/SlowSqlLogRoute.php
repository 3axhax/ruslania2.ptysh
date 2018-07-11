<?php
/*Created by Кирилл (11.07.2018 10:19)*/
class SlowSqlLogRoute extends CWebLogRoute {

	protected function render($view,$data) {
		$startTime = 0;
		foreach ($data as $i=>$log) {
			if ($i%2) {
				$resultTime = $log[3] - $startTime;
				if ($resultTime > 1) {
					file_put_contents(Yii::getPathOfAlias('webroot') . '/test/slow_sql.log', implode("\t", array(
							date('d.m.Y H:i:s'),
							$resultTime . 'сек',
							$log[0],
							getenv('HTTP_HOST') . getenv('REQUEST_URI'),
							getenv('HTTP_REFERER'))
					) . "\n", FILE_APPEND);
				}
			}
			else $startTime = $log[3];
		}
	}

}
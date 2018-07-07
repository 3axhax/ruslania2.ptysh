<?php
/*Created by Кирилл (17.05.2018 8:25)*/
class ProductLang {
	private static $_langs = null, $_langItems = null;

	static function getLangItems($entity, $cat) {
		if (self::$_langItems === null) {
			$join = array();
			if (!empty($cat)) {
				$entities = Entity::GetEntitiesList();
				$tbl = $entities[$entity]['site_table'];
				$join['tI'] = 'join ' . $tbl . ' tI on (tI.id = tAIL.item_id) and ((tI.code = ' . $cat['id'] . ') or (tI.subcode = ' . $cat['id'] . '))';
			}
			$sql = ''.
				'select t.language_id '.
				'from all_items_languages t '.
				implode(' ', $join) . ' '.
				'where (t.entity = ' . (int) $entity . ') '.
				'group by t.language_id '.
				'';
			$langIds = Yii::app()->db->createCommand($sql)->queryColumn();
			if (empty($langIds)) return array();

			$sql = ''.
				'select tL.id, tL.title_'.Yii::app()->language . ' title '.
				'from languages tL ' .
				'where (tL.id in (' . implode(',', $langIds) . ')) '.
				'order by title '.
				'';
			self::$_langItems = Yii::app()->db->createCommand($sql)->queryAll();
		}
		return self::$_langItems;
	}

	static function getLangs($entity, $cat) {
		$rows = self::getLangItems($entity, $cat);
		$result = array(
			0=>Yii::app()->ui->item('A_NEW_FILTER_TITLE_LANG') . Yii::app()->ui->item('A_NEW_FILTER_ALL'),
			7=>false,
			14=>false,
			9=>false,
			8=>false,
		);
		foreach ($rows as $row) $result[(int)$row['id']] = Yii::app()->ui->item('A_NEW_FILTER_TITLE_LANG') . $row['title'];
		return array_filter($result);
	}

	static function getShortLang() {
		if (self::$_langs === null) {
			$sql = ''.
				'select tL.id, tL.in_path title '.
				'from languages tL ' .
				'';
			$rows = Yii::app()->db->createCommand($sql)->queryAll();
			self::$_langs = array();
			foreach ($rows as $row) self::$_langs[(int)$row['id']] = mb_strtolower($row['title'], 'utf-8');
		}
		return self::$_langs;
	}

}
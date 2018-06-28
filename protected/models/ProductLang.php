<?php
/*Created by Кирилл (17.05.2018 8:25)*/
class ProductLang {

	static function getLangs($entity, $cat) {
		$entities = Entity::GetEntitiesList();
		$tbl = $entities[$entity]['site_table'];
		$join = ['tAIL'=>'join all_items_languages tAIL on (tAIL.language_id = tL.id) and (tAIL.entity = ' . (int) $entity . ')'];
		if (!empty($cat)) {
			$join['t'] = 'join ' . $tbl . ' t on (t.id = tAIL.item_id) and ((t.code = ' . $cat['id'] . ') or (t.subcode = ' . $cat['id'] . '))';
		}

		$sql = ''.
			'select tL.id, tL.title_'.Yii::app()->language . ' title '.
			'from languages tL ' .
				implode(' ', $join) . ' '.
			'group by tL.id '.
			'order by title '.
		'';
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
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
		$sql = ''.
			'select tL.id, tL.title_en title '.
			'from languages tL ' .
		'';
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		$result = array();
		foreach ($rows as $row) $result[(int)$row['id']] = mb_strtolower($row['title'], 'utf-8');
		return $result;
	}

}
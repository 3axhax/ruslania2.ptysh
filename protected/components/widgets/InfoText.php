<?php /*Created by Кирилл (27.09.2018 21:38)*/

class InfoText extends CWidget {
	/**
	 * @var array здесь массив начальных значений
	 */
	protected $_params = array( );

	function init() {

	}

	function __set($name, $value) {
		if ($value !== null) $this->_params[$name] = $value;
	}

	function run() {
		$item = self::_getItem();
		if (empty($item)) return;

		if (!empty($item['path_route'])) {
			$params = array( );
			if (!empty($item['path_entity'])){
				$params['entity'] = $item['path_entity'];
				if (!empty($item['path_id'])) {
					$idName = HrefTitles::get()->getIdName($params['entity'], $item['path_route']);
					if (!empty($idName)) $params[$idName] = $item['path_id'];
				}
			}
			$href = Yii::app()->createUrl($item['path_route'], $params);
		}
		else $href = $item['url'];

		$this->render('info_text', array('name'=>$item['name'], 'href'=>$href));
	}

	private function _getItem() {
		$sql = 'select name, url, path_entity, path_route, path_id from info_text order by id desc limit 1';
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		return $row;
	}


}


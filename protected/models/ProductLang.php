<?php
/*Created by Кирилл (17.05.2018 8:25)*/
class ProductLang {
    private static $_langs = null, $_langItems = null;

    static function getLangItems($entity, $cat) {
        if (self::$_langItems === null) {
            $conditionHandler = Condition::get($entity, empty($cat['id'])?0:$cat['id']);
            $condition = $conditionHandler->getCondition();
            $join = $conditionHandler->getJoin();
            $avail = !empty($condition['avail']) || !empty($join['tL_support']);
            if (!empty($cat['id'])) {
                $category = new Category();
                $allChildren = $category->GetChildren($entity, $cat['id']);
                $allChildren[] = $cat['id'];
                if ($avail) {
                    $condition['cid'] = '(tLang.category_id in (' . implode(',', $allChildren) . '))';
                }
            }
            unset($join['tL_all'], $join['tL_support']);
            if (!$avail) {
                foreach ($join as $k => $v) {
                    $join[$k] = str_replace('t.id', 'tLang.item_id', $v);
                }
            }
            else {
                foreach ($join as $k => $v) {
                    $join[$k] = str_replace('t.id', 'tLang.id', $v);
                }
            }

            if (!empty($condition)) {
                if (!$avail||(count($condition) > 1)||empty($condition['cid'])) {
                    $entities = Entity::GetEntitiesList();
                    array_unshift($join, 'join ' . $entities[$entity]['site_table'] . ' t on (t.id = tLang.' . (!$avail?'item_id':'id') . ')');
                }
            }

            if (!$avail) {
                $condition['eid'] = '(tLang.entity = ' . (int) $entity . ')';
                $sql = ''.
                    'select tLang.language_id '.
                    'from all_items_languages tLang '.
                    implode(' ', $join) . ' '.
                    'where ' . implode(' and ', $condition) . ' '.
                    'group by tLang.language_id '.
                '';
//                $langIds = Yii::app()->db->createCommand($sql)->queryColumn();
            }
            else {
                unset($condition['avail']);
                $condSupport = $conditionHandler->onlySupportCondition(false);
                unset($condSupport['language_id'], $condSupport['cid']);
                foreach ($condSupport as $k=>$v) {
                    $condition[] = str_replace('(t.', '(tLang.', $v);
                }
                $entityStr = Entity::GetUrlKey($entity);
                $supportTable = '_support_languages_' . $entityStr;
                $sql = ''.
                    'select tLang.language_id '.
                    'from ' . $supportTable . ' tLang '.
                    implode(' ', $join) . ' '.
                    (empty($condition)?'':'where ' . implode(' and ', $condition) . ' ') .
                    'group by tLang.language_id '.
                '';
//                $langIds = Yii::app()->db->createCommand($sql)->queryColumn();
            }
            $cacheKey = md5($sql);
            self::$_langItems = Yii::app()->memcache->get($cacheKey);
            if (self::$_langItems === false) {
                $langIds = Yii::app()->db->createCommand($sql)->queryColumn();
                if (empty($langIds)) self::$_langItems = array();
                else {
                    $sql = ''.
                        'select tL.id, tL.title_'.Yii::app()->language . ' title, predl, country '.
                        'from languages tL ' .
                        'where (tL.id in (' . implode(',', $langIds) . ')) '.
                        'order by title '.
                    '';
                    self::$_langItems = Yii::app()->db->createCommand($sql)->queryAll();
                }
                Yii::app()->memcache->set($cacheKey, self::$_langItems, Yii::app()->params['listMemcacheTime']);
            }
        }
        return self::$_langItems;
    }

    static function getLangs($entity, $cat, $prefix = true) {
        $rows = self::getLangItems($entity, $cat);
        if ($entity == Entity::PRINTED) {
            $result = array(
                0=>($prefix?Yii::app()->ui->item('A_NEW_FILTER_TITLE_THEME'):'') . Yii::app()->ui->item('A_NEW_FILTER_ALL'),
                7=>false,
                14=>false,
                9=>false,
                8=>false,
            );
            foreach ($rows as $row) {
                $title = $row['title'];
                if (!empty($row['country'])) {
                    $row['country'] = unserialize($row['country']);
                    if (!empty($row['country'][Yii::app()->getLanguage()])) $title = $row['country'][Yii::app()->getLanguage()];
                }
                $result[(int)$row['id']] = ($prefix?Yii::app()->ui->item('A_NEW_FILTER_TITLE_THEME'):'') . $title;
            }
        }
        else {
            $result = array(
                0=>($prefix?Yii::app()->ui->item('A_NEW_FILTER_TITLE_LANG'):'') . Yii::app()->ui->item('A_NEW_FILTER_ALL'),
                7=>false,
                14=>false,
                9=>false,
                8=>false,
            );
            foreach ($rows as $row) $result[(int)$row['id']] = ($prefix?Yii::app()->ui->item('A_NEW_FILTER_TITLE_LANG'):'') . $row['title'];
        }
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
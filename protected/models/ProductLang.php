<?php
/*Created by Кирилл (17.05.2018 8:25)*/
class ProductLang {
    private static $_langs = null, $_langItems = null;

    static function getLangItems($entity, $cat) {
        if (self::$_langItems === null) {
            $avail = Yii::app()->getController()->GetAvail(1);
            $allChildren = array();
            if (!empty($cat['id'])) {
                $category = new Category();
                $allChildren = $category->GetChildren($entity, $cat['id']);
                $allChildren[] = $cat['id'];
            }

            if (empty($avail)) {
                $join = array();
                if (!empty($cat)) {
                    $entities = Entity::GetEntitiesList();
                    $tbl = $entities[$entity]['site_table'];
                    $join['tI'] = 'join ' . $tbl . ' tI on (tI.id = t.item_id) and ((tI.code in (' . implode(',', $allChildren) . ')) or (tI.subcode in (' . implode(',', $allChildren) . ')))';
                }
                $sql = ''.
                    'select t.language_id '.
                    'from all_items_languages t '.
                    implode(' ', $join) . ' '.
                    'where (t.entity = ' . (int) $entity . ') '.
                    'group by t.language_id '.
                    '';
                $langIds = Yii::app()->db->createCommand($sql)->queryColumn();
            }
            else {
                $entityStr = Entity::GetUrlKey($entity);
                $supportTable = '_support_languages_' . $entityStr;
                $sql = ''.
                    'select t.language_id '.
                    'from ' . $supportTable . ' t '.
                    (empty($allChildren)?'':'where (t.category_id in (' . implode(',', $allChildren) . ')) ') .
                    'group by t.language_id '.
                '';
                $langIds = Yii::app()->db->createCommand($sql)->queryColumn();
            }
            if (empty($langIds)) return array();

            $sql = ''.
                'select tL.id, tL.title_'.Yii::app()->language . ' title, predl, country '.
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
        if ($entity == Entity::PRINTED) {
            $result = array(
                0=>Yii::app()->ui->item('A_NEW_FILTER_TITLE_THEME') . Yii::app()->ui->item('A_NEW_FILTER_ALL'),
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
                $result[(int)$row['id']] = Yii::app()->ui->item('A_NEW_FILTER_TITLE_THEME') . $title;
            }
        }
        else {
            $result = array(
                0=>Yii::app()->ui->item('A_NEW_FILTER_TITLE_LANG') . Yii::app()->ui->item('A_NEW_FILTER_ALL'),
                7=>false,
                14=>false,
                9=>false,
                8=>false,
            );
            foreach ($rows as $row) $result[(int)$row['id']] = Yii::app()->ui->item('A_NEW_FILTER_TITLE_LANG') . $row['title'];
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
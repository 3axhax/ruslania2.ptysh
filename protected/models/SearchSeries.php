<?php

class SearchSeries
{
    static private $_self = null;

    static function get() {
        if (self::$_self === null) self::$_self = new self;
        return self::$_self;
    }

    function  getSeriesForFilters($entity, $q, $cid = 0, $limit = 20) {
        if (!Entity::checkEntityParam($entity, 'series')) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $series_tbl = $entities[$entity]['site_series_table'];

        $whereLike = 'LOWER(title_ru) LIKE LOWER(:q) OR LOWER(title_rut) LIKE LOWER(:q) OR 
            LOWER(title_en) LIKE LOWER(:q) OR LOWER(title_fi) LIKE LOWER(:q)';

        if ($cid > 0) {
            $sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi 
            FROM (SELECT id, title_ru, title_rut, title_en, title_fi FROM '.$series_tbl.' 
            WHERE ('.$whereLike.')) as st 
            LEFT JOIN ' . $tbl . ' as tc   
            ON (tc.series_id=st.id)
            WHERE tc.avail_for_order=1 AND (tc.`code`=:code OR tc.`subcode`=:code) 
            GROUP BY tc.series_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid, ':q' => '%'.$q.'%'));
        } else {
            $sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi 
            FROM (SELECT id, title_ru, title_rut, title_en, title_fi FROM '.$series_tbl.' 
            WHERE ('.$whereLike.')) as st 
            LEFT JOIN ' . $tbl . ' as tc   
            ON (tc.series_id=st.id)
            WHERE tc.avail_for_order=1
            GROUP BY tc.series_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => '%'.$q.'%'));
        }
        $series = [];
        $i = 0;
        foreach ($rows as $row) {
            if (mb_stripos($row['title_ru'], $q) !== false) {
                $series[$i]['id'] = $row['series_id'];
                $series[$i]['title'] = $row['title_ru'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_rut'], $q) !== false) {
                $series[$i]['id'] = $row['series_id'];
                $series[$i]['title'] = $row['title_rut'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_en'], $q) !== false) {
                $series[$i]['id'] = $row['series_id'];
                $series[$i]['title'] = $row['title_en'];
                $i++;
                continue;
            }
            if (mb_stripos($row['title_fi'], $q) !== false) {
                $series[$i]['id'] = $row['series_id'];
                $series[$i]['title'] = $row['title_fi'];
                $i++;
                continue;
            }

        }
        return $series;
    }
}
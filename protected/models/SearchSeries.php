<?php

class SearchSeries
{
    static function  getSeriesForFilters($entity, $q, $cid = 0, $limit = 20) {
        if (!Entity::checkEntityParam($entity, 'series')) return array();

        $entities = Entity::GetEntitiesList();
        $tbl = $entities[$entity]['site_table'];
        $series_tbl = $entities[$entity]['site_series_table'];

        $whereLike = 'LOWER(title_ru) LIKE LOWER(:q) OR LOWER(title_rut) LIKE LOWER(:q) OR 
            LOWER(title_en) LIKE LOWER(:q) OR LOWER(title_fi) LIKE LOWER(:q)';
        $start = microtime(true);

        if ($cid > 0) {
            $sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi FROM ' . $tbl . ' as tc, '.$series_tbl.' as st 
            WHERE (tc.`code`=:code OR tc.`subcode`=:code) 
            AND tc.avail_for_order=1 AND tc.series_id=st.id AND ('.$whereLike.') GROUP BY tc.series_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':code' => $cid, ':q' => '%'.$q.'%'));
        } else {
            /*$sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi FROM ' . $tbl . ' as tc, '.$series_tbl.' as st
            WHERE tc.avail_for_order=1 AND tc.series_id=st.id AND ('.$whereLike.') GROUP BY tc.series_id LIMIT 0,'.$limit;*/
            $sql = 'SELECT tc.series_id, st.title_ru, st.title_rut, st.title_en, st.title_fi 
            FROM (SELECT id, title_ru, title_rut, title_en, title_fi FROM books_series 
            WHERE ('.$whereLike.')) as st 
            LEFT JOIN books_catalog as tc   
            ON (tc.series_id=st.id)
            WHERE tc.avail_for_order=1
            GROUP BY tc.series_id LIMIT 0,'.$limit;
            $rows = Yii::app()->db->createCommand($sql)->queryAll(true, array(':q' => '%'.$q.'%'));
        }
        $end = microtime(true) - $start;
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
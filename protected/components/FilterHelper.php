<?php


class FilterHelper
{
    /*
     * Струкутра $data (выходного массива фильтра):
     * 'entity'
     * 'cid'
     * 'avail'
     * 'lang_sel'
     * 'sort'
     * 'year_min'
     * 'year_max'
     * 'cost_min'
     * 'cost_max'
     * 'author'
     * 'publisher'
     * 'series'
     * 'binding'
     * 'format_video'
     * 'lang_video'
     * 'subtitles_video'
     *
     */

    protected static $data = false;

    static function getEnableFilters ($entity, $cid = 0) {
        $filters = [];

        $category = new Category();
        $filters['max-min'] = $category->getFilterSlider($entity, $cid);
        if (Entity::checkEntityParam($entity, 'authors')) $filters['author'] = true;
        if (Entity::checkEntityParam($entity, 'publisher')) $filters['publisher'] = true;
        if (Entity::checkEntityParam($entity, 'series')) $filters['series'] = true;
        if (Entity::checkEntityParam($entity, 'years')) $filters['years'] = true;

        if ($entity == 40) {
            $filters['langVideo'] = $category->getFilterLangsVideo($entity, $cid);
            $filters['langSubtitles'] = $category->getSubtitlesVideo($entity, $cid);
            $filters['formatVideo'] = $category->getFilterFormatVideo($entity, $cid);
        }

        if ($entity != 30) {
            $filters['avail'] = true;
        }

        $filters['binding'] = $category->getFilterBinding($entity, $cid);

        return $filters;
    }

    static function setFiltersData ($entity, $cid = 0, $data) {
        self::normalizeData($data);
        $key = 'filter_e' . $entity . '_c_' . $cid;
        if (Yii::app()->session[$key] != serialize(self::$data)) {
            Yii::app()->session[$key] = serialize(self::$data);
        }
        $filtersData = FiltersData::instance();
        $filtersData->setFiltersData($key, self::$data);
    }

    static function getFiltersData ($entity, $cid = 0) {
        $key = 'filter_e' . $entity . '_c_' . $cid;
        $filtersData = FiltersData::instance();
        if ($filtersData->isSetKey($key)) {
            $data = $filtersData->getFiltersData($key);
        }
        else $data = unserialize(Yii::app()->session[$key]);
        self::normalizeData($data);

        return self::$data;
    }

    static function setOneFiltersData ($entity, $cid = 0, $key, $value) {
        $data = self::getFiltersData($entity, $cid);
        if ($key == 'binding_id') $data[$key][0] = $value;
        else $data[$key] = $value;
        $data['entity'] = $entity;
        self::setFiltersData($entity, $cid, $data);
    }

    static function deleteEntityFilter ($entity, $cid = 0) {
        Yii::app()->session['filter_e' . $entity . '_c_' . $cid] = '';
        $filtersData = FiltersData::instance();
        $filtersData->deleteFiltersData();
    }

    static private function normalizeData ($data) {
        self::$data = [];
        self::$data['entity'] = $data['entity'] ?: $data['entity_val'] ?: false;
        if (!isset(self::$data['entity']) || self::$data['entity'] == '') {
            self::$data = [];
            return false;
        }
        self::$data['cid'] = $data['cid'] ?: $data['cid_val'] ?: 0;
        self::$data['avail'] = $data['avail'] ?: false;
        self::$data['lang_sel'] = $data['lang_sel'] ?: $data['langsel'] ?: Yii::app()->getRequest()->getParam('lang', false);
        self::$data['sort'] = $data['sort'] ?: false;
        self::$data['year_min'] = $data['year_min'] ?: $data['ymin'] ?: false;
        self::$data['year_max'] = $data['year_max'] ?: $data['ymax'] ?: false;
        self::$data['cost_min'] = $data['min_cost'] ?: $data['cost_min'] ?: $data['cmin'] ?: false;
        self::$data['cost_max'] = $data['max_cost'] ?: $data['cost_max'] ?: $data['cmax'] ?: false;
        self::$data['author'] = $data['author'] ?: false;
        self::$data['publisher'] = $data['publisher'] ?: $data['izda'] ?: false;
        self::$data['series'] = $data['series'] ?: $data['seria'] ?: false;
        self::$data['binding'] = $data['binding'] ?: $data['binding_id'] ?: false;
        self::$data['format_video'] = $data ['format_video'] ?: $data ['formatVideo'] ?: false;
        self::$data['lang_video'] = $data ['lang_video'] ?: $data ['langVideo'] ?: false;
        self::$data['subtitles_video'] = $data ['subtitles_video'] ?: $data ['subtitlesVideo'] ?: false;

    }
}
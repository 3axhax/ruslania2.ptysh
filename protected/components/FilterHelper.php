<?php


class FilterHelper
{
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

        $filters['binding'] = $category->getFilterBinding($entity, $cid);

        return $filters;
    }

    static function setFiltersData ($entity, $cid = 0, $data) {
        $key = 'filter_e' . $entity . '_c_' . $cid;
        if (Yii::app()->session[$key] != serialize($data)) {
            Yii::app()->session[$key] = serialize($data);
        }
        $filtersData = FiltersData::instance();
        $filtersData->setFiltersData($key, $data);
    }

    static function getFiltersData ($entity, $cid = 0) {
        $key = 'filter_e' . $entity . '_c_' . $cid;
        $filtersData = FiltersData::instance();
        if ($filtersData->isSetKey($key)) {
            $data = $filtersData->getFiltersData($key);
        }
        else $data = unserialize(Yii::app()->session[$key]);
        return $data;
    }

    static function setOneFiltersData ($entity, $cid = 0, $key, $value) {
        $data = self::getFiltersData($entity, $cid);
        if ($key == 'binding_id') $data[$key][0] = $value;
        else $data[$key] = $value;
        self::setFiltersData($entity, $cid, $data);
    }

    static function deleteEntityFilter ($entity, $cid = 0) {
        Yii::app()->session['filter_e' . $entity . '_c_' . $cid] = '';
        $filtersData = FiltersData::instance();
        $filtersData->deleteFiltersData();
    }
}
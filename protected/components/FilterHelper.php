<?php


class FilterHelper
{
    static function getEnableFilters ($entity, $cid = 0) {
        $filters = [];

        $category = new Category();
        $filters['max-min'] = $category->getFilterSlider($entity, $cid);
        $filters['author'] = true;
        if ($entity != 30 && $entity != 40) $filters['publisher'] = true;
        if ($entity != 60 && $entity != 50 && $entity != 30 && $entity != 40 && $entity != 20) $filters['series'] = true;
        if ($entity != 30) $filters['years'] = true;

        if ($entity == 40) {
            $filters['langVideo'] = $category->getFilterLangsVideo($entity, $cid);
            $filters['langSubtitles'] = $category->getSubtitlesVideo($entity, $cid);
            $filters['formatVideo'] = $category->getFilterFormatVideo($entity, $cid);
        }

        $filters['binding'] = $category->getFilterBinding($entity, $cid);

        return $filters;
    }

    static function setFiltersData ($entity, $cid = 0, $data) {
        if (Yii::app()->session['filter_e' . $entity . '_c_' . $cid] != serialize($data)) {
            Yii::app()->session['filter_e' . $entity . '_c_' . $cid] = serialize($data);
        }
    }

    static function getFiltersData ($entity, $cid = 0) {
        $data = unserialize(Yii::app()->session['filter_e' . $entity . '_c_' . $cid]);
        return $data;
    }

    static function setOneFiltersData ($entity, $cid = 0, $key, $value) {
        $data = unserialize(Yii::app()->session['filter_e' . $entity . '_c_' . $cid]);
        if ($key == 'binding_id') $data[$key][0] = $value;
        else $data[$key] = $value;
        Yii::app()->session['filter_e' . $entity . '_c_' . $cid] = serialize($data);
    }

    static function deleteEntityFilter ($entity, $cid = 0) {
        Yii::app()->session['filter_e' . $entity . '_c_' . $cid] = '';
    }
}
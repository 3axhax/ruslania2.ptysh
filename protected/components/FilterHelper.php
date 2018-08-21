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
    protected static $sessionData = [];

    static function getEnableFilters ($entity, $cid = 0) {
        $filters = [];

        $category = new Category();
        $filters['max-min'] = $category->getFilterSlider($entity, $cid);
        if (Entity::checkEntityParam($entity, 'authors')) $filters['author'] = true;
        if (Entity::checkEntityParam($entity, 'publisher')) $filters['publisher'] = true;
        if (Entity::checkEntityParam($entity, 'series')) $filters['series'] = true;
        if (Entity::checkEntityParam($entity, 'years')) $filters['years'] = true;

        if (Entity::checkEntityParam($entity, 'performers')) $filters['performers'] = true;
        if ($entity == 40) {
            $filters['langVideo'] = $category->getFilterLangsVideo($entity, $cid);
            $filters['langSubtitles'] = $category->getSubtitlesVideo($entity, $cid);
            $filters['formatVideo'] = $category->getFilterFormatVideo($entity, $cid);
        }
        if ($entity == 10) {
            $filters['pre_sale'] = true;
        }

        if ($entity != 30) {
            $filters['avail'] = true;
        }

        $filters['binding'] = $category->getFilterBinding($entity, $cid);

        return $filters;
    }

    static function setFiltersData ($entity, $cid = 0, $data) {
        self::normalizeData($data);
        $key = 'filter_e' . (int) $entity . '_c_' . (int) $cid;
        if (Yii::app()->session[$key] != serialize(self::$data)) {
            Yii::app()->session[$key] = serialize(self::$data);
        }
        $filtersData = FiltersData::instance();
        $filtersData->setFiltersData($key, self::$data);
    }

    static function getFiltersData ($entity, $cid = 0) {
        $key = 'filter_e' . (int) $entity . '_c_' . (int) $cid;
        self::$sessionData = unserialize(Yii::app()->session[$key]);
        $filtersData = FiltersData::instance();
        if ($filtersData->isSetKey($key)) {
            self::$sessionData = $filtersData->getFiltersData($key);
        }
        self::$data = [];
        self::getEntity();
        if (!isset(self::$data['entity']) || self::$data['entity'] == '') {
            self::$data = [];
            return self::$data;
        }
        self::getCid();
        self::getAvail();
        self::getLangSel();
        self::getSort();
        self::getYears();
        self::getCost();
        self::getAuthor();
        self::getPublisher();
        self::getSeries();
        self::getBinding();
        self::getFormatVideo();
        self::getLangVideo();
        self::getSubtitlesVideo();
        self::getPreSale();
        self::getPerformer();

        /*$filtersData = FiltersData::instance();
        if ($filtersData->isSetKey($key)) {
            $data = $filtersData->getFiltersData($key);
        }
        else $data = unserialize(Yii::app()->session[$key]);
        self::normalizeData($data);*/

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
        self::getEntity();
        if (!isset(self::$data['entity']) || self::$data['entity'] == '') {
            self::$data = [];
            return false;
        }
        self::$data['cid'] = $data['cid'] ?: $data['cid_val'] ?: 0;
        self::$data['avail'] = $data['avail'] ?: 0;
        self::$data['lang_sel'] = $data['lang_sel'] ?: $data['langsel'] ?: Yii::app()->getRequest()->getParam('lang', false);
        self::$data['sort'] = $data['sort'] ?: 8;
        self::$data['year_min'] = $data['year_min'] ?: $data['ymin'] ?: false;
        self::$data['year_max'] = $data['year_max'] ?: $data['ymax'] ?: false;
        self::$data['cost_min'] = $data['cost_min'] ?: $data['min_cost'] ?: $data['cmin'] ?: false;
        self::$data['cost_max'] = $data['cost_max'] ?: $data['max_cost'] ?: $data['cmax'] ?: false;
        self::$data['author'] = $data['author'] ?: false;
        self::$data['publisher'] = $data['publisher'] ?: false;
        self::$data['series'] = $data['series'] ?: $data['seria'] ?: false;
        self::$data['binding'] = $data['binding'] ?: $data['binding_id'] ?: false;
        self::$data['format_video'] = $data ['format_video'] ?: $data ['formatVideo'] ?: false;
        self::$data['lang_video'] = $data ['lang_video'] ?: $data ['langVideo'] ?: false;
        self::$data['subtitles_video'] = $data ['subtitles_video'] ?: $data ['subtitlesVideo'] ?: false;
        self::$data['pre_sale'] = $data ['pre_sale'] ?: false;
        self::$data['performer'] = $data ['performer'] ?: false;

    }

    static private function getEntity(){
        $entity = Yii::app()->getRequest()->getParam('entity', false);
        if ($entity !== false) {
            if (!ctype_digit($entity)) {
                $entity = Entity::ParseFromString($entity);
            }
            self::$data['entity'] = (int) $entity;
            return true;
        }
        $entity = Yii::app()->getRequest()->getParam('entity_val', false);
        if ($entity !== false) {
            self::$data['entity'] = (int) $entity;
            return true;
        }
        if (isset(self::$sessionData['entity']) && self::$sessionData['entity'] != '') {
            self::$data['entity'] = (int) self::$sessionData['entity'];
            return true;
        }
        return false;
    }

    static private function getCid(){
        $cid = Yii::app()->getRequest()->getParam('cid', false);
        if ($cid !== false) {
            self::$data['cid'] = (int) $cid;
            return true;
        }
        $cid = Yii::app()->getRequest()->getParam('cid_val', false);
        if ($cid !== false) {
            self::$data['cid'] = (int) $cid;
            return true;
        }
        if (isset(self::$sessionData['cid']) && self::$sessionData['cid'] != '') {
            self::$data['cid'] = (int) self::$sessionData['cid'];
            return true;
        }
        self::$data['cid'] = 0;
        return false;
    }

    static private function getAvail() {
        if (isset($_REQUEST['avail']) && ($_REQUEST['avail'] === "1" || $_REQUEST['avail'] === "0")) {
            $avail = Yii::app()->getRequest()->getParam('avail', true);
            self::$data['avail'] = (int) $avail;
            return true;
        }
        if (isset(self::$sessionData['avail']) && (self::$sessionData['avail'] === 1 || self::$sessionData['avail'] === 0)) {
            self::$data['avail'] = (int) self::$sessionData['avail'];
            return true;
        }
            $availCookie = Yii::app()->request->cookies['avail'];
            if (!empty($availCookie)) {
                $avail = $availCookie->value ? true : false;
            } else {
                $avail = true;
            }
        self::$data['avail'] = (int) $avail;
    }

    static private function getLangSel(){
        $lang_sel = Yii::app()->getRequest()->getParam('lang', false);
        if ($lang_sel !== false) {
            self::$data['lang_sel'] = (int) $lang_sel;
            return true;
        }
        if (isset(self::$sessionData['lang_sel']) && self::$sessionData['lang_sel'] != '') {
            self::$data['lang_sel'] = (int) self::$sessionData['lang_sel'];
            return true;
        }
        self::$data['lang_sel'] = 0;
        return false;
    }

    static private function getSort() {
        $sort = Yii::app()->getRequest()->getParam('sort', false);
        if ($sort !== false) {
            self::$data['sort'] = (int) $sort;
            return true;
        }
        if (isset(self::$sessionData['sort']) && self::$sessionData['sort'] != '') {
            self::$data['sort'] = (int) self::$sessionData['sort'];
            return true;
        }
        self::$data['sort'] = 8;
        return false;
    }

    static private function getYears() {
        $year_min = Yii::app()->getRequest()->getParam('year_min', false);
        $year_max = Yii::app()->getRequest()->getParam('year_max', false);
        if ($year_min !== false) {
            self::$data['year_min'] = (int) $year_min;
        }
        if ($year_max !== false) {
            self::$data['year_max'] = (int) $year_max;
        }
        if (isset(self::$sessionData['year_min']) && self::$sessionData['year_min'] != '') {
            self::$data['year_min'] = (int) self::$sessionData['year_min'];
        }
        if (isset(self::$sessionData['year_max']) && self::$sessionData['year_max'] != '') {
            self::$data['year_max'] = (int) self::$sessionData['year_max'];
        }
        if (!isset(self::$data['year_min'])) self::$data['year_min'] = false;
        if (!isset(self::$data['year_max'])) self::$data['year_max'] = false;
        return false;
    }

    static private function getCost() {
        $cost_min = Yii::app()->getRequest()->getParam('cost_min', false);
        $cost_max = Yii::app()->getRequest()->getParam('cost_max', false);
        if ($cost_min !== false) {
            self::$data['cost_min'] = (float) str_replace(',','.', $cost_min);
        }
        elseif (isset(self::$sessionData['cost_min']) && self::$sessionData['cost_min'] != '') {
            self::$data['cost_min'] = (float) str_replace(',','.', self::$sessionData['cost_min']);
        }
        if ($cost_max !== false) {
            self::$data['cost_max'] = (float) str_replace(',','.', $cost_max);
        }
        elseif (isset(self::$sessionData['cost_max']) && self::$sessionData['cost_max'] != '') {
            self::$data['cost_max'] = (float) str_replace(',','.', self::$sessionData['cost_max']);
        }
        if (!isset(self::$data['cost_min'])) self::$data['cost_min'] = false;
        if (!isset(self::$data['cost_max'])) self::$data['cost_max'] = false;
        return false;
    }

    static private function getAuthor() {
        $author = Yii::app()->getRequest()->getParam('aid', false);
        if ($author !== false) {
            self::$data['author'] = (int) $author;
            return true;
        }
        $author = Yii::app()->getRequest()->getParam('author', false);
        if ($author !== false) {
            self::$data['author'] = (int) $author;
            return true;
        }
        if (isset(self::$sessionData['author']) && self::$sessionData['author'] != '') {
            self::$data['author'] = (int) self::$sessionData['author'];
            return true;
        }
        self::$data['author'] = false;
        return false;
    }

    static private function getPublisher() {
        $publisher = Yii::app()->getRequest()->getParam('pid', false);
        if ($publisher !== false) {
            self::$data['publisher'] = (int) $publisher;
            return true;
        }
        $publisher = Yii::app()->getRequest()->getParam('publisher', false);
        if ($publisher !== false) {
            self::$data['publisher'] = (int) $publisher;
            return true;
        }
        if (isset(self::$sessionData['publisher']) && self::$sessionData['publisher'] != '') {
            self::$data['publisher'] = (int) self::$sessionData['publisher'];
            return true;
        }
        self::$data['publisher'] = false;
        return false;
    }

    static private function getSeries() {
        $series = Yii::app()->getRequest()->getParam('sid', false);
        if ($series !== false) {
            self::$data['series'] = (int) $series;
            return true;
        }
        $series = Yii::app()->getRequest()->getParam('series', false);
        if ($series !== false) {
            self::$data['series'] = (int) $series;
            return true;
        }
        if (isset(self::$sessionData['series']) && self::$sessionData['series'] != '') {
            self::$data['series'] = (int) self::$sessionData['series'];
            return true;
        }
        self::$data['series'] = false;
        return false;
    }

    static private function getBinding() {
        $binding = Yii::app()->getRequest()->getParam('bid', false);
        if ($binding !== false) {
            self::$data['binding'][0] = $binding;
            return true;
        }
        $binding = Yii::app()->getRequest()->getParam('binding', false);
        if ($binding !== false) {
            self::$data['binding'] = $binding;
            return true;
        }
        if (isset(self::$sessionData['binding']) && self::$sessionData['binding'] != '') {
            self::$data['binding'] = self::$sessionData['binding'];
            return true;
        }
        self::$data['binding'] = false;
        return false;
    }

    static private function getFormatVideo() {
        $format_video = Yii::app()->getRequest()->getParam('format_video', false);
        if ($format_video !== false) {
            self::$data['format_video'] = (int) $format_video;
            return true;
        }
        if (isset(self::$sessionData['format_video']) && self::$sessionData['format_video'] != '') {
            self::$data['format_video'] = (int) self::$sessionData['format_video'];
            return true;
        }
        self::$data['format_video'] = false;
        return false;
    }

    static private function getLangVideo() {
        $lang_video = Yii::app()->getRequest()->getParam('lang_video', false);
        if ($lang_video !== false) {
            self::$data['lang_video'] = (int) $lang_video;
            return true;
        }
        if (isset(self::$sessionData['lang_video']) && self::$sessionData['lang_video'] != '') {
            self::$data['lang_video'] = (int) self::$sessionData['lang_video'];
            return true;
        }
        self::$data['lang_video'] = false;
        return false;
    }

    static private function getSubtitlesVideo() {
        $subtitles_video = Yii::app()->getRequest()->getParam('subtitles_video', false);
        if ($subtitles_video !== false) {
            self::$data['subtitles_video'] = (int) $subtitles_video;
            return true;
        }
        if (isset(self::$sessionData['subtitles_video']) && self::$sessionData['subtitles_video'] != '') {
            self::$data['subtitles_video'] = (int) self::$sessionData['subtitles_video'];
            return true;
        }
        self::$data['subtitles_video'] = false;
        return false;
    }

    static private function getPreSale() {
        $pre_sale = Yii::app()->getRequest()->getParam('pre_sale', false);
        if ($pre_sale !== false) {
            self::$data['pre_sale'] = (int) $pre_sale;
            return true;
        }
        if (isset(self::$sessionData['pre_sale']) && self::$sessionData['pre_sale'] != '') {
            self::$data['pre_sale'] = (int) self::$sessionData['pre_sale'];
            return true;
        }
        self::$data['pre_sale'] = false;
        return false;
    }
    
    static private function getPerformer() {
        $performer = Yii::app()->getRequest()->getParam('performer', false);
        if ($performer !== false) {
            self::$data['performer'] = (int) $performer;
            return true;
        }
        if (isset(self::$sessionData['performer']) && self::$sessionData['performer'] != '') {
            self::$data['performer'] = (int) self::$sessionData['performer'];
            return true;
        }
        self::$data['performer'] = false;
        return false;
    }
}
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
     * 'pre_sale'
     * 'performer'
     * 'directors'
     * 'actors'
     * 'release_year_min'
     * 'release_year_max'
     * 'studio'
     */

    /**
     * @var array нужно, что бы много раз не получать self::$data;
     */
    protected static $_data = array();
    protected static $data = false;
    protected static $sessionData = [];

    static function getEnableFilters ($entity, $cid = 0) {
        $filters = [];

        $category = new Category();
        $filters['price'] = true;
        $filters['max-min'] = $category->getFilterSlider($entity, $cid);
        if (Entity::checkEntityParam($entity, 'authors')) $filters['author'] = true;
        if (Entity::checkEntityParam($entity, 'publisher')) $filters['publisher'] = true;
        if (Entity::checkEntityParam($entity, 'series')) $filters['series'] = true;
        if (Entity::checkEntityParam($entity, 'years')) $filters['years'] = true;
        if (Entity::checkEntityParam($entity, 'performers')) $filters['performers'] = true;
        if (Entity::checkEntityParam($entity, 'studios')) $filters['studios'] = true;

        if ($entity == Entity::SOFT) {
            unset($filters['author']);
            unset($filters['years']);
        }

        if ($entity == Entity::PRINTED && ($cid == 6 || in_array($cid, $category->GetChildren($entity, 6)))) {
            unset($filters);
            $filters['price'] = true;
        }

        if ($entity == Entity::VIDEO) {
            $filters['directors'] = true;
            $filters['actors'] = true;
            $filters['langVideo'] = $category->getFilterLangsVideo($entity, $cid);
            $filters['langSubtitles'] = $category->getSubtitlesVideo($entity, $cid);
            $filters['formatVideo'] = $category->getFilterFormatVideo($entity, $cid);
            $filters['release_years'] = true;
        }
        if ($entity == Entity::BOOKS) {
            $filters['pre_sale'] = true;
        }

        if ($entity != Entity::PERIODIC) {
            $filters['avail'] = true;
        }

        if ($entity == Entity::PERIODIC) {
            $filters['country'] = $category->getPeriodicCountry($entity, $cid);
        }

        $filters['binding'] = $category->getFilterBinding($entity, $cid);

        return $filters;
    }

    static function setFiltersData ($entity, $cid = 0, $data, $saveDataInSession = 1) {
        self::normalizeData($data, $entity);
        $key = 'filter_e' . (int) $entity . '_c_' . (int) $cid;
        if ($saveDataInSession) {
            if (Yii::app()->request->cookies[$key]->value != serialize(self::$data)) {
                Yii::app()->request->cookies[$key] = new CHttpCookie($key, serialize(self::$data));
            }
            if (Yii::app()->session[$key] != serialize(self::$data)) {
                Yii::app()->session[$key] = serialize(self::$data);
            }
        }
        self::$_data[$key] = self::$data;
        $filtersData = FiltersData::instance();
        $filtersData->setFiltersData($key, self::$data);
    }

    static function getFiltersData ($entity, $cid = 0) {
        $key = 'filter_e' . (int) $entity . '_c_' . (int) $cid;
        if (!isset(self::$_data[$key])) {
            if (defined('cronAction')&&cronAction) {
                self::$_data = array();
                self::$_data[$key] = array();
                self::$_data[$key]['entity'] = $entity;
                self::$_data[$key]['cid'] = (int) $cid;
            }
            else {
                if (isset(Yii::app()->request->cookies[$key]->value) && Yii::app()->request->cookies[$key]->value != '') {
                    self::$sessionData = unserialize(Yii::app()->request->cookies[$key]->value);
                }
                if (isset(Yii::app()->session[$key]) && Yii::app()->session[$key] != '') {
                    self::$sessionData = unserialize(Yii::app()->session[$key]);
                }
                $filtersData = FiltersData::instance();
                if ($filtersData->isSetKey($key)) {
                    self::$sessionData = $filtersData->getFiltersData($key);
                }

                $data = self::$data;
                self::$data = [];
                foreach (array('authorStr', 'actorsStr', 'directorsStr', 'seriesStr', 'publishersStr', 'performersStr') as $strName) {
                    if (!empty($data[$strName])) self::$data[$strName] = $data[$strName];
                }
                unset($data);
                self::getEntity($entity);
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
                if (Entity::checkEntityParam($entity, 'authors')) self::getAuthor();
                if (Entity::checkEntityParam($entity, 'publisher')) self::getPublisher();
                if (Entity::checkEntityParam($entity, 'series')) self::getSeries();
                self::getBinding();
                self::getFormatVideo();
                self::getLangVideo();
                if (Entity::checkEntityParam($entity, 'subtitles')) self::getSubtitlesVideo();
                self::getPreSale();
                if (Entity::checkEntityParam($entity, 'performers')) self::getPerformer();
                if (Entity::checkEntityParam($entity, 'studios')) self::getStudio();
                self::getCountry();
                if (Entity::checkEntityParam($entity, 'directors')) self::getDirector();
                if (Entity::checkEntityParam($entity, 'actors')) self::getActor();
                self::getReleaseYears();
                self::getSale();
                self::$_data[$key] = self::$data;
            }
        }

        return self::$_data[$key];
    }

    static function setOneFiltersData ($entity, $cid = 0, $key, $value) {
        $data = self::getFiltersData($entity, $cid);
        if ($key == 'binding_id') $data[$key][0] = $value;
        else $data[$key] = $value;
        $data['entity'] = $entity;
        self::setFiltersData($entity, $cid, $data);
    }

    static function deleteEntityFilter ($entity, $cid = 0) {
        $key = 'filter_e' . $entity . '_c_' . $cid;
        Yii::app()->session[$key] = '';
        Yii::app()->request->cookies[$key] = new CHttpCookie($key, serialize(''));
        $filtersData = FiltersData::instance();
        $filtersData->deleteFiltersData();
    }

    /** чистит фильтр в сессии и куках если на страницу попали с какой-то определенной страницы
     * @param $roure
     * @param $entity
     * @param int $cid
     * @throws CHttpException
     */
    static function deleteEntityFilterIfReferer ($roure, $entity, $cid = 0) {
        $referer = Yii::app()->getRequest()->getUrlReferrer();
        $request = new MyRefererRequest();
        $request->setFreePath($referer);
        //$request->getParams();//здесь $entity (текстовый), id и другие параметры из адреса referer
        $refererRoute = Yii::app()->getUrlManager()->parseUrl($request);

        if ($roure == $refererRoute) self::deleteEntityFilter ($entity, $cid);
    }

    static private function normalizeData ($data, $entity) {
        self::$data = [];
        self::getEntity($entity);
        if (!isset(self::$data['entity']) || self::$data['entity'] == '') {
            self::$data = [];
            return;
        }
        self::$data['cid'] = $data['cid'] ?: $data['cid_val'] ?: 0;
        self::$data['sale'] = $data['sale'] ?: 0;
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
        self::$data['country'] = $data ['country'] ?: 0;
        self::$data['directors'] = $data['directors'] ?: false;
        self::$data['actors'] = $data['actors'] ?: false;
        self::$data['release_year_min'] = $data['release_year_min'] ?: false;
        self::$data['release_year_max'] = $data['release_year_max'] ?: false;

        //далее строковые значения из фильтра для живого поиска, сохраняю только с длиной строки > 2
        if (!empty($data['new_author'])&&(mb_strlen($data['new_author'], 'utf-8') > 2)) self::$data['authorStr'] = $data['new_author'];
        if (!empty($data['new_actors'])&&(mb_strlen($data['new_actors'], 'utf-8') > 2)) self::$data['actorsStr'] = $data['new_actors'];
        if (!empty($data['new_directors'])&&(mb_strlen($data['new_directors'], 'utf-8') > 2)) self::$data['directorsStr'] = $data['new_directors'];
        if (!empty($data['new_performer'])&&(mb_strlen($data['new_performer'], 'utf-8') > 2)) self::$data['performersStr'] = $data['new_performer'];

        if (!empty($data['new_series'])&&(mb_strlen($data['new_series'], 'utf-8') > 2)) self::$data['seriesStr'] = $data['new_series'];
        if (!empty($data['new_publisher'])&&(mb_strlen($data['new_publisher'], 'utf-8') > 2)) self::$data['publishersStr'] = $data['new_publisher'];
    }

    static private function getEntity($entity){
        if (empty($entity)) $entity = Yii::app()->getRequest()->getParam('entity', false);
        if (!empty($entity)) {
            if (!is_numeric($entity)) {
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

    static private function getSale(){
        if (Yii::app()->getController()->action->id === 'salelist') {
            self::$data['sale'] = 1;
            return true;
        }

        $sale = (int)Yii::app()->getRequest()->getParam('sale');
        if (($sale > 0)/*&&(Yii::app()->request->isPostRequest)*/) {
            self::$data['sale'] = 1;
            return true;
        }
        self::$data['sale'] = 0;
        return false;
    }

    static private function getAvail() {
        if (isset($_REQUEST['avail']) && ($_REQUEST['avail'] === "1" || $_REQUEST['avail'] === "0")) {
            $avail = Yii::app()->getRequest()->getParam('avail', true);
            self::$data['avail'] = (int) $avail;
            return;
        }
        if (isset(self::$sessionData['avail']) && (self::$sessionData['avail'] === 1 || self::$sessionData['avail'] === 0)) {
            self::$data['avail'] = (int) self::$sessionData['avail'];
            return;
        }
/*        $availCookie = Yii::app()->request->cookies['avail'];
        if (!empty($availCookie)) {
            $avail = $availCookie->value ? true : false;
        } else {
            $avail = true;
        }*/
        self::$data['avail'] = 1;
    }

    static private function getLangSel(){
        $lang_sel = Yii::app()->getRequest()->getParam('lang', false);
        if ($lang_sel !== false) {
            self::$data['lang_sel'] = (int) $lang_sel;
            return true;
        }
        self::$data['lang_sel'] = 0;
        return false;
    }

    static private function getSort() {
        if (!isset(self::$data['sort'])) {
            $sort = Yii::app()->getRequest()->getParam('sort', false);
            if ($sort !== false) {
                self::$data['sort'] = (int) $sort;
                return true;
            }
/*            if (isset(self::$sessionData['sort']) && self::$sessionData['sort'] != '') {
                self::$data['sort'] = (int) self::$sessionData['sort'];
                return true;
            }*/
            self::$data['sort'] = SortOptions::GetDefaultSort();
        }
        return false;
    }

    static private function getYears() {
        $year = false;
        if (Yii::app()->getController()->action->id == 'byyear')
            $year = Yii::app()->getRequest()->getParam('year', false);
        if ($year !== false) {
            self::$data['year_min'] = (int) $year;
            self::$data['year_max'] = (int) $year;
            return true;
        }
        $year_min = Yii::app()->getRequest()->getParam('year_min', false);
        $year_max = Yii::app()->getRequest()->getParam('year_max', false);
        if ($year_min !== false && $year_min !== '') {
            self::$data['year_min'] = (int) $year_min;
        }
        if ($year_max !== false && $year_max !== '') {
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
        if ($cost_min !== false && $cost_min !== '') {
            self::$data['cost_min'] = (float) str_replace(',','.', $cost_min);
        }
        elseif (isset(self::$sessionData['cost_min']) && self::$sessionData['cost_min'] != '') {
            self::$data['cost_min'] = (float) str_replace(',','.', self::$sessionData['cost_min']);
        }
        if ($cost_max !== false && $cost_max !== '') {
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
        $publisher = false;
        if (Yii::app()->getController()->action->id == 'bypublisher')
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
        $series = Yii::app()->getRequest()->getParam('seria', false);
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

        //тип издания
        $binding = Yii::app()->getRequest()->getParam('type', false);
        if ($binding !== false) {
            self::$data['binding'][0] = $binding;
            return true;
        }
        //тип носителя
        $binding = Yii::app()->getRequest()->getParam('mid', false);
        if ($binding !== false) {
            self::$data['binding'][0] = $binding;
            return true;
        }
        self::$data['binding'] = false;
        return false;
    }

    static private function getFormatVideo() {
        $format_video = false;
        if (Yii::app()->getController()->action->id == 'bymedia')
            $format_video = Yii::app()->getRequest()->getParam('mid', false);
        if ($format_video !== false) {
            self::$data['format_video'] = (int) $format_video;
            return true;
        }
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
        $lang_video = false;
        if (Yii::app()->getController()->action->id == 'byaudiostream')
            $lang_video = Yii::app()->getRequest()->getParam('sid', false);
        if ($lang_video !== false) {
            self::$data['lang_video'] = (int) $lang_video;
            return true;
        }
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
        $subtitles_video = false;
        if (Yii::app()->getController()->action->id == 'bysubtitle')
            $subtitles_video = Yii::app()->getRequest()->getParam('sid', false);
        if ($subtitles_video !== false) {
            self::$data['subtitles_video'] = (int) $subtitles_video;
            return true;
        }
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
        $performer = false;
        if (Yii::app()->getController()->action->id == 'byperformer')
        $performer = Yii::app()->getRequest()->getParam('pid', false);
        if ($performer !== false) {
            self::$data['performer'] = (int) $performer;
            return true;
        }
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

    static private function getStudio() {
//        $studio = false;
//        if (Yii::app()->getController()->action->id == 'bystudio')
        $studio = Yii::app()->getRequest()->getParam('sid', false);
        if ($studio !== false) {
            self::$data['studio'] = (int) $studio;
            return true;
        }
        if (isset(self::$sessionData['studio']) && self::$sessionData['studio'] != '') {
            self::$data['studio'] = (int) self::$sessionData['studio'];
            return true;
        }
        self::$data['studio'] = false;
        return false;
    }

    static private function getCountry() {
        $country = Yii::app()->getRequest()->getParam('country', false);
        if ($country !== false) {
            self::$data['country'] = (int) $country;
            return true;
        }
        if (isset(self::$sessionData['country']) && self::$sessionData['country'] != '') {
            self::$data['country'] = (int) self::$sessionData['country'];
            return true;
        }
        self::$data['country'] = 0;
        return false;
    }

    static private function getDirector() {
        $directors = Yii::app()->getRequest()->getParam('did', false);
        if ($directors !== false) {
            self::$data['directors'] = (int) $directors;
            return true;
        }
        $directors = Yii::app()->getRequest()->getParam('directors', false);
        if ($directors !== false) {
            self::$data['directors'] = (int) $directors;
            return true;
        }
        if (isset(self::$sessionData['directors']) && self::$sessionData['directors'] != '') {
            self::$data['directors'] = (int) self::$sessionData['directors'];
            return true;
        }
        self::$data['directors'] = false;
        return false;
    }

    static private function getActor() {
        $actors = Yii::app()->getRequest()->getParam('aid', false);
        if ($actors !== false) {
            self::$data['actors'] = (int) $actors;
            return true;
        }
        $actors = Yii::app()->getRequest()->getParam('actors', false);
        if ($actors !== false) {
            self::$data['actors'] = (int) $actors;
            return true;
        }
        if (isset(self::$sessionData['actors']) && self::$sessionData['actors'] != '') {
            self::$data['actors'] = (int) self::$sessionData['actors'];
            return true;
        }
        self::$data['actors'] = false;
        return false;
    }

    static private function getReleaseYears () {
        $release_year = false;
        if (Yii::app()->getController()->action->id == 'byyearrelease')
            $release_year = Yii::app()->getRequest()->getParam('year', false);
        if ($release_year !== false) {
            self::$data['release_year_min'] = (int) $release_year;
            self::$data['release_year_max'] = (int) $release_year;
            return true;
        }
        $release_year_min = Yii::app()->getRequest()->getParam('release_year_min', false);
        $release_year_max = Yii::app()->getRequest()->getParam('release_year_max', false);
        if ($release_year_min !== false && $release_year_min !== '') {
            self::$data['release_year_min'] = (int) $release_year_min;
        }
        if ($release_year_max !== false && $release_year_max !== '') {
            self::$data['release_year_max'] = (int) $release_year_max;
        }
        if (isset(self::$sessionData['release_year_min']) && self::$sessionData['release_year_min'] != '') {
            self::$data['release_year_min'] = (int) self::$sessionData['release_year_min'];
        }
        if (isset(self::$sessionData['release_year_max']) && self::$sessionData['release_year_max'] != '') {
            self::$data['release_year_max'] = (int) self::$sessionData['release_year_max'];
        }
        if (!isset(self::$data['release_year_min'])) self::$data['release_year_min'] = false;
        if (!isset(self::$data['release_year_max'])) self::$data['release_year_max'] = false;
        return false;
    }
}
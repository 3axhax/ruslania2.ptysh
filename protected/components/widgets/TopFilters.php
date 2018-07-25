<?php

class TopFilters extends CWidget
{
    public $lang;
    public $entity;
    public $cid;
    public $filters;
    public $title_cat;
    public $filter_data;

    public function run() {
        $ui = Yii::app()->ui;
        $title_search = $ui->item('A_NEW_SEARCH_CAT').': "'.$this->title_cat.'"';
        if($this->cid == 0) $title_search =  $ui->item('A_NEW_SEARCH_ENT').': "'.Entity::GetTitle($this->entity).'"';

        // Заполняем цену и год в первую очередь из фильтра, потом из максимальных и минимальных значений
        if (isset($this->filter_data['min_cost']) && $this->filter_data['min_cost'] != '') $min_p = $this->filter_data['min_cost'];
        elseif (isset($this->filters['max-min'][2]) && $this->filters['max-min'][2] != '') $min_p = $this->filters['max-min'][2];
        else $min_p = false;
        $this->filters['max-min'][2] = $min_p;

        if (isset($this->filter_data['max_cost']) && $this->filter_data['max_cost'] != '') $max_p = $this->filter_data['max_cost'];
        elseif (isset($this->filters['max-min'][3]) && $this->filters['max-min'][3] != '') $max_p = $this->filters['max-min'][3];
        else $max_p = false;
        $this->filters['max-min'][3] = $max_p;

        if (isset($this->filter_data['ymin']) && $this->filter_data['ymin'] != '') $min_y = $this->filter_data['ymin'];
        elseif (isset($this->filters['max-min'][0]) && $this->filters['max-min'][0] != '') $min_y = $this->filters['max-min'][0];
        else $min_y = false;
        $this->filters['max-min'][0] = $min_y;

        if (isset($this->filter_data['ymax']) && $this->filter_data['ymax'] != '') $max_y = $this->filter_data['ymax'];
        elseif (isset($this->filters['max-min'][1]) && $this->filters['max-min'][1] != '') $max_y = $this->filters['max-min'][1];
        else $max_y = false;
        $this->filters['max-min'][1] = $max_y;
        // Конец блока

        $this->render('top_filters', array(
            'filters' => $this->filters,
            'lang' => $this->lang,
            'entity' => $this->entity,
            'ui' => $ui,
            'title_search' => $title_search,
            'filter_data' => $this->filter_data,
            'cid' => $this->cid));
    }
}
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
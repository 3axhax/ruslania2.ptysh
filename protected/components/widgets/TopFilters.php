<?php

class TopFilters extends CWidget
{
    public $lang;
    public $entity;
    public $cid;

    public function run() {
        $this->render('top_filters', array(
            'lang' => $this->lang,
            'entity' => $this->entity,
            'cid' => $this->cid));
    }
}
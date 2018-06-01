<?php

class PagesWidget extends MyWidget
{
    public $paginatorInfo;
    public $pageChar;

    public function run()
    {
        $this->render('pages_widget');
    }
}
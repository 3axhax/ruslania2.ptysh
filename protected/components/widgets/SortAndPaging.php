<?php

class SortAndPaging extends MyWidget
{
    public $paginatorInfo;

    public function run()
    {
        $this->render('sort_and_paging');
    }
}
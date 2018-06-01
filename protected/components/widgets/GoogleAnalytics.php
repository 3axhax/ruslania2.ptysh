<?php

class GoogleAnalytics extends CWidget
{
    public $account;
    public $domain;

    public function run()
    {
        $this->render('ga');
    }
}
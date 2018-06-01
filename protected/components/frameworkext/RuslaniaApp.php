<?php


class RuslaniaApp extends CWebApplication
{
    public $currency = 1; // EUR
}

function mydump($obj)
{
    if (isset($_COOKIE['XDEBUG_SESSION']) && $_COOKIE['XDEBUG_SESSION'] == 'PHPSTORM' && $_SERVER['REMOTE_ADDR'] == '83.145.211.92')
    {
        echo '<pre>';
        echo CHtml::encode(print_r($obj, true));
        echo '</pre>';
    }
}
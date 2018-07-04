<?php


class RuslaniaApp extends CWebApplication
{
    private $_languageInstalled = false;//признак, что Yii::app()->language уже установлен. Много раз изменяется, что не дает возможность правильной установки языковой версии сайта
    public $currency = 1; // EUR

    function setLanguage($language) {
        if (!$this->_languageInstalled&&$this->params['ValidLanguages']) {
            //первый раз после настроек
            parent::setLanguage($language);
            $this->_languageInstalled = true;
        }
    }
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
<?php

class DMultilangHelper
{
    public static function processLangInUrl($url)
    {
        $domains = explode('/', ltrim($url, '/'));
		
		
		
        $isLangExists = in_array($domains[0], Yii::app()->params['ValidLanguages']);
        $isDefaultLang = $domains[0] == Yii::app()->params['DefaultLanguage'];

        if ($isLangExists && !$isDefaultLang)
        {
			
            $lang = array_shift($domains);
            Yii::app()->language = $lang;
        
		}

        return '/' . implode('/', $domains);
    }

    public static function addLangToUrl($url)
    {
        $domains = explode('/', ltrim($url, '/'));
        $isHasLang = in_array($domains[0], array_keys(Yii::app()->params['ValidLanguages']));
        $isDefaultLang = Yii::app()->language == Yii::app()->params['DefaultLanguage'];

        if ($isHasLang && $isDefaultLang)
            array_shift($domains);

        if (!$isHasLang && !$isDefaultLang)
            array_unshift($domains, Yii::app()->language);

        return '/' . implode('/', $domains);
    }
}
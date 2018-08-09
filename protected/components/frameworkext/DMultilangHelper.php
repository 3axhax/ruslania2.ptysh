<?php

class DMultilangHelper
{

    public static function processLangInUrl($url) {
        $ind = mb_strpos($url, "?", null, 'utf-8');
        if ($ind !== false) {
            $domains = explode('/', ltrim(mb_substr($url, 0, $ind, 'utf-8'), '/'));
        }
        else $domains = explode('/', ltrim($url, '/'));
		
/*        $isLangExists = in_array($domains[0], Yii::app()->params['ValidLanguages']);
        $isDefaultLang = $domains[0] == Yii::app()->params['DefaultLanguage'];

        if ($isLangExists && !$isDefaultLang) {
			
            $lang = array_shift($domains);
            Yii::app()->language = $lang;
        
		}*/

        $validLanguages = Yii::app()->params['ValidLanguages'];
        $paramLang = (string)Yii::app()->getRequest()->getParam('language');

        $langs = array(
            'byPath'=>$domains[0],
            'byParam'=>$paramLang,
            'default'=>Yii::app()->params['DefaultLanguage'],
        );

        $showSelLang = (int) Yii::app()->getRequest()->cookies['showSelLang']->value;
        if (!empty($_GET['sel'])) {
            $cookie = new CHttpCookie('showSelLang', '1');
            $cookie->expire = time() + (60*60*24*20000); // 20000 days
            Yii::app()->getRequest()->cookies['showSelLang'] = $cookie;
            $showSelLang = 1;
        }

        foreach ($langs as $by=>$lang) {
            if (!empty($lang)&&in_array($lang, $validLanguages)) {
                Yii::app()->language = $lang;
                switch ($by) {
                    case 'byPath':
                        $cookie = new CHttpCookie('showSelLang', '1');
                        $cookie->expire = time() + (60*60*24*20000); // 20000 days
                        Yii::app()->getRequest()->cookies['showSelLang'] = $cookie;
                        $showSelLang = 1;
                        if ($paramLang !== '') {
                            //если адрес начинается с языка, то из параметров убираю language
                            $url = preg_replace("/\blanguage=" . $paramLang . "\b/ui", '', $url);
                            $url = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $url);
                            $url = preg_replace("/\?+$/ui", '', $url);
                            if(!empty($showSelLang)) Yii::app()->getRequest()->redirect($url,true,301);
                        }
                        break;
                    case 'byParam':
                        if (Yii::app()->language !== 'rut') {
                            //если язык в паметрах и не rut
                            $url = preg_replace("/\blanguage=" . Yii::app()->language . "\b/ui", '', $url, -1);
                            $url = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $url);
                            $url = preg_replace("/\?+$/ui", '', $url);
                            $url = '/' . implode('/', array(Yii::app()->language, ltrim($url, '/')));
                            if(!empty($showSelLang)) Yii::app()->getRequest()->redirect($url,true,301);
                        }
                        break;
                    default:
                        if (($paramLang !== '')&&(Yii::app()->language !== $paramLang)) throw new CHttpException(404);

                        if ($paramLang !== '') {
                            $url = preg_replace("/\blanguage=" . $paramLang . "\b/ui", '', $url, -1);
                            $url = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $url);
                            $url = preg_replace("/\?+$/ui", '', $url);
                        }
                        $url = '/' . implode('/', array(Yii::app()->language, ltrim($url, '/')));
                        if(!empty($showSelLang)) Yii::app()->getRequest()->redirect($url,true,301);
                        break;
                }
                return;
            }
        }
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
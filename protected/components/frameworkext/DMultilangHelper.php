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
                        if ((count($domains) > 1)&&!empty($domains[1])) {
                            $cookie = new CHttpCookie('showSelLang', '1');
                            $cookie->expire = time() + (60*60*24*20000); // 20000 days
                            Yii::app()->getRequest()->cookies['showSelLang'] = $cookie;
                            $showSelLang = 1;
                        }
                        if ($paramLang !== '') {
                            $url = preg_replace("/\bavail=1\b/ui", '', $url, -1);
                            //если адрес начинается с языка, то из параметров убираю language
                            $url = preg_replace("/\blanguage=" . $paramLang . "\b/ui", '', $url);
                            $url = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $url);
                            $url = preg_replace("/\?+$/ui", '', $url);
                            if(!empty($showSelLang)) Yii::app()->getRequest()->redirect($url,true,301);
                        }
                        break;
                    case 'byParam':
                        if (Yii::app()->language !== 'rut') {
                            $url = preg_replace("/\bavail=1\b/ui", '', $url, -1);
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
//                        if (empty($domains[0])) Yii::app()->getRequest()->redirect('/' . Yii::app()->language . '/',true,301);

                        $oldUrl = $url;
                        if ($ind !== false) {
                            $oldUrl = mb_substr($url, 0, $ind, 'utf-8');
                        }
                        HrefTitles::get()->redirectOldPage($oldUrl);

                        if ($paramLang !== '') {
                            $url = preg_replace("/\bavail=1\b/ui", '', $url, -1);
                            $url = preg_replace("/\blanguage=" . $paramLang . "\b/ui", '', $url, -1);
                            $url = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $url);
                            $url = preg_replace("/\?+$/ui", '', $url);
                        }
                        $url = ltrim($url, '/');
                        self::_photoRedirect($url);
                        $url = '/' . implode('/', array(Yii::app()->language, $url));
                        Yii::app()->getRequest()->redirect($url, true, 301);
                        break;
                }
                return;
            }
        }
    }

    protected function _photoRedirect($url) {
        $ean = '';
        $label = 'o';
        if (mb_strpos($url, 'pictures/big/', null, 'utf-8') === 0) {
            $ean = mb_substr($url, mb_strlen('pictures/big/', 'utf-8'), null, 'utf-8');
        }
        elseif (mb_strpos($url, 'pictures/small/', null, 'utf-8') === 0) {
            $ean = mb_substr($url, mb_strlen('pictures/small/', 'utf-8'), null, 'utf-8');
            $label = 'l';
        }
        if (!empty($ean)) {
            $ean = explode('.', $ean);
            $ean = array_shift($ean);
            if (!preg_match("/\D/ui", $ean)) {
                $model = new SphinxProducts(1, 0);
                $code = $model->isCode($ean);
                if (!empty($code)) {
                    $find = $model->getByCode($code, $ean);
                    if (!empty($find)) {
                        $find = array_shift($find);
                        $photoTable = Entity::GetEntitiesList()[$find['entity']]['photo_table'];
                        $modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
                        /**@var $photoModel ModelsPhotos*/
                        $photoModel = $modelName::model();
                        $photoId = $photoModel->getFirstId($find['id']);
                        if ($photoId > 0) {
                            $photoPath = $photoModel->getUnixDir() . $photoModel->getRelativePath($photoId) . $label . '.jpg';
                            if (($label === 'o')&&(!file_exists($photoPath))) {
                                $label = 'd';
                                $photoPath = $photoModel->getUnixDir() . $photoModel->getRelativePath($photoId) . $label . '.jpg';
                            }
                            if (file_exists($photoPath)) {
//                                Yii::app()->getRequest()->redirect($photoModel->getHrefPath($photoId, $label, '', 'jpg'), true, 301);
                                header("Content-type:image/jpeg");
                                header('Content-Length:' . filesize($photoPath));
                                readfile($photoPath);
                                exit;
                            }
                        }
                    }
                }
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
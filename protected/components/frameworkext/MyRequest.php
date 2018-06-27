<?php

class MyRequest extends CHttpRequest
{
    public $dontCheckCsrf = array();

    private $_requestUri = null;

    public function getRequestUri() {
        parent::getRequestUri();
        DMultilangHelper::processLangInUrl($this->_requestUri);

        return $this->_requestUri;
    }

    public function getOriginalUrl()
    {
        return $this->getOriginalRequestUri();
    }

    public function getOriginalRequestUri()
    {
        return DMultilangHelper::addLangToUrl($this->getRequestUri());
    }

    function getPathInfo() {
        $path = parent::getPathInfo();
        $language = Yii::app()->language;
        if (!empty($language)) {
            $langLen = mb_strlen($language, 'utf-8');
            if ($path == $language) $path = '';
            elseif (mb_strpos($path, $language . '/', null, 'utf-8') === 0) $path = mb_substr($path, $langLen+1, null, 'utf-8');
        }
        return $path;
    }

    public function validateCsrfToken($event)
    {
        // only validate POST requests
        if ($this->getIsPostRequest())
        {
            $route = Yii::app()->getUrlManager()->parseUrl(Yii::app()->getRequest());

            $explode = explode('/', $route);
            if(count($explode) > 2)
            {
                $explode = array_splice($explode, 0, 2);
                $route = implode('/', $explode);
            }

            if(in_array($route, $this->dontCheckCsrf)) return;
            $csrfValue = @$_POST[$this->csrfTokenName];

            $cookies = $this->getCookies();
            if ($cookies->contains($this->csrfTokenName) && $csrfValue !== null)
            {
                $tokenFromCookie = $cookies->itemAt($this->csrfTokenName)->value;
                $valid = $tokenFromCookie === $csrfValue;
            }
            else
                $valid = false;
            if (!$valid)
            {
                if(Yii::app()->request->isAjaxRequest)
                {
                    echo CJSON::encode(array('hasError' => true, 'error' => 'Wrong CSRF'));
                    Yii::app()->end();
                }
                else
                {
                    throw new CHttpException(400, Yii::t('yii', 'The CSRF token could not be verified.'));
                }
            }
        }
    }
}
